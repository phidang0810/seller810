<?php

namespace App\Models;

class Photo extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'photo', 'thumb', 'active', 'order'
    ];
}
