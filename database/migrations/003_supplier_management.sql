-- ============================================================
-- Phase 03: Supplier Management Migration
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────────────────────
-- Table: system_sequences (for sequence-safe code generation)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `system_sequences` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `seq_name`   VARCHAR(50)  NOT NULL,
    `next_val`   BIGINT UNSIGNED NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_seq_name` (`seq_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initialize supplier sequence
INSERT IGNORE INTO `system_sequences` (`seq_name`, `next_val`) VALUES ('supplier_code', 1);

-- ─────────────────────────────────────────────────────────────
-- Table: suppliers
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `suppliers` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `supplier_code`   VARCHAR(50)  NOT NULL,
    `company_name`    VARCHAR(150) NOT NULL,
    `contact_person`  VARCHAR(150)     NULL,
    `email`           VARCHAR(191)     NULL,
    `phone`           VARCHAR(30)      NULL,
    `mobile`          VARCHAR(30)      NULL,
    `website`         VARCHAR(191)     NULL,
    `tax_number`      VARCHAR(50)      NULL,
    `trade_license`   VARCHAR(100)     NULL,
    `country`         VARCHAR(100)     NULL,
    `state`           VARCHAR(100)     NULL,
    `city`            VARCHAR(100)     NULL,
    `zip_code`        VARCHAR(20)      NULL,
    `address`         TEXT             NULL,
    `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `balance_type`    ENUM('Debit','Credit') NOT NULL DEFAULT 'Credit',
    `credit_limit`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `payment_terms`   VARCHAR(255)     NULL,
    `notes`           TEXT             NULL,
    `status`          ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`      INT UNSIGNED     NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_suppliers_code` (`supplier_code`),
    UNIQUE KEY `uq_suppliers_company` (`company_name`),
    CONSTRAINT `fk_supplier_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes for performance as requested
    INDEX `idx_suppliers_code` (`supplier_code`),
    INDEX `idx_suppliers_company` (`company_name`),
    INDEX `idx_suppliers_status` (`status`),
    INDEX `idx_suppliers_city` (`city`),
    INDEX `idx_suppliers_country` (`country`),
    INDEX `idx_suppliers_created_by` (`created_by`),
    INDEX `idx_suppliers_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Permissions for Supplier Management
-- ─────────────────────────────────────────────────────────────
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
('View Suppliers',     'suppliers.view',     'supplier', 'View supplier list and details'),
('Create Suppliers',   'suppliers.create',   'supplier', 'Add new suppliers'),
('Edit Suppliers',     'suppliers.edit',     'supplier', 'Modify existing suppliers'),
('Delete Suppliers',   'suppliers.delete',   'supplier', 'Soft delete suppliers'),
('Restore Suppliers',  'suppliers.restore',  'supplier', 'Restore soft-deleted suppliers');

-- Assign new permissions to the Administrator role (role_id = 1)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `module` = 'supplier';

SET FOREIGN_KEY_CHECKS = 1;
