<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/10/18
 * Time: 2:53 PM
 */

namespace App\Repositories;

use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;

class CategoryRepository
{
    public function dataTable($request)
    {
        $data = Category::select(['id', 'name', 'level', 'parent_id', 'active', 'created_at']);

        $dataTable = DataTables::eloquent($data)
            ->addColumn('status', function ($item) {
                $html = '<span class="label label-primary">Active</span>';
                return $html;
            })
            ->rawColumns(['status'])
            ->toJson();

        return $dataTable;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Category::find($id);
        } else {
            $model = new Category;
        }
        $model->name = $data['name'];
        if (key_exists('description', $data )) {
            $model->description = $data['description'];
        }

        $model->active = ACTIVE;
        $model->save();

        return $model;
    }

    public function dataList($search)
    {
        $data = Category::select('id', 'name', 'description', 'level', 'parent_id');

        if (isset($search['order']) && isset($search['by'])) {
            $data->orderBy($search['order'], $search['by']);
        }

        return $data->get();
    }
    public function delete($panoramaID)
    {
        //
    }

    public function setTag($id, $panoramaID)
    {

    }
}