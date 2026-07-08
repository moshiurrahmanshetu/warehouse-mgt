<?php
/**
 * Logout Entry Point
 * Warehouse Management System
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once CONTROLLER_PATH . '/AuthController.php';

$auth = new AuthController();
$auth->logout();
