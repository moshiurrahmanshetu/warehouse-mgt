<?php
/**
 * Global Helper Functions
 * Warehouse Management System
 */

// ─── Output / Sanitization ────────────────────────────────────────────────────

/**
 * Escape a string for safe HTML output (XSS prevention).
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize a string input value (trim + strip tags).
 */
function sanitize(string $input): string
{
    return trim(strip_tags($input));
}



// ─── CSRF ─────────────────────────────────────────────────────────────────────

/**
 * Generate (or retrieve existing) CSRF token and store in session.
 */
function csrfToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        startSecureSession();
    }
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Render a hidden CSRF input field.
 */
function csrfField(): string
{
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrfToken() . '">';
}

/**
 * Verify the CSRF token from POST data.
 * Throws on failure.
 */
function verifyCsrf(): void
{
    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(419);
        die('CSRF token mismatch. Please go back and try again.');
    }
}

// ─── Session ──────────────────────────────────────────────────────────────────

/**
 * Start the session with secure settings.
 */
function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $cookieParams = [
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => SESSION_SECURE,
        'httponly' => SESSION_HTTPONLY,
        'samesite' => 'Lax',
    ];

    session_name(SESSION_NAME);
    session_set_cookie_params($cookieParams);
    session_start();

    // Regenerate session ID periodically to prevent fixation
    if (empty($_SESSION['_initiated'])) {
        session_regenerate_id(true);
        $_SESSION['_initiated'] = true;
    }
}

/**
 * Check whether the current session is timed out; destroy if so.
 */
function checkSessionTimeout(): bool
{
    if (!isset($_SESSION['_last_activity'])) {
        $_SESSION['_last_activity'] = time();
        return false;
    }

    if (time() - $_SESSION['_last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        return true;
    }

    $_SESSION['_last_activity'] = time();
    return false;
}

// ─── Authentication ───────────────────────────────────────────────────────────

/**
 * Return true if a user is currently logged in.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Return the currently logged-in user array from session, or null.
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Redirect to login page if not authenticated.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect(APP_URL . '/login.php');
    }
}

/**
 * Check if the current user has a specific role slug.
 */
function hasRole(string $roleSlug): bool
{
    $roles = $_SESSION['roles'] ?? [];
    return in_array($roleSlug, $roles, true);
}

/**
 * Check if the current user has a specific permission slug.
 */
function hasPermission(string $permSlug): bool
{
    $perms = $_SESSION['permissions'] ?? [];
    return in_array($permSlug, $perms, true);
}

/**
 * Require a specific permission, using RoleMiddleware to handle failures.
 */
function requirePermission(string $permSlug): void
{
    if (class_exists('RoleMiddleware')) {
        RoleMiddleware::requirePermission($permSlug);
    } else {
        if (!hasPermission($permSlug)) {
            http_response_code(403);
            die('Forbidden: You do not have the required permission.');
        }
    }
}

// ─── Navigation / Redirects ───────────────────────────────────────────────────

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Build a full URL relative to APP_URL.
 */
function url(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

// ─── Flash Messages ───────────────────────────────────────────────────────────

/**
 * Set a flash message.
 *
 * @param string $type  One of: success | error | warning | info
 */
function flashMessage(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Render and clear flash messages as Bootstrap alerts.
 */
function renderFlash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $map = [
        'success' => 'success',
        'error'   => 'danger',
        'warning' => 'warning',
        'info'    => 'info',
    ];
    foreach ($_SESSION['flash'] as $type => $message) {
        $cls = $map[$type] ?? 'secondary';
        echo '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">';
        echo e($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
    unset($_SESSION['flash']);
}

// ─── Formatting ───────────────────────────────────────────────────────────────

/**
 * Format a datetime string for display.
 */
function formatDate(string $datetime, string $format = 'd M Y, h:i A'): string
{
    if (empty($datetime)) {
        return '—';
    }
    try {
        $dt = new DateTime($datetime);
        return $dt->format($format);
    } catch (Exception $e) {
        return '—';
    }
}

/**
 * Truncate a string to a maximum length.
 */
function truncate(string $text, int $limit = 80, string $suffix = '…'): string
{
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . $suffix;
}

// ─── Security ─────────────────────────────────────────────────────────────────

/**
 * Get the client IP address.
 */
function getClientIp(): string
{
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

/**
 * Generate a secure random token.
 */
function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

// ─── Logging ──────────────────────────────────────────────────────────────────

/**
 * Write an application-level log entry to the logs/ directory.
 */
function writeLog(string $level, string $message): void
{
    $logFile = LOG_PATH . '/' . date('Y-m-d') . '.log';
    $line    = sprintf('[%s] [%s] %s%s', date('Y-m-d H:i:s'), strtoupper($level), $message, PHP_EOL);
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}

/**
 * Record user activity into the database activity_logs table.
 */
function logActivity(string $action, string $module = 'system', string $description = ''): void
{
    if (!class_exists('Database')) {
        return;
    }
    try {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO activity_logs (user_id, action, module, description, ip_address, user_agent)
             VALUES (:user_id, :action, :module, :description, :ip, :ua)",
            [
                ':user_id'     => $_SESSION['user_id'] ?? null,
                ':action'      => $action,
                ':module'      => $module,
                ':description' => $description,
                ':ip'          => getClientIp(),
                ':ua'          => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]
        );
    } catch (Exception $e) {
        writeLog('error', 'logActivity failed: ' . $e->getMessage());
    }
}

// ─── Misc ─────────────────────────────────────────────────────────────────────

/**
 * Return "active" CSS class if the current URL contains the given segment.
 */
function activeClass(string $segment): string
{
    $current = $_SERVER['REQUEST_URI'] ?? '';
    return (str_contains($current, $segment)) ? 'active' : '';
}

/**
 * Convert bytes to a human-readable file size.
 */
function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow   = $bytes > 0 ? floor(log($bytes) / log(1024)) : 0;
    $pow   = min($pow, count($units) - 1);
    return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
}

// ─── Phase 03 Reusable Helpers ────────────────────────────────────────────────

/**
 * Sequence-safe code generator.
 * Uses the `system_sequences` table with an atomic update to ensure uniqueness.
 */
function generateSequenceCode(string $seqName, string $prefix, int $padLength = 6): string
{
    $db = Database::getInstance();
    $db->beginTransaction();
    try {
        // Lock the row for update
        $row = $db->fetchOne("SELECT next_val FROM system_sequences WHERE seq_name = :name FOR UPDATE", [':name' => $seqName]);
        if (!$row) {
            $db->execute("INSERT INTO system_sequences (seq_name, next_val) VALUES (:name, 2)", [':name' => $seqName]);
            $nextVal = 1;
        } else {
            $nextVal = (int) $row['next_val'];
            $db->execute("UPDATE system_sequences SET next_val = next_val + 1 WHERE seq_name = :name", [':name' => $seqName]);
        }
        $db->commit();
        return $prefix . str_pad((string)$nextVal, $padLength, '0', STR_PAD_LEFT);
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

/**
 * Render Bootstrap 5 reusable pagination.
 */
function renderPagination(int $totalRecords, int $limit, int $currentPage, string $baseUrl = ''): string
{
    if ($totalRecords <= $limit) return '';
    $totalPages = ceil($totalRecords / $limit);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    // Maintain existing query params
    $queryParams = $_GET;
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm justify-content-center">';
    
    // Prev
    if ($currentPage > 1) {
        $queryParams['page'] = $currentPage - 1;
        $url = $baseUrl . '?' . http_build_query($queryParams);
        $html .= '<li class="page-item"><a class="page-link" href="'.e($url).'">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link">Previous</a></li>';
    }
    
    // Pages
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $queryParams['page'] = $i;
        $url = $baseUrl . '?' . http_build_query($queryParams);
        $active = ($i === $currentPage) ? 'active' : '';
        $html .= '<li class="page-item '.$active.'"><a class="page-link" href="'.e($url).'">'.$i.'</a></li>';
    }
    
    // Next
    if ($currentPage < $totalPages) {
        $queryParams['page'] = $currentPage + 1;
        $url = $baseUrl . '?' . http_build_query($queryParams);
        $html .= '<li class="page-item"><a class="page-link" href="'.e($url).'">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><a class="page-link">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Reusable CSV Export Helper.
 */
function exportCsv(string $filename, array $headers, array $data): void
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

