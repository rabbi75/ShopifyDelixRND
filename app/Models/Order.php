<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'email',
        'customer_name',
        'total_price',
        'financial_status',
        'fulfillment_status',
        'delivery_status',
        'tracking_number',
        'tracking_url',
        'raw_data',
    ];
}
