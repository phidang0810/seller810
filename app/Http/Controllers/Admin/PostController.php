<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\GroupPostRepository;
use App\Repositories\RoleRepository;
use App\Repositories\PostRepository;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;

class PostController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách bài viết', route('admin.posts.index'));
    }

    public function index(PostRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách baì viết';
        return view('admin.posts.index', $this->_data);
    }

    public function view(PostRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm bài viết';
        if ($id) {
            $this->_data['title'] = 'Sửa bài viết';
            $this->_data['data'] = $model->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.posts.view', $this->_data);
    }

    public function store(PostRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'title' => 'required|string|max:100',
            'active' => 'required'
        ];
        $message = 'Bài viết '.$input['title'].' đã được tạo.';

        if ($id) {
            $message = 'Bài viết '.$input['title'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules, [
            'title.required' => 'Vui lòng nhập tiêu đề.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.posts.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.posts.index')->withSuccess($message);
    }

    public function delete(PostRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(PostRepository $model)
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
