-- ============================================================
-- Phase 04: Customer Management Migration
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Initialize customer sequence
INSERT IGNORE INTO `system_sequences` (`seq_name`, `next_val`) VALUES ('customer_code', 1);

-- ─────────────────────────────────────────────────────────────
-- Table: customers
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `customers` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_code`    VARCHAR(50)  NOT NULL,
    `customer_type`    ENUM('Individual','Business') NOT NULL DEFAULT 'Individual',
    `company_name`     VARCHAR(150)     NULL,
    `customer_name`    VARCHAR(150) NOT NULL,
    `email`            VARCHAR(191)     NULL,
    `phone`            VARCHAR(30)      NULL,
    `mobile`           VARCHAR(30)      NULL,
    `website`          VARCHAR(191)     NULL,
    `tax_number`       VARCHAR(50)      NULL,
    `national_id`      VARCHAR(50)      NULL,
    `trade_license`    VARCHAR(100)     NULL,
    `country`          VARCHAR(100)     NULL,
    `state`            VARCHAR(100)     NULL,
    `city`             VARCHAR(100)     NULL,
    `zip_code`         VARCHAR(20)      NULL,
    `address`          TEXT             NULL,
    `shipping_address` TEXT             NULL,
    `credit_limit`     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `opening_balance`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `current_balance`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `balance_type`     ENUM('Debit','Credit') NOT NULL DEFAULT 'Debit',
    `payment_terms`    VARCHAR(255)     NULL,
    `notes`            TEXT             NULL,
    `status`           ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`       INT UNSIGNED     NULL,
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customers_code` (`customer_code`),
    CONSTRAINT `fk_customer_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes for performance
    INDEX `idx_customers_code` (`customer_code`),
    INDEX `idx_customers_name` (`customer_name`),
    INDEX `idx_customers_company` (`company_name`),
    INDEX `idx_customers_mobile` (`mobile`),
    INDEX `idx_customers_email` (`email`),
    INDEX `idx_customers_status` (`status`),
    INDEX `idx_customers_type` (`customer_type`),
    INDEX `idx_customers_created_by` (`created_by`),
    INDEX `idx_customers_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Permissions for Customer Management
-- ─────────────────────────────────────────────────────────────
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
('View Customers',     'customers.view',     'customer', 'View customer list and details'),
('Create Customers',   'customers.create',   'customer', 'Add new customers'),
('Edit Customers',     'customers.edit',     'customer', 'Modify existing customers'),
('Delete Customers',   'customers.delete',   'customer', 'Soft delete customers'),
('Restore Customers',  'customers.restore',  'customer', 'Restore soft-deleted customers');

-- Assign new permissions to the Administrator role (role_id = 1)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `module` = 'customer';

SET FOREIGN_KEY_CHECKS = 1;
