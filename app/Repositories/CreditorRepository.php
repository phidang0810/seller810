<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Creditor;
use App\Models\Product;
use App\Models\Supplier;
use Yajra\DataTables\Facades\DataTables;

class CreditorRepository
{

    public function dataTable($request)
    {
        $data = Creditor::selectRaw('suppliers.name as supplier_name, suppliers.code as supplier_code, creditors.phone, creditors.code, creditors.full_name, creditors.total, creditors.paid, creditors.status, creditors.date')
        ->join('suppliers', 'suppliers.id', '=', 'creditors.supplier_id');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('creditors.status', $request->get('status'));
                }
                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('suppliers.name', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
                if (trim($request->get('code')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('creditors.code', 'like', '%' . $request->get('code') . '%');
                    });

                }
                if (trim($request->get('start_date')) !== "") {
                    $fromDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('start_date') . ' 00:00:00')->toDateTimeString();

                    if (trim($request->get('end_date')) !== "") {

                        $toDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('end_date') . ' 23:59:59')->toDateTimeString();
                        $query->whereBetween('creditors.date', [$fromDate, $toDate]);
                    } else {
                        $query->whereDate('creditors.date', '>=', $fromDate);
                    }
                }

            }, true)
            ->addColumn('action', function ($data) {
                $html = '';
                if($data->status !== CREDITOR_PAID) {
                    $html .= '<a href="' . route('admin.creditors.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Trả Nợ</a>';
                }

                return $html;
            })
            ->addColumn('total', function($product) {
                return format_price($product->total);
            })
            ->addColumn('paid', function($product) {
                return format_price($product->paid);
            })
            ->addColumn('status', function ($data) {
                $html = '';
                if ($data->status === CREDITOR_NOT_PAID) {
                    $html  = '<label class="label label-danger">Chưa trả</label>';
                }
                if ($data->status === CREDITOR_PAYING) {
                    $html  = '<label class="label label-warning">Đang trả</label>';
                }
                if ($data->status === CREDITOR_PAID) {
                    $html  = '<label class="label label-primary">Đã trả</label>';
                }
                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $dataTable;
    }
    public function getData($id)
    {
        $data = Creditor::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Creditor::find($id);
            $model->code = general_code('du no', $id, 4);
        } else {
            $model = new Creditor;
        }
        $model->supplier_id = $data['supplier_id'];
        $model->full_name = $data['name'];
        $model->total = $data['email'];
        $model->paid = $data['tax_code'];

        if($data['paid'] == 0) {
            $model->status = CREDITOR_NOT_PAID;
        } else if($data['paid'] < $data['total']) {
            $model->status = CREDITOR_PAYING;
        } else {
            $model->status = CREDITOR_PAID;
        }

        if(isset($data['note'])) {
            $model->note = $data['note'];
        }

        $model->save();

        if (is_null($id)) {
            $model->code = general_code($model->name, $model->id, 5);
            $model->save();
        }

        return $model;
    }

}