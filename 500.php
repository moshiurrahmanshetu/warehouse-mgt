<?php
/**
 * 500 — Internal Server Error
 */
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
}
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 &mdash; Server Error</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary:#4f46e5; --secondary:#06b6d4; --bg-body:#0f172a; --text-primary:#f1f5f9; --text-muted:#64748b; }
        body { background:var(--bg-body); color:var(--text-primary); font-family:'Inter',system-ui,sans-serif; margin:0; }
    </style>
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:20px;padding:40px;text-align:center">
    <div style="font-size:100px;line-height:1;font-weight:800;background:linear-gradient(135deg,#ef4444,#f59e0b);-webkit-background-clip:text;-webkit-text-fill-color:transparent">500</div>
    <h1 style="font-size:24px;font-weight:700">Internal Server Error</h1>
    <p style="color:var(--text-muted);max-width:440px">
        Something went wrong on our end. The error has been logged and our team will look into it.
        Please try again later or contact your system administrator.
    </p>
    <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px">
        <i class="bi bi-arrow-left"></i> Go Back
    </a>
</div>
</body>
</html>
