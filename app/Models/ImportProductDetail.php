<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProductDetail extends BaseModel
{
    protected $table = 'import_product_detail';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'import_product_id', 'product_id', 'product_detail_id', 'quantity', 'status'
    ];

    /**
     * Get the import product that owns the import product detail.
     */
    public function importProduct()
    {
        return $this->belongsTo('App\Models\ImportProduct');
    }

    /**
     * Get the product detail of import product detail.
     */
    public function productDetail()
    {
        return $this->belongsTo('App\Models\ProductDetail');
    }
}