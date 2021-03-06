<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
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
        'photo', 'thumb', 'barcode_text', 'name', 'active', 'order', 'category_ids', 'supplier_id', 'brand_id', 'barcode', 'colors', 'sizes', 'price', 'sell_price', 'quantity', 'quantity_available', 'description', 'content', 'meta_keyword', 'meta_description', 'meta_robot', 'slug', 'min_quantity_sell', 'is_hot', 'is_home'
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

    /**
     * Get the brand that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warehouseProducts() {

        return $this->hasMany('App\Models\WarehouseProduct');
    }

    /**
     * A product can have many photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function importProducts() {

        return $this->hasMany('App\Models\ImportProduct');
    }
}