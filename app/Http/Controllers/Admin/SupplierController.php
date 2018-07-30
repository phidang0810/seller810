<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách nhà cung cấp', route('admin.suppliers.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SupplierRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách nhà cung cấp';

        return view('admin.suppliers.index', $this->_data);
    }

    public function view(SupplierRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm nhà cung cấp';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin nhà cung cấp';
            $this->_data['data'] = $model->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.suppliers.view', $this->_data);
    }

    public function store(SupplierRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'email' => 'required|email|string|max:100',
            'name' => 'required|string|max:50||unique:suppliers',
            'code' => 'required|max:20|unique:suppliers',
            'tax_code' => 'required|max:30|unique:suppliers',
            'active' => 'required'
        ];
        $message = 'Nhà cung cấp '.$input['name'].' đã được tạo.';

        if ($id) {
            $rules['code'] = 'required|max:20|unique:suppliers,code,' . $input['id'];
            $rules['tax_code'] = 'required|max:30|unique:suppliers,tax_code,' . $input['id'];
            $rules['name'] = 'required|max:50|unique:suppliers,name,' . $input['id'];
            $message = 'Nhà cung cấp '.$input['name'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules, [
            'code.unique' => 'Mã nhà cung cấp này đã được sử dụng.',
            'tax_code.unique' => 'Mã số thuế này đã được sử dụng.',
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
            return redirect()->route('admin.suppliers.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.suppliers.index')->withSuccess($message);
    }

    public function delete(SupplierRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(SupplierRepository $model)
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
