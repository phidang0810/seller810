<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 7/23/2018
 * Time: 9:11 PM
 */

namespace App\Repositories;

use App\Models\Category;
use App\Models\Platform;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

Class PlatformRepository
{
    public function getList()
    {
        $result = Platform::where('active', ACTIVE)->get();

        return $result;
    }
}