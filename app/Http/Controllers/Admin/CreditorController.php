<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CreditorRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditorController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách nợ', route('admin.creditors.index'));
    }


    public function index(CreditorRepository $model)
    {
        if ($this->_request->ajax()) {
            return $model->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh sách nợ';

        return view('admin.creditors.index', $this->_data);
    }

    public function view(CreditorRepository $model, SupplierRepository $supplier)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Ghi nợ';
        if ($id) {
            $this->_data['title'] = 'Trả nợ';
            $this->_data['data'] = $model->getData($id);
        }
        $this->_data['suppliers'] = $supplier->getList();
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.creditors.view', $this->_data);
    }

    public function store(CreditorRepository $model)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'full_name' => 'required|string|max:50',
            'phone' => 'required|string|max:50',
            'total' => 'required',
            'paid' => 'required'
        ];
        $message = 'Ghi nợ thành công.';

        if ($id) {
            $message = 'Thông tin đã được cập nhật.';
        }
        
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data = $model->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.creditors.view', ['id' => $data->id])->withSuccess($message);
        }
        return redirect()->route('admin.creditors.index')->withSuccess($message);
    }
}
