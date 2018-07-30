<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\GroupCustomer;
use Yajra\DataTables\Facades\DataTables;

class GroupCustomerRepository
{

    public function dataTable($request)
    {
        $data = GroupCustomer::selectRaw('group_customers.id, group_customers.active, group_customers.name, group_customers.discount_amount, group_customers.created_at, count(customers.id) as count')
            ->leftJoin('customers', function($join){
                $join->on('group_customers.id', '=', 'customers.group_customer_id');
            })

        ->groupBy('group_customers.id');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('group_customers.active', $request->get('status'));
                }
            }, true)
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.groupCustomer.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $data->id . '" data-name="' . $data->name . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';

                return $html;
            })
            ->addColumn('discount_amount', function($data){
                return format_price($data->discount_amount);
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

    public function getDataList()
    {
        $data = GroupCustomer::where('active', ACTIVE)->get();
        return $data;
    }

    public function getData($id)
    {
        $data = GroupCustomer::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = GroupCustomer::find($id);
        } else {
            $model = new GroupCustomer;
        }
        $model->name = $data['name'];
        $model->discount_amount = preg_replace('/[^0-9]/', '', $data['discount_amount']);
        $model->active = $data['active'];
        $model->save();

        return $model;
    }

    public function delete($id)
    {
        $model = GroupCustomer::find($id);
        if ($model === null) {
            $result['errors'][] = 'Nhóm khách hàng có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }
        $count = Customer::where('group_customer_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Nhóm khách hàng đang được sử dụng. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }

        GroupCustomer::destroy($id);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = GroupCustomer::find($id);
        $model->active = $status;
        return $model->save();
    }
}