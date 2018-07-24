<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 7/23/2018
 * Time: 9:11 PM
 */

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

Class CategoryRepository
{
    const CACHE_NAME_CATEGORIES = 'categories';

    public function dataTable($request)
    {
        $categories = Category::select(['categories.id', 'categories.name', 'categories.level', 'categories.active', 'categories.created_at']);

        $dataTable = DataTables::eloquent($categories)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('categories.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('categories.name', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('numbers', function ($category) {
                $html = '';
                $html .= '<a href="javascript:;">10</a>';
                return $html;
            })
            ->addColumn('action', function ($category) {
                $html = '';
                $html .= '<a href="' . route('admin.categories.view', ['id' => $category->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $category->id . '" data-name="' . $category->name . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
                return $html;
            })
            ->addColumn('status', function ($category) {
                if ($category->active === ACTIVE) {
                    $html = '<span class="label label-primary">Đã kích hoạt</span>';
                } else {
                    $html = '<span class="label">Chưa kích hoạt</span>';
                }
                return $html;
            })
            ->rawColumns(['status', 'action', 'numbers'])
            ->toJson();

        return $dataTable;
    }

    public function addCategory($category)
    {
        $categories = $this->getCategories();
        array_push($categories, $category);
        Cache::forever(self::CACHE_NAME_CATEGORIES, $categories);

        return $category;
    }

    public function getCategories()
    {
        return Cache::get(self::CACHE_NAME_CATEGORIES, []);
    }

    public function getCategory($id)
    {
        $data = Category::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Category::find($id);
        } else {
            $model = new Category;
        }
        $model->name = $data['name'];
        if ($data['parent_id']) {
            $parent = Category::find($data['parent_id']);
        }
        $model->parent_id = ($data['parent_id']) ? $data['parent_id'] : null;
        $model->level = ($data['parent_id']) ? $parent->level + 1 : 0;
        $model->description = $data['description'];
        $model->active = $data['active'];
        $model->order = $data['order'];

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
            $category = Category::find($id);
            if ($category === null) {
                $result['errors'][] = 'ID danh mục: ' . $id . ' không tồn tại';
                $result['success'] = false;
                continue;
            }
            $category->delete();
        }

        return $result;
    }
}