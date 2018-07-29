<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CategoryController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Admins', route('admin.categories.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryRepository $category){
        if ($this->_request->ajax()){
            return $category->dataTable($this->_request);
        }

        $this->_data['title'] = 'Danh mục sản phẩm';

        return view('admin.categories.index', $this->_data);
    }

    public function view(CategoryRepository $category)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới danh mục sản phẩm';
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa danh mục sản phẩm';
            $this->_data['data'] = $category->getCategory($id);
        }

        $parent_id = ($id) ? $category->getCategory($id)->parent_id : 0;
        $this->_data['categoriesTree'] = option_menu($category->getCategoriesTree(), "", $parent_id);

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.categories.view', $this->_data);
    }

    /**
     * @param CategoryRepository $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRepository $category)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50|unique:categories,name',
            'description' => 'required',
            'active' => 'required'
        ];
        $message = 'Danh mục sản phẩm đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:categories,name,' . $input['id'];
            $message = 'Danh mục sản phẩm đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $category->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.categories.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.categories.index')->withSuccess($message);
    }

    public function delete(CategoryRepository $category)
    {
        $ids = $this->_request->get('ids');
        $result = $category->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(CategoryRepository $category)
    {
        $categoryID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $category->changeStatus($categoryID, $status);

        return response()->json([
            'success' => true
        ]);
    }
}
