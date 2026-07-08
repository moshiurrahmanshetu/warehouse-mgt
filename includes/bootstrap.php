<?php

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

require_once ROOT_PATH . '/helpers/functions.php';

require_once ROOT_PATH . '/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/middleware/RoleMiddleware.php';

// Start the session globally
startSecureSession();
