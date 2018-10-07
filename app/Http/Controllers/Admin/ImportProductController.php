<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ImportProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ImportProductController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách nhập hàng', route('admin.import_products.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ImportProductRepository $importProduct){
        if ($this->_request->ajax()){
            return $importProduct->dataTable($this->_request);
        }

        $this->_data['title'] = 'Nhập hàng';

        return view('admin.import_products.index', $this->_data);
    }

    public function view(ImportProductRepository $importProduct, ProductRepository $product, CategoryRepository $category)
    {
        if ($this->_request->ajax()){
            if (isset($this->_request['product_id'])){
                return $product->retrieveProduct($this->_request);
            }
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới nhập hàng';
        $categories = array();

        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa nhập hàng';
            $this->_data['data'] = $importProduct->get($id);
            $this->_data['details'] = json_encode($importProduct->getDetails($id));
        }

        $this->_data['categoriesTree'] = make_list_hierarchy($category->getCategoriesTree(), $categories);
        $this->_data['size_options'] = $product->getSizeOptions($id);
        $this->_data['color_options'] = $product->getColorOptions($id);
        $this->_data['brand_options'] = $product->getBrandOptions($id);
        $this->_data['colors'] = $product->getColors();
        $this->_data['sizes'] = $product->getSizes();
        $this->_data['import_staff_options'] = $importProduct->getImportStaffOptions($id);
        $this->_data['warehouse_options'] = $importProduct->getWarehouseOptions($id);
        // Get all products
        $this->_data['product_options'] = $product->getProductOptions();

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.import_products.view', $this->_data);
    }

    /**
     * @param ImportProductRepository $importProduct
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ImportProductRepository $importProduct)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $product_option = $input['product_option'];

        $rules = [
            'import_quantity' => 'required',
            'import_staff_id' => 'required',
            'product_id' => 'required',
            'warehouse_id' => 'required',
            'price' => 'required',
        ];

        if ($product_option == 'new') {
            $rules['name'] = 'required|string|max:50|unique:products,name';
        }

        $message = 'Nhập hàng đã được tạo.';

        if ($id) {
            if ($product_option == 'new') {
                $rules['name'] = 'required';
            }
            $message = 'Nhập hàng đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules, [
            // 'name.unique' => 'Tên kho hàng '.$input['name'].' đã được sử dụng!'
        ]);

        if ($validator->fails()) {

            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $data = $importProduct->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.import_products.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.import_products.index')->withSuccess($message);
    }

    public function delete(ImportProductRepository $importProduct)
    {
        $ids = $this->_request->get('ids');
        $result = $importProduct->delete($ids);

        return response()->json($result);
    }
}
