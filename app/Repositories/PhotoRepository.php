<?php
namespace App\Repositories;

use App\Libraries\Photo as Upload;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PhotoRepository
{
    public function getObjDataTable($request)
    {
        $data = Photo::selectRaw('photos.id, photos.type, photo, active, thumb, created_at');
        $dataTable = DataTables::eloquent($data)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('photos.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('photos.name', 'like', '%' . $request->get('keyword') . '%');
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
                $html = '<a href="' . route('admin.photos.view', ['id' => $data->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $data->id . '" data-title="' . $data->title . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';

                return $html;
            })
            ->addColumn('type', function ($data) {
                $html= '';
                if ($data->type === PHOTO_AD) {
                    $html = '<label class="label label-default">Quảng Cáo</label>';
                }
                if ($data->type === PHOTO_BANNER) {
                    $html = '<label class="label label-primary">Slides</label>';
                }
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
            ->rawColumns(['status', 'action', 'photo', 'type'])
            ->toJson();

        return $data;
    }

    public function getData($id)
    {
        $data = Photo::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Photo::find($id);

        } else {
            $model = new Photo;
        }
        $model->active = $data['active'];
        $model->type = $data['type'];

        if(isset($data['photo'])) {

            if ($model->photo) {
                Storage::delete($model->photo);
            }
            $upload = new Upload($data['photo']);
            $model->photo = $upload->uploadTo('photos');
            $model->thumb = $upload->resizeTo(300);
        }

        $model->save();
        return $model;
    }

    public function delete($ids)
    {
        foreach ($ids as $id) {
            $model = Photo::find($id);
            Storage::delete($model->thumb);
            Storage::delete($model->photo);
        }
        Photo::destroy($ids);

        return [
            'success' => true
        ];
    }

    public function changeStatus($id, $status)
    {
        $model = Photo::find($id);
        $model->active = $status;
        return $model->save();
    }

    public function getPhotos()
    {
        $data = Photo::get();
        return $data;
    }
}