<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id',
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
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\PaymentDetail');
    }

}
