<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Partner;
use Yajra\DataTables\Facades\DataTables;
use Response;

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
            ->addColumn('discount_amount', function($data){
                return format_price($data->discount_amount);
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.partners.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
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
            $model->code = general_code($data['name'], $id, 5);
        } else {
            $model = new Partner;
        }
        $model->name = $data['name'];
        $model->email = $data['email'];
        $model->discount_amount = preg_replace('/[^0-9]/', '', $data['discount_amount']);
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

        if (is_null($id)) {
            $model->code = general_code($model->name, $model->id, 6);
            $model->save();
        }

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
        $count = Cart::where('partner_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Cộng tác viên có nhiều đơn hàng. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }

        Partner::destroy($id);

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

    public function getPartners(){
        $data = Partner::get();
        return $data;
    }

    public function getPartnerOptions($id = 0){
        return make_option($this->getPartners(), $id);
    }

    public function getPartnerDiscountAmount($request){
        $partner_id = $request->get('partner_id');

        $return = [
            'partner_id' => $partner_id,
            'message'   =>  'Lấy datas cho cộng tác viên thành công',
        ];

        if ($partner_id) {
            $partner = Partner::find($partner_id);
            $return['partner'] = $partner;
        }

        return Response::json($return);
    }
}