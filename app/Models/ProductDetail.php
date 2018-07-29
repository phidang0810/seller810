<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $table = 'product_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'color_id', 'size_id', 'quantity'
    ];

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