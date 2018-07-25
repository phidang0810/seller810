<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\GroupCustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupCustomerController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách nhóm khách hàng', route('admin.groupCustomer.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GroupCustomerRepository $group)
    {
        if ($this->_request->ajax()) {
            return $group->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách nhóm khách hàng';

        return view('admin.group_customer.index', $this->_data);
    }

    public function view(GroupCustomerRepository $group, RoleRepository $role)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm nhóm khách hàng';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin nhóm khách hàng';
            $this->_data['data'] = $group->getData($id);
        }

        $this->_data['roles'] = $role->all();

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.group_customer.view', $this->_data);
    }

    public function store(GroupCustomerRepository $group)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'name' => 'required|string|max:100|unique:group_customers',
            'active' => 'required'
        ];
        $message = 'Nhóm khách hàng '.$input['name'].' đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|max:100|unique:group_customers,name,' . $input['id'];
            $message = 'Nhóm khách hàng '.$input['name'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules, [
            'name.required' => 'Vui lòng tên nhóm khách hàng.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $group->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->back()->withSuccess($message);
        }
        return redirect()->route('admin.groupCustomer.index')->withSuccess($message);
    }

    public function delete(GroupCustomerRepository $group)
    {
        $ids = $this->_request->get('ids');
        $result = $group->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(GroupCustomerRepository $group)
    {
        $id = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $group->changeStatus($id, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
