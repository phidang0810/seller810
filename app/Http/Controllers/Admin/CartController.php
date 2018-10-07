<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\PlatformRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Response;

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
    public function index(CartRepository $cart, PlatformRepository $platform){
        if ($this->_request->ajax()){
            return $cart->dataTable($this->_request);
        }

        $this->_data['title'] = 'Đơn hàng';
        $this->_data['platforms'] = $platform->getList();

        return view('admin.carts.index', $this->_data);
    }

    public function view(CartRepository $cart, ProductRepository $product, CustomerRepository $customer, PartnerRepository $partner)
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

            if (isset($this->_request['customer_phone'])) {
                return $customer->getCustomer($this->_request);
            }

            if (isset($this->_request['partner_id'])) {
                return $partner->getPartnerDiscountAmount($this->_request);
            }
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới đơn hàng';
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
            
            // Get all platforms
            $this->_data['platform_options'] = $cart->getPlatformOptions($this->_data['data']->platform_id);

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

            // Get all platforms
            $this->_data['platform_options'] = $cart->getPlatformOptions();
        }

        // Get all products
        $this->_data['product_options'] = $product->getProductOptions();


        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.carts.view', $this->_data);
    }

    /**
     * @param CartRepository $cart
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CartRepository $cart)
    {
        if ($this->_request->ajax()) {
            $input = $this->_request->all();
            $id = $input['id'] ?? null;

            $data = $cart->createOrUpdate($input, $id);
            $result = $cart->getCartDetail($data->code);

            $return = [
                'data' => $result,
                'id' => $data->id,
                'message'   =>  'Save cart data successfull',
            ];

            return Response::json($return);
        }
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
        // dd($data);
        if($input['action'] === 'save') {
            return redirect()->route('admin.carts.view')->withSuccess($message);
        }

        if($input['action'] === 'save_print') {
            // return redirect()->route('admin.carts.view', ['id' => $data->id])->withSuccess($message);
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
    public function getCartDetail(CartRepository $cart, PlatformRepository $platform){
        $cartCode = $this->_request->get('cart_code');
        $result = $cart->getCartDetail($cartCode);
        $cart_status = make_cart_status_options($result['cart']->status);
        $payment_status = make_payment_status_options($result['cart']->payment_status);
        $platforms = $platform->getList();
        $view = view("admin._partials._cart_details", compact(['result', 'cart_status', 'payment_status', 'platforms']))->render();
        
        return response()->json([
            'success' => true,
            'result' => $result,
            'html' => $view,
        ]);
    }

    /**
     * Update cart status the application dashboard.
     * @param CartRepository $cart
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(CartRepository $cart){
        $result = [];
        $result['success'] = true;
        $result['data'] = $cart->updateStatus($this->_request);
        $result['message'] = 'Tình trạng đơn hàng đã được cập nhật.';

        if ($result['data'] == false) {
            $result['success'] = false;
            $result['message'] = 'Khách hàng còn nợ không thể chuyển sang hoàn tất';
        }

        return response()->json($result);
    }

    public function getProductAjax(ProductRepository $product){
        $data = $product->getProductsV2($this->_request);
        $message = 'Không có sản phẩm';
        if (count($data)) {
            $message = 'Sản phẩm được lấy thành công.';
        }
        return response()->json($data);
    }

    public function getPhoneAjax(CustomerRepository $customer){
        $data = $customer->getCustomersV2($this->_request);
        $message = 'Không có số điện thoại nào';
        if (count($data)) {
            $message = 'Số điện thoại được lấy thành công.';
        }
        return response()->json($data);
    }
}
