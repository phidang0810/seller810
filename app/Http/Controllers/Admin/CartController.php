<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CartController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách đơn hàng', route('admin.carts.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CartRepository $cart){
        if ($this->_request->ajax()){
            return $cart->dataTable($this->_request);
        }

        $this->_data['title'] = 'Đơn hàng';

        return view('admin.carts.index', $this->_data);
    }

    public function view(CartRepository $cart)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới đơn hàng';
        $this->_data['hasSidebar'] = false;
        $this->_data['hasTitle'] = false;
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa đơn hàng';
            $this->_data['data'] = $cart->getCart($id);
        }

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.carts.view', $this->_data);
    }

    /**
     * @param CartRepository $cart
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CartRepository $cart)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'name' => 'required|string|max:50|unique:carts,name',
            'active' => 'required'
        ];
        $message = 'Đơn hàng đã được tạo.';

        if ($id) {
            $rules['name'] = 'required|string|max:50|unique:carts,name,' . $input['id'];
            $message = 'Đơn hàng đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $cart->createOrUpdate($input, $id);

        if($input['action'] === 'save') {
            return redirect()->route('admin.carts.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.carts.index')->withSuccess($message);
    }

    public function delete(CartRepository $cart)
    {
        $ids = $this->_request->get('ids');
        $result = $cart->delete($ids);

        return response()->json($result);
    }

    public function changeStatus(CartRepository $cart)
    {
        $cartID = $this->_request->get('id');
        $status = $this->_request->get('status');
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);
        $cart->changeStatus($cartID, $status);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Show detail the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCartDetail(CartRepository $cart){
        $cartCode = $this->_request->get('cart_code');
        $result = $cart->getCartDetail($cartCode);
        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }
}
