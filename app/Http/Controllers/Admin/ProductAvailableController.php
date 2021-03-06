<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductAvailableController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách sản phẩm', route('admin.product_available.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductRepository $product, CategoryRepository $category){
        if ($this->_request->ajax()){
            return $product->dataTable($this->_request);
        }

        $id = ($this->_request->get('id')) ? $this->_request->get('id') : 0 ;
        $this->_data['title'] = 'Sản phẩm';
        $this->_data['categoryOptions'] = $category->getCategoryOptions($id);

        return view('admin.products.shop.index', $this->_data);
    }

    public function view(ProductRepository $product, CategoryRepository $category)
    {
        if ($this->_request->ajax()){
            return $product->validateAjax($this->_request);
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới Sản phẩm';
        $categories = array();
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa Sản phẩm';
            $this->_data['data'] = $product->getProduct($id);
            $categories = $product->idCategories($id);
            $this->_data['categories'] = array_to_string($categories);

            $this->_data['details'] = json_encode($product->getDetails($id));
            $this->_data['photos'] = json_encode($product->getPhotos($id));
        }

        $this->_data['categoriesTree'] = make_list_hierarchy($category->getCategoriesTree(), $categories);
        $this->_data['size_options'] = $product->getSizeOptions($id);
        $this->_data['color_options'] = $product->getColorOptions($id);
        $this->_data['brand_options'] = $product->getBrandOptions($id);
        $this->_data['colors'] = $product->getColors();
        $this->_data['sizes'] = $product->getSizes();

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.products.shop.view', $this->_data);
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
            'name' => 'required|string|max:50|unique:products,name',
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
        $data = $product->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.product_available.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.product_available.index')->withSuccess($message);
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
