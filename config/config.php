<?php
/**
 * Application Configuration
 * Warehouse Management System
 */

// Prevent direct access
defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));

// ─── Environment ────────────────────────────────────────────────────────────
define('APP_ENV',     'development'); // 'development' | 'production'
define('APP_NAME',    'Warehouse Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL',     'http://localhost/warehouse-mgt');
define('APP_CHARSET', 'UTF-8');
define('APP_TIMEZONE','Asia/Dhaka');

// ─── Paths ───────────────────────────────────────────────────────────────────
define('CONFIG_PATH',     BASEPATH . '/config');
define('CONTROLLER_PATH', BASEPATH . '/controllers');
define('MODEL_PATH',      BASEPATH . '/models');
define('VIEW_PATH',       BASEPATH . '/views');
define('INCLUDE_PATH',    BASEPATH . '/includes');
define('HELPER_PATH',     BASEPATH . '/helpers');
define('MIDDLEWARE_PATH', BASEPATH . '/middleware');
define('UPLOAD_PATH',     BASEPATH . '/uploads');
define('LOG_PATH',        BASEPATH . '/logs');
define('ASSET_PATH',      APP_URL  . '/assets');

// ─── Session ─────────────────────────────────────────────────────────────────
define('SESSION_NAME',    'WMS_SESSION');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_SECURE',  false); // true when HTTPS is used
define('SESSION_HTTPONLY', true);

// ─── Security ────────────────────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// ─── Pagination ──────────────────────────────────────────────────────────────
define('DEFAULT_PAGE_LIMIT', 25);

// ─── Error Handling ──────────────────────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . '/php_errors.log');
}

// ─── Timezone ────────────────────────────────────────────────────────────────
date_default_timezone_set(APP_TIMEZONE);
