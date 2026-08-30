<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; <?= e(APP_NAME) ?></title>
    <meta name="description" content="Sign in to the Warehouse Management System.">
    <meta name="robots" content="noindex, nofollow">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- App Styles -->
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/style.css">
    <!-- Login Dedicated Styles -->
    <link rel="stylesheet" href="<?= e(ASSET_PATH) ?>/css/login.css">
</head>
<body>
<div class="login-page">
    <!-- Background pattern -->
    <div class="login-bg-pattern"></div>

    <div class="login-wrapper animate-in">
        <div class="login-grid">

            <!-- Left Panel (Branding & Overview) -->
            <div class="login-left">
                <div>
                    <div class="login-brand">
                        <div class="login-brand-icon">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <h2 class="login-brand-title">WMS Pro</h2>
                            <p class="login-brand-subtitle">Enterprise Logistics</p>
                        </div>
                    </div>

                    <h1 class="login-headline">Warehouse<br>Management<br><span class="text-gradient">System</span></h1>
                    <p class="login-desc">
                        A powerful, intelligent platform engineered to streamline and optimize your warehouse, inventory, supply chain, and logistics operations.
                    </p>

                    <div class="login-features">
                        <div class="login-feature">
                            <div class="login-feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <span>Real-time inventory & stock tracking</span>
                        </div>
                        <div class="login-feature">
                            <div class="login-feature-icon"><i class="bi bi-diagram-3"></i></div>
                            <span>Multi-warehouse, zone, rack & bin hierarchy</span>
                        </div>
                        <div class="login-feature">
                            <div class="login-feature-icon"><i class="bi bi-shield-check"></i></div>
                            <span>Role-based granular access control (RBAC)</span>
                        </div>
                        <div class="login-feature">
                            <div class="login-feature-icon"><i class="bi bi-clock-history"></i></div>
                            <span>Comprehensive security & audit logging</span>
                        </div>
                    </div>
                </div>

                <div class="login-left-footer">
                    <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
                </div>
            </div>

            <!-- Right Panel (Login Form) -->
            <div class="login-right">
                <div class="login-header">
                    <h2 class="login-form-title">Welcome back 👋</h2>
                    <p class="login-form-sub">Sign in with your credentials to access the system.</p>
                </div>

                <!-- Flash messages -->
                <?php renderFlash(); ?>

                <form action="<?= e(APP_URL) ?>/login.php" method="POST" id="loginForm" novalidate>
                    <?= csrfField() ?>

                    <!-- Email -->
                    <div class="mb-3">
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
                    <div class="mb-3">
                        <label for="password" class="form-label-wms">Password</label>
                        <div class="input-group-wms">
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

                    <!-- Options: Remember Me -->
                    <div class="login-options">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-wms-primary" id="btnLogin">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In</span>
                    </button>
                </form>

                <div class="login-divider"></div>
                <p class="login-footer-text">
                    <?= e(APP_NAME) ?> &mdash; v<?= e(APP_VERSION) ?>
                </p>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- App JS -->
<script src="<?= e(ASSET_PATH) ?>/js/main.js"></script>
</body>
</html>
