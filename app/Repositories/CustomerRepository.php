<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use Response;

class CustomerRepository
{
    public function getObjDataTable($request)
    {
        $data = Customer::selectRaw('customers.id, customers.name, code, group_customers.name as group_name, email, phone, address, customers.active, customers.group_customer_id, customers.created_at')
        ->leftJoin('group_customers', 'group_customers.id', '=', 'customers.group_customer_id');
        $dataTable = DataTables::eloquent($data)
        ->filter(function ($query) use ($request) {
            if (trim($request->get('status')) !== "") {
                $query->where('customers.active', $request->get('status'));
            }
            if (trim($request->get('group_customer_id')) !== "") {
                $query->where('customers.group_customer_id', $request->get('group_customer_id'));
            }
            if (trim($request->get('keyword')) !== "") {
                $query->where(function ($sub) use ($request) {
                    $sub->where('customers.name', 'like', '%' . $request->get('keyword') . '%')
                    ->orWhere('customers.email', 'like', '%' . $request->get('keyword') . '%')
                    ->orWhere('customers.phone', 'like', '%' . $request->get('keyword') . '%')
                    ->orWhere('customers.code', 'like', '%' . $request->get('keyword') . '%');
                });

            }
        }, true)
        ->addColumn('action', function ($data) {
            $html = '<a href="' . route('admin.customers.history', [$data->id]) . '" class="btn btn-xs btn-warning" style="margin-right: 5px"><i class="fa fa-history" aria-hidden="true"></i> Lịch sử</a>';
            $html .= '<a href="' . route('admin.customers.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
            $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $data->id . '" data-name="' . $data->name . '">';
            $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';

            return $html;
        })
        ->addColumn('status', function ($data) {
            $active = '';
            if ($data->active === ACTIVE) {
                $active  = 'checked';
            }
            $html = '<input type="checkbox" data-name="'.$data->name.'" data-id="'.$data->id.'" name="social' . $data->active . '" class="js-switch" value="' . $data->active . '" ' . $active . ' ./>';
            return $html;
        })
        ->addColumn('total_dept', function ($data){
            $total_dept = 0;
            $carts = Cart::select(['needed_paid'])->where('customer_id', $data->id)->where('needed_paid', '>', 0)->get();
            if(count($carts) > 0){
                foreach ($carts as $cart) {
                    $total_dept += $cart->needed_paid;
                }
            }
            return format_price($total_dept);
        });
        return $dataTable;
    }

    public function dataTable($request)
    {
        $data = $this->getObjDataTable($request)
        ->rawColumns(['status', 'action', 'total_dept'])
        ->toJson();

        return $data;
    }
    public function getData($id)
    {
        $data = Customer::find($id);
        return $data;
    }

    public function getHistoryByID($id, $request)
    {
        $query = Cart::select('carts.code', 'carts.total_price', 'carts.created_at', 'carts.status', 'platforms.name as platform_name')
        ->leftJoin('platforms', 'platforms.id', '=', 'carts.platform_id')
        ->where('customer_id', $id);

        $dataTable = DataTables::eloquent($query)
        ->filter(function ($query) use ($request) {
            if (trim($request->get('status')) !== "") {
                $query->where('custocartsmers.active', $request->get('status'));
            }
        }, true)
        ->addColumn('status', function ($data) {
            switch ($data->status) {
                case 1:
                default:
                $html = '<label class="label label-success">Đã giao</label>';
            }

            return $html;
        })
        ->addColumn('code', function ($cart) {
            $html = '<a href="'.route('admin.carts.index', ['cart_code' => $cart->code]) . '">' . $cart->code .'</a>';
            return $html;
        })
        ->rawColumns(['code', 'status'])
        ->toJson();

        return $dataTable;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Customer::find($id);
            $model->code = general_code($data['name'], $id, 5);
        } else {
            $model = new Customer;
        }
        $model->group_customer_id = $data['group_customer_id'];
        $model->name = $data['name'];
        $model->email = $data['email'];
        $model->active = $data['active'];
        if(isset($data['phone'])) {
            $model->phone = preg_replace('/\s+/', '', $data['phone']);
        }
        if(isset($data['address'])) {
            $model->address = $data['address'];
        }
        if(isset($data['city_id'])) {
            $model->city_id = $data['city_id'];
        }
        if(isset($data['description'])) {
            $model->description = $data['description'];
        }

        $model->save();

        if (is_null($id)) {
            $model->code = general_code($model->name, $model->id, 5);
            $model->save();
        }
        return $model;
    }

    public function delete($id)
    {
        $model = Customer::find($id);
        if ($model === null) {
            $result['errors'][] = 'Khách hàng có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }
        $count = Cart::where('customer_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Khách hàng '. $model->name .' . đã có đơn hàng. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }

        Customer::destroy($id);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Customer::find($id);
        $model->active = $status;
        return $model->save();
    }

    public function getCustomers()
    {
        $data = Customer::get();
        return $data;
    }

    public function getPhoneOptions($select = 0){
        return make_option($this->getCustomers(), $select, 'phone');
    }

    public function getNameOptions($select = 0){
        return make_option($this->getCustomers(), $select, 'name');
    }

    public function getCustomerName($request){
        $customer_id = $request->get('customer_name');
        $new_customer = ($request->get('new_customer')) ? $request->get('new_customer') : 'false';

        $return = [
            'status'    =>  'true',
            'customer_id' => $customer_id,
            'message'   =>  'Lấy datas cho khách hàng thành công',
        ];

        if ($new_customer == 'false') {
            if ($customer_id) {
                $customer = Customer::find($customer_id);
                $customer->city;
                $customer->group;
                $return['customer'] = $customer;
            }else{
                $return['status'] = 'false';
            }
        }else{
            $customer = Customer::where('name', '=' ,$customer_name)->first();
            if ($customer) {
                $customer->city;
                $customer->group;
                $return['customer'] = $customer;
            }else{
                $return['status'] = 'false';
            }
        }

        return Response::json($return);
    }

    public function getCustomer($request){
        $customer_id = $request->get('customer_phone');
        $new_customer = ($request->get('new_customer')) ? $request->get('new_customer') : 'false';

        $return = [
            'status'    =>  'true',
            'customer_id' => $customer_id,
            'message'   =>  'Lấy datas cho khách hàng thành công',
        ];

        if ($new_customer == 'false') {
            if ($customer_id) {
                $customer = Customer::find($customer_id);
                $customer->city;
                $customer->group;
                $return['customer'] = $customer;
            }else{
                $return['status'] = 'false';
            }
        }else{
            $customer_phone = preg_replace('/[^0-9]/', '', $customer_id);
            $customer = Customer::where('phone', '=' ,$customer_phone)->first();
            if ($customer) {
                $customer->city;
                $customer->group;
                $return['customer'] = $customer;
            }else{
                $return['status'] = 'false';
            }
        }

        return Response::json($return);
    }

    public function getCustomerNames($request)
    {
        $formatted_customers = [];
        $term = trim($request->q);

        $customers_list = Customer::where('name','LIKE', '%'.$term.'%')->get();
        foreach ($customers_list as $customer) {
            $formatted_customers[] = ['id' => $customer->id, 'text' => $customer->name];
        }

        return $formatted_customers;
    }

    public function getCustomersV2($request)
    {
        $formatted_customers = [];
        $term = trim($request->q);
        $term = preg_replace('/\s+/', '', $term);

        $customers_list = Customer::where('phone','LIKE', '%'.$term.'%')->get();
        foreach ($customers_list as $customer) {
            $formatted_customers[] = ['id' => $customer->id, 'text' => $customer->phone];
        }

        return $formatted_customers;
    }

    public function getTotalNeededPaid($customerID)
    {
        $data = Cart::where('customer_id', $customerID)
        ->where('payment_status', PAYING_NOT_ENOUGH)
        ->selectRaw('SUM(needed_paid) as total')
        ->groupBy('customer_id')
        ->first();
        if ($data) {
            return $data->total;
        }

        return 0;
    }

    public function dataTableForDept($request){
        $customer_id = ($request->get('customer_id') !== null) ? $request->get('customer_id') : 0;
        $cart = Cart::select(['id', 'code', 'quantity', 'needed_paid', 'status', 'payment_status', 'created_at'])
        ->where('customer_id', $customer_id)->where('needed_paid', '>', 0);

        $dataTable = DataTables::eloquent($cart)
        ->addColumn('status', function ($cart) {
            $html = '<span class="label label-'.CART_LABEL[$cart->status].'">'.CART_TEXT[$cart->status].'</span>';
            return $html;
        })
        ->addColumn('payment_status', function ($cart) {
            $html = '<span class="label label-'.CART_PAYMENT_LABEL[$cart->payment_status].'">'.CART_PAYMENT_TEXT[$cart->payment_status].'</span>';
            return $html;
        })
        ->addColumn('pay', function($cart){
            $html = '';
            $html .= '<input type="text" class="form-control" name="pay-'.$cart->id.'">';
            return $html;
        })
        ->addColumn('action', function($cart){
            $html = '<a href="#" class="bt-pay btn btn-primary btn-xs" data-id="' . $cart->id . '" data-name="' . $cart->code . '">Trả</a>';
            return $html;
        })
        ->addColumn('needed_paid', function ($data){
            return format_price($data->needed_paid);
        })
        ->rawColumns(['status', 'payment_status', 'action', 'pay', 'needed_paid'])
        ->toJson();

        return $dataTable;
    }
}