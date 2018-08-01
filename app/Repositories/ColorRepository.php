<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 7/24/2018
 * Time: 7:24 PM
 */

namespace App\Repositories;

use App\Models\Color;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;

Class ColorRepository
{
	const CACHE_NAME_COLORS = 'colors';

	public function dataTable($request)
	{
		$colors = Color::select(['colors.id', 'colors.photo', 'colors.code','colors.name', 'colors.active', 'colors.created_at']);

		$dataTable = DataTables::eloquent($colors)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('colors.active', $request->get('status'));
			}

			if (trim($request->get('keyword')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('colors.name', 'like', '%' . $request->get('keyword') . '%');
				});

			}
		}, true)
		->addColumn('action', function ($color) {
			$html = '';
			$html .= '<a href="' . route('admin.colors.view', ['id' => $color->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $color->id . '" data-name="' . $color->name . '">';
			$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			return $html;
		})
        ->addColumn('status', function ($color) {
            $active = '';
            $disable = '';
            if ($color->active === ACTIVE) {
                $active  = 'checked';
            }
            $html = '<input type="checkbox" '.$disable.' data-name="'.$color->name.'" data-id="'.$color->id.'" name="social' . $color->active . '" class="js-switch" value="' . $color->active . '" ' . $active . ' ./>';
            return $html;
        })
		->addColumn('name', function ($color) {
			if ($color->code) {
				$html = '<span class="c-code-name">'.$color->name.' <i class="fa fa-square" aria-hidden="true" style="color: '.$color->code.';"></i></span>';
			} else {
				$html = '';
			}
			return $html;
		})
		->addColumn('photo', function ($color) {
                if ($color->photo) {
                    $html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $color->photo). '" />';
                } else {
                    $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
                }
                return $html;
            })
		->rawColumns(['photo','name','status', 'action'])
		->toJson();

		return $dataTable;
	}

	public function getColor($id)
	{
		$data = Color::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = Color::find($id);
		} else {
			$model = new Color;
		}
		
		$model->name = $data['name'];
		$model->active = $data['active'];
		$model->order = $data['order'];
		$model->code = $data['code'];
		if(isset($data['photo'])) {

            if ($model->photo) {
                Storage::delete($model->photo);
            }
            $upload = new Photo($data['photo']);
            $model->photo = $upload->uploadTo('colors');
        }

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
			$color = Color::find($id);
			if ($color === null) {
				$result['errors'][] = 'ID màu sắc: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			if ($color->photo) {
                Storage::delete($user->photo);
            }
			$color->delete();
		}

		return $result;
	}

    public function changeStatus($colorID, $status)
    {
        $model = Color::find($colorID);
        $model->active = $status;
        return $model->save();
    }

	public function validateAjax($request){
		$name = $request->get('name');
		$value = $request->get('value');
		$return;

		$model = Color::where($name,$value)->first();
		if ($model) {
			$return = false;
		}else{
			$return = true;
		}
		return Response::json($return);
	}
}