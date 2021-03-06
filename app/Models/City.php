<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends BaseModel
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'active', 'order'
    ];
}
