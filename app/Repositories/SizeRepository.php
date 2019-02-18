<?php

namespace App\Repositories;

use App\Models\ProductDetail;
use App\Models\Size;
use App\Models\SizeGroup;
use Yajra\DataTables\Facades\DataTables;

class SizeRepository
{

    public function dataTable($request)
    {
        $data = Size::selectRaw('id, name, active, created_at');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('size.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('name', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('status', function ($data) {
                $active = '';
                $disable = '';
                if($data->active == ACTIVE) {
                    $active  = 'checked';
                }
                $html = '<input type="checkbox" '.$disable.' data-name="'.$data->name.'" data-id="'.$data->id.'" name="social' . $data->active . '" class="js-switch" value="' . $data->active . '" ' . $active . ' ./>';
                return $html;
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.size.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $data->id . '" data-name="' . $data->name . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';

                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $dataTable;
    }
    public function getData($id)
    {
        $data = Size::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Size::find($id);
        } else {
            $model = new Size;
        }
        $model->name = $data['name'];
        $model->code = strtolower($data['name']);
        $model->active = $data['status'];
        $model->save();
        return $model;
    }

    public function delete($id)
    {
        $model = Size::find($id);
        if ($model === null) {
            $result['errors'][] = 'Phiếu chi có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }

        $used = ProductDetail::where('size_id', $id)->count();
        if($used) {
            $result['errors'][] = 'Size ' . $model->name . ' đã được sử dụng. Bạn không thể xóa!';
            $result['success'] = false;
        }
        Size::destroy($id);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Size::find($id);
        $model->active = $status;
        return $model->save();
    }

    public function getSizes() {
        $sizes = Size::select(['sizes.id', 'sizes.name'])->get();
        return $sizes;
    }

}