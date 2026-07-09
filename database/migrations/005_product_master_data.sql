-- ============================================================
-- Phase 05: Product Master Data Migration
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Initialize sequences
INSERT IGNORE INTO `system_sequences` (`seq_name`, `next_val`) VALUES
('category_code',  1),
('brand_code',     1),
('unit_code',      1),
('attribute_code', 1),
('tag_code',       1);

-- ─────────────────────────────────────────────────────────────
-- Table: categories (unlimited parent-child hierarchy)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id`     INT UNSIGNED     NULL,
    `category_code` VARCHAR(50)  NOT NULL,
    `category_name` VARCHAR(150) NOT NULL,
    `description`   TEXT             NULL,
    `image`         VARCHAR(255)     NULL,
    `sort_order`    INT UNSIGNED NOT NULL DEFAULT 0,
    `status`        ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`    INT UNSIGNED     NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`    DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_category_code` (`category_code`),
    CONSTRAINT `fk_category_parent`
        FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_category_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_cat_parent`     (`parent_id`),
    INDEX `idx_cat_name`       (`category_name`),
    INDEX `idx_cat_status`     (`status`),
    INDEX `idx_cat_sort`       (`sort_order`),
    INDEX `idx_cat_created_by` (`created_by`),
    INDEX `idx_cat_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: brands
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `brands` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `brand_code`  VARCHAR(50)  NOT NULL,
    `brand_name`  VARCHAR(150) NOT NULL,
    `description` TEXT             NULL,
    `logo`        VARCHAR(255)     NULL,
    `website`     VARCHAR(191)     NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`  INT UNSIGNED     NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`  DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_brand_code` (`brand_code`),
    UNIQUE KEY `uq_brand_name` (`brand_name`),
    CONSTRAINT `fk_brand_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_brand_name`       (`brand_name`),
    INDEX `idx_brand_status`     (`status`),
    INDEX `idx_brand_created_by` (`created_by`),
    INDEX `idx_brand_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: units (with conversion support)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `units` (
    `id`                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `unit_code`         VARCHAR(50)   NOT NULL,
    `unit_name`         VARCHAR(100)  NOT NULL,
    `short_name`        VARCHAR(20)   NOT NULL,
    `unit_type`         VARCHAR(50)       NULL COMMENT 'e.g. Weight, Volume, Length, Quantity',
    `base_unit_id`      INT UNSIGNED      NULL COMMENT 'Points to the base unit for conversion',
    `conversion_factor` DECIMAL(18,8) NOT NULL DEFAULT 1.00000000,
    `status`            ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`        INT UNSIGNED      NULL,
    `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`        DATETIME          NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_unit_code`  (`unit_code`),
    UNIQUE KEY `uq_unit_name`  (`unit_name`),
    CONSTRAINT `fk_unit_base`
        FOREIGN KEY (`base_unit_id`) REFERENCES `units`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_unit_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_unit_name`       (`unit_name`),
    INDEX `idx_unit_type`       (`unit_type`),
    INDEX `idx_unit_status`     (`status`),
    INDEX `idx_unit_created_by` (`created_by`),
    INDEX `idx_unit_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: tax_rates
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `tax_rates` (
    `id`             INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `tax_name`       VARCHAR(100)   NOT NULL,
    `tax_percentage` DECIMAL(8,4)   NOT NULL DEFAULT 0.0000,
    `tax_type`       ENUM('Inclusive','Exclusive') NOT NULL DEFAULT 'Exclusive',
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`     INT UNSIGNED       NULL,
    `created_at`     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME           NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tax_name` (`tax_name`),
    CONSTRAINT `fk_tax_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_tax_name`       (`tax_name`),
    INDEX `idx_tax_status`     (`status`),
    INDEX `idx_tax_created_by` (`created_by`),
    INDEX `idx_tax_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: currencies (only one can be default)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `currencies` (
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `currency_code`   VARCHAR(10)   NOT NULL,
    `currency_name`   VARCHAR(100)  NOT NULL,
    `currency_symbol` VARCHAR(10)   NOT NULL,
    `exchange_rate`   DECIMAL(18,6) NOT NULL DEFAULT 1.000000,
    `is_default`      TINYINT(1)    NOT NULL DEFAULT 0,
    `status`          ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`      INT UNSIGNED      NULL,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME          NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_currency_code` (`currency_code`),
    CONSTRAINT `fk_currency_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_currency_code`       (`currency_code`),
    INDEX `idx_currency_status`     (`status`),
    INDEX `idx_currency_is_default` (`is_default`),
    INDEX `idx_currency_created_by` (`created_by`),
    INDEX `idx_currency_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: product_attributes
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_attributes` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attribute_code` VARCHAR(50)  NOT NULL,
    `attribute_name` VARCHAR(100) NOT NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`     INT UNSIGNED     NULL,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attribute_code` (`attribute_code`),
    UNIQUE KEY `uq_attribute_name` (`attribute_name`),
    CONSTRAINT `fk_attribute_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_attr_name`       (`attribute_name`),
    INDEX `idx_attr_status`     (`status`),
    INDEX `idx_attr_created_by` (`created_by`),
    INDEX `idx_attr_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: product_attribute_values
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attribute_id` INT UNSIGNED NOT NULL,
    `value`        VARCHAR(150) NOT NULL,
    `sort_order`   INT UNSIGNED NOT NULL DEFAULT 0,
    `status`       ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`   INT UNSIGNED     NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`   DATETIME         NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_attr_val_attribute`
        FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_attr_val_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY `uq_attr_val` (`attribute_id`, `value`),
    INDEX `idx_attr_val_attribute`  (`attribute_id`),
    INDEX `idx_attr_val_status`     (`status`),
    INDEX `idx_attr_val_sort`       (`sort_order`),
    INDEX `idx_attr_val_created_by` (`created_by`),
    INDEX `idx_attr_val_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: product_tags
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_tags` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tag_code`   VARCHAR(50)  NOT NULL,
    `tag_name`   VARCHAR(100) NOT NULL,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by` INT UNSIGNED     NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_tag_code` (`tag_code`),
    UNIQUE KEY `uq_tag_name` (`tag_name`),
    CONSTRAINT `fk_tag_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX `idx_tag_name`       (`tag_name`),
    INDEX `idx_tag_status`     (`status`),
    INDEX `idx_tag_created_by` (`created_by`),
    INDEX `idx_tag_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Permissions for Product Master Data
-- ─────────────────────────────────────────────────────────────
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
-- Categories
('View Categories',           'categories.view',             'category',  'View category list'),
('Create Categories',         'categories.create',           'category',  'Add new categories'),
('Edit Categories',           'categories.edit',             'category',  'Edit categories'),
('Delete Categories',         'categories.delete',           'category',  'Soft delete categories'),
('Restore Categories',        'categories.restore',          'category',  'Restore deleted categories'),
-- Brands
('View Brands',               'brands.view',                 'brand',     'View brand list'),
('Create Brands',             'brands.create',               'brand',     'Add new brands'),
('Edit Brands',               'brands.edit',                 'brand',     'Edit brands'),
('Delete Brands',             'brands.delete',               'brand',     'Soft delete brands'),
('Restore Brands',            'brands.restore',              'brand',     'Restore deleted brands'),
-- Units
('View Units',                'units.view',                  'unit',      'View unit list'),
('Create Units',              'units.create',                'unit',      'Add new units'),
('Edit Units',                'units.edit',                  'unit',      'Edit units'),
('Delete Units',              'units.delete',                'unit',      'Soft delete units'),
('Restore Units',             'units.restore',               'unit',      'Restore deleted units'),
-- Tax Rates
('View Tax Rates',            'tax_rates.view',              'tax_rate',  'View tax rate list'),
('Create Tax Rates',          'tax_rates.create',            'tax_rate',  'Add new tax rates'),
('Edit Tax Rates',            'tax_rates.edit',              'tax_rate',  'Edit tax rates'),
('Delete Tax Rates',          'tax_rates.delete',            'tax_rate',  'Soft delete tax rates'),
('Restore Tax Rates',         'tax_rates.restore',           'tax_rate',  'Restore deleted tax rates'),
-- Currencies
('View Currencies',           'currencies.view',             'currency',  'View currency list'),
('Create Currencies',         'currencies.create',           'currency',  'Add new currencies'),
('Edit Currencies',           'currencies.edit',             'currency',  'Edit currencies'),
('Delete Currencies',         'currencies.delete',           'currency',  'Soft delete currencies'),
('Restore Currencies',        'currencies.restore',          'currency',  'Restore deleted currencies'),
-- Product Attributes
('View Attributes',           'attributes.view',             'attribute', 'View attribute list'),
('Create Attributes',         'attributes.create',           'attribute', 'Add new attributes'),
('Edit Attributes',           'attributes.edit',             'attribute', 'Edit attributes'),
('Delete Attributes',         'attributes.delete',           'attribute', 'Soft delete attributes'),
('Restore Attributes',        'attributes.restore',          'attribute', 'Restore deleted attributes'),
-- Attribute Values
('View Attribute Values',     'attribute_values.view',       'attribute', 'View attribute values'),
('Create Attribute Values',   'attribute_values.create',     'attribute', 'Add attribute values'),
('Edit Attribute Values',     'attribute_values.edit',       'attribute', 'Edit attribute values'),
('Delete Attribute Values',   'attribute_values.delete',     'attribute', 'Delete attribute values'),
('Restore Attribute Values',  'attribute_values.restore',    'attribute', 'Restore deleted attribute values'),
-- Tags
('View Product Tags',         'product_tags.view',           'tag',       'View product tag list'),
('Create Product Tags',       'product_tags.create',         'tag',       'Add new product tags'),
('Edit Product Tags',         'product_tags.edit',           'tag',       'Edit product tags'),
('Delete Product Tags',       'product_tags.delete',         'tag',       'Soft delete product tags'),
('Restore Product Tags',      'product_tags.restore',        'tag',       'Restore deleted product tags');

-- Assign all new permissions to Administrator (role_id = 1)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`
WHERE `module` IN ('category','brand','unit','tax_rate','currency','attribute','tag');

SET FOREIGN_KEY_CHECKS = 1;
