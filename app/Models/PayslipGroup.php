<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayslipGroup extends BaseModel
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
