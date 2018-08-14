<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductPhoto extends BaseModel
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

    public function deleteImageOnStorage(){
        if ($this->origin) {
           Storage::delete($this->origin);
        }
        if ($this->large) {
           Storage::delete($this->large);
        }
        if ($this->thumb) {
           Storage::delete($this->thumb);
        }
    }
}