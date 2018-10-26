<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Creditor extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id', 'code','full_name', 'note', 'total', 'paid', 'date', 'paid_date','phone', 'note', 'status'
    ];

}
