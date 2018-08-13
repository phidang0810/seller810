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
use App\Models\ProductDetail;
use App\Models\Transport;
use App\Models\City;
use App\Models\Platform;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

Class CartRepository
{

	const CACHE_NAME_CART = 'carts';
	public function dataTable($request)
	{
		$carts = Cart::select(['carts.id', 'carts.city_id', 'carts.partner_id', 'carts.customer_id', 'carts.code', 'carts.quantity', 'carts.status', 'carts.active', 'carts.created_at', 'customers.name as customer_name', 'customers.phone as customer_phone', 'platforms.name as platform_name'])
		->join('customers', 'customers.id', '=', 'carts.customer_id')
		->leftJoin('platforms', 'platforms.id', '=', 'carts.platform_id');

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

			if (trim($request->get('platform_name')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('carts.platform_id', $request->get('platform_name'));
				});
			}

			if (trim($request->get('start_date')) !== "") {
				$fromDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('start_date').' 00:00:00')->toDateTimeString();

				if (trim($request->get('end_date')) !== "") {

					$toDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('end_date').' 23:59:59')->toDateTimeString();
					$query->whereBetween('carts.created_at', [$fromDate, $toDate]);
				} else {
					$query->whereDate('carts.created_at', '>=', $fromDate);
				}
			}

			if (trim($request->get('status')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('carts.status', 'like', '%' . $request->get('status') . '%');
				});
			}
			
		}, true)
		->addColumn('created_at', function ($cart) {
			$html = date('d/m/Y', strtotime($cart->created_at));
			return $html;
		})
		->addColumn('status', function ($cart) {
			$html = parse_status($cart->status);
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
		$cart = Cart::select(['carts.id', 'carts.city_id', 'carts.partner_id', 'carts.customer_id', 
			'carts.code', 'carts.quantity', 'carts.status', 'carts.active', 'carts.created_at', 
			'carts.transport_id as transport_id', 'carts.total_price', 'carts.shipping_fee', 
			'carts.vat_amount', 'carts.total_discount_amount', 'carts.price', 'carts.needed_paid', 'carts.paid_amount', 
			'customers.name as customer_name', 'customers.phone as customer_phone', 'customers.email as customer_email', 
			'customers.address as customer_address', 'cart_detail.product_id', 'carts.platform_id', 'platforms.name as platform_name', 
			'transports.name as transport_name', 'carts.payment_status'])
		->leftjoin('customers', 'customers.id', '=', 'carts.customer_id')
		->leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'carts.id')
		->leftjoin('products', 'products.id', '=', 'cart_detail.product_id')
		->leftjoin('platforms', 'platforms.id', '=', 'carts.platform_id')
		->leftjoin('transports', 'transports.id', '=', 'carts.transport_id')
		->where('carts.code', '=', $cartCode)
		->first();

		$cartDetails = Cart::select(['carts.id', 'cart_detail.quantity as quantity', 'cart_detail.price as price', 'carts.total_price as total_price', 'carts.shipping_fee as shipping_fee', 'carts.code', 'products.code as product_code', ])
		->leftjoin('cart_detail', 'cart_detail.cart_id', '=', 'carts.id')
		->leftjoin('products', 'products.id', '=', 'cart_detail.product_id')
		->where('carts.code', '=', $cartCode)
		->get();

		$cartResult = array(
			"cart" => $cart,
			"cart_details" => $cartDetails,
		);

		return $cartResult;
	}

	public function updateStatus($request){
		$cartCode = $request->get('cart_code');
		$status = $request->get('status');
		$platform_id = $request->get('platform_id');
		$pay_amount = $request->get('pay_amount');
		$needed_paid = $request->get('needed_paid');
		$model = Cart::where('code','=',$cartCode)->first();
		$model->paid_amount = $model->paid_amount + $pay_amount;
		$model->platform_id = $platform_id;
		$model->needed_paid = $needed_paid;
		// excute payment_status
		if ($model->paid_amount && $model->paid_amount > 0) {
			$model->payment_status = 2;
			if ($model->paid_amount >= $model->price) {
				if ($model->platform_id != 0) {
					$model->payment_status = 3;
				}else{
					$model->payment_status = 4;
				}
				
			}
		}else{
			$model->payment_status = 1;
		}

		$model->save();

		// Excute status
		if ($status == 4) {
			if ($model->payment_status == 3 || $model->payment_status == 4) {
				$model->status = $status;

				$model->save();
			}else{
				return false;
			}
		}
		return $model;
	}

	public function getTransports(){
		$data = Transport::get();
		return $data;
	}

	public function getTransportOptions($id = 0){
		return make_option($this->getTransports(), $id);
	}

	public function getPlatforms(){
		$data = Platform::get();
		return $data;
	}

	public function getPlatformOptions($id = 0){
		return make_option($this->getPlatforms(), $id);
	}

	public function getCities(){
		$data = City::get();
		return $data;
	}

	public function getCityOptions($id = 0){
		return make_option($this->getCities(), $id);
	}

	public function createOrUpdate($data, $id = null){
		if ($id) {
			$model = Cart::find($id);
			$model->code = general_code('DH', $id, 6);
		} else {
			$model = new Cart;
		}

		$model->city_id = $data['customer_city'];
		$model->partner_id = $data['partner']; 

		// Excute customer
		if ($customer = Customer::find($data['customer_phone'])) {
			$customer->city_id = $data['customer_city'];
			$customer->name = $data['customer_name'];
			$customer->email = $data['customer_email'];
			// $customer->phone = $data['customer_phone'];
			$customer->address = $data['customer_address'];

			$customer->save();
		}else{
			$customer = new Customer;

			$customer->city_id = $data['customer_city'];
			$customer->name = $data['customer_name'];
			$customer->email = $data['customer_email'];
			$customer->phone = $data['customer_phone'];
			$customer->address = $data['customer_address'];

			$customer->save();
		}
		
		$model->customer_id = $customer->id;


		$model->transport_id = $data['transporting_service'];
		$model->quantity = $data['quantity'];
		$model->partner_discount_amount = $data['partner_discount_amount'];
		$model->customer_discount_amount = $data['customer_discount_amount'];
		$model->total_discount_amount = $data['total_discount_amount'];
		$model->total_price = preg_replace('/[^0-9]/', '', $data['total_price']);
		$model->price = preg_replace('/[^0-9]/', '', $data['price']);
		$model->shipping_fee = $data['shipping_fee'];
		$model->vat_percent = 10;
		$model->vat_amount = $data['vat_amount'];
		$model->prepaid_amount = $data['prepaid_amount'];
		$model->paid_amount = $data['paid_amount'];
		$model->needed_paid = $data['needed_paid'];
		$model->descritption = $data['descritption'];
		$model->platform_id = $data['platform_id'];
		// excute payment_status
		if ($data['paid_amount'] && $data['paid_amount'] > 0) {
			$model->payment_status = 2;
			if ($data['paid_amount'] >= $model->price) {
				if ($model->platform_id != 0) {
					$model->payment_status = 3;
				}else{
					$model->payment_status = 4;
				}
				
			}
		}else{
			$model->payment_status = 1;
		}

		// Excute status
		if ($data['status'] == 3 || $data['status'] == 4) {
			if ($model->payment_status == 4) {
				$model->status = $data['status'];
			}else{
				$model->status = 3;
			}
		}
		
		// $model->active = $data['active'];
		// $model->order = $data['order'];

		$model->save();

		if (is_null($id)) {
			$model->code = general_code('DH', $model->id, 6);
			$model->save();
		}

		// Excute cart details
		if (isset($data['cart_details'])) {
			$this->addDetails($model->id, $data['cart_details']);
		}

		// Excute if cart status is COMPLETED then copy cart & cart detail to payment & payment detail
		if ($model->status = COMPLETED) {
			$model->details;
			$payment_repo = new PaymentRepository();
			$payment = $payment_repo->createOrUpdate($model);
		}

		return $model;
	}

	public function addDetails($id, $details){
		$details = json_decode($details);
		$model = Cart::find($id);

		foreach ($details as $detail) {
			if (isset($detail->id)) {
				$modelDetail = CartDetail::find($detail->id);
				if ($modelDetail) {
					if (isset($detail->delete) && $detail->delete == true) {
						$modelDetail->delete();
					}
				}
			}
		}

		foreach ($details as $detail) {

			if (isset($detail->id)) {
				$modelDetail = CartDetail::find($detail->id);
				if ($modelDetail) {
					if (!isset($detail->delete) || $detail->delete != true) {
						$modelDetail->product_id = (isset($detail->product_name)) ? $detail->product_name->id : 0;
						$modelDetail->product_detail_id = (isset($detail->product_detail)) ? $detail->product_detail->id : 0;
						$modelDetail->quantity = (isset($detail->product_quantity)) ? $detail->product_quantity : 0;
						$modelDetail->discount_amount = (isset($detail->discount_amount)) ? $detail->discount_amount : 0;
						$modelDetail->price = (isset($detail->product_price)) ? $detail->product_price : 0;
						$modelDetail->total_price = (isset($detail->total_price)) ? $detail->total_price : 0;
						$modelDetail->save();
					}
				}
			}else{
				if (!isset($detail->delete) || $detail->delete != true) {
					$modelDetail = new CartDetail([
						'product_id' => (isset($detail->product_name)) ? $detail->product_name->id : 0,
						'product_detail_id' => (isset($detail->product_detail)) ? $detail->product_detail->id : 0,
						'quantity' => (isset($detail->product_quantity)) ? $detail->product_quantity : 0,
						'discount_amount' => (isset($detail->discount_amount)) ? $detail->discount_amount : 0,
						'price' => (isset($detail->product_price)) ? $detail->product_price : 0,
						'total_price' => (isset($detail->total_price)) ? $detail->total_price : 0,
					]);
					$model->details()->save($modelDetail);
				}
			}

			// Subtract product detail quantity
			if (isset($detail->product_detail)) {
				if ($modelProductDetail = ProductDetail::find($detail->product_detail->id)) {
					$modelProductDetail->quantity -= $detail->product_quantity;
					$modelProductDetail->save();
				}
				
				if ($modelProduct = Product::find($detail->product_detail->product_id)) {
					$modelProduct->quantity_available -= $detail->product_quantity;
					$modelProduct->save();
				}
			}
			
		}
	}

	function getCart($id){
		$data = Cart::find($id);
		$data->customer;
		return $data;
	}

	public function getDetails($id){
		$model = Cart::find($id);
		foreach ($model->details as $detail) {
			$detail->product;
			$detail->productDetail;
			$detail->productDetail->size;
			$detail->productDetail->color;
		}
		$return = [];
		foreach ($model->details as $key => $value) {
			$return[] = [
				'id' => $value->id,
				'product_image' => ($value->product->photo) ? asset('storage/' . $value->product->photo) : asset(NO_PHOTO),
				'product_code' => ($value->product->code) ? $value->product->code : 0,
				'product_price' => ($value->price) ? $value->price : 0,
				'total_price' => ($value->total_price) ? $value->total_price : 0,
				'product_quantity' => ($value->quantity) ? $value->quantity : 0,
				'product_editable_price' => ($value->price) ? $value->price : 0,
				'product_name' => [
					'id' => ($value->product->id) ? $value->product->id : 0,
					'name' => ($value->product->name) ? $value->product->name : ''
				],
				'product_size' => [
					'id' => ($value->productDetail->size) ? $value->productDetail->size->id : 0,
					'name' => ($value->productDetail->size) ? $value->productDetail->size->name : ''
				],
				'product_color' => [
					'id' => ($value->productDetail->color) ? $value->productDetail->color->id : 0,
					'name' => ($value->productDetail->color) ? $value->productDetail->color->name : ''
				],
				'product_detail' => ($value->productDetail) ? $value->productDetail : [],
				'color_code' => [
					'id' => ($value->color) ? $value->color->id : 0,
					'name'	=>	($value->color) ? $value->color->name : ""
				],
				'size' => [
					'id' => ($value->size) ? $value->size->id : 0,
					'name'	=>	($value->size) ? $value->size->name : ""
				],
				'quantity' => ($value->quantity) ? $value->quantity : 0
			];
		}
		return $return;
	}

}