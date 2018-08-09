<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $table = 'cart_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id', 'product_id', 'product_detail_id', 'discount_amount', 'quantity', 'price', 'total_price'
    ];

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productDetail() {

        return $this->hasOne('App\Models\ProductDetail', 'id', 'product_detail_id');
    }

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product() {

        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
}
