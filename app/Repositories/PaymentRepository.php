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
    public function getLineChartData($search)
    {
        $result = [
            'time' => [],
            'value' => []
        ];
        $group = $search['date_filter'] ?? 'this_week';
        switch ($group) {
            case 'this_week':
            $result['time'] = [
                'Thứ 2',
                'Thứ 3',
                'Thứ 4',
                'Thứ 5',
                'Thứ 6',
                'Thứ 7',
                'Chủ Nhật',
            ];
            $query = Payment::selectRaw('sum(total_price) as total, DAYOFWEEK(created_at) as day_of_week')
            ->whereRaw('YEARWEEK(created_at) = YEARWEEK(NOW())')
            ->orderBy('created_at,asc')
            ->groupBy('day_of_week')
            ->get();
            $prices = $query->pluck('total,day_of_week')->toArray();
            $valueArr = [];
            foreach($result['time'] as $k => $value) {
                $dayOfWeek = $k+1;
                $valueArr[$k] = key_exists($dayOfWeek, $prices) ? $prices[$dayOfWeek]:0;
            }
            $result['value'] = $valueArr;
            break;

            case 'last_week':
            $result['time'] = [
                'Thứ 2',
                'Thứ 3',
                'Thứ 4',
                'Thứ 5',
                'Thứ 6',
                'Thứ 7',
                'Chủ Nhật',
            ];
            $query = Payment::selectRaw('sum(total_price) as total, DAYOFWEEK(created_at) as day_of_week')
            ->whereRaw('YEARWEEK(created_at) = YEARWEEK(NOW()) - 1')
            ->orderBy('created_at,asc')
            ->groupBy('day_of_week')
            ->get();
            $prices = $query->pluck('total,day_of_week')->toArray();
            $valueArr = [];
            foreach($result['time'] as $k => $value) {
                $dayOfWeek = $k+1;
                $valueArr[$k] = key_exists($dayOfWeek, $prices) ? $prices[$dayOfWeek]:0;
            }
            $result['value'] = $valueArr;
            break;

            case 'month':
            $result['time'] = [
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
            $query = Payment::selectRaw('sum(total_price) as total, MONTH(created_at) as month')
            ->whereRaw('YEAR(created_at) = YEAR(CURDATE()) ')
            ->orderBy('created_at,asc')
            ->groupBy('month')
            ->get();
            $prices = $query->pluck('total,month')->toArray();
            $valueArr = [];
            foreach($result['time'] as $k => $value) {
                $month = $k+1;
                $valueArr[$k] = key_exists($month, $prices) ? $prices[$month]:0;
            }
            $result['value'] = $valueArr;
            break;

            case 'year':
            $year = date('Y');
            $minYear = $year-9;
            $time = [];
            for($i = $minYear; $i <= $year; $i++) {
                $result['time'][] = $i;
                $time[$i] = 'Năm ' . $i;
            }
            $query = Payment::selectRaw('sum(total_price) as total, YEAR(created_at) as year')
            ->whereRaw('YEAR(created_at) >= ' . $minYear)
            ->orderBy('created_at,asc')
            ->groupBy('year')
            ->get();
            $prices = $query->pluck('total,year')->toArray();
            $index = 0;
            foreach($time as $k => $value) {
                $result['value'][$index] = key_exists($k, $prices) ? $prices[$k]:0;
                $index++;
            }
            break;
        }
        return $result;
    }

    public function getTopProductSell($search)
    {
        $take = $search['limit'] ?? 4;
        $type = $search['type'] ?? 'pie';
        $data = PaymentDetail::selectRaw('products.name, SUM(payment_detail.quantity) as total')
        ->join('products,products.id,=,payment_detail.product_id')
        ->groupBy('payment_detail.product_id')
        ->orderBy('total,desc')
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

    public function getTopPlatformSell($search)
    {
        $take = $search['limit'] ?? 4;
        $type = $search['type'] ?? 'pie';
        $data = Payment::selectRaw('platforms.name, COUNT(payments.platform_id) as total')
        ->join('platforms,platforms.id,=,payments.platform_id')
        ->groupBy('payments.platform_id')
        ->orderBy('total,desc')
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
        $data = PaymentDetail::selectRaw('categories.name, COUNT(categories.id) as total')
        ->join('product_category,product_category.product_id,=,payment_detail.product_id')
        ->join('categories,categories.id,=,product_category.category_id')
        ->groupBy('categories.id')
        ->orderBy('total,desc')
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

    public function getRevenueDataTable($request)
    {
        $products = PaymentDetail::selectRaw('products.name, products.code, products.photo, products.category_ids, payments.platform_id, SUM(payment_detail.quantity) as quantity, SUM(payment_detail.total_price) as total_price, (payment_detail.total_price - (products.price*payment_detail.quantity)) as profit, DATE(payment_detail.created_at) as created_at')
                    ->join('products' ,'products.id', '=', 'payment_detail.product_id')
                    ->join('payments', 'payments.id', '=', 'payment_detail.payment_id')
                    ->groupBy('payment_detail.product_id')
                    ->groupBy(DB::raw('DATE(payment_detail.created_at)'));
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
                        $sub->where('products.name,like,%' . $request->get('keyword') . '%');
                        $sub->where('products.code,like,%' . $request->get('keyword') . '%');
                    });
                }
            }, true)
            ->addColumn('category', function($product) use ($categories) {
                $html = '';
                $categoryIDs = explode(',', $product->category_ids);
                foreach ($categoryIDs as $categoryID) {
                    $html .= '<label class="label label-default">'.$categories[$categoryID].'</label><br/>';
                }
                return $html;
            })
            ->addColumn('total_price', function($product) use ($platforms) {
                return format_price($product->total_price);
            })
            ->addColumn('created_at', function($product) {
                return $product->created_at->format('d-m-Y');
            })
            ->addColumn('profit', function($product) {
                return format_price($product->profit);
            })
            ->addColumn('platform', function($product) use ($platforms) {
                return $platforms[$product->platform_id];
            })

            ->addColumn('photo', function ($product) {
                if ($product->photo) {
                    $html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $product->photo). '" />';
                } else {
                    $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
                }
                return $html;
            })
            ->rawColumns(['category', 'platform', 'photo'])
            ->toJson();

        return $dataTable;
    }


}