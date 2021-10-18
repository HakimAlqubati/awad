<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;
    protected   $fillable = ['product_unit_id', 'order_id', 'qty', 'price','updated_at','created_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
