<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\PayslipRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayslipController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách phiếu chi', route('admin.payslips.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PayslipRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách phiếu chi';

        return view('admin.payslips.index', $this->_data);
    }

    public function view(PayslipRepository $model)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Thêm phiếu chi';
        if ($id) {
            $this->_data['title'] = 'Sửa thông tin phiếu chi';
            $this->_data['data'] = $model->getData($id);
        }
        $this->_data['status'] = $model->getStatus();

        $this->_data['groups'] = $model->getGroups();
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.payslips.view', $this->_data);
    }

    public function store(PayslipRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'status' => 'required',
            'group' => 'required'
        ];
        $message = 'Phiếu chi đã được tạo.';

        if ($id) {
            $message = 'Phiếu chi '.$input['code'].' đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.payslips.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.payslips.index')->withSuccess($message);
    }

    public function delete(PayslipRepository $model)
    {
        $ids = $this->_request->get('ids');
        $result = $model->delete($ids);

        return response()->json($result);
    }
}
