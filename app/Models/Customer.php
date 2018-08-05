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
        'group_customer_id', 'name', 'code', 'city_id', 'email', 'address', 'phone', 'active', 'order'
    ];

    /**
     * A customer can have many carts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carts() {

        return $this->hasMany('App\Models\Cart');
    }
}
