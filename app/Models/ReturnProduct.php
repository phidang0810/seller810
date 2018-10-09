<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends BaseModel
{
    protected $table = 'return_products';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'return_staff_id', 'return_date', 'quantity', 'reason', 'status'
    ];

    /**
     * A transport warehouse can have many details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\ReturnProductDetail');
    }

    /**
     * Get the import staff of import product detail.
     */
    public function staff()
    {
        return $this->hasOne('App\Models\User', 'id', 'return_staff_id');
    }
}
