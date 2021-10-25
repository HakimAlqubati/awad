<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UnitPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;
use App\Models\Unit;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected Request $request;
    public function index(Request $request)
    {


        $this->request = $request;


        $data = Product::with('unitPrices')->where(function ($query) {

            if ($this->request->cat_id && $this->request->product_id) {
                return  $query->where(
                    [
                        ['id', '=', $this->request->product_id],
                        ['cat_id', $this->request->cat_id]
                    ]
                );
            } elseif ($this->request->cat_id) {
                return  $query->where(
                    [
                        ['cat_id', $this->request->cat_id]
                    ]
                );
            } elseif ($this->request->product_id) {
                return  $query->where(
                    [
                        ['id', $this->request->product_id]
                    ]
                );
            }
        })->get();

        foreach ($data as   $value) {
            $obj = new stdClass();
            $obj->id = $value->id;
            $obj->name = $value->name;
            $obj->desc = $value->desc;
            $obj->cat_id = $value->cat_id;
            $obj->code = $value->code;
            $obj->active = $value->active;
            $obj->created_at = $value->created_at;
            $obj->updated_at = $value->updated_at;
            $obj->unitPrices = $this->getProductUnitPrices($value->id);
            $result[] = $obj;
        }
        return $result;
    }

    public function getProductUnitPrices($productId)
    {
        $unitPrices =  UnitPrice::where('product_id', $productId)->get();
        foreach ($unitPrices as $key => $value) {
            $obj = new stdClass();
            $obj->id = $value->id;
            $obj->unit_id = $value->unit_id;
            $obj->unit_name = Unit::where('id', $value->unit_id)->get()[0]->name;
            $obj->product_id = $value->product_id;
            $obj->product_name = Product::where('id', $value->product_id)->get()[0]->name;
            $obj->price = $value->price;
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
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
