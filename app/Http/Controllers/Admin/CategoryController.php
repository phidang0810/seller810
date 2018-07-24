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
    public function index(CategoryRepository $category)
    {
        if ($this->_request->ajax()) {
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
            'name' => 'required|string|max:50',
            'active' => 'required'
        ];
        $message = 'The account has been created.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:name,' . $input['id'];
            $message = 'The account has been updated.';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category->createOrUpdate($input, $id);

        return redirect()->route('admin.categories.index')->withSuccess($message);
    }

    public function delete(CategoryRepository $category)
    {
        $ids = $this->_request->get('ids');
        $result = $category->delete($ids);

        return response()->json($result);
    }
}
