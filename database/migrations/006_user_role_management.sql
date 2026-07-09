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

-- Users table
CALL AddColumnIfNotExists(DATABASE(), 'users', 'avatar', 'VARCHAR(255) NULL');
CALL AddColumnIfNotExists(DATABASE(), 'users', 'last_login', 'DATETIME NULL');
CALL AddColumnIfNotExists(DATABASE(), 'users', 'last_activity', 'DATETIME NULL');
CALL AddColumnIfNotExists(DATABASE(), 'users', 'is_active', 'TINYINT(1) NOT NULL DEFAULT 1');
CALL AddColumnIfNotExists(DATABASE(), 'users', 'deleted_at', 'DATETIME NULL');

-- Roles table
CALL AddColumnIfNotExists(DATABASE(), 'roles', 'description', 'TEXT NULL');
CALL AddColumnIfNotExists(DATABASE(), 'roles', 'is_system', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL AddColumnIfNotExists(DATABASE(), 'roles', 'is_active', 'TINYINT(1) NOT NULL DEFAULT 1');
CALL AddColumnIfNotExists(DATABASE(), 'roles', 'deleted_at', 'DATETIME NULL');

-- Update Administrator role to be a system role
UPDATE `roles` SET `is_system` = 1 WHERE `slug` = 'admin';

-- Drop the temporary procedure
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;
