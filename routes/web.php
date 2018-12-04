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
