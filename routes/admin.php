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
    Route::put ('/change-status', 'UserController@changeStatus')->name('admin.users.changeStatus');
});

Route::prefix('cong-tac-vien')
    ->middleware('permission:partner_manager')->group(function () {
        Route::get ('/', 'PartnerController@index')->name('admin.partners.index');
        Route::get ('/chi-tiet', 'PartnerController@view')->name('admin.partners.view');
        Route::get ('/them', 'PartnerController@view')->name('admin.partners.create');
        Route::post ('/them', 'PartnerController@store')->name('admin.partners.store');
        Route::delete ('/', 'PartnerController@delete')->name('admin.partners.delete');
        Route::put ('/change-status', 'PartnerController@changeStatus')->name('admin.partners.changeStatus');
    });

Route::prefix('nha-cung-cap')
    ->middleware('permission:supplier_manager')->group(function () {
        Route::get ('/', 'SupplierController@index')->name('admin.suppliers.index');
        Route::get ('/chi-tiet', 'SupplierController@view')->name('admin.suppliers.view');
        Route::get ('/them', 'SupplierController@view')->name('admin.suppliers.create');
        Route::post ('/them', 'SupplierController@store')->name('admin.suppliers.store');
        Route::delete ('/', 'SupplierController@delete')->name('admin.suppliers.delete');
        Route::put ('/change-status', 'SupplierController@changeStatus')->name('admin.suppliers.changeStatus');
    });

Route::prefix('khach-hang')
->middleware('permission:customer_manager')->group(function () {
    Route::get ('/', 'CustomerController@index')->name('admin.customer.index');
    Route::get ('/chi-tiet', 'CustomerController@view')->name('admin.customer.view');
    Route::get ('/them', 'CustomerController@view')->name('admin.customer.create');
    Route::post ('/them', 'CustomerController@store')->name('admin.customer.store');
    Route::delete ('/', 'CustomerController@delete')->name('admin.customer.delete');
    Route::put ('/change-status', 'CustomerController@changeStatus')->name('admin.customer.changeStatus');

    Route::get ('/nhom/', 'GroupCustomerController@index')->name('admin.groupCustomer.index');
    Route::get ('/nhom/chi-tiet', 'GroupCustomerController@view')->name('admin.groupCustomer.view');
    Route::get ('/nhom/them', 'GroupCustomerController@view')->name('admin.groupCustomer.create');
    Route::post ('/nhom/them', 'GroupCustomerController@store')->name('admin.groupCustomer.store');
    Route::delete ('/nhom/', 'GroupCustomerController@delete')->name('admin.groupCustomer.delete');
    Route::put ('/nhom/change-status', 'GroupCustomerController@changeStatus')->name('admin.groupCustomer.changeStatus');
});


Route::prefix('danh-muc-san-pham')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/', 'CategoryController@index')->name('admin.categories.index');
    Route::get ('/chi-tiet', 'CategoryController@view')->name('admin.categories.view');
    Route::get ('/them', 'CategoryController@view')->name('admin.categories.create');
    Route::post ('/them', 'CategoryController@store')->name('admin.categories.store');
    Route::delete ('/', 'CategoryController@delete')->name('admin.categories.delete');
    Route::put ('/change-status', 'CategoryController@changeStatus')->name('admin.categories.changeStatus');
});

Route::prefix('mau-sac')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/', 'ColorController@index')->name('admin.colors.index');
    Route::get ('/chi-tiet', 'ColorController@view')->name('admin.colors.view');
    Route::get ('/them', 'ColorController@view')->name('admin.colors.create');
    Route::post ('/them', 'ColorController@store')->name('admin.colors.store');
    Route::delete ('/', 'ColorController@delete')->name('admin.colors.delete');
    Route::put ('/change-status', 'ColorController@changeStatus')->name('admin.colors.changeStatus');
});

Route::prefix('san-pham')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/', 'ProductController@index')->name('admin.products.index');
    Route::get ('/chi-tiet', 'ProductController@view')->name('admin.products.view');
    Route::get ('/them', 'ProductController@view')->name('admin.products.create');
    Route::post ('/them', 'ProductController@store')->name('admin.products.store');
    Route::delete ('/', 'ProductController@delete')->name('admin.products.delete');
    Route::put ('/change-status', 'ProductController@changeStatus')->name('admin.products.changeStatus');
});

Route::prefix('don-hang')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/', 'CartController@index')->name('admin.carts.index');
    Route::get ('/chi-tiet', 'CartController@view')->name('admin.carts.view');
    Route::get ('/them', 'CartController@view')->name('admin.carts.create');
    Route::post ('/them', 'CartController@store')->name('admin.carts.store');
    Route::delete ('/', 'CartController@delete')->name('admin.carts.delete');
    Route::put ('/change-status', 'CartController@changeStatus')->name('admin.carts.changeStatus');
    Route::get ('/thong-tin-khach-hang', 'CartController@getCartDetail')->name('admin.carts.getCartDetail');
});