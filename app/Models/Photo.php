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
        'type', 'link', 'photo', 'thumb', 'active', 'order'
    ];
}
