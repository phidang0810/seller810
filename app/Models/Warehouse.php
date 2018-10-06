<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends BaseModel
{
    protected $table = 'warehouses';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'address', 'phone', 'email', 'active', 'order'
    ];

    /**
     * A product belong to many products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('App\Models\Products', 'warehouse_product', 'warehouse_id', 'product_id');
    }

    /**
     * A product belong to many product details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function productDetails()
    {
        return $this->belongsToMany('App\Models\ProductDetail', 'warehouse_product', 'warehouse_id', 'product_detail_id');
    }
}
