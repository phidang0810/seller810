<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ImportProductRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PlatformRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Http\Request;

class StatisticsController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function importProduct(ImportProductRepository $product, CategoryRepository $category)
    {
        if ($this->_request->ajax()) {
            return $product->getStaticDataTable($this->_request);
        }

        $this->_data['title'] = 'Chi Phí Nhập Hàng';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");

        return view('admin.statistics.import_product', $this->_data);
    }

    public function exportProduct(ImportProductRepository $model)
    {
        $data = $model->getStaticDataTableObj($this->_request)->toArray();
        $fileName = 'Chi Phí Nhập Hàng - ' . date('m-m-Y');
        \Maatwebsite\Excel\Facades\Excel::create($fileName, function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                $excelData  = [];
                foreach($data['data'] as $k => $row) {
                    $index = $k+1;
                    $excelData[] = [
                        '#' => $index,
                        'MÃ SẢN PHẨM' => $row['product_code'],
                        'TÊN SẢN PHẨM' => $row['product_name'],
                        'DANH MỤC' => $row['category'],
                        'NHÀ KHO' => $row['warehouse_name'],
                        'NHÀ CUNG CẤP' => $row['supplier_name'],
                        'SỐ LƯỢNG' => $row['quantity'],
                        'TỔNG GIÁ' => $row['total_price'],
                        'NGÀY NHẬP' => $row['created_at']
                    ];
                }
                $sheet->fromArray($excelData);

            });

        })->export('xls');
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

    public function productQuantity(WarehouseRepository $warehouse, CategoryRepository $category)
    {
        if ($this->_request->ajax()) {
            return $warehouse->getProductQuantityTable($this->_request);
        }

        $this->_data['title'] = 'Thống Kê Sản Phẩm Theo Kho';
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "");
        $this->_data['warehouses'] = $warehouse->getDataList();

        return view('admin.statistics.product_quantity', $this->_data);
    }

    public function exportProductQuantity(WarehouseRepository $warehouse)
    {
        $data = $warehouse->getProductQuantityObj($this->_request)->toArray();
        $fileName = 'Thong Ke Kho Hang - ' . date('m-m-Y');
        \Maatwebsite\Excel\Facades\Excel::create($fileName, function($excel) use($data) {

            $excel->sheet('Sheetname', function($sheet) use($data) {
                $excelData  = [];
                foreach($data['data'] as $k => $row) {
                    $index = $k+1;
                    $excelData[] = [
                        '#' => $index,
                        'KHO HÀNG' => $row['warehouse_name'],
                        'MÃ SẢN PHẨM' => $row['product_code'],
                        'TÊN SẢN PHẨM' => $row['product_name'],
                        'DANH MỤC' => $row['category'],
                        'TỔNG SL' => $row['quantity'],
                        'SL CÒN' => $row['quantity_available'],
                        'SL ĐÃ BÁN' => $row['quantity_sell']
                    ];
                }
                $sheet->fromArray($excelData);

            });

        })->export('xls');
    }
}
