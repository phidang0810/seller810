<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'discount_amount', 'active', 'order'
    ];
}
