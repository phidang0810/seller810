<?php

namespace App\Models;

class PostCategory extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'name', 'active', 'order'
    ];

    protected $table = 'post_categories';
}
