<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'active', 'order'
    ];
}
