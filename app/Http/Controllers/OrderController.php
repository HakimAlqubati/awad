<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\UnitPrice;
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
    public function index()
    {
        //
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
        if ($currentRole == 3 || $currentRole == 1) {
            $data =  $this->getUnitPriceData($request->product_id, $request->unit);
            foreach ($request->all() as   $value) {
                $order = new Order(
                    [
                        'active' =>  1,
                        'request_state_id' => 2,
                        'desc' => $value['desc'],
                        'created_by' => $request->user()->id,
                        'restricted_state_id' => 6
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
        //
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
