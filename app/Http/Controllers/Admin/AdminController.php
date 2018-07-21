<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    protected $_data;
    protected $_request;
    protected $_view = 'admin/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->_data['auth'] = Auth::user();
        $this->_data['breadcrumbs'] = [];
        $this->_request = $request;
    }

    protected function _pushBreadCrumbs($name, $link = null)
    {
        $data['name'] = $name;
        if ($link) $data['link'] = $link;
        array_push($this->_data['breadcrumbs'], $data);
    }
}
