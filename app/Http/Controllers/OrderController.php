<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\RequestState;
use App\Models\UnitPrice;
use App\Models\User;
use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use stdClass;

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
                    $this->request->branch_id
                ) {
                    return  $query->where(
                        [
                            ['id', '=', $this->request->id],
                            ['created_by', '=', $this->request->created_by],
                            ['request_state_id', '=', $this->request->request_state_id],
                            ['restricted_state_id', '=', $this->request->restricted_state_id],
                            ['branch_id', '=', $this->request->branch_id],
                        ]
                    );
                } elseif (
                    $this->request->id &&
                    $this->request->created_by &&
                    $this->request->request_state_id &&
                    $this->request->restricted_state_id
                ) {
                    return  $query->where(
                        [
                            ['id', '=', $this->request->id],
                            ['created_by', '=', $this->request->created_by],
                            ['request_state_id', '=', $this->request->request_state_id],
                            ['restricted_state_id', '=', $this->request->restricted_state_id]
                        ]
                    );
                }
            }
        )->get();
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
                if ($value->branch_id != 0) {
                    $obj->branch_name =  Branch::where('id', $value->branch_id)->get()[0]->name;
                }
                $obj->created_at = $value->created_at;
                $obj->updated_at = $value->updated_at;
                $array[] = $obj;
            }
            return $array;
        } else {
            return  [];
        }
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
        $branch = Branch::where('manager_id', $request->user()->id)->get();


        if ($currentRole == 3 || $currentRole == 1) {
            $data =  $this->getUnitPriceData($request->product_id, $request->unit);
            foreach ($request->all() as   $value) {
                $order = new Order(
                    [
                        'active' =>  1,
                        'request_state_id' => 2,
                        'desc' => $value['desc'],
                        'created_by' => $request->user()->id,
                        'restricted_state_id' => 6,
                        'branch_id' => $branch[0]->id
                    ]
                );
                $order->save();
                foreach ($value['orderDetails'] as   $data) {
                    $unitPrice = $this->getUnitPriceData($data['product_id'], $data['product_unit_id']);
                    $resultPrice = $unitPrice[0]->price;
                    $answers[] = [
                        "product_id" => $data['product_id'],
                        "product_unit_id" =>  $data['product_unit_id'],
                        "qty" =>  $data['qty'],
                        "order_id" => $order->id,
                        "price" =>  $resultPrice  * $data['qty']
                    ];
                }

                OrderDetails::insert($answers);
            }

            $obj = new stdClass();
            $obj->res = 'success';
            $obj->msg = 'done successfully';
            $obj->yourOrder = $order->with('orderDetails')->where('id', $order->id)->get();
            $result[] = $obj;
            return $result;
        } else {
            $obj = new stdClass();
            $obj->res = "error";
            $obj->msg = "you are not authrozied";
            $result[] = $obj;
            return $result;;
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
        )->get();
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
            $order->request_state_id = $request->request_state_id;
            $update =  $order->save();
            if ($update == 1) {
                $obj = new stdClass();
                $obj->res = "success";
                $obj->msg = "Order no " . $order->id . " has been updated ";
                $result[] = $obj;
            } else {
                $obj = new stdClass();
                $obj->res = "faild";
                $obj->msg = "there is some errors";
                $result[] = $obj;
            }
        } else {
            $obj = new stdClass();
            $obj->res = "error";
            $obj->msg = "you are not authrozied";
            $result[] = $obj;
        }

        return $result;
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
}
