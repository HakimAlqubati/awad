<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UnitPrice;
use Illuminate\Http\Request;
use stdClass;
use App\Models\Unit;

class UnitPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->request = $request;
        $data =  UnitPrice::where(function ($query) {
            if ($this->request->unit_id && $this->request->product_id) {
                return  $query->where(
                    [
                        ['product_id', '=', $this->request->product_id],
                        ['unit_id', $this->request->unit_id]
                    ]
                );
            } elseif ($this->request->unit_id) {
                return  $query->where(
                    [
                        ['unit_id', $this->request->unit_id]
                    ]
                );
            } elseif ($this->request->product_id) {
                return  $query->where(
                    [
                        ['product_id', '=', $this->request->product_id],
                    ]
                );
            } else {
                return $query;
            }
        })->get();

        foreach ($data as $key => $value) {
            $obj = new stdClass();
            $obj->id = $value->id;
            $obj->unit_id = $value->unit_id;
            $obj->unit_name = Unit::where('id', $value->unit_id)->get()[0]->name;
            $obj->product_id = $value->product_id;
            $obj->product_name = Product::where('id', $value->product_id)->get()[0]->name;
            $obj->price = $value->price;
            $obj->created_at = $value->created_at;
            $obj->updated_at = $value->updated_at;
            $result[] = $obj;
        }



        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UnitPrice  $unitPrice
     * @return \Illuminate\Http\Response
     */
    public function show(UnitPrice $unitPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UnitPrice  $unitPrice
     * @return \Illuminate\Http\Response
     */
    public function edit(UnitPrice $unitPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UnitPrice  $unitPrice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UnitPrice $unitPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UnitPrice  $unitPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(UnitPrice $unitPrice)
    {
        //
    }
}
