<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\BrandRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class BrandController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh Sách Thương Hiệu', route('admin.brands.index'));
    }

    public function index(BrandRepository $brand){
        if ($this->_request->ajax()){
            return $brand->dataTable($this->_request);
        }
        $this->_data['title'] = 'Danh sách Thương Hiệu';
        return view('admin.brands.index', $this->_data);
    }

    public function view(BrandRepository $brand)
    {        
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới';
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa thông tin';
            $this->_data['data'] = $brand->getData($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.brands.view', $this->_data);
    }

    /**
     * @param BrandRepository $brand
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BrandRepository $brand)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50|unique:brands,name',
            'active' => 'required'
        ];
        $message = 'Tạo thành công.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:brands,name,' . $input['id'];
            $message = 'Cập nhật thành công';
        }

        $validator = Validator::make($input, $rules, [
            'name.unique' => 'Tên thương hiệu '.$input['name'].' đã được sử dụng!'
        ]);

        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $brand->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.brands.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.brands.index')->withSuccess($message);
    }

    public function delete(BrandRepository $brand)
    {
        $id = $this->_request->get('id');
        $result = $brand->delete($id);

        return response()->json($result);
    }

    public function changeStatus(BrandRepository $brand)
    {
        $brandID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $brand->changeStatus($brandID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
