<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Partner;
use Yajra\DataTables\Facades\DataTables;

class PartnerRepository
{

    public function dataTable($request)
    {
        $data = Partner::selectRaw('partners.id, partners.active, partners.name,code, phone, address, email, partners.discount_amount, partners.created_at');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('partners.active', $request->get('status'));
                }
                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('email', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('phone', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.Partner.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
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
        $data = Partner::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Partner::find($id);
        } else {
            $model = new Partner;
        }
        $model->name = $data['name'];
        $model->email = $data['name'];
        $model->code = $data['code'];
        $model->discount_amount = $data['discount_amount'];
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
        $model->save();

        return $model;
    }

    public function delete($id)
    {
        $model = Partner::find($id);
        if ($model === null) {
            $result['errors'][] = 'Cộng tác viên có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }
        $count = Customer::where('partner_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Nhóm khách hàng đang được sử dụng. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }
        $model->delete();

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Partner::find($id);
        $model->active = $status;
        return $model->save();
    }
}