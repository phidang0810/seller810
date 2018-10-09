<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportWarehouseDetail extends BaseModel
{
    protected $table = 'transport_warehouse_detail';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transport_warehouse_id', 'from_warehouse_id', 'receive_warehouse_id', 'product_id', 'product_detail_id', 'quantity', 'status'
    ];

    /**
     * A transport warehouse detail belong to 1 transport warehouse.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transportWarehouse() {

        return $this->belongsTo('App\Models\TransportWarehouse');
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
    public function fromWarehouse() {

        return $this->hasOne('App\Models\Warehouse', 'id', 'from_warehouse_id');
    }

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function receiveWarehouse() {

        return $this->hasOne('App\Models\Warehouse', 'id', 'receive_warehouse_id');
    }
}
