<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;
use DNS1D;

Class WarehouseRepository
{
	const CACHE_NAME_PRODUCTS = 'warehouses';

	public function dataTable($request){
		$warehouses = Warehouse::select(['id', 'name', 'code', 'address', 'phone', 'email', 'active', 'order', 'created_at']);

		$dataTable = DataTables::eloquent($warehouses)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('active', $request->get('status'));
			}

			if (trim($request->get('keyword')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('name', 'like', '%' . $request->get('keyword') . '%');
				});

			}
		}, true)
		->addColumn('action', function ($warehouses) {
			$html = '';
			$html .= '<a href="' . route('admin.warehouses.view', ['id' => $warehouses->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $warehouses->id . '" data-name="' . $warehouses->name . '">';
			$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			return $html;
		})
		->addColumn('status', function ($warehouses) {
			$active = '';
			$disable = '';
			if ($warehouses->active === ACTIVE) {
				$active  = 'checked';
			}
			$html = '<input type="checkbox" '.$disable.' data-name="'.$warehouses->name.'" data-id="'.$warehouses->id.'" name="social' . $warehouses->active . '" class="js-switch" value="' . $warehouses->active . '" ' . $active . ' ./>';
			return $html;
		})
		->rawColumns(['status', 'action'])
		->toJson();

		return $dataTable;
	}

	public function getWarehouse($id)
	{
		$data = Warehouse::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = Warehouse::find($id);
		} else {
			$model = new Warehouse;
		}
		
		$model->name = $data['name'];
		$model->code = general_code('K H', $id, 2);
		$model->address = $data['address'];
		$model->phone = $data['phone'];
		$model->email = $data['email'];
		$model->order = $data['order'];
		$model->active = $data['active'];

		$model->save();

		return $model;
	}


	public function delete($ids)
	{
		$result = [
			'success' => true,
			'errors' => []
		];
		foreach ($ids as $id) {
			$warehouse = Warehouse::find($id);
			if ($warehouse === null) {
				$result['errors'][] = 'ID kho hàng: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			$warehouse->delete();
		}

		return $result;
	}

	public function changeStatus($warehouseID, $status)
	{
		$model = Warehouse::find($warehouseID);
		$model->active = $status;
		return $model->save();
	}
}