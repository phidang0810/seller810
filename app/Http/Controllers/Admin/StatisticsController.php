<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CategoryRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PlatformRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function importProduct(ProductRepository $product, CategoryRepository $category)
    {
        if ($this->_request->ajax()) {
            return $product->dataTable($this->_request);
        }

        $this->_data['title'] = 'Chi Phí Nhập Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");

        return view('admin.statistics.import_product', $this->_data);
    }

    public function revenue(PaymentRepository $payment, CategoryRepository $category, PlatformRepository $platform)
    {
        if ($this->_request->ajax()) {
            return $payment->getRevenueDataTable($this->_request);
        }

        $this->_data['title'] = 'Chi Phí Nhập Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");
        $this->_data['platforms'] = $platform->getList();

        return view('admin.statistics.revenue', $this->_data);
    }
}