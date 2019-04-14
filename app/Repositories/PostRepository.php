<?php
namespace App\Repositories;

use App\Libraries\Photo;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PostRepository
{
    public function getObjDataTable($request)
    {
        $data = Post::selectRaw('posts.id, posts.title, photo, active, thumb, created_at');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('posts.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('posts.name', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('photo', function ($data) {
                if ($data->photo) {
                    $html = '<a class="fancybox" href="' . asset('storage/' . $data->photo). '" title="'.$data->name.'"><img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $data->thumb). '" /></a>';
                } else {
                    $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
                }
                return $html;
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.posts.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $data->id . '" data-title="' . $data->title . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';

                return $html;
            })
            ->addColumn('status', function ($data) {
                $active = '';
                if ($data->active === ACTIVE) {
                    $active = 'checked';
                }
                $html = '<input type="checkbox" data-title="' . $data->title . '" data-id="' . $data->id . '" title="social' . $data->active . '" class="js-switch" value="' . $data->active . '" ' . $active . ' ./>';
                return $html;
            });
        return $dataTable;
    }

    public function dataTable($request)
    {
        $data = $this->getObjDataTable($request)
            ->rawColumns(['status', 'action', 'photo'])
            ->toJson();

        return $data;
    }

    public function getData($id)
    {
        $data = Post::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Post::find($id);

        } else {
            $model = new Post;
            $model->category_id = POST_CATEGORY_TIN_TUC;
        }

        $model->title = $data['title'];
        $model->slug = str_slug($data['title']);
        $model->active = $data['active'];
        if (isset($data['content'])) {
            $model->content = $data['content'];
        }

        if (isset($data['description'])) {
            $model->description = $data['description'];
        }

        if(isset($data['photo'])) {

            if ($model->photo) {
                Storage::delete($model->photo);
            }
            $upload = new Photo($data['photo']);
            $model->photo = $upload->uploadTo('posts');
            $model->thumb = $upload->resizeTo(300);
        }

        $model->save();
        return $model;
    }

    public function delete($ids)
    {
        foreach ($ids as $id) {
            $model = Post::find($id);
            Storage::delete($model->photo);
            Storage::delete($model->thumb);

        }
        Post::destroy($ids);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Post::find($id);
        $model->active = $status;
        return $model->save();
    }

    public function getPosts()
    {
        $data = Post::get();
        return $data;
    }

    public function getList()
    {
        $data = Post::select('id', 'title', 'photo', 'thumb', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $data;
    }
}