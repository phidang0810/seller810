<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\Brand;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;

Class BrandRepository
{

	public function dataTable($request)
    {
		$builder = Brand::select(['id', 'name', 'active', 'order', 'created_at']);

		$dataTable = DataTables::eloquent($builder)
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
        ->addColumn('name', function ($item) {
            $html = '';
            $html .= '<a href="' . route('admin.brands.view', ['id' => $item->id]) . '">'.$item->name.'</a>';
            return $html;
        })
		->addColumn('action', function ($item) {
			$html = '';
			$html .= '<a href="' . route('admin.brands.view', ['id' => $item->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $item->id . '" data-name="' . $item->name . '">';
			$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			return $html;
		})
		->addColumn('status', function ($item) {
			$active = '';
			$disable = '';
			if ($item->active === ACTIVE) {
				$active  = 'checked';
			}
			$html = '<input type="checkbox" '.$disable.' data-name="'.$item->name.'" data-id="'.$item->id.'" name="social' . $item->active . '" class="js-switch" value="' . $item->active . '" ' . $active . ' ./>';
			return $html;
		})
		->rawColumns(['name', 'status', 'action'])
		->toJson();

		return $dataTable;
	}

	public function getData($id)
	{
		$data = Brand::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = Brand::find($id);
		} else {
			$model = new Brand;
		}
		
		$model->name = $data['name'];
		$model->order = $data['order'];
		$model->active = $data['active'];

		$model->save();

		return $model;
	}


	public function delete($id)
	{
        $model = Brand::find($id);
        if ($model === null) {
            $result['errors'][] = 'Thương hiệu có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }
        $count = Product::where('brand_id', $id)->count();
        if ($count) {
            $result['errors'][] = 'Thương hiệu ' . $model->name. ' đang được sử dụng. Bạn không thể xóa!';
            $result['success'] = false;
            return $result;
        }

        Brand::destroy($id);

        return [
            'success' => true
        ];
	}

	public function changeStatus($id, $status)
	{
		$model = Brand::find($id);
		$model->active = $status;
		return $model->save();
	}
}