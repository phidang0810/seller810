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
define('CART_PAYMENT_TEXT', [
    1	=>	'Chưa thanh toán',
    2	=>	'Chưa thanh toán đủ',
    3	=>	'Đã thanh toán',
    4	=>	'Đã nhận tiền'
]);

// cart status: chưa giao, đang giao, đã giao, đã hoàn tất, đã hủy
define('CART_NEW', 1);
define('CART_EXCUTING', 2);
define('CART_TRANSPORTING', 3);
define('CART_TRANSPORTED', 4);
define('CART_COMPLETED', 5);
define('CART_CANCELED', 6);
define('CART_TEXT', [
	1	=>	'Mới tạo',
	2	=>	'Đang xử lý',
	3	=>	'Đang giao',
	4	=>	'Đã giao',
	5	=>	'Đã hoàn tất',
	6	=>	'Đã hủy',
]);

define('PAYSLIP_PENDING', 1);
define('PAYSLIP_APPROVED', 2);
define('PAYSLIP_CANCEL', 3);
define('PAYSLIP_TEXT', [
    1 => 'Chưa xác nhận',
    2 => 'Đã chi',
    3 => 'Từ chối'
]);

define('IMPORT_IMPORTING', 1);
define('IMPORT_IMPORTED', 2);
define('IMPORT_CHECKED', 3);
define('IMPORT_COMPLETING', 5);
define('IMPORT_COMPLETED', 4);
define('IMPORT_TEXT', [
    1   =>  'Đang nhập',
    2   =>  'Đang kiểm',
    3   =>  'Đã kiểm',
    5   =>  'Đang nhập kho',
    4   =>  'Đã nhập kho'
]);
define('IMPORT_LABEL', [
    1   =>  'default',
    2   =>  'Warning',
    3   =>  'info',
    5   =>  'success',
    4   =>  'primary'
]);

define('IMPORT_DETAIL_UNCONFIMRED', 1);
define('IMPORT_DETAIL_CONFIMRED', 2);
define('IMPORT_DETAIL_IMPORTED', 3);
define('IMPORT_DETAIL_TEXT', [
    1   =>  'Chưa xác nhận',
    2   =>  'Đã xác nhận',
    3   =>  'Đã nhập kho'
]);
define('IMPORT_DETAIL_ACTION_TEXT', [
    1   =>  'xác nhận',
    2   =>  'Nhập kho'
]);

define('TRANSPORT_TRANSPORTING', 1);
define('TRANSPORT_TRANSPORTED', 2);
define('TRANSPORT_TEXT', [
    1   =>  'Đang chuyển',
    2   =>  'Đã chuyển',
]);
define('TRANSPORT_LABEL', [
    1   =>  'primary',
    2   =>  'success',
]);

define('TRANSPORT_DETAIL_UNRECEIVE', 1);
define('TRANSPORT_DETAIL_RECEIVED', 2);
define('TRANSPORT_DETAIL_TEXT', [
    1   =>  'Chưa nhận',
    2   =>  'Đã nhận'
]);
define('TRANSPORT_DETAIL_ACTION_TEXT', [
    1   =>  'Nhận hàng'
]);

define('RETURN_RETURNING', 1);
define('RETURN_RETURNED', 2);
define('RETURN_TEXT', [
    1   =>  'Đang trả',
    2   =>  'Đã trả',
]);
define('RETURN_LABEL', [
    1   =>  'primary',
    2   =>  'success',
]);
define('RETURN_ACTION_TEXT', [
    1   =>  'Đã trả',
]);

define('CREDITOR_PAID',3);
define('CREDITOR_NOT_PAID',1);
define('CREDITOR_PAYING',2);