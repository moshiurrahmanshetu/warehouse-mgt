<?php
/**
 * 404 — Page Not Found
 */
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
    require_once __DIR__ . '/config/config.php';
    require_once __DIR__ . '/helpers/functions.php';
}
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 &mdash; Page Not Found &mdash; <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:20px;padding:40px;text-align:center">
    <div style="font-size:100px;line-height:1;font-weight:800;background:linear-gradient(135deg,#4f46e5,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent">404</div>
    <h1 style="font-size:24px;font-weight:700;color:var(--text-primary)">Page Not Found</h1>
    <p style="color:var(--text-muted);max-width:400px">The page you're looking for doesn't exist or has been moved. Please check the URL and try again.</p>
    <a href="<?= e(APP_URL) ?>" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px">
        <i class="bi bi-house"></i> Go to Dashboard
    </a>
</div>
</body>
</html>
