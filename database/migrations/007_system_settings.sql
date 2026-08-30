-- ============================================================
-- Migration: 007_system_settings.sql
-- Warehouse Management System — Phase 05.2
-- ============================================================

DELIMITER //

CREATE PROCEDURE AddColumnIfNotExists(
    IN dbName VARCHAR(255),
    IN tableName VARCHAR(255),
    IN columnName VARCHAR(255),
    IN columnDefinition TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = dbName
        AND TABLE_NAME = tableName 
        AND COLUMN_NAME = columnName
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', tableName, '` ADD COLUMN `', columnName, '` ', columnDefinition);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //

DELIMITER ;

-- 1. Ensure system_settings table columns
CALL AddColumnIfNotExists(DATABASE(), 'system_settings', 'created_at', 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
CALL AddColumnIfNotExists(DATABASE(), 'system_settings', 'updated_by', 'INT UNSIGNED NULL AFTER `description`');

-- Add foreign key constraint for updated_by if not exists
SET @fk_exists = (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'system_settings'
    AND CONSTRAINT_NAME = 'fk_settings_updated_by'
);

SET @sql_fk = IF(@fk_exists = 0,
    'ALTER TABLE `system_settings` ADD CONSTRAINT `fk_settings_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON UPDATE CASCADE ON DELETE SET NULL;',
    'SELECT 1;'
);
PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk;

-- 2. Seed Default Settings
INSERT INTO `system_settings` (`key`, `value`, `type`, `group`, `description`) VALUES
('company_name',        'WMS Logistics Inc.',          'string',  'general',      'Registered company name'),
('company_email',       'info@wmslogistics.com',       'string',  'general',      'Company contact email'),
('company_phone',       '+1 (555) 019-2834',           'string',  'general',      'Company phone number'),
('company_address',     '123 Supply Chain Ave, Suite 400, Logistics City', 'string', 'general', 'Company physical address'),
('company_website',     'https://wmslogistics.example.com', 'string', 'general',  'Company official website'),
('default_currency_id', '1',                           'integer', 'localization', 'Default system currency ID'),
('timezone',            'Asia/Dhaka',                  'string',  'localization', 'Application timezone'),
('date_format',         'd M Y',                       'string',  'localization', 'Default date display format'),
('time_format',         'h:i A',                       'string',  'localization', 'Default time display format'),
('default_warehouse_id','',                            'integer', 'warehouse',    'Default warehouse ID for operations'),
('app_favicon',         '',                            'string',  'branding',     'Favicon image path')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- Update group for app_name and app_logo if needed
UPDATE `system_settings` SET `group` = 'general' WHERE `key` = 'app_name';
UPDATE `system_settings` SET `group` = 'branding' WHERE `key` = 'app_logo';

-- 3. Ensure permissions settings.view and settings.edit exist
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
('View Settings',   'settings.view', 'settings', 'View system settings'),
('Edit Settings',   'settings.edit', 'settings', 'Update system settings')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`);

-- Assign settings permissions to Administrator role (id = 1)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `slug` IN ('settings.view', 'settings.edit', 'settings.manage');

-- Drop the temporary procedure
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;
