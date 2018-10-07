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
        'warehouse_id', 'product_id', 'product_detail_id', 'quantity'
    ];
}
