-- ============================================================
-- Warehouse Management System — Initial Database Dump
-- For Marketplace Distribution & Clean Installation
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- -------------------------------------------------------------
-- Table structure for `roles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `permissions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `module` varchar(100) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permissions_slug` (`slug`),
  KEY `idx_permissions_module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `role_permissions`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `users`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `user_roles`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_ur_role` (`role_id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `activity_logs`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `module` varchar(100) NOT NULL DEFAULT 'system',
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_al_user_id` (`user_id`),
  KEY `idx_al_module` (`module`),
  KEY `idx_al_created` (`created_at`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `system_settings`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `group` varchar(100) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_settings_key` (`key`),
  KEY `idx_settings_group` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `system_sequences`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `system_sequences`;
CREATE TABLE `system_sequences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `seq_name` varchar(50) NOT NULL,
  `next_val` bigint(20) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_seq_name` (`seq_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `warehouses`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_code` varchar(50) NOT NULL,
  `warehouse_name` varchar(150) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_warehouses_code` (`warehouse_code`),
  KEY `fk_warehouse_created_by` (`created_by`),
  CONSTRAINT `fk_warehouse_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `warehouse_zones`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `warehouse_zones`;
CREATE TABLE `warehouse_zones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warehouse_id` int(10) unsigned NOT NULL,
  `zone_code` varchar(50) NOT NULL,
  `zone_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_zones_code` (`zone_code`),
  KEY `fk_zone_warehouse` (`warehouse_id`),
  CONSTRAINT `fk_zone_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `warehouse_racks`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `warehouse_racks`;
CREATE TABLE `warehouse_racks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` int(10) unsigned NOT NULL,
  `rack_code` varchar(50) NOT NULL,
  `rack_name` varchar(150) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_racks_code` (`rack_code`),
  KEY `fk_rack_zone` (`zone_id`),
  CONSTRAINT `fk_rack_zone` FOREIGN KEY (`zone_id`) REFERENCES `warehouse_zones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `warehouse_shelves`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `warehouse_shelves`;
CREATE TABLE `warehouse_shelves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rack_id` int(10) unsigned NOT NULL,
  `shelf_code` varchar(50) NOT NULL,
  `shelf_name` varchar(150) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shelves_code` (`shelf_code`),
  KEY `fk_shelf_rack` (`rack_id`),
  CONSTRAINT `fk_shelf_rack` FOREIGN KEY (`rack_id`) REFERENCES `warehouse_racks` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `warehouse_bins`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `warehouse_bins`;
CREATE TABLE `warehouse_bins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shelf_id` int(10) unsigned NOT NULL,
  `bin_code` varchar(50) NOT NULL,
  `bin_name` varchar(150) NOT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bins_code` (`bin_code`),
  KEY `fk_bin_shelf` (`shelf_id`),
  CONSTRAINT `fk_bin_shelf` FOREIGN KEY (`shelf_id`) REFERENCES `warehouse_shelves` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `suppliers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `contact_person` varchar(150) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_type` enum('Debit','Credit') NOT NULL DEFAULT 'Credit',
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_terms` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_suppliers_code` (`supplier_code`),
  UNIQUE KEY `uq_suppliers_company` (`company_name`),
  KEY `idx_suppliers_code` (`supplier_code`),
  KEY `idx_suppliers_company` (`company_name`),
  KEY `idx_suppliers_status` (`status`),
  KEY `idx_suppliers_city` (`city`),
  KEY `idx_suppliers_country` (`country`),
  KEY `idx_suppliers_created_by` (`created_by`),
  KEY `idx_suppliers_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_supplier_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `customers`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) NOT NULL,
  `customer_type` enum('Individual','Business') NOT NULL DEFAULT 'Individual',
  `company_name` varchar(150) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `national_id` varchar(50) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_type` enum('Debit','Credit') NOT NULL DEFAULT 'Debit',
  `payment_terms` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_code` (`customer_code`),
  KEY `idx_customers_code` (`customer_code`),
  KEY `idx_customers_name` (`customer_name`),
  KEY `idx_customers_company` (`company_name`),
  KEY `idx_customers_mobile` (`mobile`),
  KEY `idx_customers_email` (`email`),
  KEY `idx_customers_status` (`status`),
  KEY `idx_customers_type` (`customer_type`),
  KEY `idx_customers_created_by` (`created_by`),
  KEY `idx_customers_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_customer_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `categories`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_code` (`category_code`),
  KEY `idx_cat_parent` (`parent_id`),
  KEY `idx_cat_name` (`category_name`),
  KEY `idx_cat_status` (`status`),
  KEY `idx_cat_sort` (`sort_order`),
  KEY `idx_cat_created_by` (`created_by`),
  KEY `idx_cat_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_category_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `brands`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_code` varchar(50) NOT NULL,
  `brand_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brand_code` (`brand_code`),
  UNIQUE KEY `uq_brand_name` (`brand_name`),
  KEY `idx_brand_name` (`brand_name`),
  KEY `idx_brand_status` (`status`),
  KEY `idx_brand_created_by` (`created_by`),
  KEY `idx_brand_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_brand_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `units`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_code` varchar(50) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `short_name` varchar(20) NOT NULL,
  `unit_type` varchar(50) DEFAULT NULL COMMENT 'e.g. Weight, Volume, Length, Quantity',
  `base_unit_id` int(10) unsigned DEFAULT NULL COMMENT 'Points to the base unit for conversion',
  `conversion_factor` decimal(18,8) NOT NULL DEFAULT 1.00000000,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unit_code` (`unit_code`),
  UNIQUE KEY `uq_unit_name` (`unit_name`),
  KEY `fk_unit_base` (`base_unit_id`),
  KEY `idx_unit_name` (`unit_name`),
  KEY `idx_unit_type` (`unit_type`),
  KEY `idx_unit_status` (`status`),
  KEY `idx_unit_created_by` (`created_by`),
  KEY `idx_unit_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_unit_base` FOREIGN KEY (`base_unit_id`) REFERENCES `units` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_unit_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `tax_rates`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tax_rates`;
CREATE TABLE `tax_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(100) NOT NULL,
  `tax_percentage` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `tax_type` enum('Inclusive','Exclusive') NOT NULL DEFAULT 'Exclusive',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tax_name` (`tax_name`),
  KEY `idx_tax_name` (`tax_name`),
  KEY `idx_tax_status` (`status`),
  KEY `idx_tax_created_by` (`created_by`),
  KEY `idx_tax_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_tax_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `currencies`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(100) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT 1.000000,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_currency_code` (`currency_code`),
  KEY `idx_currency_code` (`currency_code`),
  KEY `idx_currency_status` (`status`),
  KEY `idx_currency_is_default` (`is_default`),
  KEY `idx_currency_created_by` (`created_by`),
  KEY `idx_currency_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_currency_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_attributes`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE `product_attributes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_code` varchar(50) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attribute_code` (`attribute_code`),
  UNIQUE KEY `uq_attribute_name` (`attribute_name`),
  KEY `idx_attr_name` (`attribute_name`),
  KEY `idx_attr_status` (`status`),
  KEY `idx_attr_created_by` (`created_by`),
  KEY `idx_attr_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_attribute_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_attribute_values`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_attribute_values`;
CREATE TABLE `product_attribute_values` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` int(10) unsigned NOT NULL,
  `value` varchar(150) NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attr_val` (`attribute_id`,`value`),
  KEY `idx_attr_val_attribute` (`attribute_id`),
  KEY `idx_attr_val_status` (`status`),
  KEY `idx_attr_val_sort` (`sort_order`),
  KEY `idx_attr_val_created_by` (`created_by`),
  KEY `idx_attr_val_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_attr_val_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_attr_val_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table structure for `product_tags`
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `product_tags`;
CREATE TABLE `product_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_code` varchar(50) NOT NULL,
  `tag_name` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tag_code` (`tag_code`),
  UNIQUE KEY `uq_tag_name` (`tag_name`),
  KEY `idx_tag_name` (`tag_name`),
  KEY `idx_tag_status` (`status`),
  KEY `idx_tag_created_by` (`created_by`),
  KEY `idx_tag_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_tag_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Seed: roles
-- -------------------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_active`, `is_system`) VALUES
(1, 'Administrator', 'admin', 'Full system access with complete administrative privileges', 1, 1),
(2, 'Manager', 'manager', 'Warehouse and inventory operations manager', 1, 0),
(3, 'Staff', 'staff', 'Standard warehouse staff operator', 1, 0);

-- -------------------------------------------------------------
-- Seed: permissions
-- -------------------------------------------------------------
INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`) VALUES
(1, 'View Dashboard', 'dashboard.view', 'dashboard', 'Access the main dashboard'),
(2, 'Manage Users', 'users.manage', 'users', 'Create, update, delete users'),
(3, 'View Users', 'users.view', 'users', 'View user list'),
(4, 'Manage Roles', 'roles.manage', 'roles', 'Create, update, delete roles'),
(5, 'View Roles', 'roles.view', 'roles', 'View roles list'),
(6, 'Manage Permissions', 'permissions.manage', 'permissions', 'Manage permission assignments'),
(7, 'View Activity Logs', 'logs.view', 'logs', 'View system activity logs'),
(8, 'Manage Settings', 'settings.manage', 'settings', 'Update system settings'),
(9, 'View Settings', 'settings.view', 'settings', 'View system settings'),
(10, 'View Warehouses', 'warehouses.view', 'warehouse', 'View warehouse list'),
(11, 'Manage Warehouses', 'warehouses.manage', 'warehouse', 'Create, update, delete warehouses'),
(12, 'View Zones', 'zones.view', 'warehouse', 'View zones list'),
(13, 'Manage Zones', 'zones.manage', 'warehouse', 'Create, update, delete zones'),
(14, 'View Racks', 'racks.view', 'warehouse', 'View racks list'),
(15, 'Manage Racks', 'racks.manage', 'warehouse', 'Create, update, delete racks'),
(16, 'View Shelves', 'shelves.view', 'warehouse', 'View shelves list'),
(17, 'Manage Shelves', 'shelves.manage', 'warehouse', 'Create, update, delete shelves'),
(18, 'View Bins', 'bins.view', 'warehouse', 'View bins list'),
(19, 'Manage Bins', 'bins.manage', 'warehouse', 'Create, update, delete bins'),
(30, 'View Suppliers', 'suppliers.view', 'supplier', 'View supplier list and details'),
(31, 'Create Suppliers', 'suppliers.create', 'supplier', 'Add new suppliers'),
(32, 'Edit Suppliers', 'suppliers.edit', 'supplier', 'Modify existing suppliers'),
(33, 'Delete Suppliers', 'suppliers.delete', 'supplier', 'Soft delete suppliers'),
(34, 'Restore Suppliers', 'suppliers.restore', 'supplier', 'Restore soft-deleted suppliers'),
(35, 'View Customers', 'customers.view', 'customer', 'View customer list and details'),
(36, 'Create Customers', 'customers.create', 'customer', 'Add new customers'),
(37, 'Edit Customers', 'customers.edit', 'customer', 'Modify existing customers'),
(38, 'Delete Customers', 'customers.delete', 'customer', 'Soft delete customers'),
(39, 'Restore Customers', 'customers.restore', 'customer', 'Restore soft-deleted customers'),
(40, 'View Categories', 'categories.view', 'category', 'View category list'),
(41, 'Create Categories', 'categories.create', 'category', 'Add new categories'),
(42, 'Edit Categories', 'categories.edit', 'category', 'Edit categories'),
(43, 'Delete Categories', 'categories.delete', 'category', 'Soft delete categories'),
(44, 'Restore Categories', 'categories.restore', 'category', 'Restore deleted categories'),
(45, 'View Brands', 'brands.view', 'brand', 'View brand list'),
(46, 'Create Brands', 'brands.create', 'brand', 'Add new brands'),
(47, 'Edit Brands', 'brands.edit', 'brand', 'Edit brands'),
(48, 'Delete Brands', 'brands.delete', 'brand', 'Soft delete brands'),
(49, 'Restore Brands', 'brands.restore', 'brand', 'Restore deleted brands'),
(50, 'View Units', 'units.view', 'unit', 'View unit list'),
(51, 'Create Units', 'units.create', 'unit', 'Add new units'),
(52, 'Edit Units', 'units.edit', 'unit', 'Edit units'),
(53, 'Delete Units', 'units.delete', 'unit', 'Soft delete units'),
(54, 'Restore Units', 'units.restore', 'unit', 'Restore deleted units'),
(55, 'View Tax Rates', 'tax_rates.view', 'tax_rate', 'View tax rate list'),
(56, 'Create Tax Rates', 'tax_rates.create', 'tax_rate', 'Add new tax rates'),
(57, 'Edit Tax Rates', 'tax_rates.edit', 'tax_rate', 'Edit tax rates'),
(58, 'Delete Tax Rates', 'tax_rates.delete', 'tax_rate', 'Soft delete tax rates'),
(59, 'Restore Tax Rates', 'tax_rates.restore', 'tax_rate', 'Restore deleted tax rates'),
(60, 'View Currencies', 'currencies.view', 'currency', 'View currency list'),
(61, 'Create Currencies', 'currencies.create', 'currency', 'Add new currencies'),
(62, 'Edit Currencies', 'currencies.edit', 'currency', 'Edit currencies'),
(63, 'Delete Currencies', 'currencies.delete', 'currency', 'Soft delete currencies'),
(64, 'Restore Currencies', 'currencies.restore', 'currency', 'Restore deleted currencies'),
(65, 'View Attributes', 'attributes.view', 'attribute', 'View attribute list'),
(66, 'Create Attributes', 'attributes.create', 'attribute', 'Add new attributes'),
(67, 'Edit Attributes', 'attributes.edit', 'attribute', 'Edit attributes'),
(68, 'Delete Attributes', 'attributes.delete', 'attribute', 'Soft delete attributes'),
(69, 'Restore Attributes', 'attributes.restore', 'attribute', 'Restore deleted attributes'),
(70, 'View Attribute Values', 'attribute_values.view', 'attribute', 'View attribute values'),
(71, 'Create Attribute Values', 'attribute_values.create', 'attribute', 'Add attribute values'),
(72, 'Edit Attribute Values', 'attribute_values.edit', 'attribute', 'Edit attribute values'),
(73, 'Delete Attribute Values', 'attribute_values.delete', 'attribute', 'Delete attribute values'),
(74, 'Restore Attribute Values', 'attribute_values.restore', 'attribute', 'Restore deleted attribute values'),
(75, 'View Product Tags', 'product_tags.view', 'tag', 'View product tag list'),
(76, 'Create Product Tags', 'product_tags.create', 'tag', 'Add new product tags'),
(77, 'Edit Product Tags', 'product_tags.edit', 'tag', 'Edit product tags'),
(78, 'Delete Product Tags', 'product_tags.delete', 'tag', 'Soft delete product tags'),
(79, 'Restore Product Tags', 'product_tags.restore', 'tag', 'Restore deleted product tags'),
(81, 'Edit Settings', 'settings.edit', 'settings', 'Update system settings'),
(82, 'View Activity Logs', 'activity_logs.view', 'logs', 'View system activity logs');

-- -------------------------------------------------------------
-- Seed: role_permissions
-- -------------------------------------------------------------
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 47),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(1, 62),
(1, 63),
(1, 64),
(1, 65),
(1, 66),
(1, 67),
(1, 68),
(1, 69),
(1, 70),
(1, 71),
(1, 72),
(1, 73),
(1, 74),
(1, 75),
(1, 76),
(1, 77),
(1, 78),
(1, 79),
(1, 81),
(1, 82),
(2, 1),
(2, 3),
(2, 5),
(2, 7),
(2, 9),
(2, 82),
(3, 1);

-- -------------------------------------------------------------
-- Seed: system_settings
-- -------------------------------------------------------------
INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group`, `description`) VALUES
(1, 'app_name', 'Warehouse Management System', 'string', 'general', 'Application name'),
(2, 'app_logo', '', 'string', 'branding', 'Logo file path'),
(3, 'items_per_page', '25', 'integer', 'general', 'Default pagination limit'),
(4, 'session_timeout', '3600', 'integer', 'security', 'Session timeout in seconds'),
(5, 'enable_activity_log', '1', 'boolean', 'security', 'Log all user activities'),
(6, 'maintenance_mode', '0', 'boolean', 'general', 'Put site into maintenance mode'),
(7, 'company_name', 'WMS Logistics Inc.', 'string', 'general', 'Registered company name'),
(8, 'company_email', 'info@wmslogistics.com', 'string', 'general', 'Company contact email'),
(9, 'company_phone', '+1 (555) 019-2834', 'string', 'general', 'Company phone number'),
(10, 'company_address', '123 Supply Chain Ave, Suite 400, Logistics City', 'string', 'general', 'Company physical address'),
(11, 'company_website', 'https://wmslogistics.example.com', 'string', 'general', 'Company official website'),
(12, 'default_currency_id', '1', 'integer', 'localization', 'Default system currency ID'),
(13, 'timezone', 'Asia/Dhaka', 'string', 'localization', 'Application timezone'),
(14, 'date_format', 'd M Y', 'string', 'localization', 'Default date display format'),
(15, 'time_format', 'h:i A', 'string', 'localization', 'Default time display format'),
(16, 'default_warehouse_id', '', 'integer', 'warehouse', 'Default warehouse ID for operations'),
(17, 'app_favicon', '', 'string', 'branding', 'Favicon image path');

-- -------------------------------------------------------------
-- Seed: system_sequences
-- -------------------------------------------------------------
INSERT INTO `system_sequences` (`id`, `seq_name`, `next_val`) VALUES
(1, 'supplier_code', 1),
(2, 'customer_code', 1),
(3, 'category_code', 1),
(4, 'brand_code', 1),
(5, 'unit_code', 1),
(6, 'tax_rate_code', 1),
(7, 'currency_code', 1);

-- -------------------------------------------------------------
-- Seed: currencies
-- -------------------------------------------------------------
INSERT INTO `currencies` (`id`, `currency_code`, `currency_name`, `currency_symbol`, `exchange_rate`, `is_default`, `status`) VALUES
(1, 'USD', 'US Dollar', '$', 1.000000, 1, 'active'),
(2, 'EUR', 'Euro', '€', 0.920000, 0, 'active'),
(3, 'GBP', 'British Pound', '£', 0.790000, 0, 'active'),
(4, 'BDT', 'Bangladeshi Taka', '৳', 120.000000, 0, 'active');

-- -------------------------------------------------------------
-- Seed: units
-- -------------------------------------------------------------
INSERT INTO `units` (`id`, `unit_code`, `unit_name`, `short_name`, `unit_type`, `conversion_factor`, `status`) VALUES
(1, 'UNT-0001', 'Piece', 'pc', 'Quantity', 1.00000000, 'active'),
(2, 'UNT-0002', 'Kilogram', 'kg', 'Weight', 1.00000000, 'active'),
(3, 'UNT-0003', 'Gram', 'g', 'Weight', 0.00100000, 'active'),
(4, 'UNT-0004', 'Box', 'box', 'Quantity', 1.00000000, 'active'),
(5, 'UNT-0005', 'Carton', 'ctn', 'Quantity', 1.00000000, 'active'),
(6, 'UNT-0006', 'Meter', 'm', 'Length', 1.00000000, 'active'),
(7, 'UNT-0007', 'Liter', 'L', 'Volume', 1.00000000, 'active');

-- -------------------------------------------------------------
-- Seed: tax_rates
-- -------------------------------------------------------------
INSERT INTO `tax_rates` (`id`, `tax_name`, `tax_percentage`, `tax_type`, `status`) VALUES
(1, 'Zero Tax (0%)', 0.0000, 'Exclusive', 'active'),
(2, 'Standard VAT (5%)', 5.0000, 'Exclusive', 'active'),
(3, 'Standard VAT (10%)', 10.0000, 'Exclusive', 'active'),
(4, 'Standard VAT (15%)', 15.0000, 'Exclusive', 'active');

SET FOREIGN_KEY_CHECKS = 1;
