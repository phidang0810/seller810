<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProduct extends BaseModel
{
    protected $table = 'import_products';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'quantity', 'import_staff_id', 'product_id', 'warehouse_id', 'price', 'total_price', 'note', 'status', 'supplier_id', 'brand_id', 'barcode_text', 'barcode', 'name', 'colors', 'sizes', 'sell_price', 'photo', 'description', 'content', 'active', 'order'
    ];

    /**
     * A import product can have many details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\ImportProductDetail');
    }

    /**
     * Get the import staff of import product detail.
     */
    public function staff()
    {
        return $this->hasOne('App\Models\User', 'id', 'import_staff_id');
    }

    /**
     * Get the import supplier of import product detail.
     */
    public function supplier()
    {
        return $this->hasOne('App\Models\Supplier', 'id', 'supplier_id');
    }

    /**
     * Get the product of import product detail.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Get the product of import product detail.
     */
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
}