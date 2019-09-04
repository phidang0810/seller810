<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends BaseModel
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
        'customer_id',
        'transport_id',
        'quantity',
        'discount_amount',
        'partner_discount_amount',
        'customer_discount_amount',
        'total_discount_amount',
        'price',
        'total_price',
        'total_import_price',
        'shipping_fee',
        'vat_percent',
        'vat_amount',
        'status',
        'active',
        'payment_method',
        'transport_method',
        'transport_info_name',
        'transport_info_phone',
        'customer_express_id'
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

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\CartDetail');
    }

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnDetails() {

        return $this->hasMany('App\Models\ReturnCartDetail');
    }

    /**
     * Get the customer that owns the cart.
     */
    public function platform()
    {
        return $this->belongsTo('App\Models\Platform');
    }

    /**
     * Get the customer that owns the cart.
     */
    public function transport()
    {
        return $this->belongsTo('App\Models\Transport');
    }

    /**
     * Get the customer that owns the cart.
     */
    public function customerExpress()
    {
        return $this->belongsTo('App\Models\CustomerExpress');
    }
    /**
     * Get the brand that owns the product.
     */
    public function transportMethod()
    {
        return $this->belongsTo('App\Models\Transport', 'transport_method');
    }
}
