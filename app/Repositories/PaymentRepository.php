<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 8/04/2018
 * Time: 10:29 AM
 */

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Category;
use App\Models\Creditor;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Platform;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

Class PaymentRepository
{
    private function _getDayOfWeeks()
    {
        return [
            'Thứ 2',
            'Thứ 3',
            'Thứ 4',
            'Thứ 5',
            'Thứ 6',
            'Thứ 7',
            'Chủ Nhật',
        ];
    }

    private function _getMonths()
    {
        return [
            'Tháng 1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            '11',
            '12'
        ];
    }

    private function _getYears()
    {
        $year = date('Y');
        $minYear = $year-9;
        $result = [];
        for($i = $minYear; $i <= $year; $i++) {
            $result[] = $i;
        }

        return $result;
    }

    public function getProfitChartData($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'month';
        switch ($group) {
            case 'month':
                $curMonth = date('m');
                $result['time'] = [
                    'Tháng ' . ($curMonth - 3),
                    'Tháng ' . ($curMonth - 2),
                    'Tháng ' . ($curMonth - 1),
                    'Tháng ' . $curMonth
                ];

                $result['value'] = $this->_getProfitMonthOfYear($result['time']);
                break;

            case 'year':
                $curYear = date('Y');
                $result['time'] = [
                    $curYear - 3,
                    $curYear - 2,
                    $curYear - 1,
                    $curYear,
                ];
                $result['value'] = $this->_getProfitYears($result['time']);
                break;
        }
        return $result;
    }

    public function getLineChartData($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'this_week';
        $select = $search['select'] ?? 'amount';
        switch ($group) {
            case 'this_week':
                $result['time'] = $this->_getDayOfWeeks();

                $result['value'] = $this->_getPaymentThisWeek($result['time'], ['select' => $select]);
                break;

            case 'last_week':
                $result['time'] = $this->_getDayOfWeeks();
                $result['value'] = $this->_getPaymentLastWeek($result['time'], ['select' => $select]);
                break;

            case 'month':
                $result['time'] = $this->_getMonths();

                $result['value'] = $this->_getPaymentMonthOfYear($result['time'], ['select' => $select]);
                break;

            case 'year':
                $result['time'] = $this->_getYears();
                $result['value'] = $this->_getPaymentYears($result['time'],['select' => $select]);
                break;
        }
        return $result;
    }


    public function getCreditorBarChart($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'year';
        $supplierID = empty($search['supplier_id']) ? null: $search['supplier_id'];
        switch ($group) {

            case 'month':
                $result['time'] = $this->_getMonths();

                $result['total'] = $this->_getCreditorMonthOfYear($result['time'], $supplierID);
                break;

            case 'year':
                $result['time'] = $this->_getYears();
                $result['total'] = $this->_getCreditorYears($result['time'], $supplierID);
                break;
        }
        return $result;
    }

    public function getBarChartData($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'this_week';
        $select = $search['select'] ?? 'amount';
        switch ($group) {
            case 'this_week':
                $result['time'] = $this->_getDayOfWeeks();

                $result['total'] = $this->_getPaymentThisWeek($result['time'], ['select' => $select]);
                $result['cancel'] = $this->_getPaymentThisWeek($result['time'], [
                    'select' => $select,
                    'status' => CART_CANCELED
                ]);
            break;

            case 'last_week':
                $result['time'] = $this->_getDayOfWeeks();
                $result['total'] = $this->_getPaymentLastWeek($result['time'], ['select' => $select]);
                $result['cancel'] = $this->_getPaymentLastWeek($result['time'], [
                    'select' => $select,
                    'status' => CART_CANCELED
                ]);
                break;

            case 'month':
                $result['time'] = $this->_getMonths();

                $result['total'] = $this->_getPaymentMonthOfYear($result['time'], ['select' => $select]);
                $result['cancel'] = $this->_getPaymentMonthOfYear($result['time'], [
                    'select' => $select,
                    'status' => CART_CANCELED
                ]);
            break;

            case 'year':
                $result['time'] = $this->_getYears();
                $result['total'] = $this->_getPaymentYears($result['time'],['select' => $select]);
                $result['cancel'] = $this->_getPaymentYears($result['time'], [
                    'select' => $select,
                    'status' => CART_CANCELED
                ]);
            break;
        }
        return $result;
    }

    public function getMixChartData($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'this_week';
        switch ($group) {
            case 'this_week':
                $result['time'] = $this->_getDayOfWeeks();

                $result['value']['amount'] = $this->_getPaymentThisWeek($result['time'], ['select' => 'amount']);
                $result['value']['cancel_cart'] = $this->_getPaymentThisWeek($result['time'], [
                    'select' => 'number_cart',
                    'status' => CART_CANCELED
                ]);
                $result['value']['total_cart'] = $this->_getPaymentThisWeek($result['time'], [
                    'select' => 'number_cart'
                ]);
                break;

            case 'last_week':
                $result['time'] = $this->_getDayOfWeeks();
                $result['value']['amount'] = $this->_getPaymentLastWeek($result['time'], ['select' => 'amount']);
                $result['value']['cancel_cart'] = $this->_getPaymentLastWeek($result['time'], [
                    'select' => 'number_cart',
                    'status' => CART_CANCELED
                ]);
                $result['value']['total_cart'] = $this->_getPaymentLastWeek($result['time'], [
                    'select' => 'number_cart'
                ]);
                break;

            case 'month':
                $result['time'] = $this->_getMonths();

                $result['value']['amount'] = $this->_getPaymentMonthOfYear($result['time'], ['select' => 'amount']);
                $result['value']['cancel_cart'] = $this->_getPaymentMonthOfYear($result['time'], [
                    'select' => 'number_cart',
                    'status' => CART_CANCELED
                ]);
                $result['value']['total_cart'] = $this->_getPaymentMonthOfYear($result['time'], [
                    'select' => 'number_cart',
                ]);
                break;

            case 'year':
                $result['time'] = $this->_getYears();
                $result['value']['amount'] = $this->_getPaymentYears($result['time'], ['select' => 'amount']);
                $result['value']['cancel_cart'] = $this->_getPaymentYears($result['time'], [
                    'select' => 'number_cart',
                    'status' => CART_CANCELED
                ]);
                $result['value']['total_cart'] = $this->_getPaymentYears($result['time'], [
                    'select' => 'number_cart'
                ]);
                break;
        }
        return $result;
    }

    private function _getPaymentLastWeek($time, array $option = [])
    {
        if ($option['select'] === 'number_cart') {
            $query = Cart::selectRaw('count(carts.id) as total, DAYOFWEEK(created_at) as day_of_week');
            if (key_exists('status', $option)) {
                $query->where('carts.status', $option['status']);
            }
        } else {
            $query = Payment::selectRaw('sum(price) as total, DAYOFWEEK(created_at) as day_of_week');
        }
        $query->whereRaw('YEARWEEK(created_at) = YEARWEEK(NOW()) - 1')
            ->orderBy('created_at','asc')
            ->groupBy('day_of_week')
            ->get();
        $prices = $query->pluck('total','day_of_week')->toArray();
        $valueArr = [];
        foreach($time as $k => $value) {
            $dayOfWeek = $k+1;
            $valueArr[$k] = key_exists($dayOfWeek, $prices) ? $prices[$dayOfWeek]:0;
        }
        return $valueArr;
    }

    private function _getPaymentThisWeek($time, array $option = [])
    {
        if ($option['select'] === 'number_cart') {
            $query = Cart::selectRaw('count(carts.id) as total, DAYOFWEEK(created_at) as day_of_week');
            if (key_exists('status', $option)) {
                $query->where('status', $option['status']);
            }
        } else {
            $query = Payment::selectRaw('sum(price) as total, DAYOFWEEK(created_at) as day_of_week');
        }
        $query->whereRaw('YEARWEEK(created_at) = YEARWEEK(NOW())')
            ->orderBy('created_at','asc')
            ->groupBy('day_of_week')
            ->get();
        $prices = $query->pluck('total','day_of_week')->toArray();

        $valueArr = [];
        foreach($time as $k => $value) {
            $dayOfWeek = $k+1;
            $valueArr[$k] = key_exists($dayOfWeek, $prices) ? $prices[$dayOfWeek]:0;
        }
        return $valueArr;
    }

    private function _getCreditorMonthOfYear($time, $supplierID = null)
    {
        $query = Creditor::selectRaw('sum(total) as total, MONTH(date) as month');

        $query->whereRaw('YEAR(date) = YEAR(CURDATE()) ');
        if($supplierID) {
            $query->where('supplier_id', $supplierID);
        }
        $query->orderBy('date','asc')
            ->groupBy('month')
            ->get();
        $prices = $query->pluck('total','month')->toArray();
        $valueArr = [];
        foreach($time as $k => $value) {
            $month = $k+1;
            $valueArr[$k] = key_exists($month, $prices) ? $prices[$month]:0;
        }
        return $valueArr;
    }

    private function _getProfitMonthOfYear($time)
    {
        $query = Payment::selectRaw('sum(price - total_import_price) as total, MONTH(created_at) as month');

        $query->whereRaw('YEAR(created_at) = YEAR(CURDATE()) ')
            ->orderBy('created_at','asc')
            ->groupBy('month')
            ->get();

        $prices = $query->pluck('total','month')->toArray();
        $valueArr = [];
        foreach($time as $monthInString) {
            $month = substr($monthInString,7);
            $valueArr[] = key_exists($month, $prices) ? $prices[$month]:0;
        }
        return $valueArr;
    }

    private function _getPaymentMonthOfYear($time, array $option = [])
    {
        if ($option['select'] === 'number_cart') {
            $query = Cart::selectRaw('count(carts.id) as total, MONTH(created_at) as month');
            if (key_exists('status', $option)) {
                $query->where('carts.status', $option['status']);
            }
        } else {
            $query = Payment::selectRaw('sum(price) as total, MONTH(created_at) as month');
        }

        $query->whereRaw('YEAR(created_at) = YEAR(CURDATE()) ')
            ->orderBy('created_at','asc')
            ->groupBy('month')
            ->get();
        $prices = $query->pluck('total','month')->toArray();
        $valueArr = [];
        foreach($time as $k => $value) {
            $month = $k+1;
            $valueArr[$k] = key_exists($month, $prices) ? $prices[$month]:0;
        }

        return $valueArr;
    }

    private function _getCreditorYears($time, $supplierID)
    {
        $result = [];
        $query = Creditor::selectRaw('sum(total) as total, YEAR(date) as year');

        $query->whereRaw('YEAR(date) >= ' . $time[0]);
        if($supplierID) {
            $query->where('supplier_id', $supplierID);
        }
        $query->orderBy('date','asc')
            ->groupBy('year')
            ->get();
        $data = $query->pluck('total','year')->toArray();
        $index = 0;
        foreach($time as $value) {
            $result[$index] = key_exists($value, $data) ? $data[$value]:0;
            $index++;
        }
        return $result;
    }

    private function _getProfitYears($time)
    {
        $result = [];
        $query = Payment::selectRaw('sum(price - total_import_price) as total, YEAR(created_at) as year');

        $query->whereRaw('YEAR(created_at) >= ' . $time[0])
            ->orderBy('created_at','asc')
            ->groupBy('year')
            ->get();
        $data = $query->pluck('total','year')->toArray();
        $index = 0;
        foreach($time as $value) {
            $result[$index] = key_exists($value, $data) ? $data[$value]:0;
            $index++;
        }
        return $result;
    }

    private function _getPaymentYears($time, array $option = [])
    {
        $result = [];
        if ($option['select'] === 'number_cart') {
            $query = Cart::selectRaw('count(carts.id) as total, YEAR(created_at) as year');
            if(key_exists('status', $option)) {
                $query->where('status', $option['status']);
            }
        } else {
            $query = Payment::selectRaw('sum(price) as total, YEAR(created_at) as year');
        }

           $query->whereRaw('YEAR(created_at) >= ' . $time[0])
            ->orderBy('created_at','asc')
            ->groupBy('year')
            ->get();
        $data = $query->pluck('total','year')->toArray();
        $index = 0;
        foreach($time as $value) {
            $result[$index] = key_exists($value, $data) ? $data[$value]:0;
            $index++;
        }
        return $result;
    }

    public function getTopProductSell($search)
    {
        $take = $search['limit'] ?? 4;
        $type = $search['type'] ?? 'pie';
        $select = $search['select'] ?? 'count';
        if ($select === 'count') {
            $data = CartDetail::selectRaw('products.name, SUM(cart_detail.quantity) as total')
                ->join('products','products.id','=','cart_detail.product_id');
            $table = 'cart_detail';
        } else {
            $data = PaymentDetail::selectRaw('products.name, SUM(payment_detail.total_price) as total')
                ->join('products','products.id','=','payment_detail.product_id');
            $table = 'payment_detail';
        }
        if(isset($search['date'])) {
            $data = $this->_addFilterDate($data, $search['date'], $table);
        }
        $data = $data->groupBy('products.id')
        ->orderBy('total','desc')
        ->take($take)
        ->get();

        $result = [
            'labels' => [],
            'values' => [],
        ];
        if ($type === 'pie') {
            foreach($data as $item) {
                $result['labels'][] = $item->name;
                $result['values'][] = $item->total;
            }
        }
        return $result;
    }

    private function _addFilterDate($builder, $date, $table)
    {
        switch ($date) {
            case 'last_week':
                $builder->whereRaw('YEARWEEK(' . $table . '.created_at) = YEARWEEK(NOW()) - 1');

                break;

            case 'this_week':
                $builder->whereRaw('YEARWEEK(' . $table . '.created_at) = YEARWEEK(NOW())');
                break;

            case 'last_month':
                $builder->whereRaw('YEAR(' . $table . '.created_at) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                                    AND MONTH(' . $table . '.created_at) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)');
                break;

            case 'this_month':
                $builder->whereRaw('YEAR(' . $table . '.created_at) = YEAR(CURRENT_DATE)
                                    AND MONTH(' . $table . '.created_at) = MONTH(CURRENT_DATE)');
                break;

            case 'last_year':
                $builder->whereRaw('YEAR(' . $table . '.created_at) = YEAR(CURDATE())-1');
                break;

            case 'this_year':
                $builder->whereRaw('YEAR(' . $table . '.created_at) = YEAR(CURDATE()) ');
                break;
        }

        return $builder;

    }

    public function getTopPlatformSell($search)
    {
        $take = $search['limit'] ?? 4;
        $type = $search['type'] ?? 'pie';
        $select = $search['select'] ?? 'count';
        if ($select === 'count') {
            $data = Cart::selectRaw('platforms.name, COUNT(carts.id) as total')
                ->join('platforms', 'platforms.id','=','carts.platform_id');
            $table = 'carts';
        } else {
            $data = Payment::selectRaw('platforms.name, SUM(payments.price) as total')
                ->join('platforms', 'platforms.id','=','payments.platform_id');
            $table = 'payments';
        }
        if(isset($search['date'])) {
            $data = $this->_addFilterDate($data, $search['date'], $table);
        }
        $data = $data->groupBy('platforms.id')
        ->orderBy('total','desc')
        ->take($take)
        ->get();

        $result = [
            'labels' => [],
            'values' => [],
        ];
        if ($type === 'pie') {
            foreach($data as $item) {
                $result['labels'][] = $item->name;
                $result['values'][] = $item->total;
            }
        }
        return $result;
    }

    public function getTopCategorySell($search)
    {
        $take = $search['limit'] ?? 4;
        $type = $search['type'] ?? 'pie';
        $select = $search['select'] ?? 'count';
        if ($select === 'count') {
            $data = CartDetail::selectRaw('categories.name, COUNT(DISTINCT cart_detail.cart_id) as total')
                ->join('products','products.id', '=', 'cart_detail.product_id');
            $table = 'cart_detail';
        } else {
            $data = PaymentDetail::selectRaw('categories.name, SUM(payment_detail.total_price) as total')
                ->join('products','products.id', '=', 'payment_detail.product_id');
            $table = 'payment_detail';
        }
        if(isset($search['date'])) {
            $data = $this->_addFilterDate($data, $search['date'], $table);
        }
        $data = $data->join('categories','categories.id', '=', 'products.main_cate')
        ->groupBy('categories.id')
        ->orderBy('total','desc')
        ->take($take)
        ->get();

        $result = [
            'labels' => [],
            'values' => [],
        ];
        if ($type === 'pie') {
            foreach($data as $item) {
                $result['labels'][] = $item->name;
                $result['values'][] = $item->total;
            }
        }
        return $result;
    }

    public function createOrUpdate($data, $id = null){
        if ($id) {
            $model = Payment::find($id);
        } else {
            $model = new Payment;
        }

        $model->cart_id = $data->id;
        $model->city_id = $data->city_id;
        $model->partner_id = $data->partner_id; 
        $model->customer_id = $data->customer_id; 
        $model->transport_id = $data->transport_id; 
        $model->quantity = $data->quantity; 
        $model->partner_discount_amount = $data->partner_discount_amount; 
        $model->customer_discount_amount = $data->customer_discount_amount; 
        $model->total_discount_amount = $data->total_discount_amount; 
        $model->total_price = $data->total_price; 
        $model->total_import_price = $data->total_import_price; 
        $model->price = $data->price; 
        $model->shipping_fee = $data->shipping_fee; 
        $model->vat_percent = $data->vat_percent; 
        $model->vat_amount = $data->vat_amount; 
        $model->prepaid_amount = $data->prepaid_amount; 
        $model->needed_paid = $data->needed_paid; 
        $model->descritption = $data->descritption; 
        $model->shipping_fee = $data->shipping_fee; 
        $model->payment_status = $data->payment_status; 
        $model->status = $data->status; 
        $model->code = $data->code; 
        $model->platform_id = $data->platform_id; 

        $model->save();

        // Excute payment details
        if (isset($data->details)) {
            $this->addDetails($model->id, $data->id, $data->details);
        }

        return $model;
    }

    public function addDetails($id, $cart_id, $details){
        $model = Payment::find($id);

        foreach ($details as $detail) {

            $modelDetail = new PaymentDetail([
                'payment_id' => ($id) ? $id : 0,
                'cart_id' => ($cart_id) ? $cart_id : 0,
                'cart_detail_id' => ($detail->id) ? $detail->id : 0,
                'product_id' => ($detail->product_id) ? $detail->product_id : 0,
                'product_detail_id' => ($detail->product_detail_id) ? $detail->product_detail_id : 0,
                'quantity' => ($detail->quantity) ? $detail->quantity : 0,
                'discount_amount' => ($detail->discount_amount) ? $detail->discount_amount : 0,
                'price' => ($detail->price) ? $detail->price : 0,
                'total_price' => ($detail->total_price) ? $detail->total_price : 0,
            ]);
            $model->details()->save($modelDetail);
        }
    }

    public function getRevenueObjDataTable($request)
    {
        $products = PaymentDetail::selectRaw('products.name, products.main_cate, products.barcode_text, products.photo, products.category_ids, payments.platform_id, SUM(payment_detail.quantity) as quantity, SUM(payment_detail.total_price) as total_price, SUM(payment_detail.total_price - (products.price*payment_detail.quantity)) as profit, DATE(payment_detail.created_at) as created_at, COUNT(payments.cart_id) total_cart')
            ->join('products' ,'products.id', '=', 'payment_detail.product_id')
            ->join('payments', 'payments.id', '=', 'payment_detail.payment_id')
            ->groupBy('payment_detail.product_id');
        if ($request->has('date')) {
            $products->groupBy(DB::raw('DATE(payment_detail.created_at)'));
        }
        $categories = Category::get()->pluck('name', 'id')->toArray();
        $platforms = Platform::get()->pluck('name','id')->toArray();
        $dataTable = DataTables::eloquent($products)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('category')) !== "") {
                    $query->join('product_category', 'products.id', '=', 'product_category.product_id')
                        ->where('product_category.category_id', $request->get('category'));
                }

                if (trim($request->get('platform_id')) !== "") {
                    $query->where('payments.platform_id', $request->get('platform_id'));
                }

                if (trim($request->get('date_from')) !== "") {
                    $dateFrom = \DateTime::createFromFormat('d/m/Y', $request->get('date_from'));
                    $dateFrom = $dateFrom->format('Y-m-d 00:00:00');
                    $query->where('payment_detail.created_at', '>=', $dateFrom);
                }

                if (trim($request->get('date_to')) !== "") {
                    $dateTo = \DateTime::createFromFormat('d/m/Y', $request->get('date_to'));
                    $dateTo = $dateTo->format('Y-m-d 23:59:50');
                    $query->where('payment_detail.created_at', '<=', $dateTo);
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('products.name','like','%' . $request->get('keyword') . '%');
                        $sub->where('products.barcode_text','like','%' . $request->get('keyword') . '%');
                    });
                }
            }, true)
            ->addColumn('category', function($product) use ($categories) {
                return $categories[$product->main_cate] ?? '';
            })
            ->addColumn('total_price', function($product) use ($platforms) {
                return format_price($product->total_price);
            })
            ->addColumn('total_cart', function($product) use ($platforms) {
                return format_number($product->total_cart);
            })
            ->addColumn('profit', function($product) {
                return format_price($product->profit);
            })
            ->addColumn('platform', function($product) use ($platforms) {
                return $platforms[$product->platform_id] ?? '';
            })

            ->addColumn('photo', function ($product) {
                if ($product->photo) {
                    $html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $product->photo). '" />';
                } else {
                    $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
                }
                return $html;
            });

            return $dataTable;
    }

    public function getRevenueDataTable($request)
    {
        $data = $this->getRevenueObjDataTable($request)
            ->rawColumns(['category', 'platform', 'photo'])
            ->toJson();

        return $data;
    }


}