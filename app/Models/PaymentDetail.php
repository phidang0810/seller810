<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    protected $table = 'payment_detail';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'cart_id',
        'cart_detail_id',
        'product_id',
        'product_detail_id',
        'quantity',
        'discount_amount',
        'price',
        'total_price',
        'active'
    ];

}
