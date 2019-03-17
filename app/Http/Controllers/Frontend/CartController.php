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

}
