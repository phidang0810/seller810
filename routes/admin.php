<?php
Route::get ('/', 'DashboardController@index')->name('admin.dashboard');
Route::get ('roles', 'RoleController@index')->name('admin.roles.index');
Route::get ('roles/create', 'RoleController@create')->name('admin.roles.create');

Route::get ('/update-db', 'AdminController@updateDB')->name('admin.setings.updateDB');

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
    Route::get ('/danh-sach-no', 'CreditorController@index')->name('admin.creditors.index');
    Route::post ('/danh-sach-no/tra-no', 'CreditorController@store')->name('admin.creditors.store');
    Route::get ('/danh-sach-no/view', 'CreditorController@view')->name('admin.creditors.view');

    Route::get ('/', 'SupplierController@index')->name('admin.suppliers.index');

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

    Route::get ('/no', 'CustomerController@dept')->name('admin.customers.dept');
    Route::put ('/tra', 'CustomerController@pay')->name('admin.customers.pay');

    Route::get ('/nhom/', 'GroupCustomerController@index')->name('admin.groupCustomer.index');
    Route::get ('/nhom/chi-tiet', 'GroupCustomerController@view')->name('admin.groupCustomer.view');
    Route::get ('/nhom/them', 'GroupCustomerController@view')->name('admin.groupCustomer.create');
    Route::post ('/nhom/them', 'GroupCustomerController@store')->name('admin.groupCustomer.store');
    Route::delete ('/nhom/', 'GroupCustomerController@delete')->name('admin.groupCustomer.delete');
    Route::put ('/nhom/change-status', 'GroupCustomerController@changeStatus')->name('admin.groupCustomer.changeStatus');

    Route::get ('/export', 'CustomerController@export')->name('admin.customers.export');
});

Route::prefix('bai-viet')
    ->group(function () {
        Route::get ('/', 'PostController@index')->name('admin.posts.index');
        Route::get ('/chi-tiet', 'PostController@view')->name('admin.posts.view');
        Route::get ('/them', 'PostController@view')->name('admin.posts.create');
        Route::post ('/them', 'PostController@store')->name('admin.posts.store');
        Route::delete ('/', 'PostController@delete')->name('admin.posts.delete');
        Route::put ('/change-status', 'PostController@changeStatus')->name('admin.posts.changeStatus');
    });

Route::prefix('anh')
    ->group(function () {
        Route::get ('/', 'PhotoController@index')->name('admin.photos.index');
        Route::get ('/chi-tiet', 'PhotoController@view')->name('admin.photos.view');
        Route::get ('/them', 'PhotoController@view')->name('admin.photos.create');
        Route::post ('/them', 'PhotoController@store')->name('admin.photos.store');
        Route::delete ('/', 'PhotoController@delete')->name('admin.photos.delete');
        Route::put ('/change-status', 'PhotoController@changeStatus')->name('admin.photos.changeStatus');
    });

Route::prefix('thuong-hieu')
->middleware('permission:product_manager')->group(function () {
    Route::get ('/', 'BrandController@index')->name('admin.brands.index');
    Route::get ('/chi-tiet', 'BrandController@view')->name('admin.brands.view');
    Route::get ('/them', 'BrandController@view')->name('admin.brands.create');
    Route::post ('/them', 'BrandController@store')->name('admin.brands.store');
    Route::delete ('/', 'BrandController@delete')->name('admin.brands.delete');
    Route::put ('/change-status', 'BrandController@changeStatus')->name('admin.brands.changeStatus');
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

    Route::prefix('nhap-hang')->group(function () {
        Route::get ('/', 'ImportProductController@index')->name('admin.import_products.index');
        Route::get ('/chi-tiet', 'ImportProductController@view')->name('admin.import_products.view');
        Route::get ('/them', 'ImportProductController@view')->name('admin.import_products.create');
        Route::post ('/them', 'ImportProductController@store')->name('admin.import_products.store');
        Route::delete ('/', 'ImportProductController@delete')->name('admin.import_products.delete');
        Route::get ('/nhan-hang', 'ImportProductController@receive')->name('admin.import_products.receive');
        Route::get ('/kiem-hang', 'ImportProductController@check')->name('admin.import_products.check');
        Route::post ('/kiem-hang/hoan-tat', 'ImportProductController@checkCompleted')->name('admin.import_products.check_completed');
        Route::get ('/kiem-hang/xac-nhan', 'ImportProductController@confirm')->name('admin.import_products.confirm');
        Route::get ('/nhap-kho', 'ImportProductController@import')->name('admin.import_products.import');
        Route::get ('/nhap-kho/xac-nhan', 'ImportProductController@confirmImport')->name('admin.import_products.confirmImport');
        Route::post ('/nhap-kho/hoan-tat', 'ImportProductController@importCompleted')->name('admin.import_products.import_completed');
        Route::get ('/in', 'ImportProductController@print')->name('admin.import_products.print');
    });

    Route::prefix('chuyen-kho')->group(function () {
        Route::get ('/', 'TransportWarehouseController@index')->name('admin.transport_warehouse.index');
        Route::get ('/chi-tiet', 'TransportWarehouseController@view')->name('admin.transport_warehouse.view');
        Route::get ('/them', 'TransportWarehouseController@view')->name('admin.transport_warehouse.create');
        Route::post ('/them', 'TransportWarehouseController@store')->name('admin.transport_warehouse.store');
        Route::delete ('/', 'TransportWarehouseController@delete')->name('admin.transport_warehouse.delete');
        Route::get ('/nhan-hang', 'TransportWarehouseController@receive')->name('admin.transport_warehouse.receive');
        Route::get ('/nhan-hang/san-pham', 'TransportWarehouseController@receiveProduct')->name('admin.transport_warehouse.receiveProduct');
        Route::post ('/nhan-hang/hoan-tat', 'TransportWarehouseController@received')->name('admin.transport_warehouse.received');
        Route::get ('/in', 'TransportWarehouseController@print')->name('admin.transport_warehouse.print');
    });

    Route::prefix('tra-hang')->group(function () {
        Route::get ('/', 'ReturnProductController@index')->name('admin.return_products.index');
        Route::get ('/chi-tiet', 'ReturnProductController@view')->name('admin.return_products.view');
        Route::get ('/them', 'ReturnProductController@view')->name('admin.return_products.create');
        Route::post ('/them', 'ReturnProductController@store')->name('admin.return_products.store');
        Route::delete ('/', 'ReturnProductController@delete')->name('admin.return_products.delete');
        Route::get ('/da-tra', 'ReturnProductController@returned')->name('admin.return_products.returned');
        Route::get ('/in', 'ReturnProductController@print')->name('admin.return_products.print');
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
    Route::get ('/danh-sach-san-pham', 'ProductController@getProductEmptiableAjax')->name('admin.products.getProductEmptiableAjax');

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
    Route::get ('/danh-sach-ten-khach-hang', 'CartController@getNameAjax')->name('admin.carts.getNameAjax');

    Route::prefix('tra-hang')->group(function () {
        Route::get ('/', 'CartController@returnIndex')->name('admin.carts.returnIndex');
        Route::get ('/chi-tiet', 'CartController@returnView')->name('admin.carts.returnView');
        Route::get ('/them', 'CartController@returnView')->name('admin.carts.returnCreate');
        Route::post ('/them', 'CartController@returnStore')->name('admin.carts.returnStore');
        Route::get ('/thong-tin-chi-tiet', 'CartController@getReturnCartDetail')->name('admin.carts.getReturnCartDetail');
        Route::get ('/danh-sach-don-hang', 'CartController@getCartsAjax')->name('admin.carts.getCartsAjax');
    });
});

Route::prefix('thong-ke')
->middleware('permission:report_manager')->group(function () {
    Route::get ('/doanh-thu', 'StatisticsController@revenueChart')->name('admin.statistics.revenueChart');
    Route::get ('/don-hang', 'StatisticsController@cartChart')->name('admin.statistics.cartChart');
    Route::get ('/no', 'StatisticsController@creditorChart')->name('admin.statistics.creditorChart');

    Route::get ('/data-profit', 'StatisticsController@getProfitDataChart')->name('admin.statistics.getProfitDataChart');
    Route::get ('/data-payment', 'StatisticsController@getPaymentChart')->name('admin.statistics.getPaymentChart');
    Route::get ('/cart-barchart', 'StatisticsController@getCartBarChart')->name('admin.statistics.getCartBarChart');
    Route::get ('/creditor', 'StatisticsController@getCreditorBarChart')->name('admin.statistics.getCreditorBarChart');
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

    Route::get ('/thong-ke-kho', 'StatisticsController@productQuantity')->name('admin.statistics.productQuantity');
    Route::get ('/thong-ke-kho/export', 'StatisticsController@exportProductQuantity')->name('admin.statistics.exportProductQuantity');

    Route::get ('/chi-phi-nhap-hang', 'StatisticsController@importProduct')->name('admin.statistics.importProduct');
    Route::get ('/chi-phi-nhap-hang/export', 'StatisticsController@exportProduct')->name('admin.statistics.exportProduct');
});