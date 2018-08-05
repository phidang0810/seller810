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
        'code', 'city_id', 'quantity', 'discount_amount', 'active'
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
