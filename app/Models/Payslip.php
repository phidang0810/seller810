<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends BaseModel
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id', 'code', 'description', 'price', 'status'
    ];
}
