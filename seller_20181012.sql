/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : itvn_seller

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-10-12 22:58:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for brands
-- ----------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of brands
-- ----------------------------

-- ----------------------------
-- Table structure for carts
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(11) unsigned DEFAULT NULL,
  `partner_id` int(11) unsigned DEFAULT NULL,
  `customer_id` int(11) unsigned DEFAULT NULL,
  `transport_id` int(11) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT '1',
  `partner_discount_amount` int(11) DEFAULT '0',
  `customer_discount_amount` int(11) DEFAULT '0',
  `total_discount_amount` int(11) DEFAULT '0',
  `price` int(11) DEFAULT '0',
  `total_price` int(11) DEFAULT '0',
  `shipping_fee` int(11) DEFAULT '0',
  `vat_percent` int(11) DEFAULT '0',
  `vat_amount` int(11) DEFAULT '0',
  `prepaid_amount` int(11) DEFAULT NULL,
  `needed_paid` int(11) DEFAULT NULL,
  `descritption` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `paid_amount` int(11) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of carts
-- ----------------------------

-- ----------------------------
-- Table structure for cart_detail
-- ----------------------------
DROP TABLE IF EXISTS `cart_detail`;
CREATE TABLE `cart_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_detail_id` int(11) NOT NULL,
  `warehouse_product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  `discount_amount` int(11) DEFAULT '0',
  `price` int(11) DEFAULT '0',
  `fixed_price` int(11) DEFAULT NULL,
  `total_price` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cart_detail
-- ----------------------------

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `code` varchar(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `level` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of categories
-- ----------------------------

-- ----------------------------
-- Table structure for cities
-- ----------------------------
DROP TABLE IF EXISTS `cities`;
CREATE TABLE `cities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cities
-- ----------------------------

-- ----------------------------
-- Table structure for colors
-- ----------------------------
DROP TABLE IF EXISTS `colors`;
CREATE TABLE `colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of colors
-- ----------------------------

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_customer_id` int(11) DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers
-- ----------------------------

-- ----------------------------
-- Table structure for group_customers
-- ----------------------------
DROP TABLE IF EXISTS `group_customers`;
CREATE TABLE `group_customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `discount_amount` int(11) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of group_customers
-- ----------------------------

-- ----------------------------
-- Table structure for import_products
-- ----------------------------
DROP TABLE IF EXISTS `import_products`;
CREATE TABLE `import_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `import_staff_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `barcode_text` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `barcode` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `colors` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `sizes` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `sell_price` int(11) DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `content` text CHARACTER SET utf8,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `meta_keyword` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `meta_robot` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `main_cate` int(11) DEFAULT NULL,
  `category_ids` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of import_products
-- ----------------------------

-- ----------------------------
-- Table structure for import_product_detail
-- ----------------------------
DROP TABLE IF EXISTS `import_product_detail`;
CREATE TABLE `import_product_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `import_product_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned DEFAULT '0',
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `color_id` int(11) unsigned DEFAULT NULL,
  `size_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of import_product_detail
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------

-- ----------------------------
-- Table structure for partners
-- ----------------------------
DROP TABLE IF EXISTS `partners`;
CREATE TABLE `partners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `discount_amount` int(11) DEFAULT '0',
  `email` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of partners
-- ----------------------------

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) DEFAULT NULL,
  `city_id` int(11) unsigned DEFAULT NULL,
  `partner_id` int(11) unsigned DEFAULT NULL,
  `customer_id` int(11) unsigned DEFAULT NULL,
  `transport_id` int(11) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT '1',
  `partner_discount_amount` int(11) DEFAULT '0',
  `customer_discount_amount` int(11) DEFAULT '0',
  `total_discount_amount` int(11) DEFAULT '0',
  `price` int(11) DEFAULT '0',
  `total_price` int(11) DEFAULT '0',
  `shipping_fee` int(11) DEFAULT '0',
  `vat_percent` int(11) DEFAULT '0',
  `vat_amount` int(11) DEFAULT '0',
  `prepaid_amount` int(11) DEFAULT NULL,
  `needed_paid` int(11) DEFAULT NULL,
  `descritption` varchar(255) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT NULL,
  `platform_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payments
-- ----------------------------

-- ----------------------------
-- Table structure for payment_detail
-- ----------------------------
DROP TABLE IF EXISTS `payment_detail`;
CREATE TABLE `payment_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `cart_id` int(11) unsigned NOT NULL,
  `cart_detail_id` int(11) DEFAULT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_detail_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT '1',
  `discount_amount` int(11) DEFAULT '0',
  `price` int(11) DEFAULT '0',
  `total_price` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment_detail
-- ----------------------------

-- ----------------------------
-- Table structure for payslips
-- ----------------------------
DROP TABLE IF EXISTS `payslips`;
CREATE TABLE `payslips` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payslips
-- ----------------------------

-- ----------------------------
-- Table structure for payslip_groups
-- ----------------------------
DROP TABLE IF EXISTS `payslip_groups`;
CREATE TABLE `payslip_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payslip_groups
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES ('1', 'Cửa hàng', 'shop_manager', '1', '0', '2018-04-09 04:46:03', '2018-07-24 13:21:41');
INSERT INTO `permissions` VALUES ('2', 'Quản lý bán hàng', 'seller_manager', '1', '0', '2018-04-09 04:46:57', '2018-07-24 13:21:26');
INSERT INTO `permissions` VALUES ('3', 'Sản phẩm', 'product_manager', '1', '0', '2018-04-09 04:47:16', '2018-07-24 13:21:58');
INSERT INTO `permissions` VALUES ('4', 'Kho hàng', 'warehouse_manager', '1', '0', '2018-07-24 13:22:43', '2018-07-24 13:22:43');
INSERT INTO `permissions` VALUES ('5', 'Kế toán', 'accountant_manager', '1', '0', '2018-07-24 13:23:02', '2018-07-24 13:23:31');
INSERT INTO `permissions` VALUES ('6', 'Quản lý tài khoản', 'user_manager', '1', '0', '2018-07-24 13:24:37', '2018-07-24 13:24:37');
INSERT INTO `permissions` VALUES ('7', 'Thống kê', 'report_manager', '1', '0', '2018-07-24 13:25:00', '2018-07-24 13:25:00');
INSERT INTO `permissions` VALUES ('8', 'Cài đặt', 'setting_manager', '1', '0', '2018-07-24 13:25:09', '2018-07-24 13:25:09');
INSERT INTO `permissions` VALUES ('9', 'Quản lý khách hàng', 'customer_manager', '1', '0', '2018-07-25 14:24:05', '2018-07-25 14:24:05');
INSERT INTO `permissions` VALUES ('10', 'Cộng Tác Viên', 'partner_manager', '1', '0', '2018-07-26 14:06:12', '2018-07-26 14:06:12');
INSERT INTO `permissions` VALUES ('11', 'Nhà Cung Cấp', 'supplier_manager', '1', '0', '2018-07-30 13:58:29', '2018-07-30 13:58:29');
INSERT INTO `permissions` VALUES ('12', 'Nhân viên nhập hàng', 'product_importer', '1', '0', '2018-10-06 21:27:56', '2018-10-06 21:27:56');
INSERT INTO `permissions` VALUES ('13', 'Nhân viên chuyển kho', 'warehouse_transporter', '1', '0', '2018-10-09 01:50:59', '2018-10-09 01:50:59');
INSERT INTO `permissions` VALUES ('14', 'Nhân viên trả hàng', 'product_returner', '1', '0', '2018-10-09 22:01:05', '2018-10-09 22:01:05');

-- ----------------------------
-- Table structure for platforms
-- ----------------------------
DROP TABLE IF EXISTS `platforms`;
CREATE TABLE `platforms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of platforms
-- ----------------------------

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `barcode_text` varchar(50) DEFAULT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `colors` varchar(500) DEFAULT NULL,
  `sizes` varchar(500) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `sell_price` int(11) DEFAULT NULL,
  `quantity` int(11) unsigned DEFAULT NULL,
  `quantity_available` int(10) unsigned DEFAULT NULL,
  `photo` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `content` text,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_robot` varchar(255) DEFAULT NULL,
  `main_cate` int(11) DEFAULT NULL,
  `category_ids` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of products
-- ----------------------------

-- ----------------------------
-- Table structure for product_category
-- ----------------------------
DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `product_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `product_id` (`product_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_category
-- ----------------------------

-- ----------------------------
-- Table structure for product_detail
-- ----------------------------
DROP TABLE IF EXISTS `product_detail`;
CREATE TABLE `product_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `color_id` int(11) unsigned DEFAULT NULL,
  `size_id` int(11) unsigned DEFAULT NULL,
  `quantity` int(11) unsigned DEFAULT '0',
  `quantity_available` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`size_id`,`color_id`),
  KEY `product_id_2` (`product_id`),
  KEY `color_id` (`color_id`),
  KEY `size_id` (`size_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_detail
-- ----------------------------

-- ----------------------------
-- Table structure for product_photos
-- ----------------------------
DROP TABLE IF EXISTS `product_photos`;
CREATE TABLE `product_photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(50) DEFAULT '',
  `origin` varchar(255) NOT NULL DEFAULT '',
  `large` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `color_code` varchar(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product_photos
-- ----------------------------

-- ----------------------------
-- Table structure for return_products
-- ----------------------------
DROP TABLE IF EXISTS `return_products`;
CREATE TABLE `return_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `return_staff_id` int(11) NOT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of return_products
-- ----------------------------

-- ----------------------------
-- Table structure for return_product_detail
-- ----------------------------
DROP TABLE IF EXISTS `return_product_detail`;
CREATE TABLE `return_product_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_detail_id` int(11) unsigned NOT NULL,
  `supplier_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of return_product_detail
-- ----------------------------

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `alias` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', 'Admin', 'admin', '1', '0', '2018-04-09 04:26:57', '2018-04-09 04:27:42');
INSERT INTO `roles` VALUES ('2', 'Customer', 'customer', '1', '1', '2018-04-09 04:26:58', '2018-04-09 04:27:16');
INSERT INTO `roles` VALUES ('3', 'Manager', 'manager', '1', '0', '2018-04-10 03:46:55', '2018-04-10 03:46:55');
INSERT INTO `roles` VALUES ('4', 'Staff', 'staff', '1', '0', '2018-10-06 21:28:46', '2018-10-06 21:28:46');

-- ----------------------------
-- Table structure for role_permission
-- ----------------------------
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission` (
  `role_id` int(11) unsigned NOT NULL,
  `permission_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `permission_id` (`permission_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role_permission
-- ----------------------------
INSERT INTO `role_permission` VALUES ('1', '1', '2018-07-24 13:59:18', '2018-07-24 13:59:18');
INSERT INTO `role_permission` VALUES ('1', '2', '2018-07-24 13:59:25', '2018-07-24 13:59:25');
INSERT INTO `role_permission` VALUES ('1', '3', '2018-07-24 13:59:28', '2018-07-24 13:59:28');
INSERT INTO `role_permission` VALUES ('1', '4', '2018-07-24 13:59:31', '2018-07-24 13:59:31');
INSERT INTO `role_permission` VALUES ('1', '5', '2018-07-24 13:59:34', '2018-07-24 13:59:34');
INSERT INTO `role_permission` VALUES ('1', '6', '2018-07-24 13:59:37', '2018-07-25 14:48:25');
INSERT INTO `role_permission` VALUES ('1', '7', '2018-07-24 13:59:41', '2018-07-24 13:59:41');
INSERT INTO `role_permission` VALUES ('1', '8', '2018-07-24 13:59:43', '2018-07-24 13:59:43');
INSERT INTO `role_permission` VALUES ('1', '9', '2018-07-25 14:48:28', '2018-07-25 14:48:28');
INSERT INTO `role_permission` VALUES ('1', '10', '2018-07-26 14:06:24', '2018-07-26 14:06:24');
INSERT INTO `role_permission` VALUES ('1', '11', '2018-07-30 14:13:34', '2018-07-30 14:13:34');
INSERT INTO `role_permission` VALUES ('1', '12', '2018-10-06 22:21:11', '2018-10-06 22:21:11');
INSERT INTO `role_permission` VALUES ('4', '12', '2018-10-06 21:28:57', '2018-10-06 21:28:57');
INSERT INTO `role_permission` VALUES ('1', '13', '2018-10-09 01:51:26', '2018-10-09 01:51:26');
INSERT INTO `role_permission` VALUES ('4', '13', '2018-10-09 01:51:30', '2018-10-09 01:51:30');
INSERT INTO `role_permission` VALUES ('1', '14', '2018-10-10 00:49:52', '2018-10-10 00:49:52');
INSERT INTO `role_permission` VALUES ('4', '14', '2018-10-10 00:49:59', '2018-10-10 00:49:59');

-- ----------------------------
-- Table structure for sizes
-- ----------------------------
DROP TABLE IF EXISTS `sizes`;
CREATE TABLE `sizes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sizes
-- ----------------------------

-- ----------------------------
-- Table structure for suppliers
-- ----------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `tax_code` varchar(50) DEFAULT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `responsible_person` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of suppliers
-- ----------------------------

-- ----------------------------
-- Table structure for sys_failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `sys_failed_jobs`;
CREATE TABLE `sys_failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for sys_jobs
-- ----------------------------
DROP TABLE IF EXISTS `sys_jobs`;
CREATE TABLE `sys_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of sys_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for transports
-- ----------------------------
DROP TABLE IF EXISTS `transports`;
CREATE TABLE `transports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of transports
-- ----------------------------

-- ----------------------------
-- Table structure for transport_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `transport_warehouse`;
CREATE TABLE `transport_warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `transport_staff_id` int(11) NOT NULL,
  `transport_date` timestamp NULL DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of transport_warehouse
-- ----------------------------

-- ----------------------------
-- Table structure for transport_warehouse_detail
-- ----------------------------
DROP TABLE IF EXISTS `transport_warehouse_detail`;
CREATE TABLE `transport_warehouse_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transport_warehouse_id` int(11) NOT NULL,
  `from_warehouse_id` int(11) NOT NULL,
  `receive_warehouse_id` int(11) NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_detail_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned DEFAULT '0',
  `status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of transport_warehouse_detail
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) unsigned NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `full_name` varchar(151) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '4', 'hieu', 'hieu@coolglow.com', '$2y$10$FF/Gp/LiUXEfzrdpWeNObOHL8yY.Mi2BPV4wD1tCzdDQAMMJRdfDK', 'public/users/62d9b96b66228da6e45abc1664bc741e.png', 'LjFDdeJvkZoAiqgIu0ANCN8gPxNxx9jViO8z4u3eQbCQVrHHkQqtEqgur7A6', 'Dang', 'Diep', 'Dang111', null, null, '1', null, '2018-04-10 03:46:21', '2018-10-06 21:29:22');
INSERT INTO `users` VALUES ('10', '1', '', 'phidangmtv@gmail.com', '$2y$10$zsn9yzd87SNmUeXoBDdSiO97InBGMeuUFYJMEIHtWkIyxUNOBOZkK', null, 'nlCCjbRrgXSI3WLW3IbmI92e75citOnzQnQ2nuBnB1f2rL2HiOmJ61G8M7iN', 'aa', 'bb', 'aa bb', null, null, '1', null, '2018-04-11 06:12:44', '2018-08-25 13:18:22');

-- ----------------------------
-- Table structure for warehouses
-- ----------------------------
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `order` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of warehouses
-- ----------------------------

-- ----------------------------
-- Table structure for warehouse_product
-- ----------------------------
DROP TABLE IF EXISTS `warehouse_product`;
CREATE TABLE `warehouse_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `product_detail_id` int(11) unsigned NOT NULL,
  `quantity` int(11) unsigned DEFAULT '0',
  `quantity_available` int(11) unsigned DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of warehouse_product
-- ----------------------------
