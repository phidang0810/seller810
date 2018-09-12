<?php

namespace App\Repositories;

use App\Models\Payslip;
use App\Models\PayslipGroup;
use Yajra\DataTables\Facades\DataTables;

class PayslipRepository
{

    public function dataTable($request)
    {
        $data = Payslip::selectRaw('payslips.id, payslips.status, payslips.description, payslip_groups.name as group_name, code, payslips.price, payslips.created_at')
                ->join('payslip_groups', 'payslip_groups.id', '=', 'payslips.group_id');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('payslips.status', $request->get('status'));
                }
                if (trim($request->get('date_from')) !== "") {
                    $dateFrom = \DateTime::createFromFormat('d/m/Y', $request->get('date_from'));
                    $dateFrom = $dateFrom->format('Y-m-d 00:00:00');
                    $query->where('payslips.created_at', '>=', $dateFrom);
                }

                if (trim($request->get('date_to')) !== "") {
                    $dateTo = \DateTime::createFromFormat('d/m/Y', $request->get('date_to'));
                    $dateTo = $dateTo->format('Y-m-d 23:59:50');
                    $query->where('payslips.created_at', '<=', $dateTo);
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('code', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('price', function($data){
                return format_price($data->price);
            })
            ->addColumn('status', function ($data) {
                $html = '';
                $text = PAYSLIP_TEXT[$data->status];
                if ($data->status == PAYSLIP_PENDING) {
                    $html  = '<label class="label label-warning">'.$text.'</label>';
                }

                if ($data->status == PAYSLIP_APPROVED) {
                    $html  = '<label class="label label-success">'.$text.'</label>';
                }

                if ($data->status == PAYSLIP_CANCEL) {
                    $html  = '<label class="label label-default">'.$text.'</label>';
                }
                return $html;
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.payslips.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
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
        $data = Payslip::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Payslip::find($id);
        } else {
            $model = new Payslip;
        }

        //check group
        $groupName = trim($data['group']);
        $groupExist = PayslipGroup::where('name', $groupName)->first();
        if($groupExist) {
            $groupId = $groupExist->id;
        } else {
            $group = new PayslipGroup;
            $group->name = $groupName;
            $group->save();

            $groupId = $group->id;
        }
        $model->group_id = $groupId;
        $model->description = $data['description'];
        $model->price = preg_replace('/[^0-9]/', '', $data['price']);
        $model->status = $data['status'];
        $model->save();

        if (is_null($id)) {
            $model->code = general_code('phieu chi', $model->id, 5);
            $model->save();
        }

        return $model;
    }

    public function delete($id)
    {
        $model = Payslip::find($id);
        if ($model === null) {
            $result['errors'][] = 'Phiếu chi có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }

        Payslip::destroy($id);

        return [
            'success' => true
        ];
    }

    public function getGroups()
    {
        return PayslipGroup::get();
    }

    public function getStatus()
    {
        return [
            PAYSLIP_PENDING => PAYSLIP_TEXT[PAYSLIP_PENDING],
            PAYSLIP_APPROVED => PAYSLIP_TEXT[PAYSLIP_APPROVED],
            PAYSLIP_CANCEL => PAYSLIP_TEXT[PAYSLIP_CANCEL]
        ];
    }
}