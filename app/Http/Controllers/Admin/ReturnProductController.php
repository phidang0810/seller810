<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ReturnProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReturnProductController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách trả hàng', route('admin.return_products.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ReturnProductRepository $returnProduct){
        if ($this->_request->ajax()){
            return $returnProduct->dataTable($this->_request);
        }

        $this->_data['title'] = 'Trả hàng';

        return view('admin.return_products.index', $this->_data);
    }

    public function view(ReturnProductRepository $returnProduct, ProductRepository $product, WarehouseRepository $warehouse)
    {
        if ($this->_request->ajax()){
            if (isset($this->_request['product_id'])) {
                if (isset($this->_request['color_id'])) {
                    if (isset($this->_request['size_id'])) {
                        if (isset($this->_request['warehouse_id'])) {
                            if ($this->_request['get_data'] == true) {
                                return $product->getProductDatas($this->_request);
                            }
                            return $product->getProductDetailquantity($this->_request);
                        }
                        return $product->getProductDetailWarehouseOptions($this->_request);
                    }
                    return $product->getProductDetailSizeOptions($this->_request);
                }
                return $product->getProductDetailColorOptions($this->_request);
            }
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới trả hàng';

        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa trả hàng';
            $this->_data['data'] = $returnProduct->get($id);
            $this->_data['return_details'] = json_encode($returnProduct->getDetails($id));
        }
        $this->_data['return_staff_options'] = $returnProduct->getReturnStaffOptions($id);
        $this->_data['product_options'] = $product->getProductOptions();
        $this->_data['status_options'] = $returnProduct->getStatusOptions($id);

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.return_products.view', $this->_data);
    }

    /**
     * @param ReturnProductRepository $returnProduct
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReturnProductRepository $returnProduct)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'return_staff_id' => 'required',
            'return_date' => 'required'
        ];
        $message = 'Đơn trả hàng đã được tạo.';

        if ($id) {
            // $rules['name'] = 'required|string|max:50|unique:carts,name,' . $input['id'];
            $message = 'Đơn trả hàng đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {

            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $data = $returnProduct->createOrUpdate($input, $id);
        // dd($data);
        if($input['action'] === 'save') {
            return redirect()->route('admin.return_products.index')->withSuccess($message);
        }

        if($input['action'] === 'save_print') {
            // return redirect()->route('admin.carts.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.return_products.index')->withSuccess($message);
    }

    public function delete(ReturnProductRepository $returnProduct)
    {
        $ids = $this->_request->get('ids');
        $result = $returnProduct->delete($ids);

        return response()->json($result);
    }

    public function returned(ReturnProductRepository $returnProduct)
    {
        $id = $this->_request->get('id');
        $result = $returnProduct->returned($id);

        return response()->json($result);
    }
}
