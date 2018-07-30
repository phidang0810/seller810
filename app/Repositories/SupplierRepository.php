<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;

class SupplierRepository
{

    public function dataTable($request)
    {
        $data = Supplier::selectRaw('suppliers.id, suppliers.active, suppliers.name, code, tax_code, phone, address, email, suppliers.responsible_person, suppliers.created_at');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('suppliers.active', $request->get('status'));
                }
                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('email', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('phone', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('code', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('tax_code', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.suppliers.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
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
        $data = Supplier::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Supplier::find($id);
        } else {
            $model = new Supplier;
        }
        $model->name = $data['name'];
        $model->email = $data['email'];
        $model->code = $data['code'];
        $model->tax_code = $data['tax_code'];
        $model->responsible_person = $data['responsible_person'];
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
        $model = Supplier::find($id);
        if ($model === null) {
            $result['errors'][] = 'Nhà cung cấp có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }
        $count = Product::where('supplier_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Nhà cung cấp có nhiều sản phẩm. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }

        Supplier::destroy($id);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Supplier::find($id);
        $model->active = $status;
        return $model->save();
    }
}