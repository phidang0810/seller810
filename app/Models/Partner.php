<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'city_id', 'email', 'address', 'phone', 'discount_amount', 'active', 'order'
    ];
}
