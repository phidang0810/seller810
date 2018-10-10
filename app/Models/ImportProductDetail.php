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
        'import_product_id', 'product_id', 'product_detail_id', 'quantity', 'status', 'color_id', 'size_id'
    ];

    /**
     * Get the import product that owns the import product detail.
     */
    public function importProduct()
    {
        return $this->belongsTo('App\Models\ImportProduct');
    }

    /**
     * A product detail can have many sizes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function size() {

        return $this->hasOne('App\Models\Size', 'id', 'size_id');
    }

    /**
     * A product detail can have many colors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function color() {

        return $this->hasOne('App\Models\Color', 'id', 'color_id');
    }
}