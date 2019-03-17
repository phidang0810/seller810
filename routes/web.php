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


Route::get ('/', function(){
    die('Coming soon!');
})->name('home');

Route::get ('/error/{code}', 'Controller@error')->name('error');
Route::get ('/test', function(\Illuminate\Http\Request $request){
    $request->session()->put('auth', 'abcd');
    $request->session()->get('auth');

    return view('auth.test');
})->name('test');
Auth::routes();

Route::get ('/san-pham', 'Frontend\ProductController@index')->name('frontend.products.index');
Route::get ('/san-pham/{slug}', 'Frontend\ProductController@view')->name('frontend.products.view');
Route::get ('/san-pham/so-luong', 'Frontend\ProductController@getMaxQuantity')->name('frontend.products.getMaxQuantity');
Route::post ('/gio-hang/them', 'Frontend\CartController@addDetail')->name('frontend.carts.addDetail');
Route::get ('/gio-hang/so-luong', 'Frontend\CartController@getNumberDetails')->name('frontend.carts.getNumberDetails');
Route::get ('/gio-hang', 'Frontend\CartController@index')->name('frontend.carts.index');
Route::get ('/dat-hang', 'Frontend\CartController@order')->name('frontend.carts.order');
