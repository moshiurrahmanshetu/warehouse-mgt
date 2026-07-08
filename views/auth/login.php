<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; <?= e(APP_NAME) ?></title>
    <meta name="description" content="Sign in to the Warehouse Management System.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- App Styles -->
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/style.css">
</head>
<body>
<div class="login-page">
    <!-- Background gradient pattern -->
    <div class="login-bg-pattern"></div>

    <div class="login-grid animate-in">

        <!-- Left Panel -->
        <div class="login-left">
            <div class="login-left-icon">📦</div>
            <h1 class="login-headline">Warehouse<br>Management<br><span class="text-gradient">System</span></h1>
            <p class="login-desc">
                A powerful, scalable platform to manage your entire
                warehouse operations from a single dashboard.
            </p>
            <div class="login-features">
                <div class="login-feature"><span class="feature-dot"></span>Real-time inventory tracking</div>
                <div class="login-feature"><span class="feature-dot"></span>Role-based access control</div>
                <div class="login-feature"><span class="feature-dot"></span>Comprehensive audit logs</div>
                <div class="login-feature"><span class="feature-dot"></span>Multi-warehouse support</div>
            </div>
        </div>

        <!-- Right Panel — Login Form -->
        <div class="login-right">
            <h2 class="login-form-title">Welcome back 👋</h2>
            <p class="login-form-sub">Sign in to your account to continue.</p>

            <!-- Flash messages -->
            <?php renderFlash(); ?>

            <form action="<?= e(APP_URL) ?>/login.php" method="POST" id="loginForm" novalidate>
                <?= csrfField() ?>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label-wms">Email Address</label>
                    <div class="input-group-wms">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control-wms"
                               placeholder="admin@example.com"
                               value="<?= e($_POST['email'] ?? '') ?>"
                               autocomplete="email"
                               required>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label-wms">Password</label>
                    <div class="input-group-wms" style="position:relative">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control-wms"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button"
                                class="input-toggle-pass"
                                data-target="password"
                                aria-label="Toggle password visibility">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-wms-primary" id="btnLogin">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </button>
            </form>

            <div class="divider"></div>
            <p style="font-size:12px;color:var(--text-muted);text-align:center">
                <?= e(APP_NAME) ?> &mdash; v<?= e(APP_VERSION) ?>
            </p>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
<script src="<?= e(ASSET_PATH) ?>/js/main.js"></script>
</body>
</html>
