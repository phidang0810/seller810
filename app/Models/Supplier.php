<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'tax_code', 'responsible person', 'city_id', 'email', 'address', 'phone', 'active', 'order'
    ];
}
