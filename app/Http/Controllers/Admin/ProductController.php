<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Admins', route('admin.products.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductRepository $product){
        if ($this->_request->ajax()){
            return $product->dataTable($this->_request);
        }

        $this->_data['title'] = 'Sản phẩm';

        return view('admin.products.index', $this->_data);
    }

    public function view(ProductRepository $product)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới Sản phẩm';
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa Sản phẩm';
            $this->_data['data'] = $product->getProduct($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.products.view', $this->_data);
    }

    /**
     * @param ProductRepository $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRepository $product)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50',
            'active' => 'required'
        ];
        $message = 'Sản phẩm đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:products,name,' . $input['id'];
            $message = 'Sản phẩm đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->back()->withSuccess($message);
        }

        return redirect()->route('admin.products.index')->withSuccess($message);
    }

    public function delete(ProductRepository $product)
    {
        $ids = $this->_request->get('ids');
        $result = $product->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(ProductRepository $product)
    {
        $productID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $product->changeStatus($productID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
