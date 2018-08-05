<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 8/04/2018
 * Time: 10:29 AM
 */

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

Class CartRepository
{

	const CACHE_NAME_CART = 'carts';
	public function dataTable($request)
	{
		$carts = Cart::select(['carts.id', 'carts.city_id', 'carts.partner_id', 'carts.customer_id', 'carts.code', 'carts.quantity', 'carts.status', 'carts.active', 'carts.created_at', 'customers.name as customer_name', 'customers.phone as customer_phone', 'cart_detail.product_id', 'suppliers.name as supplier_name'])
		->join('customers', 'customers.id', '=', 'carts.customer_id')
		->join('cart_detail', 'cart_detail.cart_id', '=', 'carts.id')
		->join('products', 'products.id', '=', 'cart_detail.product_id')
		->join('suppliers', 'suppliers.id', '=', 'products.supplier_id');

		$dataTable = DataTables::eloquent($carts)
		->filter(function ($query) use ($request) {

			
		}, true)
		->addColumn('created_at', function ($cart) {
			$html = date('d/m/Y', strtotime($cart->created_at));
			return $html;
		})
		->addColumn('status', function ($cart) {
			$html = '';
			$html .= '<span class="label label-success">'.$cart->status.'</span>';
			return $html;
		})
		->rawColumns(['created_at', 'status'])
		->toJson();
		return $dataTable;
	}

	public function getProduct($id)
	{
		$data = Cart::find($id);
		return $data;
	}
}