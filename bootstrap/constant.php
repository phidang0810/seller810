<?php

define('ROLE_ADMIN', 'admin');
define('ROLE_MANAGER', 'manager');
define('ROLE_CUSTOMER', 'customer');

define('NO_PHOTO', 'themes/inspinia/img/no-photo.png');

define('ACTIVE', 1);
define('INACTIVE', 0);

// payment status: chưa thanh toán, chưa thanh toán đủ, đã thanh toán, đã nhận tiền
define('NOT_PAYING', 1);
define('PAYING_NOT_ENOUGH', 2);
define('PAYING_OFF', 3);
define('RECEIVED_PAYMENT', 4);

// payment status: chưa thanh toán, chưa thanh toán đủ, đã thanh toán, đã nhận tiền
define('NOT_PAYING_TEXT', 'Chưa thanh toán');
define('PAYING_NOT_ENOUGH_TEXT', 'Chưa thanh toán đủ');
define('PAYING_OFF_TEXT', 'Đã thanh toán');
define('RECEIVED_PAYMENT_TEXT', 'Đã nhận tiền');

// cart status: chưa giao, đang giao, đã giao, đã hoàn tất, đã hủy
define('CART_NEW', 1);
define('EXCUTING', 2);
define('CART_TRANSPORTING', 3);
define('TRANSPORTED', 4);
define('COMPLETED', 5);
define('CANCELED', 6);

// cart status: chưa giao, đang giao, đã giao, đã hoàn tất, đã hủy
define('EXCUTING_TEXT', 'Đang xử lý');
define('TRANSPORTING_TEXT', 'Đang giao');
define('TRANSPORTED_TEXT', 'Đã giao');
define('COMPLETED_TEXT', 'Đã hoàn tất');
define('CANCELED_TEXT', 'Đã hủy');

define('PAYSLIP_PENDING', 1);
define('PAYSLIP_APPROVED', 2);
define('PAYSLIP_CANCEL', 3);
define('PAYSLIP_TEXT', [
    1 => 'Chưa xác nhận',
    2 => 'Đã chi',
    3 => 'Từ chối'
]);