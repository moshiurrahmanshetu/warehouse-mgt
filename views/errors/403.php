<?php
/**
 * 403 Forbidden — Access Denied
 * Shown by RoleMiddleware when a user lacks permission.
 */
if (!defined('BASEPATH')) exit('No direct script access');
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 &mdash; Access Denied</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= defined('ASSET_PATH') ? e(ASSET_PATH) : '/warehouse-mgt/assets' ?>/css/style.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:20px;padding:40px;text-align:center">
    <div style="font-size:100px;line-height:1;font-weight:800;background:linear-gradient(135deg,#f59e0b,#ef4444);-webkit-background-clip:text;-webkit-text-fill-color:transparent">403</div>
    <h1 style="font-size:24px;font-weight:700;color:var(--text-primary)">Access Denied</h1>
    <p style="color:var(--text-muted);max-width:440px">
        You do not have permission to view this page. Please contact your administrator
        if you believe this is a mistake.
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
        <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:rgba(255,255,255,0.06);color:var(--text-primary);border-radius:8px;text-decoration:none;font-weight:600;font-size:14px;border:1px solid rgba(255,255,255,0.1)">
            <i class="bi bi-arrow-left"></i> Go Back
        </a>
        <?php if (defined('APP_URL')): ?>
        <a href="<?= e(APP_URL) ?>/dashboard.php" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px">
            <i class="bi bi-house"></i> Dashboard
        </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
