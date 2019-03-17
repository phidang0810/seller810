<?php

namespace App\Models;

class Post extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'description', 'title', 'slug', 'content', 'active', 'order'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\PostCategory', 'category_id');
    }
}
