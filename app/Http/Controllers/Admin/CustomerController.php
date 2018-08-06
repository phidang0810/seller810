<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\GroupCustomerRepository;
use App\Repositories\RoleRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách khách hàng', route('admin.customers.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CustomerRepository $model, GroupCustomerRepository $group)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách khách hàng';
        $this->_data['groups'] = $group->getDataList();
        return view('admin.customers.index', $this->_data);
    }

    public function view(CustomerRepository $model, GroupCustomerRepository $group)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm khách hàng';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin khách hàng';
            $this->_data['data'] = $model->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);

        $this->_data['groups'] = $group->getDataList();
        return view('admin.customers.view', $this->_data);
    }

    public function store(CustomerRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'email' => 'required|email|string|max:100|unique:customers',
            'name' => 'required|string|max:100',
            'active' => 'required'
        ];
        $message = 'Khách hàng '.$input['name'].' đã được tạo.';

        if ($id) {
            $rules['email'] = 'required|email|max:100|unique:customers,email,' . $input['id'];
            $message = 'Khách hàng '.$input['name'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules, [
            'email.unique' => 'Email này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập email.',
            'name.required' => 'Vui lòng nhập tên.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.customers.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.customers.index')->withSuccess($message);
    }

    public function delete(CustomerRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(CustomerRepository $model)
    {
        $id = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $model->changeStatus($id, $status);

        return response()->json([
            'success' => true
        ]);
    }

    public function history($id, CustomerRepository $model)
    {
        $this->_data['title'] = 'Lịch sử mua hàng';
        $customer = $model->getData($id);
        if (!$customer) {
            return redirect()->route('error', [404]);
        }

        if ($this->_request->ajax()) {

            return $model->getHistoryByID($id, $this->_request);

        }
        $this->_data['customer'] = $customer;

        $this->_pushBreadCrumbs($customer->name, route('admin.customers.view', ['id' => $customer->id]));
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.customers.history', $this->_data);
    }
}
