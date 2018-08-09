<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use Response;

class CustomerRepository
{

    public function dataTable($request)
    {
        $data = Customer::selectRaw('customers.id, group_customers.name as group_name, customers.active, customers.name,code, phone, address, email, customers.group_customer_id, customers.created_at')
                            ->join('group_customers', 'group_customers.id', '=', 'customers.group_customer_id');
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
                $html = '<a href="' . route('admin.customers.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
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
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $dataTable;
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
            ->rawColumns(['status'])
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
            $model->phone = $data['phone'];
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

    public function getCustomer($request){
        $customer_id = $request->get('customer_phone');

        $return = [
            'customer_id' => $customer_id,
            'message'   =>  'Lấy datas cho khách hàng thành công',
        ];

        if ($customer_id) {
            $customer = Customer::find($customer_id);
            $customer->city;
            $customer->group;
            $return['customer'] = $customer;
        }

        return Response::json($return);
    }
}