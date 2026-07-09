<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . " &mdash; " : "" ?><?= e(APP_NAME) ?></title>
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- App Styles -->
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/style.css">

    <script>
        window.WMS_SESSION_TIMEOUT = <?= (int) SESSION_TIMEOUT ?>;
        window.WMS_LOGOUT_URL      = "<?= e(APP_URL) ?>/logout.php";
    </script>
</head>
<body class="authenticated">
<div class="wms-wrapper">
