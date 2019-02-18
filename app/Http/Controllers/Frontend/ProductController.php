<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\BaseController;

class ProductController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh Sách sản phẩm', route('frontend.products.index'));
    }

    /**
     * Show the products list page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $this->_data['title'] = 'Danh sách sản phẩm';

        return view('frontend.products.index', $this->_data);
    }

}
