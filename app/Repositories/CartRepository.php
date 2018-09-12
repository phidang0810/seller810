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
use App\Models\Category;
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
        $carts = Cart::select(['carts.id', 'carts.city_id', 'carts.partner_id', 'carts.customer_id', 'carts.code', 'carts.quantity', 'carts.status', 'carts.active', 'carts.created_at', 'customers.name as customer_name', 'customers.phone as customer_phone', 'platforms.name as platform_name', 'carts.payment_status'])
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
                $fromDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('start_date') . ' 00:00:00')->toDateTimeString();

                if (trim($request->get('end_date')) !== "") {

                    $toDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('end_date') . ' 23:59:59')->toDateTimeString();
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

            if (trim($request->get('payment_status')) !== "") {
                $query->where(function ($sub) use ($request) {
                    $sub->where('carts.payment_status', 'like', '%' . $request->get('payment_status') . '%');
                });
            }

        }, true)
        ->addColumn('created_at', function ($cart) {
                $html = $cart->created_at;//date('d/m/Y', strtotime($cart->created_at));
                return $html;
            })
        ->addColumn('status', function ($cart) {
            $html = parse_status($cart->status);
            return $html;
        })
        ->addColumn('payment_status', function ($cart) {
            $html = parse_payment_status($cart->payment_status);
            return $html;
        })
        ->addColumn('code', function ($cart) {
            $html = '<a href="'.route('admin.carts.index', ['cart_code' => $cart->code]) . '">' . '<span id="'.$cart->code.'">'.$cart->code.'</span>' .'</a>';
            return $html;
        })
        ->rawColumns(['created_at', 'status', 'payment_status', 'code'])
        ->order(function ($query) {
            $query->orderBy('created_at', 'desc');
        })
        ->toJson();
        return $dataTable;
    }

    public function getProduct($id)
    {
        $data = Cart::find($id);
        return $data;
    }

    public function getCartDetail($cartCode)
    {
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

        $cartDetails = Cart::select(['carts.id', 'cart_detail.quantity as quantity', 'cart_detail.price as price', 'carts.total_price as total_price', 'carts.shipping_fee as shipping_fee', 'carts.code', 'products.barcode_text as product_code', 'products.name as product_name', 'products.photo as product_photo'])
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

    public function updateStatus($request)
    {
        $cartCode = $request->get('cart_code');
        $status = $request->get('status');
        $transport_id = $request->get('transport_id');
        $pay_amount = ($request->get('pay_amount') !== null) ? $request->get('pay_amount') : 0;
        $needed_paid = $request->get('needed_paid');
        $model = Cart::where('code', '=', $cartCode)->first();
        $model->paid_amount = ($model->paid_amount) ? $model->paid_amount : 0;
        $model->paid_amount += $pay_amount;
        $model->needed_paid = $needed_paid;
        $model->transport_id = $transport_id;

        //---> excute payment_status
        if ($model->paid_amount && $model->paid_amount > 0) {
            $model->payment_status = PAYING_NOT_ENOUGH;
            if ($model->paid_amount >= $model->price) {
                if ($model->platform_id && $model->platform_id != 0) {
                    $model->payment_status = PAYING_OFF;
                } else {
                    $model->payment_status = RECEIVED_PAYMENT;
                }

            }
        } else {
            $model->payment_status = NOT_PAYING;
        }

        $model->save();

        // Excute status
        if ($status == CART_COMPLETED) {
            if ($model->payment_status == PAYING_OFF || $model->payment_status == RECEIVED_PAYMENT) {
                $model->status = $status;

                $model->save();
            } else {
                return false;
            }
        } else {
            if ($status == CART_CANCELED) {
                if ($model->details) {
                    foreach ($model->details as $detail) {
                        $this->deleteDetails($detail->id);
                    }
                }
            }
            $model->status = $status;
            $model->save();
        }

        // Excute if cart status is COMPLETED then copy cart & cart detail to payment & payment detail
        if ($model->status == CART_COMPLETED) {
            $model->details;
            $payment_repo = new PaymentRepository();
            $payment = $payment_repo->createOrUpdate($model);
        }

        return $model;
    }

    public function deleteDetails($id)
    {
        $modelDetail = CartDetail::find($id);
        if ($modelDetail) {
            $modelProductDetail = ProductDetail::find($modelDetail->product_detail_id);
            if ($modelProductDetail) {
                $modelProductDetail->quantity += $modelDetail->quantity;
                $modelProductDetail->save();
            }

            $modelProduct = Product::find($modelDetail->product_id);
            if ($modelProduct) {
                $modelProduct->quantity_available += $modelDetail->quantity;
                $modelProduct->save();
            }

            // Delete detail
            $modelDetail->delete();
        }
    }

    public function getTransports()
    {
        $data = Transport::get();
        return $data;
    }

    public function getTransportOptions($id = 0)
    {
        return make_option($this->getTransports(), $id);
    }

    public function getPlatforms()
    {
        $data = Platform::get();
        return $data;
    }

    public function getPlatformOptions($id = 0)
    {
        return make_option($this->getPlatforms(), $id);
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

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Cart::find($id);
            $model->code = general_code('DH', $id, 6);
        } else {
            $model = new Cart;
        }

        $model->city_id = $data['customer_city'];
        $model->partner_id = $data['partner'];

        $data['customer_phone'] = preg_replace('/\s+/', '', $data['customer_phone']);

        // Excute customer
        if ($customer = Customer::find($data['customer_phone'])) {
            $customer->city_id = $data['customer_city'];
            $customer->name = $data['customer_name'];
            $customer->email = $data['customer_email'];
            $customer->address = $data['customer_address'];

            $customer->save();
        } else {
            $customer = new Customer;

            $customer->city_id = $data['customer_city'];
            $customer->name = $data['customer_name'];
            $customer->email = $data['customer_email'];
            $customer->phone = $data['customer_phone'];
            $customer->address = $data['customer_address'];

            $customer->save();

            $customer->code = general_code($customer->name, $customer->id, 5);
            $customer->save();
        }

        $model->customer_id = $customer->id;


        $model->transport_id = $data['transporting_service'];
        $model->quantity = $data['quantity'];
        $model->partner_discount_amount = preg_replace('/[^0-9]/', '', $data['partner_discount_amount']);
        $model->customer_discount_amount = preg_replace('/[^0-9]/', '', $data['customer_discount_amount']);
        $model->total_discount_amount = preg_replace('/[^0-9]/', '', $data['total_discount_amount']);
        $model->total_price = preg_replace('/[^0-9]/', '', $data['total_price']);
        $model->price = preg_replace('/[^0-9]/', '', $data['price']);
        $model->shipping_fee = preg_replace('/[^0-9]/', '', $data['shipping_fee']);
        $model->vat_percent = 10;
        $model->vat_amount = preg_replace('/[^0-9]/', '', $data['vat_amount']);
        $model->paid_amount = preg_replace('/[^0-9]/', '', $data['paid_amount']);
        $model->needed_paid = preg_replace('/[^0-9]/', '', $data['needed_paid']);
        $model->descritption = $data['descritption'];
        $model->platform_id = $data['platform_id'];
        // excute payment_status
        if ($data['paid_amount'] && $data['paid_amount'] > 0) {
            $model->payment_status = PAYING_NOT_ENOUGH;
            if ($data['paid_amount'] >= $model->price) {
                if ($model->platform_id != 0) {
                    $model->payment_status = PAYING_OFF;
                } else {
                    $model->payment_status = RECEIVED_PAYMENT;
                }

            }
        } else {
            $model->payment_status = NOT_PAYING;
        }

        // Excute status, if new then status is new
        if (!$id) {
            $model->status = CART_NEW;
        } else {
            if (isset($data['status'])) {
                if ($data['status'] == CART_COMPLETED) {
                    if ($model->payment_status == PAYING_OFF || $model->payment_status == RECEIVED_PAYMENT) {
                        $model->status = $data['status'];
                    } else {
                        $model->status = CART_TRANSPORTED;
                    }
                } else {
                    $model->status = $data['status'];
                }
            }
        }

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
        if ($model->status == CART_COMPLETED) {
            $model->details;
            $payment_repo = new PaymentRepository();
            $payment = $payment_repo->createOrUpdate($model);
        }

        return $model;
    }

    public function addDetails($id, $details)
    {
        $details = json_decode($details);
        $model = Cart::find($id);

        foreach ($details as $detail) {
            if (isset($detail->id)) {
                $modelDetail = CartDetail::find($detail->id);
                if ($modelDetail) {
                    if (!isset($detail->delete) || $detail->delete != true) {
                        $old_quantity = $modelDetail->quantity;
                        $modelDetail->product_id = (isset($detail->product_name)) ? $detail->product_name->id : 0;
                        $modelDetail->product_detail_id = (isset($detail->product_detail)) ? $detail->product_detail->id : 0;
                        $modelDetail->quantity = (isset($detail->product_quantity)) ? $detail->product_quantity : 0;
                        $modelDetail->discount_amount = (isset($detail->discount_amount)) ? $detail->discount_amount : 0;
                        $modelDetail->price = (isset($detail->product_price)) ? $detail->product_price : 0;
                        $modelDetail->fixed_price = (isset($detail->product_fixed_price)) ? $detail->product_fixed_price : null;
                        $modelDetail->total_price = (isset($detail->total_price)) ? $detail->total_price : 0;
                        $modelDetail->save();
                        if (isset($detail->product_detail)) {
                            if ($modelProductDetail = ProductDetail::find($detail->product_detail->id)) {
                                $modelProductDetail->quantity -= $detail->product_quantity - $old_quantity;
                                $modelProductDetail->save();
                            }

                            if ($modelProduct = Product::find($detail->product_detail->product_id)) {
                                $modelProduct->quantity_available -= $detail->product_quantity - $old_quantity;
                                $modelProduct->save();
                            }
                        }
                    } else {
                        $this->deleteDetails($modelDetail->id);
                    }
                }
            } else {
                if (!isset($detail->delete) || $detail->delete != true) {
                    $modelDetail = new CartDetail([
                        'product_id' => (isset($detail->product_name)) ? $detail->product_name->id : 0,
                        'product_detail_id' => (isset($detail->product_detail)) ? $detail->product_detail->id : 0,
                        'quantity' => (isset($detail->product_quantity)) ? $detail->product_quantity : 0,
                        'discount_amount' => (isset($detail->discount_amount)) ? $detail->discount_amount : 0,
                        'price' => (isset($detail->product_price)) ? $detail->product_price : 0,
                        'fixed_price' => (isset($detail->product_fixed_price)) ? $detail->product_fixed_price : null,
                        'total_price' => (isset($detail->total_price)) ? $detail->total_price : 0,
                    ]);
                    $model->details()->save($modelDetail);
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
        }
    }

    function getCart($id)
    {
        $data = Cart::find($id);
        $data->customer;
        return $data;
    }

    public function getDetails($id)
    {
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
                'product_code' => ($value->product->barcode_text) ? $value->product->barcode_text : 0,
                'product_price' => ($value->price) ? $value->price : 0,
                'total_price' => ($value->total_price) ? $value->total_price : 0,
                'product_quantity' => ($value->quantity) ? $value->quantity : 0,
                'product_fixed_price' => ($value->fixed_price) ? $value->fixed_price : null,
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
                    'name' => ($value->color) ? $value->color->name : ""
                ],
                'size' => [
                    'id' => ($value->size) ? $value->size->id : 0,
                    'name' => ($value->size) ? $value->size->name : ""
                ],
                'quantity' => ($value->quantity) ? $value->quantity : 0
            ];
        }
        return $return;
    }

    public function getStaticsCartDataTable($request)
    {
        $products = CartDetail::selectRaw('products.name, products.main_cate, products.barcode_text, carts.code as cart_code, products.photo, products.category_ids,carts.city_id, carts.platform_id, SUM(cart_detail.quantity) as quantity, SUM(cart_detail.total_price) as total_price, (cart_detail.total_price - (products.price*cart_detail.quantity)) as profit, DATE(cart_detail.created_at) as created_at, COUNT(carts.id) total_cart')
        ->join('products', 'products.id', '=', 'cart_detail.product_id')
        ->join('carts', 'carts.id', '=', 'cart_detail.cart_id')
        ->groupBy('cart_detail.product_id');
        if ($request->has('date')) {
            $products->groupBy(DB::raw('DATE(cart_detail.created_at)'));
        }
        $categories = Category::get()->pluck('name', 'id')->toArray();
        $platforms = Platform::get()->pluck('name', 'id')->toArray();
        $cities = City::get()->pluck('name', 'id')->toArray();
        $dataTable = DataTables::eloquent($products)
        ->filter(function ($query) use ($request) {
            if (trim($request->get('category')) !== "") {
                $query->join('product_category', 'products.id', '=', 'product_category.product_id')
                ->where('product_category.category_id', $request->get('category'));
            }

            if (trim($request->get('platform_id')) !== "") {
                $query->where('carts.platform_id', $request->get('platform_id'));
            }

            if (trim($request->get('date_from')) !== "") {
                $dateFrom = \DateTime::createFromFormat('d/m/Y', $request->get('date_from'));
                $dateFrom = $dateFrom->format('Y-m-d 00:00:00');
                $query->where('cart_detail.created_at', '>=', $dateFrom);
            }

            if (trim($request->get('date_to')) !== "") {
                $dateTo = \DateTime::createFromFormat('d/m/Y', $request->get('date_to'));
                $dateTo = $dateTo->format('Y-m-d 23:59:50');
                $query->where('cart_detail.created_at', '<=', $dateTo);
            }

            if (trim($request->get('keyword')) !== "") {
                $query->where(function ($sub) use ($request) {
                    $sub->where('products.name', 'like', '%' . $request->get('keyword') . '%');
                    $sub->where('products.barcode_text', 'like', '%' . $request->get('keyword') . '%');
                });
            }
        }, true)
        ->addColumn('category', function ($product) use ($categories) {
            $html = '';
            $categoryName = $categories[$product->main_cate] ?? '';
            $html .= '<label class="label label-default">' . $categoryName . '</label><br/>';
            return $html;
        })
        ->addColumn('total_price', function ($product) use ($platforms) {
            return format_price($product->total_price);
        })
        ->addColumn('total_cart', function ($product) use ($platforms) {
            return format_number($product->total_cart);
        })
        ->addColumn('profit', function ($product) {
            return format_price($product->profit);
        })
        ->addColumn('platform', function ($product) use ($platforms) {
            return $platforms[$product->platform_id] ?? '';
        })
        ->addColumn('city', function ($product) use ($cities) {
            return $cities[$product->city_id] ?? '';
        })
        ->addColumn('photo', function ($product) {
            if ($product->photo) {
                $html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $product->photo) . '" />';
            } else {
                $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset(NO_PHOTO) . '" >';
            }
            return $html;
        })
        ->rawColumns(['category', 'platform', 'photo'])
        ->toJson();

        return $dataTable;
    }

    public function getTotalCart($status)
    {
        $data = Cart::where('status', $status)->count();
        return $data;
    }

}