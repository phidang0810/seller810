<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\BaseController;
use App\Repositories\Frontend\CartRepository;

class CartController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(CartRepository $cart) {
        if ($this->_request->ajax()){
            if (isset($this->_request['id'])) {
                return response()->json($cart->updateCartDetail($this->_request));
            }
            return response()->json($cart->getCustomerInCartCart($this->_request));
        }

        $this->_data['title'] = 'Giỏ hàng';
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('frontend.carts.index', $this->_data);
    }

    public function addDetail(Request $request, CartRepository $cart) {
        return response()->json($cart->addDetail($request));
    }
    
    public function getNumberDetails (Request $request, CartRepository $cart) {
        return response()->json($cart->getNumberDetails($request));
    }

    public function payment(CartRepository $cart) {
        if ($this->_request->ajax()){
            if (isset($this->_request['type'])) {
                if ($this->_request['type'] == "get_customer") {
                    return response()->json($cart->getCustomerInfo($this->_request));
                }
            }
            return response()->json($cart->getCustomerInCartCart($this->_request));
        }

        $this->_data['title'] = 'Thanh toán';
        $this->_pushBreadCrumbs($this->_data['title']);

        $this->_data['city_options'] = $cart->getCityOptions();

        return view('frontend.payments.index', $this->_data);
    }

    public function storePayment(CartRepository $cart) {
        return response()->json($cart->paymentCart($this->_request));
    }

    public function paymentBank($cart_code, CartRepository $cart) {
        $this->_data['title'] = 'Thanh toán';
        $this->_pushBreadCrumbs($this->_data['title']);

        $this->_data['cart_code'] = $cart_code;
        $this->_data['payment_method'] = $cart->getPaymentMethodByCartCode($cart_code);

        return view('frontend.payments.complete', $this->_data);
    }

}
