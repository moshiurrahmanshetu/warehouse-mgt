<?php

define('ROOT_PATH', dirname(__DIR__));

// Check if application is installed
if (!file_exists(ROOT_PATH . '/storage/install.lock')) {
    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($script !== 'install.php') {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $dir = str_replace('\\', '/', $dir);
        if ($dir === '/' || $dir === '.') $dir = '';
        $installUrl = rtrim("$protocol://$host$dir", '/') . '/install.php';
        header('Location: ' . $installUrl);
        exit;
    }
}

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

require_once ROOT_PATH . '/helpers/functions.php';

require_once ROOT_PATH . '/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/middleware/RoleMiddleware.php';

// Start the session globally
startSecureSession();
