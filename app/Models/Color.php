<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends BaseModel
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'photo', 'code', 'name', 'active', 'order'
    ];
}
