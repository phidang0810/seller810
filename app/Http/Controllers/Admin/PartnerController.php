<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\PartnerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách cộng tác viên', route('admin.partners.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PartnerRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách cộng tác viên';

        return view('admin.partners.index', $this->_data);
    }

    public function view(PartnerRepository $model, RoleRepository $role)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm cộng tác viên';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin cộng tác viên';
            $this->_data['data'] = $model->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.partners.view', $this->_data);
    }

    public function store(PartnerRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'email' => 'required|email|string|max:100|unique:partners',
            'name' => 'required|string|max:100',
            'code' => 'required|max:20|unique:partners',
            'active' => 'required'
        ];
        $message = 'Nhóm khách hàng '.$input['name'].' đã được tạo.';

        if ($id) {
            $rules['email'] = 'required|email|max:100|unique:partners,email,' . $input['id'];
            $rules['code'] = 'required|max:20|unique:partners,code,' . $input['id'];
            $message = 'Cộng tác viên '.$input['name'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules, [
            'email.unique' => 'Email này đã được sử dụng.',
            'code.unique' => 'Mã nhân viên này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'code.required' => 'Vui lòng nhập mã.',
            'name.required' => 'Vui lòng nhập tên.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.partners.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.partners.index')->withSuccess($message);
    }

    public function delete(PartnerRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(PartnerRepository $model)
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
