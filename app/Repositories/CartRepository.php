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
			if (trim($request->get('code')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('carts.code', 'like', '%' . $request->get('code') . '%');
				});
			}

			if (trim($request->get('customer_name')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('customers.name', 'like', '%' . $request->get('customer_name') . '%');
				});
			}

			if (trim($request->get('customer_phone')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('customers.phone', 'like', '%' . $request->get('customer_phone') . '%');
				});
			}

			if (trim($request->get('supplier_name')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('suppliers.name', 'like', '%' . $request->get('supplier_name') . '%');
				});
			}

			if (trim($request->get('start_date')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('carts.created_at', '>=', date_create($request->get('start_date')) );
				});
			}

			if (trim($request->get('end_date')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('carts.created_at', '<=', date_create($request->get('end_date')));
				});
			}
			
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

	public function getCartDetail($cartCode){
		$cart = Cart::select(['carts.id', 'carts.city_id', 'carts.partner_id', 'carts.customer_id', 'carts.code', 'carts.quantity', 'carts.status', 'carts.active', 'carts.created_at', 'carts.transport_id as transport_id', 'customers.name as customer_name', 'customers.phone as customer_phone', 'customers.email as customer_email', 'customers.address as customer_address', 'cart_detail.product_id', 'suppliers.name as supplier_name', 'transports.name as transport_name'])
		->join('customers', 'customers.id', '=', 'carts.customer_id')
		->join('cart_detail', 'cart_detail.cart_id', '=', 'carts.id')
		->join('products', 'products.id', '=', 'cart_detail.product_id')
		->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
		->join('transports', 'transports.id', '=', 'carts.transport_id')
		->where('carts.code', '=', $cartCode)
		->first();

		$cartDetails = Cart::select(['carts.id', 'cart_detail.quantity as quantity', 'cart_detail.price as price', 'carts.total_price as total_price', 'carts.shipping_fee as shipping_fee', 'products.barcode as barcode', 'products.code as product_code', ])
		->join('cart_detail', 'cart_detail.cart_id', '=', 'carts.id')
		->join('products', 'products.id', '=', 'cart_detail.product_id')
		->where('carts.code', '=', $cartCode)

		->get();

		$cartResult = array(
			"cart" => $cart,
			"cart_detail" => $cartDetails
		);

		return $cartResult;
	}

}