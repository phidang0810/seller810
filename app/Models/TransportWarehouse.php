<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportWarehouse extends BaseModel
{
    protected $table = 'transport_warehouse';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'transport_staff_id', 'transport_date', 'status'
    ];

    /**
     * A transport warehouse can have many details.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details() {

        return $this->hasMany('App\Models\TransportWarehouseDetail');
    }

    /**
     * Get the import staff of import product detail.
     */
    public function staff()
    {
        return $this->hasOne('App\Models\User', 'id', 'transport_staff_id');
    }
}
