<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnProductDetail extends BaseModel
{
    protected $table = 'return_product_detail';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_product_id', 'warehouse_id', 'product_id', 'product_detail_id', 'supplier_id', 'quantity', 'status'
    ];

    /**
     * A transport warehouse detail belong to 1 transport warehouse.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnProduct() {

        return $this->belongsTo('App\Models\ReturnProduct');
    }

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

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function warehouse() {

        return $this->hasOne('App\Models\Warehouse', 'id', 'warehouse_id');
    }

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplier() {

        return $this->hasOne('App\Models\supplier', 'id', 'supplier_id');
    }
}
