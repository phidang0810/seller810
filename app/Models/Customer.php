<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_customer_id', 'description', 'name', 'code', 'city_id', 'email', 'address', 'phone', 'active', 'order'
    ];

    public function group()
    {
        return $this->belongsTo('App\Models\GroupCustomer', 'group_customer_id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    /**
     * A customer can have many carts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carts() {

        return $this->hasMany('App\Models\Cart');
    }
}
