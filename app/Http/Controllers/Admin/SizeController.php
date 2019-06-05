<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\SizeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SizeController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách size', route('admin.size.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SizeRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách size';

        return view('admin.size.index', $this->_data);
    }

    public function view(SizeRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm size';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin size';
            $this->_data['data'] = $model->getData($id);
        }
        $this->_data['status'] = ['Chưa kích hoạt', 'Đã kích hoạt'];
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.size.view', $this->_data);
    }

    public function store(SizeRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'name' => 'required|max:100|unique:sizes',
            'status' => 'required'
        ];
        $message = 'Đã tạo size mới thành công.';

        if ($id) {
            $message = 'Size '.$input['name'].' đã được cập nhật.';
            $rules['name'] = 'required|max:100|unique:sizes,name,' . $input['id'];
        }
        
        $validator = Validator::make($input, $rules,[
            'name.unique' => 'Size này đã có rồi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.size.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.size.index')->withSuccess($message);
    }

    public function delete(SizeRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(SizeRepository $user)
    {
        $userID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $user->changeStatus($userID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
