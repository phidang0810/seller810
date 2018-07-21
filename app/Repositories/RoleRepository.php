<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/10/18
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class RoleRepository
{
    public function all()
    {
        $data = Role::select('id', 'name', 'alias')->where('active', ACTIVE)->get();
        return $data;
    }
}