<?php
/**
 * Bootstrap — Loads all core dependencies.
 * Include this at the top of every entry-point file.
 */

define('BASEPATH', __DIR__);

require_once BASEPATH . '/config/config.php';
require_once BASEPATH . '/config/database.php';
require_once BASEPATH . '/helpers/functions.php';
require_once BASEPATH . '/middleware/AuthMiddleware.php';
require_once BASEPATH . '/middleware/RoleMiddleware.php';

// Start the session globally
startSecureSession();
