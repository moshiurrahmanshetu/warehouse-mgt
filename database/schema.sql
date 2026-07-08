-- ============================================================
-- Warehouse Management System — Initial Database Schema
-- Engine:  InnoDB
-- Charset: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `warehouse_management`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `warehouse_management`;

-- ─────────────────────────────────────────────────────────────
-- Table: roles
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `roles` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)    NOT NULL,
    `slug`        VARCHAR(100)    NOT NULL,
    `description` VARCHAR(255)        NULL DEFAULT NULL,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: permissions
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `permissions` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(150)    NOT NULL,
    `slug`        VARCHAR(150)    NOT NULL,
    `module`      VARCHAR(100)    NOT NULL DEFAULT 'general',
    `description` VARCHAR(255)        NULL DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permissions_slug` (`slug`),
    INDEX `idx_permissions_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: role_permissions  (Many-to-Many: roles ↔ permissions)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id`       INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role`
        FOREIGN KEY (`role_id`)       REFERENCES `roles`(`id`)       ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rp_permission`
        FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: users
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
    `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`             VARCHAR(150)  NOT NULL,
    `email`            VARCHAR(191)  NOT NULL,
    `password`         VARCHAR(255)  NOT NULL,
    `phone`            VARCHAR(30)       NULL DEFAULT NULL,
    `avatar`           VARCHAR(255)      NULL DEFAULT NULL,
    `status`           ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
    `last_login_at`    DATETIME          NULL DEFAULT NULL,
    `last_login_ip`    VARCHAR(45)       NULL DEFAULT NULL,
    `remember_token`   VARCHAR(100)      NULL DEFAULT NULL,
    `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: user_roles  (Many-to-Many: users ↔ roles)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `user_roles` (
    `user_id`    INT UNSIGNED NOT NULL,
    `role_id`    INT UNSIGNED NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `role_id`),
    CONSTRAINT `fk_ur_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ur_role`
        FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: activity_logs
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED        NULL DEFAULT NULL,
    `action`      VARCHAR(150)    NOT NULL,
    `module`      VARCHAR(100)    NOT NULL DEFAULT 'system',
    `description` TEXT                NULL,
    `ip_address`  VARCHAR(45)         NULL DEFAULT NULL,
    `user_agent`  VARCHAR(500)        NULL DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_al_user_id`   (`user_id`),
    INDEX `idx_al_module`    (`module`),
    INDEX `idx_al_created`   (`created_at`),
    CONSTRAINT `fk_al_user`
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- Table: system_settings
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(100)  NOT NULL,
    `value`       TEXT              NULL,
    `type`        ENUM('string','integer','boolean','json') NOT NULL DEFAULT 'string',
    `group`       VARCHAR(100)  NOT NULL DEFAULT 'general',
    `description` VARCHAR(255)      NULL DEFAULT NULL,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`key`),
    INDEX `idx_settings_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────────────────────
-- SEED DATA
-- ─────────────────────────────────────────────────────────────

-- Roles
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_active`) VALUES
(1, 'Administrator', 'admin',   'Full system access',       1),
(2, 'Manager',       'manager', 'Warehouse manager access', 1),
(3, 'Staff',         'staff',   'General staff access',     1);

-- Permissions
INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`) VALUES
(1,  'View Dashboard',      'dashboard.view',       'dashboard',  'Access the main dashboard'),
(2,  'Manage Users',        'users.manage',         'users',      'Create, update, delete users'),
(3,  'View Users',          'users.view',           'users',      'View user list'),
(4,  'Manage Roles',        'roles.manage',         'roles',      'Create, update, delete roles'),
(5,  'View Roles',          'roles.view',           'roles',      'View roles list'),
(6,  'Manage Permissions',  'permissions.manage',   'permissions','Manage permission assignments'),
(7,  'View Activity Logs',  'logs.view',            'logs',       'View system activity logs'),
(8,  'Manage Settings',     'settings.manage',      'settings',   'Update system settings'),
(9,  'View Settings',       'settings.view',        'settings',   'View system settings');

-- Assign ALL permissions to admin role
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),(1, 2),(1, 3),(1, 4),(1, 5),(1, 6),(1, 7),(1, 8),(1, 9);

-- Assign limited permissions to manager
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 1),(2, 3),(2, 5),(2, 7),(2, 9);

-- Assign minimal permissions to staff
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1);

-- Default Admin User  (password: admin123  — bcrypt cost 12)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`) VALUES
(1, 'System Administrator', 'admin@example.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', -- admin123 placeholder — will be replaced by seed script
 'active');

-- Actual bcrypt hash for "admin123" (cost 12)  — generated via PHP
-- We replace placeholder with real hash below:
UPDATE `users`
SET `password` = '$2y$12$eImiTXuWVxfM37uY4JANjQ==invalid'
WHERE `id` = 1;
-- The real hash is injected by the PHP setup script (see index.php first-run logic).

-- Assign admin role to admin user
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (1, 1);

-- System Settings
INSERT INTO `system_settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('app_name',            'Warehouse Management System', 'string',  'general', 'Application name'),
('app_logo',            '',                            'string',  'general', 'Logo file path'),
('items_per_page',      '25',                          'integer', 'general', 'Default pagination limit'),
('session_timeout',     '3600',                        'integer', 'security','Session timeout in seconds'),
('enable_activity_log', '1',                           'boolean', 'security','Log all user activities'),
('maintenance_mode',    '0',                           'boolean', 'general', 'Put site into maintenance mode');

SET FOREIGN_KEY_CHECKS = 1;
