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
        'warehouse_id', 'product_id', 'product_detail_id', 'quantity', 'quantity_available', 'barcode'
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

        return $this->hasOne('App\Models\Warehouse', 'id', 'warehouse_id');
    }

    /**
     * A product detail can have many colors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product() {

        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
}
