-- ============================================================
-- Migration: 008_activity_logs_permissions.sql
-- Warehouse Management System
-- ============================================================

-- Ensure logs.view and activity_logs.view permissions exist
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`) VALUES
('View Activity Logs', 'logs.view',          'logs', 'View system activity logs'),
('View Activity Logs', 'activity_logs.view', 'logs', 'View system activity logs')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`);

-- Assign to Administrator (role_id = 1) and Manager (role_id = 2)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `slug` IN ('logs.view', 'activity_logs.view');

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM `permissions` WHERE `slug` IN ('logs.view', 'activity_logs.view');
