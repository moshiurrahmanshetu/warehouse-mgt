-- ============================================================
-- Phase 02: Warehouse Structure Management Migration
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────────────────────
-- Table: warehouses
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warehouses` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `warehouse_code` VARCHAR(50)  NOT NULL,
    `warehouse_name` VARCHAR(150) NOT NULL,
    `contact_person` VARCHAR(150)     NULL,
    `phone`          VARCHAR(30)      NULL,
    `email`          VARCHAR(191)     NULL,
    `address`        TEXT             NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_by`     INT UNSIGNED     NULL,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_warehouses_code` (`warehouse_code`),
    CONSTRAINT `fk_warehouse_created_by`
        FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: warehouse_zones
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warehouse_zones` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `warehouse_id`   INT UNSIGNED NOT NULL,
    `zone_code`      VARCHAR(50)  NOT NULL,
    `zone_name`      VARCHAR(150) NOT NULL,
    `description`    TEXT             NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_zones_code` (`zone_code`),
    CONSTRAINT `fk_zone_warehouse`
        FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: warehouse_racks
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warehouse_racks` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `zone_id`        INT UNSIGNED NOT NULL,
    `rack_code`      VARCHAR(50)  NOT NULL,
    `rack_name`      VARCHAR(150) NOT NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_racks_code` (`rack_code`),
    CONSTRAINT `fk_rack_zone`
        FOREIGN KEY (`zone_id`) REFERENCES `warehouse_zones`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: warehouse_shelves
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warehouse_shelves` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rack_id`        INT UNSIGNED NOT NULL,
    `shelf_code`     VARCHAR(50)  NOT NULL,
    `shelf_name`     VARCHAR(150) NOT NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_shelves_code` (`shelf_code`),
    CONSTRAINT `fk_shelf_rack`
        FOREIGN KEY (`rack_id`) REFERENCES `warehouse_racks`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: warehouse_bins
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `warehouse_bins` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `shelf_id`       INT UNSIGNED NOT NULL,
    `bin_code`       VARCHAR(50)  NOT NULL,
    `bin_name`       VARCHAR(150) NOT NULL,
    `capacity`       DECIMAL(10,2)    NULL,
    `status`         ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME         NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bins_code` (`bin_code`),
    CONSTRAINT `fk_bin_shelf`
        FOREIGN KEY (`shelf_id`) REFERENCES `warehouse_shelves`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Permissions for Warehouse Management
-- ─────────────────────────────────────────────────────────────
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
('View Warehouses',    'warehouses.view',    'warehouse', 'View warehouse list'),
('Manage Warehouses',  'warehouses.manage',  'warehouse', 'Create, update, delete warehouses'),
('View Zones',         'zones.view',         'warehouse', 'View zones list'),
('Manage Zones',       'zones.manage',       'warehouse', 'Create, update, delete zones'),
('View Racks',         'racks.view',         'warehouse', 'View racks list'),
('Manage Racks',       'racks.manage',       'warehouse', 'Create, update, delete racks'),
('View Shelves',       'shelves.view',       'warehouse', 'View shelves list'),
('Manage Shelves',     'shelves.manage',     'warehouse', 'Create, update, delete shelves'),
('View Bins',          'bins.view',          'warehouse', 'View bins list'),
('Manage Bins',        'bins.manage',        'warehouse', 'Create, update, delete bins');

-- Assign new permissions to the Administrator role (role_id = 1)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `module` = 'warehouse';

SET FOREIGN_KEY_CHECKS = 1;
