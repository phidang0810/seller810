<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends BaseModel
{
    protected $table = 'warehouse_product';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_id', 'product_id', 'product_detail_id', 'quantity', 'quantity_available'
    ];

    /**
     * A product detail can have many colors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productDetail() {

        return $this->hasOne('App\Models\ProductDetail', 'id', 'product_detail_id');
    }

    /**
     * A product detail can have many colors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function warehouse() {

        return $this->hasOne('App\Models\warehouse', 'id', 'warehouse_id');
    }
}
