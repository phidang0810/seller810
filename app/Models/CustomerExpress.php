<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerExpress extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city_id', 'email', 'address', 'phone', 'customer_id'
    ];

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }
}
