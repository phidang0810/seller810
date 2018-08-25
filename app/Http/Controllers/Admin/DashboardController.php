<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class DashboardController extends AdminController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CartRepository $cart, UserRepository $user, ProductRepository $product)
    {
        $this->_data['title'] = 'Quản trị';
        $this->_data['cart_new'] = $cart->getTotalCart(CART_NEW);
        $this->_data['cart_processing'] = $cart->getTotalCart(CART_NEW);
        $this->_data['cart_transporting'] = $cart->getTotalCart(CART_TRANSPORTING);
        $this->_data['user'] = $user->getTotalUser();
        $this->_data['product_out_of_stock'] = $product->getTotalProductUnAvailable();
        $this->_data['product_need_import'] = $product->getTotalProductNeedImport();
        return view($this->_view . 'dashboard.index', $this->_data);
    }
}
