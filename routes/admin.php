<?php
Route::get ('/', 'DashboardController@index')->name('admin.dashboard');
Route::get ('roles', 'RoleController@index')->name('admin.roles.index');
Route::get ('roles/create', 'RoleController@create')->name('admin.roles.create');

Route::prefix('cai-dat')
->middleware('permission:setting_manager')->group(function () {
    Route::get ('/phong-ban/', 'RoleController@index')->name('admin.roles.index');
    Route::get ('/phong-ban/chi-tiet', 'RoleController@view')->name('admin.roles.view');
    Route::get ('/phong-ban/them', 'RoleController@view')->name('admin.roles.create');
    Route::post ('/phong-ban/them', 'RoleController@store')->name('admin.roles.store');
    Route::delete ('/phong-ban/', 'RoleController@delete')->name('admin.roles.delete');
    Route::put ('/phong-ban/change-status', 'RoleController@changeStatus')->name('admin.roles.changeStatus');
});

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
    Route::get ('/', 'CustomerController@index')->name('admin.customers.index');
    Route::get ('/lich-su/{id}', 'CustomerController@history')->name('admin.customers.history')->where('id', '[0-9]+');;
    Route::get ('/chi-tiet', 'CustomerController@view')->name('admin.customers.view');
    Route::get ('/them', 'CustomerController@view')->name('admin.customers.create');
    Route::post ('/them', 'CustomerController@store')->name('admin.customers.store');
    Route::delete ('/', 'CustomerController@delete')->name('admin.customers.delete');
    Route::put ('/change-status', 'CustomerController@changeStatus')->name('admin.customers.changeStatus');

    Route::get ('/nhom/', 'GroupCustomerController@index')->name('admin.groupCustomer.index');
    Route::get ('/nhom/chi-tiet', 'GroupCustomerController@view')->name('admin.groupCustomer.view');
    Route::get ('/nhom/them', 'GroupCustomerController@view')->name('admin.groupCustomer.create');
    Route::post ('/nhom/them', 'GroupCustomerController@store')->name('admin.groupCustomer.store');
    Route::delete ('/nhom/', 'GroupCustomerController@delete')->name('admin.groupCustomer.delete');
    Route::put ('/nhom/change-status', 'GroupCustomerController@changeStatus')->name('admin.groupCustomer.changeStatus');

    Route::get ('/export', 'CustomerController@export')->name('admin.customers.export');
});


Route::prefix('kho-hang')
->middleware('permission:product_manager')->group(function () {
    // Route::get ('/', 'ProductController@index')->name('admin.products.index');
    // Route::get ('/chi-tiet', 'ProductController@view')->name('admin.products.view');
    // Route::get ('/them', 'ProductController@view')->name('admin.products.create');
    // Route::post ('/them', 'ProductController@store')->name('admin.products.store');
    // Route::delete ('/', 'ProductController@delete')->name('admin.products.delete');
    // Route::put ('/change-status', 'ProductController@changeStatus')->name('admin.products.changeStatus');

    Route::get ('/', 'WarehouseController@index')->name('admin.warehouses.index');
    Route::get ('/chi-tiet', 'WarehouseController@view')->name('admin.warehouses.view');
    Route::get ('/them', 'WarehouseController@view')->name('admin.warehouses.create');
    Route::post ('/them', 'WarehouseController@store')->name('admin.warehouses.store');
    Route::delete ('/', 'WarehouseController@delete')->name('admin.warehouses.delete');
    Route::put ('/change-status', 'WarehouseController@changeStatus')->name('admin.warehouses.changeStatus');

    Route::prefix('san-pham')->group(function () {
        Route::get ('/', 'ProductAvailableController@index')->name('admin.product_available.index');
        Route::get ('/chi-tiet', 'ProductAvailableController@view')->name('admin.product_available.view');
        Route::post ('/them', 'ProductAvailableController@store')->name('admin.product_available.store');
        Route::delete ('/', 'ProductAvailableController@delete')->name('admin.product_available.delete');
        Route::put ('/change-status', 'ProductAvailableController@changeStatus')->name('admin.product_available.changeStatus');
    });
});

Route::prefix('san-pham')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/danh-muc-san-pham/', 'CategoryController@index')->name('admin.categories.index');
    Route::get ('/danh-muc-san-pham/chi-tiet', 'CategoryController@view')->name('admin.categories.view');
    Route::get ('/danh-muc-san-pham/them', 'CategoryController@view')->name('admin.categories.create');
    Route::post ('/danh-muc-san-pham/them', 'CategoryController@store')->name('admin.categories.store');
    Route::delete ('/danh-muc-san-pham/', 'CategoryController@delete')->name('admin.categories.delete');
    Route::put ('/danh-muc-san-pham/change-status', 'CategoryController@changeStatus')->name('admin.categories.changeStatus');
    
    Route::get ('/', 'ProductController@index')->name('admin.products.index');
    Route::get ('/chi-tiet', 'ProductController@view')->name('admin.products.view');
    Route::get ('/them', 'ProductController@view')->name('admin.products.create');
    Route::post ('/them', 'ProductController@store')->name('admin.products.store');
    Route::delete ('/', 'ProductController@delete')->name('admin.products.delete');
    Route::put ('/change-status', 'ProductController@changeStatus')->name('admin.products.changeStatus');

    Route::get ('/mau-sac/', 'ColorController@index')->name('admin.colors.index');
    Route::get ('/mau-sac/chi-tiet', 'ColorController@view')->name('admin.colors.view');
    Route::get ('/mau-sac/them', 'ColorController@view')->name('admin.colors.create');
    Route::post ('/mau-sac/them', 'ColorController@store')->name('admin.colors.store');
    Route::delete ('/mau-sac/', 'ColorController@delete')->name('admin.colors.delete');
    Route::put ('/mau-sac/change-status', 'ColorController@changeStatus')->name('admin.colors.changeStatus');

    Route::get ('/size/', 'SizeController@index')->name('admin.size.index');
    Route::get ('/size/chi-tiet', 'SizeController@view')->name('admin.size.view');
    Route::get ('/size/them', 'SizeController@view')->name('admin.size.create');
    Route::post ('/size/them', 'SizeController@store')->name('admin.size.store');
    Route::delete ('/size/', 'SizeController@delete')->name('admin.size.delete');
    Route::put ('/size/change-status', 'SizeController@changeStatus')->name('admin.size.changeStatus');
});

Route::prefix('don-hang')
->middleware('permission:shop_manager')->group(function () {
    Route::get ('/', 'CartController@index')->name('admin.carts.index');
    Route::get ('/chi-tiet', 'CartController@view')->name('admin.carts.view');
    Route::get ('/them', 'CartController@view')->name('admin.carts.create');
    Route::post ('/them', 'CartController@store')->name('admin.carts.store');
    Route::delete ('/', 'CartController@delete')->name('admin.carts.delete');
    Route::put ('/change-status', 'CartController@changeStatus')->name('admin.carts.changeStatus');
    Route::get ('/thong-tin-khach-hang', 'CartController@getCartDetail')->name('admin.carts.getCartDetail');
    Route::put ('/update-status', 'CartController@updateStatus')->name('admin.carts.updateStatus');
    Route::get ('/danh-sach-san-pham', 'CartController@getProductAjax')->name('admin.carts.getProductAjax');
    Route::get ('/danh-sach-so-dien-thoai', 'CartController@getPhoneAjax')->name('admin.carts.getPhoneAjax');
});

Route::prefix('thong-ke')
->middleware('permission:report_manager')->group(function () {
       // Route::get ('/', 'StatisticsController@importProduct')->name('admin.statistics.importProduct');
    Route::get ('/doanh-thu', 'StatisticsController@revenueChart')->name('admin.statistics.revenueChart');
    Route::get ('/don-hang', 'StatisticsController@cartChart')->name('admin.statistics.cartChart');

    Route::get ('/data-payment', 'StatisticsController@getPaymentChart')->name('admin.statistics.getPaymentChart');
    Route::get ('/cart-barchart', 'StatisticsController@getCartBarChart')->name('admin.statistics.getCartBarChart');
    Route::get ('/top-product', 'StatisticsController@getTopProductSell')->name('admin.statistics.getTopProductSell');
    Route::get ('/top-platform', 'StatisticsController@getTopPlatformSell')->name('admin.statistics.getTopPlatformSell');
    Route::get ('/top-category', 'StatisticsController@getTopCategorySell')->name('admin.statistics.getTopCategorySell');
       // Route::get ('/doanh-thu/mix', 'StatisticsController@getPaymentMixChart')->name('admin.statistics.getPaymentMixChart');
});

Route::prefix('ke-toan')
->middleware('permission:accountant_manager')->group(function () {
    Route::get ('/phieu-chi/', 'PayslipController@index')->name('admin.payslips.index');
    Route::get ('/phieu-chi/chi-tiet', 'PayslipController@view')->name('admin.payslips.view');
    Route::get ('/phieu-chi/them', 'PayslipController@view')->name('admin.payslips.create');
    Route::post ('/phieu-chi/them', 'PayslipController@store')->name('admin.payslips.store');
    Route::delete ('/phieu-chi/', 'PayslipController@delete')->name('admin.payslips.delete');
    Route::get ('/doanh-thu', 'StatisticsController@revenue')->name('admin.statistics.revenue');
    Route::get ('/doanh-thu/export', 'StatisticsController@exportRevenue')->name('admin.statistics.exportRevenue');
});