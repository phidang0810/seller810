<?php

define('ROLE_ADMIN', 'admin');
define('ROLE_MANAGER', 'manager');
define('ROLE_CUSTOMER', 'customer');

define('NO_PHOTO', 'themes/inspinia/img/no-photo.png');

define('ACTIVE', 1);
define('INACTIVE', 0);

//---> Cart status
define('CART_NEW', 1);
define('CART_COMPLETE', 2);
define('CART_IN_PROGRESS', 3);
define('CART_CANCELED', 4);

// payment status: chưa thanh toán, chưa thanh toán đủ, đã thanh toán, đã nhận tiền
define('NOT_PAYING', 1);
define('PAYING_NOT_ENOUGH', 2);
define('PAYING_OFF', 3);
define('RECEIVED_PAYMENT', 4);

// cart status: chưa giao, đang giao, đã giao, đã hoàn tất, đã hủy
define('EXCUTING', 1);
define('TRANSPORTING', 2);
define('TRANSPORTED', 3);
define('COMPLETED', 4);
define('CANCELED', 5);