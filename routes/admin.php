<?php
Route::get ('/', 'DashboardController@index')->name('admin.dashboard');
Route::get ('roles', 'RoleController@index')->name('admin.roles.index');
Route::get ('roles/create', 'RoleController@create')->name('admin.roles.create');

Route::prefix('thanh-vien')
    ->middleware('permission:user_manager')->group(function () {
    Route::get ('/', 'UserController@index')->name('admin.users.index');
    Route::get ('/chi-tiet', 'UserController@view')->name('admin.users.view');
    Route::get ('/them', 'UserController@view')->name('admin.users.create');
    Route::post ('/them', 'UserController@store')->name('admin.users.store');
    Route::delete ('/', 'UserController@delete')->name('admin.users.delete');
});

Route::get ('danh-muc-san-pham', 'CategoryController@index')->name('admin.categories.index');
Route::get ('danh-muc-san-pham/chi-tiet', 'CategoryController@view')->name('admin.categories.view');
Route::get ('danh-muc-san-pham/them', 'CategoryController@view')->name('admin.categories.create');
Route::post ('danh-muc-san-pham/them', 'CategoryController@store')->name('admin.categories.store');
Route::delete ('danh-muc-san-pham', 'CategoryController@delete')->name('admin.categories.delete');