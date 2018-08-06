<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'city_id',
        'platform_id',
        'partner_id',
        'transport_id',
        'quantity',
        'discount_amount',
        'partner_discount_amount',
        'customer_discount_amount',
        'total_discount_amount',
        'price',
        'total_price',
        'shipping_fee',
        'vat_percent',
        'vat_amount',
        'status',
        'active'
    ];

    /**
     * Get the customer that owns the cart.
     */
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    /**
     * Get the provider that owns the cart.
     */
    public function provider()
    {
        return $this->belongsTo('App\Models\Provider');
    }
}
