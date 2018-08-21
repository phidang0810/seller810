<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách phòng ban', route('admin.roles.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RoleRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách phòng ban';

        return view('admin.roles.index', $this->_data);
    }

    public function view(RoleRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm phòng ban';
        $this->_data['myPermissions'] = [];
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin phòng ban';
            $this->_data['data'] = $model->getRole($id);
            $this->_data['myPermissions'] = $model->getPermissionIDByRole($id);
        }
        $this->_data['permissions'] = $model->getPermissions();

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.roles.view', $this->_data);
    }

    public function store(RoleRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'name' => 'required|string|max:100|unique:roles',
            'active' => 'required'
        ];
        $message = 'Phòng ban '.$input['name'].' đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|max:100|unique:roles,name,' . $input['id'];
            $message = 'Thông tin phòng ban '.$input['name'].' đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules, [
            'name.required' => 'Vui lòng tên phòng ban.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.roles.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.roles.index')->withSuccess($message);
    }

    public function delete(RoleRepository $model)
    {
        $id = $this->_request->get('id');
        $result = $model->delete($id);

        return response()->json($result);
    }

    public function changeStatus(RoleRepository $model)
    {
        $modelID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $model->changeStatus($modelID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
