<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
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
