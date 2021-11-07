<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected   $fillable = ['active', 'request_state_id', 'desc', 'created_by', 'restricted_state_id','branch_id'];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
