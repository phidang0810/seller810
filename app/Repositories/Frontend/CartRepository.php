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

        DB::transaction(function () use ($cart, $product, $productDetail, $warehouseProduct, $modelDetail, $request) {
            // add detail cart
            $cart->details()->save($modelDetail);

            $cart->quantity += $request->get('quantity');
            $cart->total_discount_amount = ($request->get('quantity')*$cart->partner_discount_amount) + $cart->customer_discount_amount;
            $cart->total_price = $request->get('quantity')*$product->sell_price;
            $cart->total_import_price = $request->get('quantity')*$product->price;
            $cart->vat_amount = $cart->total_price*$cart->vat_percent;
            $cart->price = $cart->total_price+$cart->vat_amount-$cart->total_discount_amount;

            // minus quantity_avaiable in product, product detail, warehouse detail
            // $product->quantity_available -= $request->get('quantity');
            // $productDetail->quantity_available -= $request->get('quantity');
            // $warehouseProduct->quantity_available -= $request->get('quantity');

            $cart->save();
            // $product->save();
            // $productDetail->save();
            // $warehouseProduct->save();
        });

        

        $return['modelDetail'] = $modelDetail;

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

        return Cart::create($data);
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

        $return['number'] = $cart->details()->count();

        return $return;
    }
}