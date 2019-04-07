<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

class PhotoController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách ảnh', route('admin.photos.index'));
    }

    public function index(PhotoRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách ảnh';
        return view('admin.photos.index', $this->_data);
    }

    public function view(PhotoRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm ảnh';
        if ($id) {
            $this->_data['title'] = 'Sửa ảnh';
            $this->_data['data'] = $model->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.photos.view', $this->_data);
    }

    public function store(PhotoRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'active' => 'required'
        ];
        $message = 'Đã tạo thành công.';

        if ($id) {
            $message = 'Đã cập nhật thành công';
        }
        
        $validator = Validator::make($input, $rules, [
            'photo.required' => 'Vui lòng nhập tiêu đề.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.photos.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.photos.index')->withSuccess($message);
    }

    public function delete(PhotoRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(PhotoRepository $model)
    {
        $id = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $model->changeStatus($id, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
