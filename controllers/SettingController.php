<?php
/**
 * System Setting Controller
 * Warehouse Management System - Phase 05.2
 */

defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));
require_once MODEL_PATH . '/SettingModel.php';

class SettingController
{
    private SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    /**
     * Display the System Settings page.
     */
    public function index(): void
    {
        // Enforce view permission
        if (!hasPermission('settings.view') && !hasPermission('settings.manage')) {
            http_response_code(403);
            include BASEPATH . '/views/errors/403.php';
            exit;
        }

        $settings   = $this->settingModel->getAllKeyed();
        $currencies = $this->settingModel->getCurrencies();
        $warehouses = $this->settingModel->getWarehouses();

        $pageTitle = 'System Settings';
        require_once VIEW_PATH . '/settings/index.php';
    }

    /**
     * Process Settings update.
     */
    public function update(): void
    {
        // Enforce edit/manage permission
        if (!hasPermission('settings.edit') && !hasPermission('settings.manage')) {
            http_response_code(403);
            include BASEPATH . '/views/errors/403.php';
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/settings.php');
        }

        verifyCsrf();

        $userId = (int)($_SESSION['user_id'] ?? 0);

        // General
        $settingsToSave = [
            'company_name'         => sanitize($_POST['company_name'] ?? ''),
            'company_email'        => sanitize($_POST['company_email'] ?? ''),
            'company_phone'        => sanitize($_POST['company_phone'] ?? ''),
            'company_address'      => sanitize($_POST['company_address'] ?? ''),
            'company_website'      => sanitize($_POST['company_website'] ?? ''),

            // Localization
            'default_currency_id'  => (int)($_POST['default_currency_id'] ?? 0),
            'timezone'             => sanitize($_POST['timezone'] ?? 'Asia/Dhaka'),
            'date_format'          => sanitize($_POST['date_format'] ?? 'd M Y'),
            'time_format'          => sanitize($_POST['time_format'] ?? 'h:i A'),

            // Warehouse
            'default_warehouse_id' => !empty($_POST['default_warehouse_id']) ? (int)$_POST['default_warehouse_id'] : '',

            // System
            'app_name'             => sanitize($_POST['app_name'] ?? 'Warehouse Management System'),
            'items_per_page'       => max(5, min(200, (int)($_POST['items_per_page'] ?? 25))),
            'session_timeout'      => max(60, (int)($_POST['session_timeout'] ?? 3600)),
            'maintenance_mode'     => isset($_POST['maintenance_mode']) ? '1' : '0',
            'enable_activity_log'  => isset($_POST['enable_activity_log']) ? '1' : '0',
        ];

        // Handle Branding uploads
        $logoPath = $this->handleFileUpload('app_logo', 'logo');
        if ($logoPath !== null) {
            $settingsToSave['app_logo'] = $logoPath;
        }

        $faviconPath = $this->handleFileUpload('app_favicon', 'favicon');
        if ($faviconPath !== null) {
            $settingsToSave['app_favicon'] = $faviconPath;
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            $this->settingModel->setMany($settingsToSave, $userId);

            // If a default currency is specified, sync with currencies table
            if (!empty($settingsToSave['default_currency_id'])) {
                $this->settingModel->syncDefaultCurrency((int)$settingsToSave['default_currency_id']);
            }

            // Log activity
            logActivity('System Settings Updated', 'settings', 'Updated system settings');

            $db->commit();
            flashMessage('success', 'System settings updated successfully.');
        } catch (Exception $e) {
            Database::getInstance()->rollBack();
            flashMessage('error', 'Failed to update settings: ' . $e->getMessage());
        }

        $tab = sanitize($_POST['_active_tab'] ?? 'general');
        redirect(APP_URL . '/settings.php' . ($tab ? "#$tab" : ''));
    }

    /**
     * Handle Image uploads for Logo and Favicon.
     */
    private function handleFileUpload(string $inputName, string $prefix): ?string
    {
        if (empty($_FILES[$inputName]['name'])) {
            return null;
        }

        $file = $_FILES[$inputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            flashMessage('warning', "Upload error occurred for {$prefix}.");
            return null;
        }

        // Validate size (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            flashMessage('error', "{$prefix} file size must be less than 2MB.");
            return null;
        }

        // Validate MIME type
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/webp',
            'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml'
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes, true)) {
            flashMessage('error', "Invalid file format for {$prefix}. Allowed: JPG, PNG, WebP, ICO, SVG.");
            return null;
        }

        $ext = match ($mime) {
            'image/jpeg'                       => 'jpg',
            'image/png'                        => 'png',
            'image/webp'                       => 'webp',
            'image/x-icon',
            'image/vnd.microsoft.icon'         => 'ico',
            'image/svg+xml'                    => 'svg',
            default                            => 'png',
        };

        $filename = uniqid($prefix . '_', true) . '.' . $ext;
        $uploadDir = BASEPATH . '/uploads/settings/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return 'uploads/settings/' . $filename;
        }

        flashMessage('error', "Failed to save {$prefix} file.");
        return null;
    }
}
