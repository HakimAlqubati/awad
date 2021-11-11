<?php

namespace App\Http\Controllers;

use App\Jobs\FcmNotificationJob;
use App\Models\Branch;
use App\Models\NotificationOrder;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\RequestState;
use App\Models\UnitPrice;
use App\Models\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use stdClass;
use PDF;
use App\Models\Unit;
use Illuminate\Support\Facades\URL;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->request = $request;

        $orders = Order::where(
            function ($query) {
                if (
                    $this->request->id &&
                    $this->request->created_by &&
                    $this->request->request_state_id &&
                    $this->request->restricted_state_id &&
                    $this->request->branch_id &&
                    $this->request->order_id
                ) {
                    return  $query->where(
                        [
                            ['id', '=', $this->request->id],
                            ['created_by', '=', $this->request->created_by],
                            ['request_state_id', '=', $this->request->request_state_id],
                            ['restricted_state_id', '=', $this->request->restricted_state_id],
                            ['branch_id', '=', $this->request->branch_id],
                            ['id', '=', $this->request->order_id],
                        ]
                    );
                } elseif (
                    $this->request->created_by &&
                    $this->request->request_state_id
                ) {
                    return  $query->where(
                        [

                            ['created_by', '=', $this->request->created_by],
                            ['request_state_id', '=', $this->request->request_state_id],
                        ]
                    );
                } elseif (
                    $this->request->created_by
                ) {
                    return  $query->where(
                        [
                            ['created_by', '=', $this->request->created_by],
                        ]
                    );
                } elseif (
                    $this->request->order_id
                ) {
                    return  $query->where(
                        [
                            ['id', '=', $this->request->order_id],
                        ]
                    );
                
                  
	 
                } elseif (
                    $this->request->order_id&&
                    $this->request->created_by&&
                    $this->request->request_state_id
                ) {
                    return  $query->where(
                        [
                            ['id', '=', $this->request->order_id],
                            ['created_by', '=', $this->request->created_by],
                            ['request_state_id', '=', $this->request->request_state_id],
                        ]
                    );
                }
            }
        )->orderBy('id', 'DESC')->get();
        if (count($orders) > 0) {
            foreach ($orders as $key => $value) {

                $obj = new stdClass();
                $obj->id = $value->id;
                $obj->desc = $value->desc;
                $obj->created_by = $value->created_by;
                $obj->created_by_user_name = $this->getUserDataById($value->created_by)[0]->name;
                $obj->request_state_id = $value->request_state_id;
                $obj->request_state_name = RequestState::where('id', $value->request_state_id)->get()[0]->name;
                $obj->restricted_state_id = $value->restricted_state_id;
                $obj->restricted_state_name = RequestState::where('id', $value->restricted_state_id)->get()[0]->name;
                $obj->branch_id = $value->branch_id;
                $obj->full_quantity = $value->full_quantity;
                $obj->notes = $value->notes;
                if ($value->branch_id != 0) {
                    $obj->branch_name =  Branch::where('id', $value->branch_id)->get()[0]->name;
                }
                $obj->created_at = $value->created_at;
                $obj->updated_at = $value->updated_at;
                $obj->total_price = $this->getOrderDetailsTotalPrice($value->id);
                $obj->order_details = $this->getOrderDetailsByOrderId($value->id);
                $array[] = $obj;
            }
            return $array;
        } else {
            return  [];
        }
    }
    public function getOrderDetailsByOrderId($id)
    {
        $data = OrderDetails::where('order_id', $id)->get();
        $totalPrice = 0;
        $fresult = array();
        foreach ($data as $key => $value) {
            $obj = new stdClass();
            $obj->id = $value->id;
            $obj->order_id = $value->order_id;
            $obj->qty = $value->qty;
            $obj->price = $value->price;
            $obj->product_unit_id = $value->product_unit_id;
            $obj->product_unit_name =   $this->getUnitNameById($value->product_unit_id)[0]->name;
            $obj->product_id = $value->product_id;
            $obj->product_name = $this->getProductProductNameById($value->product_id)[0]->name;
            $fresult[] = $obj;

            $price = $value->price;
            $totalPrice += $price;
        }


        return $fresult;
    }


    public function getOrderDetailsTotalPrice($id)
    {
        $data = OrderDetails::where('order_id', $id)->get();
        $totalPrice = 0;

        foreach ($data as $key => $value) {
            $price = $value->price;
            $totalPrice += $price;
        }


        return  $totalPrice;
    }
    public function getUserDataById($id)
    {
        return User::where('id', $id)->get();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentRole =  $request->user()->role_id;
        $currentUser = $request->user();
        $branch = Branch::where('manager_id', $currentUser->id)->first();
        $storeManager = User::where('id', 3)->first();

        FcmNotificationJob::dispatchNow("New Order", "Order from " . $currentUser->name .
            " Manager of branch " .  $branch->name, $branch);
        // DispatchNow not run in Background but Can you change  To dispath after Test or Complated code 
        // one parameter 1 change to title 
        // tow parameter 2 change to body 
        // php artisan queue: work queue== fcm-notification

        //after change to dispatch write in terminal  php artisan queue:work --queue=fcm-notification  




        if ($currentRole == 3 || $currentRole == 1) {
            $data =  $this->getUnitPriceData($request->product_id, $request->unit);
            $order = new Order(
                [
                    'active' =>  1,
                    'request_state_id' => 2,
                    'desc' => $request->desc,
                    'created_by' => $request->user()->id,
                    'restricted_state_id' => 6,
                    'branch_id' => $branch->id
                ]
            );
            $order->save();

            foreach ($request->orderDetails as   $data) {
                $unitPrice = $this->getUnitPriceData($data['product_id'], $data['product_unit_id']);
                $resultPrice = $unitPrice->price;
                $answers[] = [
                    "product_id" => $data['product_id'],
                    "product_unit_id" =>  $data['product_unit_id'],
                    "qty" =>  $data['qty'],
                    "order_id" => $order->id,
                    "price" =>  $resultPrice  * $data['qty']
                ];
            }

            OrderDetails::insert($answers);


            $obj = new stdClass();
            $obj->res = 'success';
            $obj->msg = 'done successfully';
            $obj->yourOrder = $order->with('orderDetails')->where('id', $order->id)->get();

            return $obj;
        } else {
            $obj = new stdClass();
            $obj->res = "error";
            $obj->msg = "you are not authrozied";

            return $obj;
        }
    }

    public function getUnitPriceData($product_id, $unit_id)
    {
        return UnitPrice::where(
            [
                [
                    'product_id', '=', $product_id
                ],
                [
                    'unit_id', '=', $unit_id
                ]
            ]
        )->first();
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {


        $current_role = $request->user()->role_id;


        if ($current_role == 1 || $current_role == 4) {

            $order = Order::find($request->order_id);
            if ($order->request_state_id != 5) {
                $order->request_state_id = $request->request_state_id;
                if ($request->request_state_id == 3 || $request->request_state_id == 4) {
                    $update =  $order->save();
                    if ($update == 1) {
                        $obj = new stdClass();
                        $obj->res = "success";

                        $obj->msg = "Order no " . $order->id . " has been updated " .  $request->request_state_id;
                    } else {
                        $obj = new stdClass();
                        $obj->res = "faild";
                        $obj->msg = "there is some errors";
                    }
                } else {
                    $obj = new stdClass();
                    $obj->res = "faild";
                    $obj->msg = "Only states  (4 or 3) can be sent , because your role is store manager";
                }
            } else {
                $obj = new stdClass();
                $obj->res = "faild";
                $obj->msg = "you cannot update this order because its state is delivered";
            }
        } elseif ($current_role == 5) {

            if ($request->restricted_state_id == 7 || $request->restricted_state_id == 6) {
                $order = Order::find($request->order_id);
                $order->restricted_state_id = $request->restricted_state_id;


                if ($order->request_state_id == 5) {
                    if ($request->restricted_state_id == 7) {
                        $update =  $order->save();
                        $obj = new stdClass();
                        $obj->res = "success";
                        $obj->msg = "Order no " . $order->id . " has been restrected ";
                    } elseif ($request->restricted_state_id == 6) {
                        $update =  $order->save();
                        $obj = new stdClass();
                        $obj->res = "success";
                        $obj->msg = "Order no " . $order->id . " has been unrestrected ";
                    }
                } else {
                    $obj = new stdClass();
                    $obj->res = "error";
                    $obj->msg = "you can not restrecte this order because its state is not delivered";
                }
            } else {
                $obj = new stdClass();
                $obj->res = "error";
                $obj->msg = "you can not send order another 7";
            }
        } elseif ($current_role == 3) {

            $order = Order::find($request->order_id);

            if ($order->request_state_id == 4) {
                $order->request_state_id = $request->request_state_id;
                if ($request->request_state_id == 5) {
                    if ($request->full_quantity == 1) {

                        $update =  $order->save();
                        if ($update == 1) {
                            $obj = new stdClass();
                            $obj->res = "success";
                            $obj->msg = "Order no " . $order->id . " has been updated ";
                        } else {
                            $obj = new stdClass();
                            $obj->res = "faild";
                            $obj->msg = "there is some errors";
                        }
                    } elseif ($request->full_quantity == 0) {
                        if (isset($request->notes)) {

                            $order->full_quantity = $request->full_quantity;
                            $order->notes = $request->notes;
                            $update =  $order->save();
                            if ($update == 1) {

                                $notificationOrder = new NotificationOrder(
                                    [
                                        'sender_id' => $order->created_by,
                                        'reciver_id' => User::where('role_id', 4)->get()->first()->id,
                                        'order_id' => $order->id,
                                        'title' => 'Quantity is missing of order no ' . $order->id,
                                        'body' => 'Quantity is missing  ' . $order->notes,
                                        'active' => 1
                                    ]
                                );
                                $notificationOrder->save();

                                $obj = new stdClass();
                                $obj->res = "success";
                                $obj->msg = "Order no " . $order->id . " has been updated ";
                            } else {
                                $obj = new stdClass();
                                $obj->res = "faild";
                                $obj->msg = "there is some errors";
                            }
                        } else {
                            $obj = new stdClass();
                            $obj->res = "faild";
                            $obj->msg = "notes is required";
                        }
                    }
                } else {
                    $obj = new stdClass();
                    $obj->res = "faild";
                    $obj->msg = "Only state  ( 5 ) can be sent , because your role is branch manager";
                }
            } else {
                $obj = new stdClass();
                $obj->res = "faild";
                $obj->msg = "you cannot update this order because its state is not ready to delivery";
            }
        } else {


            $obj = new stdClass();
            $obj->res = "error";
            $obj->msg = "you are not authrozied";
        }

        return $obj;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    // Generate PDF

    public function createPDF(Request $request, $id)
    {
        // retreive all records from db

        $order = Order::where('id', $id)->get();
        $orderDetails = OrderDetails::where('order_id', $order[0]->id)->get();

        $objOrder = new stdClass();
        $objOrder->orderId = $order[0]->id;
        $objOrder->createdBy = $order[0]->created_by;
        $objOrder->createdByUserName =  User::where('id', $order[0]->created_by)->get()[0]->name;
        $objOrder->createdAt = $order[0]->created_at;
        $objOrder->stateId = $order[0]->request_state_id;
        $objOrder->state_name =  $this->getStateNameById($order[0]->request_state_id)[0]->name;;
        $objOrder->restricted_state_name =  $this->getStateNameById($order[0]->restricted_state_id)[0]->name;;
        $objOrder->desc = $order[0]->desc;
        $objOrder->branch_id = $order[0]->branch_id;
        $objOrder->branch_name =  Branch::where('id', $order[0]->branch_id)->get()[0]->name;
        $objOrder->manager_name = User::where('role_id', 4)->get()[0]->name;



        $finalResult[] = $objOrder;
        foreach ($orderDetails as $key => $value) {
            $obj = new stdClass();
            $obj->product_id = $value->product_id;
            $obj->product_name = $this->getProductProductNameById($value->product_id)[0]->name;
            $obj->unit_id = $value->product_unit_id;
            $obj->unit_name = $this->getUnitNameById($value->product_unit_id)[0]->name;
            $obj->price =  $value->price;
            $obj->qty = $value->qty;
            array_push($finalResult, $obj);
        }

        $exists = Storage::disk('local')->exists('public/pdf_files/order-no-' . $order[0]->id . '.pdf');

        if ($exists) {
            $path = storage_path('public/storage/pdf_files/order-no-' . $order[0]->id . '.pdf');
            $pathExploded = explode("/", $path);
            $obj = new stdClass();
            $obj->res = 'error';
            $obj->msg = 'pdf is exist';

            $obj->orderId =  $order[0]->id;
            $obj->path = URL::to('/') . '/' . 'public/storage/pdf_files/' . end($pathExploded);
        } else {
            $pdf = PDF::loadView('vendor.voyager.orders.pdf_view', ['finalResult' => $finalResult]);
            $content = $pdf->download()->getOriginalContent();
            $down = Storage::put('public/pdf_files/order-no-' . $order[0]->id . '.pdf', $content);
            if ($down == 1) {
                $path = storage_path('public/pdf_files/order-no-' . $order[0]->id . '.pdf');
                $pathExploded = explode("/", $path);
                $obj = new stdClass();
                $obj->res = 'success';
                $obj->msg = 'done successfully';
                $obj->orderId =  $order[0]->id;
                $obj->path = URL::to('/') . '/' . 'public/storage/pdf_files/' . end($pathExploded);
            } else {
                $obj = new stdClass();
                $obj->res = 'error';
                $obj->msg = 'there are some errors';
            }
        }
        return $obj;
    }


    public function getUnitNameById($id)
    {
        return Unit::where('id', $id)->get();
    }


    public function getProductProductNameById($id)
    {
        return Product::where('id', $id)->get();
    }

    public function getStateNameById($id)
    {
        return RequestState::where('id', $id)->get();
    }
}
