<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class WarehouseController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách kho hàng', route('admin.warehouses.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(WarehouseRepository $warehouse){
        if ($this->_request->ajax()){
            return $warehouse->dataTable($this->_request);
        }

        $this->_data['title'] = 'Kho hàng';

        return view('admin.warehouses.index', $this->_data);
    }

    public function view(WarehouseRepository $warehouse)
    {        
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới kho hàng';
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa kho hàng';
            $this->_data['data'] = $warehouse->getWarehouse($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.warehouses.view', $this->_data);
    }

    /**
     * @param WarehouseRepository $warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WarehouseRepository $warehouse)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50|unique:warehouses,name',
            'active' => 'required'
        ];
        $message = 'Kho hàng đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:warehouses,name,' . $input['id'];
            $message = 'Kho hàng đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules, [
            'name.unique' => 'Tên kho hàng '.$input['name'].' đã được sử dụng!'
        ]);

        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $warehouse->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.warehouses.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.warehouses.index')->withSuccess($message);
    }

    public function delete(WarehouseRepository $warehouse)
    {
        $ids = $this->_request->get('ids');
        $result = $warehouse->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(WarehouseRepository $warehouse)
    {
        $warehouseID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $warehouse->changeStatus($warehouseID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
