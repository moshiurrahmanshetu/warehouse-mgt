<?php
/**
 * Warehouse Management System — Professional Installer
 *
 * Multi-step commercial installation wizard:
 * 01 Requirements → 02 Database → 03 SQL Import → 04 Administrator → 05 Complete
 */

// ─── Initialize Environment ───────────────────────────────────────────────────
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('INSTALLER_ROOT', __DIR__);
$lockFile = INSTALLER_ROOT . '/storage/install.lock';

// ─── Dynamic Base URL & Asset Path ───────────────────────────────────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = str_replace('\\', '/', $scriptDir);
if ($scriptDir === '/' || $scriptDir === '.') {
    $scriptDir = '';
}
$baseUrl = rtrim("$protocol://$host$scriptDir", '/');
$assetUrl = $baseUrl . '/assets';

// ─── CSRF Token ──────────────────────────────────────────────────────────────
if (empty($_SESSION['installer_csrf'])) {
    $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['installer_csrf'];

function sanitize_input($val) {
    return htmlspecialchars(trim((string)$val), ENT_QUOTES, 'UTF-8');
}

// ─── AJAX Connection Test Handler ────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'test_db') {
    header('Content-Type: application/json');
    $dbHost = sanitize_input($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = sanitize_input($_POST['db_port'] ?? '3306');
    $dbName = sanitize_input($_POST['db_name'] ?? '');
    $dbUser = sanitize_input($_POST['db_user'] ?? 'root');
    $dbPass = (string)($_POST['db_pass'] ?? '');

    if (empty($dbName)) {
        echo json_encode(['success' => false, 'message' => 'Database name is required.']);
        exit;
    }

    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);

        // Check if database exists
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbname");
        $stmt->execute([':dbname' => $dbName]);
        $exists = $stmt->fetch();

        if ($exists) {
            echo json_encode([
                'success' => true,
                'message' => "Successfully connected! Database '{$dbName}' exists and is reachable."
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => "Successfully connected to MySQL server! Database '{$dbName}' will be created during import if permissions permit."
            ]);
        }
    } catch (PDOException $e) {
        $code = (int)$e->getCode();
        $msg = $e->getMessage();
        if ($code === 1045 || strpos($msg, 'Access denied') !== false) {
            $userMsg = "Access denied: Invalid database username or password.";
        } elseif ($code === 2002 || strpos($msg, 'Connection refused') !== false || strpos($msg, 'No such host') !== false) {
            $userMsg = "Could not connect to host '{$dbHost}' on port '{$dbPort}'. Please verify your database host and port.";
        } else {
            $userMsg = "Database connection error: " . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
        }
        echo json_encode(['success' => false, 'message' => $userMsg]);
    }
    exit;
}

// ─── Check If Already Installed ──────────────────────────────────────────────
$isInstalled = file_exists($lockFile);

if ($isInstalled) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Already Installed &mdash; Warehouse Management System</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl) ?>/css/installer.css">
    </head>
    <body>
    <div class="installer-page">
        <div class="installer-bg-pattern"></div>
        <div class="installer-card">
            <div class="installer-header">
                <div class="installer-brand">
                    <div class="installer-brand-icon"><i class="bi bi-shield-lock-fill"></i></div>
                    <h1 class="installer-brand-title">WMS Pro</h1>
                </div>
                <p class="installer-subtitle">Warehouse Management System</p>
            </div>
            <div class="installer-body text-center py-5">
                <div class="success-icon-wrap" style="background:rgba(79, 70, 229, 0.15);border-color:#4f46e5;color:#818cf8;box-shadow:0 0 24px rgba(79, 70, 229, 0.35)">
                    <i class="bi bi-check2-all"></i>
                </div>
                <h2 class="installer-step-title mb-3">Application Already Installed</h2>
                <p class="installer-step-desc mx-auto" style="max-width:540px;">
                    The Warehouse Management System is already installed and protected by an installation lock. 
                    If you need to perform a clean re-installation, refer to the manual reset procedure.
                </p>
                <div class="mt-4">
                    <a href="<?= htmlspecialchars($baseUrl) ?>/login.php" class="btn-wms-primary px-4">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Go to Login</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// ─── Current Step Determination ──────────────────────────────────────────────
$currentStep = (int)($_GET['step'] ?? ($_SESSION['installer_step'] ?? 1));
if ($currentStep < 1 || $currentStep > 5) {
    $currentStep = 1;
}

$errorMessage = '';
$successMessage = '';

// ─── STEP 1: Requirements Check Logic ────────────────────────────────────────
$requirements = [
    'php_version' => [
        'label' => 'PHP Version >= 8.0.0 (Current: ' . PHP_VERSION . ')',
        'pass' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'critical' => true
    ],
    'pdo' => [
        'label' => 'PDO Extension',
        'pass' => extension_loaded('pdo'),
        'critical' => true
    ],
    'pdo_mysql' => [
        'label' => 'PDO MySQL Driver (pdo_mysql)',
        'pass' => extension_loaded('pdo_mysql'),
        'critical' => true
    ],
    'mbstring' => [
        'label' => 'MBString Extension (mbstring)',
        'pass' => extension_loaded('mbstring'),
        'critical' => true
    ],
    'json' => [
        'label' => 'JSON Extension (json)',
        'pass' => extension_loaded('json'),
        'critical' => true
    ],
    'fileinfo' => [
        'label' => 'Fileinfo Extension (fileinfo)',
        'pass' => extension_loaded('fileinfo'),
        'critical' => true
    ],
    'openssl' => [
        'label' => 'OpenSSL Extension (openssl)',
        'pass' => extension_loaded('openssl'),
        'critical' => true
    ],
    'session' => [
        'label' => 'Session Support (session)',
        'pass' => extension_loaded('session'),
        'critical' => true
    ],
];

// Directory Permissions Check
$directories = [
    'config' => [
        'path' => INSTALLER_ROOT . '/config',
        'label' => 'config/ Directory',
        'writable' => is_writable(INSTALLER_ROOT . '/config')
    ],
    'storage' => [
        'path' => INSTALLER_ROOT . '/storage',
        'label' => 'storage/ Directory',
        'writable' => is_dir(INSTALLER_ROOT . '/storage') ? is_writable(INSTALLER_ROOT . '/storage') : is_writable(INSTALLER_ROOT)
    ],
    'uploads' => [
        'path' => INSTALLER_ROOT . '/uploads',
        'label' => 'uploads/ Directory',
        'writable' => is_dir(INSTALLER_ROOT . '/uploads') ? is_writable(INSTALLER_ROOT . '/uploads') : is_writable(INSTALLER_ROOT)
    ],
    'logs' => [
        'path' => INSTALLER_ROOT . '/logs',
        'label' => 'logs/ Directory',
        'writable' => is_dir(INSTALLER_ROOT . '/logs') ? is_writable(INSTALLER_ROOT . '/logs') : is_writable(INSTALLER_ROOT)
    ],
];

$allRequirementsPassed = true;
foreach ($requirements as $r) {
    if ($r['critical'] && !$r['pass']) {
        $allRequirementsPassed = false;
        break;
    }
}
foreach ($directories as $d) {
    if (!$d['writable']) {
        $allRequirementsPassed = false;
        break;
    }
}

// ─── Multi-Step Form Submission Handler ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrfToken, $submittedToken)) {
        $errorMessage = 'Security validation failed (CSRF token mismatch). Please retry.';
    } else {
        $postStep = (int)($_POST['step'] ?? 1);

        // Process Step 2: Database Configuration
        if ($postStep === 2) {
            $dbHost = sanitize_input($_POST['db_host'] ?? '127.0.0.1');
            $dbPort = sanitize_input($_POST['db_port'] ?? '3306');
            $dbName = sanitize_input($_POST['db_name'] ?? '');
            $dbUser = sanitize_input($_POST['db_user'] ?? 'root');
            $dbPass = (string)($_POST['db_pass'] ?? '');

            if (empty($dbName)) {
                $errorMessage = 'Please enter a Database Name.';
            } else {
                try {
                    $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbUser, $dbPass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => 5
                    ]);

                    // Create database if it does not exist
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo->exec("USE `{$dbName}`");

                    // Save verified credentials in session
                    $_SESSION['install_db'] = [
                        'host' => $dbHost,
                        'port' => $dbPort,
                        'name' => $dbName,
                        'user' => $dbUser,
                        'pass' => $dbPass
                    ];
                    $_SESSION['installer_step'] = 3;
                    header("Location: install.php?step=3");
                    exit;
                } catch (PDOException $e) {
                    $errorMessage = 'Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                }
            }
        }

        // Process Step 3: SQL Import
        if ($postStep === 3) {
            $dbConfig = $_SESSION['install_db'] ?? null;
            if (!$dbConfig) {
                header("Location: install.php?step=2");
                exit;
            }

            $sqlSource = $_POST['sql_source'] ?? 'default';
            $sqlContent = '';

            if ($sqlSource === 'upload' && isset($_FILES['custom_sql_file'])) {
                $file = $_FILES['custom_sql_file'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errorMessage = 'Failed to upload SQL file (Upload Error Code: ' . (int)$file['error'] . ').';
                } elseif ($file['size'] > 20 * 1024 * 1024) {
                    $errorMessage = 'Uploaded SQL file exceeds maximum size limit of 20MB.';
                } else {
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if ($ext !== 'sql') {
                        $errorMessage = 'Invalid file type. Only .sql database dump files are accepted.';
                    } else {
                        $sqlContent = file_get_contents($file['tmp_name']);
                    }
                }
            } else {
                // Use default bundle database/warehouse_mgt.sql
                $defaultSqlPath = INSTALLER_ROOT . '/database/warehouse_mgt.sql';
                if (!file_exists($defaultSqlPath)) {
                    $errorMessage = 'Default database dump file not found at database/warehouse_mgt.sql.';
                } else {
                    $sqlContent = file_get_contents($defaultSqlPath);
                }
            }

            if (empty($errorMessage) && !empty($sqlContent)) {
                try {
                    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]);

                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

                    // Multi-statement SQL Parser & Executor
                    $queries = [];
                    $buffer = '';
                    $inString = false;
                    $stringChar = '';
                    $length = strlen($sqlContent);

                    for ($i = 0; $i < $length; $i++) {
                        $char = $sqlContent[$i];
                        $nextChar = ($i + 1 < $length) ? $sqlContent[$i + 1] : '';

                        // Handle strings ('...' or "...")
                        if (($char === "'" || $char === '"') && ($i === 0 || $sqlContent[$i - 1] !== '\\')) {
                            if (!$inString) {
                                $inString = true;
                                $stringChar = $char;
                            } elseif ($stringChar === $char) {
                                $inString = false;
                            }
                        }

                        // Handle line comments when not inside a string
                        if (!$inString && $char === '-' && $nextChar === '-') {
                            $pos = strpos($sqlContent, "\n", $i);
                            if ($pos === false) break;
                            $i = $pos;
                            continue;
                        }
                        if (!$inString && $char === '#') {
                            $pos = strpos($sqlContent, "\n", $i);
                            if ($pos === false) break;
                            $i = $pos;
                            continue;
                        }

                        // Handle block comments
                        if (!$inString && $char === '/' && $nextChar === '*') {
                            $pos = strpos($sqlContent, '*/', $i);
                            if ($pos === false) break;
                            $i = $pos + 1;
                            continue;
                        }

                        // Semicolon separator
                        if (!$inString && $char === ';') {
                            $trimmed = trim($buffer);
                            if (!empty($trimmed)) {
                                $queries[] = $trimmed;
                            }
                            $buffer = '';
                            continue;
                        }

                        $buffer .= $char;
                    }

                    $trimmed = trim($buffer);
                    if (!empty($trimmed)) {
                        $queries[] = $trimmed;
                    }

                    foreach ($queries as $q) {
                        $pdo->exec($q);
                    }

                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

                    $_SESSION['installer_step'] = 4;
                    header("Location: install.php?step=4");
                    exit;
                } catch (PDOException $e) {
                    $errorMessage = 'SQL Import failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                }
            }
        }

        // Process Step 4: Administrator Account Setup & Finalize
        if ($postStep === 4) {
            $dbConfig = $_SESSION['install_db'] ?? null;
            if (!$dbConfig) {
                header("Location: install.php?step=2");
                exit;
            }

            $adminName = sanitize_input($_POST['admin_name'] ?? '');
            $adminEmail = sanitize_input($_POST['admin_email'] ?? '');
            $adminPass = (string)($_POST['admin_password'] ?? '');
            $adminConfirm = (string)($_POST['admin_confirm'] ?? '');

            if (empty($adminName) || empty($adminEmail) || empty($adminPass)) {
                $errorMessage = 'Full Name, Email Address, and Password are required.';
            } elseif (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = 'Please enter a valid email address format.';
            } elseif (strlen($adminPass) < 6) {
                $errorMessage = 'Administrator password must be at least 6 characters in length.';
            } elseif ($adminPass !== $adminConfirm) {
                $errorMessage = 'Password confirmation does not match.';
            } else {
                try {
                    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                    ]);

                    $pdo->beginTransaction();

                    // Check for Administrator Role
                    $stmtRole = $pdo->query("SELECT id FROM `roles` WHERE `slug` = 'admin' LIMIT 1");
                    $adminRole = $stmtRole->fetch(PDO::FETCH_ASSOC);
                    $adminRoleId = $adminRole ? (int)$adminRole['id'] : 1;

                    // Hash password using bcrypt
                    $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);

                    // Remove any existing user with ID 1 or email
                    $pdo->exec("DELETE FROM `user_roles` WHERE `user_id` = 1");
                    $pdo->exec("DELETE FROM `users` WHERE `id` = 1 OR `email` = " . $pdo->quote($adminEmail));

                    // Insert Administrator user
                    $stmtUser = $pdo->prepare("
                        INSERT INTO `users` (`id`, `name`, `email`, `password`, `status`, `is_active`, `created_at`)
                        VALUES (1, :name, :email, :password, 'active', 1, NOW())
                    ");
                    $stmtUser->execute([
                        ':name' => $adminName,
                        ':email' => $adminEmail,
                        ':password' => $hashedPassword
                    ]);

                    // Assign Administrator role
                    $stmtUr = $pdo->prepare("INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (1, :role_id)");
                    $stmtUr->execute([':role_id' => $adminRoleId]);

                    // Update System Settings App Name
                    $appName = sanitize_input($_POST['app_name'] ?? 'Warehouse Management System');
                    if (!empty($appName)) {
                        $stmtSet = $pdo->prepare("UPDATE `system_settings` SET `value` = :val WHERE `key` = 'app_name'");
                        $stmtSet->execute([':val' => $appName]);
                    }

                    // Log initial installation activity
                    $stmtLog = $pdo->prepare("
                        INSERT INTO `activity_logs` (`user_id`, `action`, `module`, `description`, `ip_address`, `created_at`)
                        VALUES (1, 'Install', 'system', 'Clean installation completed by Administrator ({$adminEmail})', :ip, NOW())
                    ");
                    $stmtLog->execute([':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

                    $pdo->commit();

                    // ── Generate config/database.php ──
                    $dbConfigCode = "<?php\n";
                    $dbConfigCode .= "/**\n * Database Configuration & PDO Connection\n * Warehouse Management System\n */\n\n";
                    $dbConfigCode .= "defined('BASEPATH') || define('BASEPATH', dirname(__DIR__));\n\n";
                    $dbConfigCode .= "// ─── Database Credentials ────────────────────────────────────────────────────\n";
                    $dbConfigCode .= "define('DB_HOST',    " . var_export($dbConfig['host'], true) . ");\n";
                    $dbConfigCode .= "define('DB_PORT',    " . var_export($dbConfig['port'], true) . ");\n";
                    $dbConfigCode .= "define('DB_NAME',    " . var_export($dbConfig['name'], true) . ");\n";
                    $dbConfigCode .= "define('DB_USER',    " . var_export($dbConfig['user'], true) . ");\n";
                    $dbConfigCode .= "define('DB_PASS',    " . var_export($dbConfig['pass'], true) . ");\n";
                    $dbConfigCode .= "define('DB_CHARSET', 'utf8mb4');\n\n";
                    $dbConfigCode .= file_get_contents(INSTALLER_ROOT . '/config/database.php');
                    // Strip the previous top definitions from database.php template if needed
                    $posClass = strpos($dbConfigCode, 'class Database');
                    if ($posClass !== false) {
                        $dbConfigCode = substr($dbConfigCode, 0, strpos($dbConfigCode, 'class Database')) . substr(file_get_contents(INSTALLER_ROOT . '/config/database.php'), strpos(file_get_contents(INSTALLER_ROOT . '/config/database.php'), 'class Database'));
                    }
                    file_put_contents(INSTALLER_ROOT . '/config/database.php', $dbConfigCode);

                    // ── Create storage/install.lock ──
                    if (!is_dir(INSTALLER_ROOT . '/storage')) {
                        mkdir(INSTALLER_ROOT . '/storage', 0755, true);
                    }
                    $lockData = json_encode([
                        'installed_at' => date('Y-m-d H:i:s'),
                        'version' => '1.0.0',
                        'admin_email' => $adminEmail,
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                    ], JSON_PRETTY_PRINT);
                    file_put_contents($lockFile, $lockData);

                    // Save install summary in session for Step 5
                    $_SESSION['install_summary'] = [
                        'app_name' => $appName ?: 'Warehouse Management System',
                        'admin_name' => $adminName,
                        'admin_email' => $adminEmail,
                        'app_url' => $baseUrl,
                        'login_url' => $baseUrl . '/login.php'
                    ];

                    unset($_SESSION['install_db']);
                    $_SESSION['installer_step'] = 5;
                    header("Location: install.php?step=5");
                    exit;
                } catch (Exception $e) {
                    if (isset($pdo) && $pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $errorMessage = 'Installation finalization failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard &mdash; Warehouse Management System</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($assetUrl) ?>/css/installer.css">
</head>
<body>

<div class="installer-page">
    <div class="installer-bg-pattern"></div>

    <div class="installer-card">
        <!-- Header -->
        <div class="installer-header">
            <div class="installer-brand">
                <div class="installer-brand-icon"><i class="bi bi-boxes"></i></div>
                <h1 class="installer-brand-title">WMS Pro</h1>
            </div>
            <p class="installer-subtitle">Warehouse Management System &mdash; First-Time Installation</p>
        </div>

        <!-- Step Progress Bar -->
        <div class="installer-steps">
            <div class="step-item <?= $currentStep === 1 ? 'active' : ($currentStep > 1 ? 'completed' : '') ?>">
                <span class="step-num"><?= $currentStep > 1 ? '<i class="bi bi-check"></i>' : '1' ?></span>
                <span>Requirements</span>
            </div>
            <div class="step-item <?= $currentStep === 2 ? 'active' : ($currentStep > 2 ? 'completed' : '') ?>">
                <span class="step-num"><?= $currentStep > 2 ? '<i class="bi bi-check"></i>' : '2' ?></span>
                <span>Database</span>
            </div>
            <div class="step-item <?= $currentStep === 3 ? 'active' : ($currentStep > 3 ? 'completed' : '') ?>">
                <span class="step-num"><?= $currentStep > 3 ? '<i class="bi bi-check"></i>' : '3' ?></span>
                <span>SQL Import</span>
            </div>
            <div class="step-item <?= $currentStep === 4 ? 'active' : ($currentStep > 4 ? 'completed' : '') ?>">
                <span class="step-num"><?= $currentStep > 4 ? '<i class="bi bi-check"></i>' : '4' ?></span>
                <span>Administrator</span>
            </div>
            <div class="step-item <?= $currentStep === 5 ? 'active' : '' ?>">
                <span class="step-num">5</span>
                <span>Complete</span>
            </div>
        </div>

        <!-- Step Body -->
        <div class="installer-body">
            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= $successMessage ?>
                </div>
            <?php endif; ?>

            <!-- ─── STEP 1: Requirements Check ──────────────────────────────── -->
            <?php if ($currentStep === 1): ?>
                <h2 class="installer-step-title">01. System Requirements & Permissions</h2>
                <p class="installer-step-desc">
                    The installer verifies that your server environment meets the minimum PHP extensions and writable directory permissions required to run the Warehouse Management System.
                </p>

                <!-- PHP Extensions -->
                <div class="req-group">
                    <div class="req-group-header">
                        <i class="bi bi-cpu me-1"></i> PHP Environment & Extensions
                    </div>
                    <?php foreach ($requirements as $key => $req): ?>
                        <div class="req-row">
                            <span class="req-label">
                                <i class="bi <?= $req['pass'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i>
                                <?= htmlspecialchars($req['label']) ?>
                            </span>
                            <span class="req-badge <?= $req['pass'] ? 'pass' : 'fail' ?>">
                                <?= $req['pass'] ? '✓ Passed' : '✗ Missing' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Directory Permissions -->
                <div class="req-group">
                    <div class="req-group-header">
                        <i class="bi bi-folder-check me-1"></i> Directory Writable Permissions
                    </div>
                    <?php foreach ($directories as $key => $dir): ?>
                        <div class="req-row">
                            <span class="req-label">
                                <i class="bi <?= $dir['writable'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>"></i>
                                <?= htmlspecialchars($dir['label']) ?>
                            </span>
                            <span class="req-badge <?= $dir['writable'] ? 'pass' : 'fail' ?>">
                                <?= $dir['writable'] ? '✓ Writable' : '✗ Not Writable' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="installer-footer">
                    <div>
                        <?php if (!$allRequirementsPassed): ?>
                            <small class="text-danger">
                                <i class="bi bi-exclamation-circle me-1"></i> Please resolve the failing requirements before proceeding.
                            </small>
                        <?php else: ?>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i> All system requirements passed successfully.
                            </small>
                        <?php endif; ?>
                    </div>
                    <a href="install.php?step=2" class="btn-wms-primary <?= !$allRequirementsPassed ? 'disabled' : '' ?>">
                        <span>Continue to Database Setup</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>

            <!-- ─── STEP 2: Database Configuration ──────────────────────────── -->
            <?php if ($currentStep === 2): 
                $dbSaved = $_SESSION['install_db'] ?? [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'name' => 'warehouse_management',
                    'user' => 'root',
                    'pass' => ''
                ];
            ?>
                <h2 class="installer-step-title">02. Database Configuration</h2>
                <p class="installer-step-desc">
                    Enter your MySQL database credentials below. You can test your connection before proceeding to verify that the database server is reachable.
                </p>

                <form action="install.php?step=2" method="POST" id="dbConfigForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="step" value="2">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label-wms">Database Host <span class="text-danger">*</span></label>
                            <input type="text" name="db_host" id="db_host" class="form-control-wms" required
                                   value="<?= htmlspecialchars($dbSaved['host']) ?>" placeholder="e.g. 127.0.0.1 or localhost">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-wms">Port <span class="text-danger">*</span></label>
                            <input type="text" name="db_port" id="db_port" class="form-control-wms" required
                                   value="<?= htmlspecialchars($dbSaved['port']) ?>" placeholder="3306">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label-wms">Database Name <span class="text-danger">*</span></label>
                            <input type="text" name="db_name" id="db_name" class="form-control-wms" required
                                   value="<?= htmlspecialchars($dbSaved['name']) ?>" placeholder="e.g. warehouse_management">
                            <div class="form-text-wms">The installer will create this database if it does not already exist.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Database Username <span class="text-danger">*</span></label>
                            <input type="text" name="db_user" id="db_user" class="form-control-wms" required
                                   value="<?= htmlspecialchars($dbSaved['user']) ?>" placeholder="e.g. root">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Database Password</label>
                            <div class="position-relative">
                                <input type="password" name="db_pass" id="db_pass" class="form-control-wms"
                                       value="<?= htmlspecialchars($dbSaved['pass']) ?>" placeholder="Enter password (leave blank if none)">
                                <button type="button" class="input-toggle-pass" data-target="db_pass" aria-label="Toggle password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn-wms-outline" id="btnTestDb">
                            <i class="bi bi-plugin me-1"></i> Test Connection
                        </button>
                        <div id="testDbResult" class="d-none"></div>
                    </div>

                    <div class="installer-footer">
                        <a href="install.php?step=1" class="btn-wms-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn-wms-primary">
                            <span>Save & Continue to SQL Import</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- ─── STEP 3: SQL File Import Engine ──────────────────────────── -->
            <?php if ($currentStep === 3): ?>
                <h2 class="installer-step-title">03. Database Schema & Seed Data Import</h2>
                <p class="installer-step-desc">
                    Choose whether to install the fresh default database schema bundled with the package or upload a custom SQL dump file.
                </p>

                <form action="install.php?step=3" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="step" value="3">

                    <div class="req-group p-3 mb-3">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="sql_source" id="sqlSourceDefault" value="default" checked>
                            <label class="form-check-label fw-semibold text-light ms-2" for="sqlSourceDefault">
                                <i class="bi bi-database-check text-primary me-1"></i> Use Bundled Database File (Recommended)
                            </label>
                            <div class="text-muted ms-4 mt-1" style="font-size:13px;">
                                Automatically imports <code>database/warehouse_mgt.sql</code> containing all 23 database tables, 71 permissions, default roles, system sequences, and initial master data.
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sql_source" id="sqlSourceUpload" value="upload">
                            <label class="form-check-label fw-semibold text-light ms-2" for="sqlSourceUpload">
                                <i class="bi bi-upload text-info me-1"></i> Upload Custom SQL File (.sql)
                            </label>
                            <div class="text-muted ms-4 mt-1" style="font-size:13px;">
                                Provide a custom database dump file from your local storage.
                            </div>
                        </div>

                        <div id="customUploadWrapper" class="ms-4 mt-3 d-none">
                            <label class="form-label-wms">Select .sql File</label>
                            <input type="file" name="custom_sql_file" class="form-control-wms" accept=".sql">
                            <div class="form-text-wms">Maximum allowed file size: 20 MB.</div>
                        </div>
                    </div>

                    <div class="installer-footer">
                        <a href="install.php?step=2" class="btn-wms-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn-wms-primary" id="btnImportSql">
                            <i class="bi bi-box-arrow-down"></i>
                            <span>Import Database & Continue</span>
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- ─── STEP 4: Administrator Account Setup ─────────────────────── -->
            <?php if ($currentStep === 4): ?>
                <h2 class="installer-step-title">04. Administrator Account Setup</h2>
                <p class="installer-step-desc">
                    Create the super administrator account for your Warehouse Management System. You will use these credentials to sign in.
                </p>

                <form action="install.php?step=4" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="step" value="4">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label-wms">Application Name</label>
                            <input type="text" name="app_name" class="form-control-wms" required
                                   value="Warehouse Management System" placeholder="e.g. Warehouse Management System">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Administrator Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="admin_name" class="form-control-wms" required
                                   value="<?= htmlspecialchars($_POST['admin_name'] ?? 'System Administrator') ?>" placeholder="e.g. John Doe">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Administrator Email <span class="text-danger">*</span></label>
                            <input type="email" name="admin_email" class="form-control-wms" required
                                   value="<?= htmlspecialchars($_POST['admin_email'] ?? 'admin@example.com') ?>" placeholder="admin@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Administrator Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="admin_password" id="admin_password" class="form-control-wms" required
                                       placeholder="Minimum 6 characters">
                                <button type="button" class="input-toggle-pass" data-target="admin_password" aria-label="Toggle password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-wms">Confirm Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" name="admin_confirm" id="admin_confirm" class="form-control-wms" required
                                       placeholder="Re-enter password">
                                <button type="button" class="input-toggle-pass" data-target="admin_confirm" aria-label="Toggle password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="installer-footer">
                        <a href="install.php?step=3" class="btn-wms-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn-wms-primary">
                            <i class="bi bi-check2-circle"></i>
                            <span>Complete Installation</span>
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- ─── STEP 5: Installation Complete ───────────────────────────── -->
            <?php if ($currentStep === 5): 
                $summary = $_SESSION['install_summary'] ?? [
                    'app_name' => 'Warehouse Management System',
                    'admin_name' => 'System Administrator',
                    'admin_email' => 'admin@example.com',
                    'app_url' => $baseUrl,
                    'login_url' => $baseUrl . '/login.php'
                ];
            ?>
                <div class="installer-success-box">
                    <div class="success-icon-wrap">
                        <i class="bi bi-check-lg"></i>
                    </div>

                    <h2 class="installer-step-title mb-2">Installation Completed Successfully!</h2>
                    <p class="installer-step-desc mx-auto" style="max-width:560px;">
                        Congratulations! Your Warehouse Management System has been configured, initialized, and secured with an installation lock.
                    </p>

                    <div class="installer-summary-card mx-auto" style="max-width:580px;">
                        <div class="summary-row">
                            <span class="summary-label">Application Name:</span>
                            <span class="summary-value"><?= htmlspecialchars($summary['app_name']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Administrator Account:</span>
                            <span class="summary-value"><?= htmlspecialchars($summary['admin_email']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Application URL:</span>
                            <span class="summary-value"><a href="<?= htmlspecialchars($summary['app_url']) ?>" class="text-info"><?= htmlspecialchars($summary['app_url']) ?></a></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Login Page URL:</span>
                            <span class="summary-value"><a href="<?= htmlspecialchars($summary['login_url']) ?>" class="text-info"><?= htmlspecialchars($summary['login_url']) ?></a></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Installation Lock:</span>
                            <span class="summary-value text-success"><i class="bi bi-shield-check me-1"></i> Active (storage/install.lock)</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($summary['login_url']) ?>" class="btn-wms-primary px-5">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Sign In to WMS Dashboard</span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Installer JS -->
<script src="<?= htmlspecialchars($assetUrl) ?>/js/installer.js"></script>
</body>
</html>
