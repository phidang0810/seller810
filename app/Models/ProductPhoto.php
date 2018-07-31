<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{

    const THUMB = 150;
    const LARGE = 300;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'name', 'origin', 'large', 'thumb', 'color_code', 'active', 'order'
    ];

    /**
     * A product detail can have many colors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function color() {

        return $this->hasOne('App\Models\Color', 'id', 'color_code');
    }
}