<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PartnerRepository;
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

    public function view(CartRepository $cart, ProductRepository $product, CustomerRepository $customer, PartnerRepository $partner)
    {
        if ($this->_request->ajax()){
            if (isset($this->_request['product_id'])) {
                if (isset($this->_request['color_id'])) {
                    if (isset($this->_request['size_id'])) {
                        if ($this->_request['get_data'] == true) {
                            return $product->getProductDatas($this->_request);
                        }
                        return $product->getProductDetailquantity($this->_request);
                    }
                    return $product->getProductDetailSizeOptions($this->_request);
                }
                return $product->getProductDetailColorOptions($this->_request);
            }

            if (isset($this->_request['customer_phone'])) {
                return $customer->getCustomer($this->_request);
            }

            if (isset($this->_request['partner_id'])) {
                return $partner->getPartnerDiscountAmount($this->_request);
            }
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới đơn hàng';
        $this->_data['hasSidebar'] = false;
        $this->_data['hasTitle'] = false;
        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa đơn hàng';
            $this->_data['data'] = $cart->getCart($id);

            $this->_data['cart_details'] = json_encode($cart->getDetails($id));

            // Get all customers
            $this->_data['customer_options'] = $customer->getPhoneOptions($this->_data['data']->customer->id);

            // Get all transports
            $this->_data['transport_options'] = $cart->getTransportOptions($this->_data['data']->transport_id);

            // Get all cities
            $this->_data['city_options'] = $cart->getCityOptions($this->_data['data']->city_id);

            // Get all partners
            $this->_data['partner_options'] = $partner->getPartnerOptions($this->_data['data']->partner_id);

            // Get all cart status
            $this->_data['status_options'] = make_cart_status_options($this->_data['data']->status);

            // Get all payment status
            $this->_data['payment_options'] = make_payment_status_options($this->_data['data']->payment_status);
        }else{
            // Get all customers
            $this->_data['customer_options'] = $customer->getPhoneOptions();

            // Get all transports
            $this->_data['transport_options'] = $cart->getTransportOptions();

            // Get all cities
            $this->_data['city_options'] = $cart->getCityOptions();

            // Get all partners
            $this->_data['partner_options'] = $partner->getPartnerOptions();

            // Get all cart status
            $this->_data['status_options'] = make_cart_status_options();

            // Get all payment status
            $this->_data['payment_options'] = make_payment_status_options();
        }

        // Get all products
        $this->_data['product_options'] = $product->getProductOptions();

        // Get all platforms
        $this->_data['platform_options'] = '';

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
            // 'name' => 'required|string|max:50|unique:carts,name',
            // 'active' => 'required'
        ];
        $message = 'Đơn hàng đã được tạo.';

        if ($id) {
            // $rules['name'] = 'required|string|max:50|unique:carts,name,' . $input['id'];
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
            return redirect()->route('admin.carts.view')->withSuccess($message);
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

    /**
     * Update cart status the application dashboard.
     * @param CartRepository $cart
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(CartRepository $cart){

        $data = $cart->updateStatus($this->_request);
        $message = 'Tình trạng đơn hàng đã được cập nhật.';

        return response()->json([
            'success' => true,
            'result' => $data,
            'message' => $message,
        ]);
    }
}
