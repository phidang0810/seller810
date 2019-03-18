<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 8/04/2018
 * Time: 10:29 AM
 */

namespace App\Repositories\Frontend;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\ReturnCartDetail;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerExpress;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Color;
use App\Models\Size;
use App\Models\Transport;
use App\Models\City;
use App\Models\Platform;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Response;
use DB;
use App\Repositories\CartRepository as AdminCartRepository;

Class CartRepository
{

    const CACHE_NAME_CART = 'carts';

    public function addDetail($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Sản phẩm đã được đưa vào giỏ hàng thành công."
        ];

        // get customer
        $customer = $this->getCustomer($request);

        // validate user is customer
        if(!$customer){
            $return['success'] = false;
            $return['error'] = 'Bạn không phải là khách hàng, không thể mua hàng.';

            return $return;
        }
        $return['customer'] = $customer;

        // Get customer's cart with status CART_IN_CART
        $cart = $this->getIncartCart($customer->id);

        // If cart with status CART_IN_CART not exists then create new cart
        if(!$cart) {
            $cart = $this->newCart($customer->id);
        }
        $return['cart'] = $cart;

        $product = Product::find($request->get('id'));

        if (!$product) {
            $return['success'] = false;
            $return['error'] = 'Sản phẩm này không tồn tại';

            return $return;
        }
        $return['product'] = $product;

        $productDetail = ProductDetail::where('product_id', $product->id)
        ->where('size_id', $request->get('size'))
        ->where('color_id', $request->get('color'))
        ->first();

        if (!$productDetail) {
            $return['success'] = false;
            $return['error'] = 'Sản phẩm này không tồn tại';

            return $return;
        }
        $return['productDetail'] = $productDetail;

        $warehouseProduct = WarehouseProduct::where('product_id', $product->id)
        ->where('product_detail_id', $productDetail->id)
        ->where('quantity_available', '>=', $request->get('quantity'))
        ->first();

        if (!$warehouseProduct) {
            $return['success'] = false;
            $return['error'] = 'Sản phẩm này không đủ số lượng';

            return $return;
        }
        $return['warehouseProduct'] = $warehouseProduct;

        $modelDetail = new CartDetail([
            'product_id' => $request->get('id'),
            'product_detail_id' => $productDetail->id,
            'warehouse_product_id' => $warehouseProduct->id,
            'quantity' => $request->get('quantity'),
            // 'discount_amount' => ?,
            'price' => $product->sell_price,
            'total_price' => $product->sell_price * $request->get('quantity'),
            'import_price' => $product->price,
        ]);

        $cart->details()->save($modelDetail);

        $adminCartRepository = new AdminCartRepository;
        $cart = $adminCartRepository->calculateCart($cart);
        $cart->save();        

        $return['modelDetail'] = $modelDetail;

        return $return;
    }

    public function getCustomerInCartCart ($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Lấy giỏ hàng thành công."
        ];

        // get customer
        $customer = $this->getCustomer($request);
        $return['customer'] = $customer;

        // validate user is customer
        if(!$customer){
            $return['success'] = false;
            $return['error'] = 'Bạn không phải là khách hàng';

            return $return;
        }

        // Get customer's cart with status CART_IN_CART
        $cart = $this->getIncartCart($customer->id);
        $cart->total_price = format_price($cart->total_price);
        $cart->price = format_price($cart->price);
        $cart->shipping_fee_text = format_price($cart->shipping_fee);
        // Get cart details
        $cart->details;
        foreach ($cart->details as $detail) {
            if ($product = Product::find($detail->product_id)) {
                $detail->name = $product->name;
                $detail->photo = $product->photo;
                $detail->supplier = $product->supplier;
                $detail->min_quantity_sell = $product->min_quantity_sell;
                $detail->product_detail = $detail->productDetail;
                $detail->total_price = format_price($detail->total_price);
                $detail->size = $detail->product_detail->size;
                $detail->color = $detail->product_detail->color;
            }
        }
        $return['cart'] = $cart;


        return $return;
    }

    public function getCustomerInfo ($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Lấy thông tin khách hàng thành công."
        ];

        $customer = $this->getCustomer($request);

        // validate user is customer
        if(!$customer){
            $return['success'] = false;
            $return['error'] = 'Bạn không phải là khách hàng';

            return $return;
        }

        if (!is_null($customer->default_payment)) {
            $customerExpress = CustomerExpress::find($customer->default_payment);
            if ($customerExpress) {
                $customer->name = $customerExpress->name;
                $customer->email = $customerExpress->email;
                $customer->phone = $customerExpress->phone;
                $customer->address = $customerExpress->address;
                $customer->city_id = $customerExpress->city_id;
                $customer->default_payment = true;
            }
        }
        
        $return['customer'] = $customer;

        return $return;
    }

    public function getCustomer ($request) {
        return Customer::where('user_id', $request->get('user_id'))->first();
    }

    public function getIncartCart ($customer_id) {
        return Cart::where('customer_id', $customer_id)->where('status', CART_IN_CART)->first();
    }

    public function newCart($customer_id) {
        $data = [
            'customer_id' => $customer_id,
            'status' => CART_IN_CART
        ];

        $cart = Cart::create($data);

        $cart->code = general_code('DH', $cart->id, 6);
        $cart->save();

        return $cart;
    }

    public function getNumberDetails($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Lấy số lượng sản phẩm trong giỏ hàng thành công."
        ];

        // get customer
        $customer = $this->getCustomer($request);

        // validate user is customer
        if(!$customer){
            $return['success'] = false;
            $return['error'] = 'Bạn không phải là khách hàng, không thể mua hàng.';

            return $return;
        }
        $return['customer'] = $customer;

        // Get customer's cart with status CART_IN_CART
        $cart = $this->getIncartCart($customer->id);

        // If cart with status CART_IN_CART not exists then create new cart
        if(!$cart) {
            $return['success'] = false;
            $return['error'] = 'Hiện chưa có sản phẩm nào trong giỏ hàng.';
        }

        $return['number'] = ($cart) ? $cart->details()->count() : 0;

        return $return;
    }

    function updateCartDetail($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Cập nhật giỏ hàng thành công."
        ];

        // define requests variables
        $detail_id = $request->get('id');
        $cart_status = $request->get('status');
        $user_id = $request->get('user_id');
        $quantity = $request->get('quantity');

        $cart_detail = CartDetail::find($detail_id);

        if (!$cart_detail) {
            $return['success'] = false;
            $return['message'] = "Chi tiết đơn hàng này không tồn tại.";
            return $return;
        }

        $cart = Cart::find($cart_detail->cart_id);

        if (!$cart) {
            $return['success'] = false;
            $return['message'] = "Đơn hàng này không tồn tại.";
            return $return;
        }

        if ($quantity == 0) {
            $cart_detail->delete();
        }else{
            $cart_detail->quantity = $quantity;
            $cart_detail->total_price = $cart_detail->price * $quantity;
            $cart_detail->save();
        }

        $adminCartRepository = new AdminCartRepository;
        $cart = $adminCartRepository->calculateCart($cart);
        $cart->save();

        $cart->total_price = format_price($cart->total_price);
        $cart->price = format_price($cart->price);
        // Get cart details
        $cart->details;
        foreach ($cart->details as $detail) {
            if ($product = Product::find($detail->product_id)) {
                $detail->name = $product->name;
                $detail->photo = $product->photo;
                $detail->supplier = $product->supplier;
                $detail->min_quantity_sell = $product->min_quantity_sell;
                $detail->product_detail = $detail->productDetail;
                $detail->total_price = format_price($detail->total_price);
            }
        }
        $return['cart'] = $cart;

        return $return;
    }

    public function getCities()
    {
        $data = City::get();
        return $data;
    }

    public function getCityOptions($id = 0)
    {
        return make_option($this->getCities(), $id);
    }

    public function paymentCart($request) {
        // define return data
        $return = [
            'success' => true,
            'error' => "",
            'message' => "Đặt mua hàng thành công."
        ];

        // define requests variables
        $customer_name = $request->get('customer_name');
        $customer_email = $request->get('customer_email');
        $customer_phone = $request->get('customer_phone');
        $customer_address = $request->get('customer_address');
        $customer_city = $request->get('customer_city');
        $is_payment_default = $request->get('is_payment_default');
        $payment_method = $request->get('payment_method');
        $transport_method = $request->get('transport_method');
        $transport_info_name = $request->get('transport_info_name');
        $transport_info_phone = $request->get('transport_info_phone');
        $user_id = $request->get('user_id');
        $cart_id = $request->get('cart_id');


        $cart = Cart::find($cart_id);
        if (!$cart) {
            $return['success'] = false;
            $return['message'] = "Đơn hàng này không tồn tại.";
            return $return;
        }

        // get customer data
        $customer = $this->getCustomer($request);

        // validate user is customer
        if(!$customer){
            $return['success'] = false;
            $return['error'] = 'Bạn không phải là khách hàng, không thể mua hàng.';

            return $return;
        }

        // compare customer data with request data
        if ($customer->city_id != $customer_city || $customer->name != $customer_name ||
            $customer->email != $customer_email || $customer->phone != $customer_phone || $customer->address != $customer_address) {
            // if different then compare with data of current customer in customer_express
            $customer_express = CustomerExpress::where('customer_id', $customer->id)
            ->where('city_id', $customer_city)
            ->where('phone', $customer_phone)
            ->where('email', $customer_email)
            ->where('name', $customer_name)
            ->where('address', $customer_address)->first();

            // if not exist then create new customer_express
            if (!$customer_express) {
             $customer_express = new CustomerExpress;
             $customer_express->name = $customer_name;
             $customer_express->phone = $customer_phone;
             $customer_express->email = $customer_email;
             $customer_express->address = $customer_address;
             $customer_express->city_id = $customer_city;
             $customer_express->customer_id = $customer->id;

             $customer_express->save();
         }

        // if check payment default then update customer current field default_payment = customer_express id
         if ($is_payment_default) {
            $customer->default_payment = $customer_express->id;
            $customer->save();
        }

        $cart->customer_express_id = $customer_express->id;
    }

        // update cart payment info
    $cart->payment_method = $payment_method;
    $cart->transport_method = $transport_method;
    $cart->transport_info_name = $transport_info_name;
    $cart->transport_info_phone = $transport_info_phone;

    // create db transaction and update cart, product, product_warehouse, product_detail
    // DB::transaction(function () use ($cart) {
    //     $cart->quantity = 0;
    //     $cart->total_price = 0;
    //     $cart->total_import_price = 0;
    //     foreach ($cart->details as $cart_detail) {
    //         $product = Product::find($cart_detail->product_id);
    //         $productDetail = ProductDetail::find($cart_detail->product_detail_id);
    //         $warehouseProduct = WarehouseProduct::find($cart_detail->warehouse_product_id);

    //         // minus quantity_avaiable in product, product detail, warehouse detail
    //         $product->quantity_available -= $cart_detail->quantity;
    //         $product->save();

    //         $productDetail->quantity_available -= $cart_detail->quantity;
    //         $productDetail->save();

    //         $warehouseProduct->quantity_available -= $cart_detail->quantity;
    //         $warehouseProduct->save();

    //         $cart_detail->total_price = $cart_detail->price * $cart_detail->quantity;
    //         $cart_detail->save();

    //         $cart->quantity += $cart_detail->quantity;
    //         $cart->total_price += $cart_detail->total_price;
    //         $cart->total_import_price += $cart_detail->import_price * $cart_detail->quantity;
    //     }

    //     $cart->total_discount_amount = ($cart->quantity*$cart->partner_discount_amount) + $cart->customer_discount_amount;
    //     $cart->vat_amount = $cart->total_price*$cart->vat_percent;
    //     $cart->price = $cart->total_price+$cart->vat_amount-$cart->total_discount_amount;

    //     $cart->status = CART_NEW;

    //     $cart->save();
    // });

        // send response
    if ($cart->payment_method == PAYMENT_METHOD_BANK) {
        $return['redirect_to'] = route('frontend.carts.paymentBank', ['cart_code' => $cart->code]);
    }

    return $return;
}
}