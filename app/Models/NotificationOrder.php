<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationOrder extends Model
{
    use HasFactory;
    protected   $fillable = ['sender_id', 'reciver_id', 'order_id', 'title', 'body', 'active'];
}
