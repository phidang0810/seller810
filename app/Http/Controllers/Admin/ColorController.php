<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ColorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ColorController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Admins', route('admin.colors.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ColorRepository $color){
        if ($this->_request->ajax()){
            return $color->dataTable($this->_request);
        }

        $this->_data['title'] = 'Màu sắc sản phẩm';

        return view('admin.colors.index', $this->_data);
    }

    public function view(ColorRepository $color)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới Màu sắc sản phẩm';
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa Màu sắc sản phẩm';
            $this->_data['data'] = $color->getcolor($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.colors.view', $this->_data);
    }

    /**
     * @param ColorRepository $color
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ColorRepository $color)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50',
            'color'  => 'required',
            'active' => 'required'
        ];
        $message = 'Màu sắc sản phẩm đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:colors,name,' . $input['id'];
            $message = 'Màu sắc sản phẩm đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($input['color'] == 'photo') {
            $rules['photo'] = 'required';
        }

        if ($input['color'] == 'code') {
            $rules['code'] = 'required';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $color->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.colors.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.colors.index')->withSuccess($message);
    }

    public function delete(ColorRepository $color)
    {
        $ids = $this->_request->get('ids');
        $result = $color->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(ColorRepository $color)
    {
        $colorID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $color->changeStatus($colorID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
