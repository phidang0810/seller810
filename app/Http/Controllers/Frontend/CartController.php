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

    public function addDetail(Request $request, CartRepository $cart) {
        return response()->json($cart->addDetail($request));
    }
    
    public function getNumberDetails (Request $request, CartRepository $cart) {
        return response()->json($cart->getNumberDetails($request));
    }

}
