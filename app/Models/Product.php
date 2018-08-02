<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    const LARGE_HEIGHT = 300;
    const LARGE_WIDTH = 400;

    const THUMB_HEIGHT = 50;
    const THUMB_WIDTH = 80;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'photo', 'code', 'name', 'active', 'order', 'supplier_id', 'brand_id', 'barcode', 'colors', 'sizes', 'price', 'sell_price', 'quantity', 'quantity_available', 'description', 'content', 'meta_keyword', 'meta_description', 'meta_robot'
    ];

    /**
     * A product belong to many categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'product_category', 'product_id', 'category_id');
    }

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos() {

        return $this->hasMany('App\Models\ProductPhoto');
    }

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\ProductDetail');
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
}