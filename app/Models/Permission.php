<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'active'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_permission', 'permission_id', 'role_id');
    }
}
