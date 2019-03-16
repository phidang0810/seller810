<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $_data;
    protected $_request;
    protected $_view = '/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->_data['breadcrumbs'] = [];
        $this->_request = $request;
    }

    protected function _pushBreadCrumbs($name, $link = null)
    {
        $data['name'] = $name;
        if ($link) $data['link'] = $link;
        array_push($this->_data['breadcrumbs'], $data);
    }

    public function home () {
        $this->_data['title'] = 'Trang chủ';
        return view('frontend.index', $this->_data);
    }
}
