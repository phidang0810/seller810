<?php

/*
|--------------------------------------------------------------------------
| Web Routes - GIT
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::get ('/', 'Frontend\HomeController@index')->name('home');
Route::get ('/lien-he', 'Frontend\HomeController@contact')->name('contact');
Route::post ('/lien-he', 'Frontend\HomeController@postContact')->name('postContact');

Route::get ('/error/{code}', 'Controller@error')->name('error');
Route::get ('/test', function(\Illuminate\Http\Request $request){
    $request->session()->put('auth', 'abcd');
    $request->session()->get('auth');

    return view('auth.test');
})->name('test');
Auth::routes();

Route::get ('redirect', function(){
    return view('frontend.panorama.redirect');
})->name('panorama.redirect');

Route::get('/login/{social}', 'Auth\SocialLoginController@login')->name('social_login')->where('social','facebook|google');
Route::get('/login/{social}/callback','Auth\SocialLoginController@handleProviderCallback')->name('social_callback')->where('social','twitter|facebook|linkedin|google|github|bitbucket');

Route::get ('/tin-tuc', 'Frontend\HomeController@listPost')->name('frontend.listPost');
Route::get ('/khuyen-mai', 'Frontend\HomeController@listPostSale')->name('frontend.listPostSale');
Route::get ('/bai-viet/{id}/{name}', 'Frontend\HomeController@detailPost')->name('frontend.detailPost');

Route::get ('/san-pham', 'Frontend\ProductController@index')->name('frontend.products.index');
Route::get ('/san-pham/{id}/{slug?}', 'Frontend\ProductController@view')->name('frontend.products.view');
Route::get ('/danh-muc/{id}/{slug?}', 'Frontend\ProductController@category')->name('frontend.products.category');
Route::get ('/so-luong-san-pham', 'Frontend\ProductController@getMaxQuantity')->name('frontend.products.getMaxQuantity');
Route::post ('/gio-hang/them', 'Frontend\CartController@addDetail')->name('frontend.carts.addDetail');
Route::get ('/gio-hang/so-luong', 'Frontend\CartController@getNumberDetails')->name('frontend.carts.getNumberDetails');
Route::get ('/gio-hang', 'Frontend\CartController@index')->name('frontend.carts.index');
Route::get ('/thanh-toan', 'Frontend\CartController@payment')->name('frontend.carts.payment');
Route::post ('/thanh-toan', 'Frontend\CartController@storePayment')->name('frontend.carts.storePayment');
Route::get ('/thanh-toan/{cart_code}', 'Frontend\CartController@paymentBank')->name('frontend.carts.paymentBank');
