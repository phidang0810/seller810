<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
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

        $this->_data['title'] = 'Doanh Thu Bán Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");
        $this->_data['platforms'] = $platform->getList();

        return view('admin.statistics.revenue', $this->_data);
    }

    public function exportRevenue(PaymentRepository $model)
    {
        $data = $model->getRevenueObjDataTable($this->_request)->toArray();
        $fileName = 'Doanh Thu - ' . date('m-m-Y');
        \Maatwebsite\Excel\Facades\Excel::create($fileName, function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                $excelData  = [];
                foreach($data['data'] as $k => $row) {
                    $index = $k+1;
                    $excelData[] = [
                        '#' => $index,
                        'TÊN' => $row['name'],
                        'MÃ SẢN PHẨM' => $row['barcode_text'],
                        'DANH MỤC' => $row['category'],
                        'TỔNG BÁN' => $row['quantity'],
                        'NGÀY BÁN' => $row['created_at'],
                        'DOANH THU' => $row['profit']
                    ];
                }
                $sheet->fromArray($excelData);

            });

        })->export('xls');
    }

    public function revenueChart(CategoryRepository $category)
    {
        $this->_data['title'] = 'Doanh Thu Bán Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");

        return view('admin.statistics.revenue_chart', $this->_data);
    }

    public function cartChart(CartRepository $cart, CategoryRepository $category)
    {
        if ($this->_request->ajax()) {
            return $cart->getStaticsCartDataTable($this->_request);
        }

        $this->_data['title'] = 'Thống Kê Đơn Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");

        return view('admin.statistics.cart_chart', $this->_data);
    }

    public function getPaymentMixChart(PaymentRepository $payment)
    {
        $data = $payment->getMixChartData($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getPaymentChart(PaymentRepository $payment)
    {
        $input = $this->_request;
        $data = $payment->getLineChartData($input);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getCartBarChart(PaymentRepository $payment)
    {
        $input = $this->_request;
        $data = $payment->getBarChartData($input);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopProductSell(PaymentRepository $payment)
    {
        $data = $payment->getTopProductSell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopPlatformSell(PaymentRepository $payment)
    {
        $data = $payment->getTopPlatformSell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }

    public function getTopCategorySell(PaymentRepository $payment)
    {
        $data = $payment->getTopCategorySell($this->_request);

        return response()->json([
            'success' => true,
            'result' => $data
        ]);
    }
}
