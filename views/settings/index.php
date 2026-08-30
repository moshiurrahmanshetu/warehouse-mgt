<?php
require_once INCLUDE_PATH . '/header.php';
require_once INCLUDE_PATH . '/navbar.php';
?>
<div class="d-flex">
    <?php require_once INCLUDE_PATH . '/sidebar.php'; ?>
    <main class="wms-main p-4 flex-grow-1">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(APP_URL) ?>/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Settings</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-gear-wide-connected me-2 text-primary"></i>System Settings</h2>
                <p class="text-muted mb-0" style="font-size:13px;">Configure company details, localization, default warehouse, and system preferences.</p>
            </div>
            <div>
                <a href="<?= e(APP_URL) ?>/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php renderFlash(); ?>

        <form action="<?= e(APP_URL) ?>/settings.php?action=update" method="POST" enctype="multipart/form-data" id="settingsForm">
            <?= csrfField() ?>
            <input type="hidden" name="_active_tab" id="activeTabInput" value="general">

            <div class="row g-4">
                
                <!-- Left Navigation Pills -->
                <div class="col-lg-3">
                    <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                        <div class="card-body p-2">
                            <div class="nav flex-column nav-pills" id="settingsNavPills" role="tablist" aria-orientation="vertical">
                                
                                <button class="nav-link active text-start py-3 px-3 mb-1 fw-semibold d-flex align-items-center" id="tab-general-btn" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab" aria-controls="tab-general" aria-selected="true" style="border-radius:8px;">
                                    <i class="bi bi-building me-2 fs-5 text-primary"></i>
                                    <div>
                                        <div style="font-size:13.5px;">General Details</div>
                                        <small class="text-muted d-block" style="font-size:11px;">Company name, contact info</small>
                                    </div>
                                </button>

                                <button class="nav-link text-start py-3 px-3 mb-1 fw-semibold d-flex align-items-center" id="tab-localization-btn" data-bs-toggle="pill" data-bs-target="#tab-localization" type="button" role="tab" aria-controls="tab-localization" aria-selected="false" style="border-radius:8px;">
                                    <i class="bi bi-globe me-2 fs-5 text-info"></i>
                                    <div>
                                        <div style="font-size:13.5px;">Localization</div>
                                        <small class="text-muted d-block" style="font-size:11px;">Currency, timezone, date formats</small>
                                    </div>
                                </button>

                                <button class="nav-link text-start py-3 px-3 mb-1 fw-semibold d-flex align-items-center" id="tab-warehouse-btn" data-bs-toggle="pill" data-bs-target="#tab-warehouse" type="button" role="tab" aria-controls="tab-warehouse" aria-selected="false" style="border-radius:8px;">
                                    <i class="bi bi-box-seam me-2 fs-5 text-warning"></i>
                                    <div>
                                        <div style="font-size:13.5px;">Warehouse Defaults</div>
                                        <small class="text-muted d-block" style="font-size:11px;">Default operating warehouse</small>
                                    </div>
                                </button>

                                <button class="nav-link text-start py-3 px-3 mb-1 fw-semibold d-flex align-items-center" id="tab-system-btn" data-bs-toggle="pill" data-bs-target="#tab-system" type="button" role="tab" aria-controls="tab-system" aria-selected="false" style="border-radius:8px;">
                                    <i class="bi bi-sliders me-2 fs-5 text-success"></i>
                                    <div>
                                        <div style="font-size:13.5px;">System &amp; Security</div>
                                        <small class="text-muted d-block" style="font-size:11px;">Pagination, session, maintenance</small>
                                    </div>
                                </button>

                                <button class="nav-link text-start py-3 px-3 fw-semibold d-flex align-items-center" id="tab-branding-btn" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab" aria-controls="tab-branding" aria-selected="false" style="border-radius:8px;">
                                    <i class="bi bi-palette me-2 fs-5 text-danger"></i>
                                    <div>
                                        <div style="font-size:13.5px;">Logos &amp; Branding</div>
                                        <small class="text-muted d-block" style="font-size:11px;">Company logo &amp; favicon</small>
                                    </div>
                                </button>

                            </div>
                        </div>
                    </div>

                    <!-- Action Save Button Card -->
                    <div class="card shadow-sm border-0 mt-3" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                        <div class="card-body p-3">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-check-circle me-1"></i> Save All Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Settings Content Panes -->
                <div class="col-lg-9">
                    <div class="tab-content" id="settingsTabContent">

                        <!-- 1. General Settings -->
                        <div class="tab-pane fade show active" id="tab-general" role="tabpanel" aria-labelledby="tab-general-btn">
                            <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                                <div class="card-header p-3 border-bottom d-flex align-items-center" style="background:transparent;border-color:var(--border-color)!important;">
                                    <i class="bi bi-building text-primary me-2 fs-5"></i>
                                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary);">General Company Settings</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" name="company_name" class="form-control" required
                                                   value="<?= e($settings['company_name'] ?? '') ?>"
                                                   placeholder="e.g. WMS Logistics Inc.">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Company Email</label>
                                            <input type="email" name="company_email" class="form-control"
                                                   value="<?= e($settings['company_email'] ?? '') ?>"
                                                   placeholder="info@example.com">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Company Phone</label>
                                            <input type="text" name="company_phone" class="form-control"
                                                   value="<?= e($settings['company_phone'] ?? '') ?>"
                                                   placeholder="+1 (555) 000-0000">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Company Website</label>
                                            <input type="url" name="company_website" class="form-control"
                                                   value="<?= e($settings['company_website'] ?? '') ?>"
                                                   placeholder="https://example.com">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Company Address</label>
                                            <textarea name="company_address" class="form-control" rows="3"
                                                      placeholder="Full physical street address"><?= e($settings['company_address'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Localization Settings -->
                        <div class="tab-pane fade" id="tab-localization" role="tabpanel" aria-labelledby="tab-localization-btn">
                            <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                                <div class="card-header p-3 border-bottom d-flex align-items-center" style="background:transparent;border-color:var(--border-color)!important;">
                                    <i class="bi bi-globe text-info me-2 fs-5"></i>
                                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary);">Localization &amp; Regional Settings</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        
                                        <!-- Default Currency (Reusing Currency Table) -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Default Currency
                                            </label>
                                            <select name="default_currency_id" class="form-select">
                                                <option value="">-- Select Default Currency --</option>
                                                <?php
                                                $currentCurId = (int)($settings['default_currency_id'] ?? 0);
                                                foreach ($currencies as $curr):
                                                    $selected = ($currentCurId === (int)$curr['id'] || (empty($currentCurId) && !empty($curr['is_default']))) ? 'selected' : '';
                                                ?>
                                                    <option value="<?= (int)$curr['id'] ?>" <?= $selected ?>>
                                                        <?= e($curr['currency_name']) ?> (<?= e($curr['currency_code']) ?> - <?= e($curr['currency_symbol']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                Currencies are synced with the Currencies Master Data table.
                                            </div>
                                        </div>

                                        <!-- Timezone -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Timezone
                                            </label>
                                            <?php
                                            $selectedTz = $settings['timezone'] ?? 'Asia/Dhaka';
                                            $commonTimezones = [
                                                'UTC'                => 'UTC (Coordinated Universal Time)',
                                                'Asia/Dhaka'         => 'Asia/Dhaka (GMT+6)',
                                                'Asia/Kolkata'       => 'Asia/Kolkata (GMT+5:30)',
                                                'Asia/Bangkok'       => 'Asia/Bangkok (GMT+7)',
                                                'Asia/Singapore'     => 'Asia/Singapore (GMT+8)',
                                                'Asia/Tokyo'         => 'Asia/Tokyo (GMT+9)',
                                                'Asia/Dubai'         => 'Asia/Dubai (GMT+4)',
                                                'Europe/London'      => 'Europe/London (GMT+0 / BST)',
                                                'Europe/Berlin'      => 'Europe/Berlin (GMT+1)',
                                                'America/New_York'   => 'America/New_York (EST/EDT)',
                                                'America/Chicago'    => 'America/Chicago (CST/CDT)',
                                                'America/Los_Angeles'=> 'America/Los_Angeles (PST/PDT)',
                                                'Australia/Sydney'   => 'Australia/Sydney (AEST)',
                                            ];
                                            ?>
                                            <select name="timezone" class="form-select">
                                                <?php foreach ($commonTimezones as $tzKey => $tzLabel): ?>
                                                    <option value="<?= e($tzKey) ?>" <?= $selectedTz === $tzKey ? 'selected' : '' ?>>
                                                        <?= e($tzLabel) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Date Format -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Date Display Format
                                            </label>
                                            <?php
                                            $selectedDateFormat = $settings['date_format'] ?? 'd M Y';
                                            $dateFormats = [
                                                'd M Y'   => 'd M Y (' . date('d M Y') . ')',
                                                'Y-m-d'   => 'Y-m-d (' . date('Y-m-d') . ')',
                                                'd/m/Y'   => 'd/m/Y (' . date('d/m/Y') . ')',
                                                'm/d/Y'   => 'm/d/Y (' . date('m/d/Y') . ')',
                                                'd-m-Y'   => 'd-m-Y (' . date('d-m-Y') . ')',
                                                'F j, Y'  => 'F j, Y (' . date('F j, Y') . ')',
                                            ];
                                            ?>
                                            <select name="date_format" class="form-select">
                                                <?php foreach ($dateFormats as $dfKey => $dfLabel): ?>
                                                    <option value="<?= e($dfKey) ?>" <?= $selectedDateFormat === $dfKey ? 'selected' : '' ?>>
                                                        <?= e($dfLabel) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Time Format -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Time Display Format
                                            </label>
                                            <?php
                                            $selectedTimeFormat = $settings['time_format'] ?? 'h:i A';
                                            $timeFormats = [
                                                'h:i A'   => '12-Hour: 02:30 PM (h:i A)',
                                                'H:i'     => '24-Hour: 14:30 (H:i)',
                                                'h:i:s A' => '12-Hour with seconds: 02:30:45 PM (h:i:s A)',
                                                'H:i:s'   => '24-Hour with seconds: 14:30:45 (H:i:s)',
                                            ];
                                            ?>
                                            <select name="time_format" class="form-select">
                                                <?php foreach ($timeFormats as $tfKey => $tfLabel): ?>
                                                    <option value="<?= e($tfKey) ?>" <?= $selectedTimeFormat === $tfKey ? 'selected' : '' ?>>
                                                        <?= e($tfLabel) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Warehouse Defaults Settings -->
                        <div class="tab-pane fade" id="tab-warehouse" role="tabpanel" aria-labelledby="tab-warehouse-btn">
                            <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                                <div class="card-header p-3 border-bottom d-flex align-items-center" style="background:transparent;border-color:var(--border-color)!important;">
                                    <i class="bi bi-box-seam text-warning me-2 fs-5"></i>
                                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary);">Warehouse Defaults</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        
                                        <!-- Default Warehouse (Reusing Warehouses Table) -->
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Default Operating Warehouse
                                            </label>
                                            <select name="default_warehouse_id" class="form-select">
                                                <option value="">-- No Default Selected --</option>
                                                <?php
                                                $currentWhId = (int)($settings['default_warehouse_id'] ?? 0);
                                                foreach ($warehouses as $wh):
                                                ?>
                                                    <option value="<?= (int)$wh['id'] ?>" <?= $currentWhId === (int)$wh['id'] ? 'selected' : '' ?>>
                                                        <?= e($wh['warehouse_name']) ?> (<?= e($wh['warehouse_code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                This warehouse will be pre-selected in receiving, shipping, and stock transfer operations.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. System & Security Settings -->
                        <div class="tab-pane fade" id="tab-system" role="tabpanel" aria-labelledby="tab-system-btn">
                            <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                                <div class="card-header p-3 border-bottom d-flex align-items-center" style="background:transparent;border-color:var(--border-color)!important;">
                                    <i class="bi bi-sliders text-success me-2 fs-5"></i>
                                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary);">System &amp; Application Preferences</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Application Name <span class="text-danger">*</span></label>
                                            <input type="text" name="app_name" class="form-control" required
                                                   value="<?= e($settings['app_name'] ?? 'Warehouse Management System') ?>"
                                                   placeholder="Warehouse Management System">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Default Items Per Page</label>
                                            <?php
                                            $currentLimit = (int)($settings['items_per_page'] ?? 25);
                                            ?>
                                            <select name="items_per_page" class="form-select">
                                                <option value="10" <?= $currentLimit === 10 ? 'selected' : '' ?>>10 records</option>
                                                <option value="15" <?= $currentLimit === 15 ? 'selected' : '' ?>>15 records</option>
                                                <option value="25" <?= $currentLimit === 25 ? 'selected' : '' ?>>25 records</option>
                                                <option value="50" <?= $currentLimit === 50 ? 'selected' : '' ?>>50 records</option>
                                                <option value="100" <?= $currentLimit === 100 ? 'selected' : '' ?>>100 records</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">Session Timeout (seconds)</label>
                                            <input type="number" name="session_timeout" class="form-control" min="60" max="86400"
                                                   value="<?= (int)($settings['session_timeout'] ?? 3600) ?>">
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                e.g. 3600 = 1 hour.
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-flex align-items-center">
                                            <div class="form-check form-switch mt-3">
                                                <input class="form-check-input" type="checkbox" name="enable_activity_log" id="enableActivityLog" value="1"
                                                    <?= (!empty($settings['enable_activity_log']) && $settings['enable_activity_log'] == '1') ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-semibold" for="enableActivityLog" style="color:var(--text-primary);font-size:13.5px;">
                                                    Enable User Activity Logging
                                                </label>
                                                <small class="text-muted d-block" style="font-size:11.5px;">
                                                    Audit logs for login, creates, updates, and deletes.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <hr style="border-color:var(--border-color);">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" value="1"
                                                    <?= (!empty($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' ?>>
                                                <label class="form-check-label fw-semibold text-danger" for="maintenanceMode" style="font-size:13.5px;">
                                                    <i class="bi bi-cone-striped me-1"></i> Maintenance Mode
                                                </label>
                                                <small class="text-muted d-block" style="font-size:11.5px;">
                                                    When enabled, non-admin users will receive a maintenance notice.
                                                </small>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Logos & Branding Settings -->
                        <div class="tab-pane fade" id="tab-branding" role="tabpanel" aria-labelledby="tab-branding-btn">
                            <div class="card shadow-sm border-0" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--border-radius);">
                                <div class="card-header p-3 border-bottom d-flex align-items-center" style="background:transparent;border-color:var(--border-color)!important;">
                                    <i class="bi bi-palette text-danger me-2 fs-5"></i>
                                    <h5 class="mb-0 fw-bold" style="color:var(--text-primary);">Logos &amp; Visual Branding</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">

                                        <!-- Application Logo -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Company / System Logo
                                            </label>
                                            <div class="p-3 mb-2 rounded text-center" style="background:rgba(255,255,255,0.03);border:1px dashed var(--border-color);min-height:120px;display:flex;align-items:center;justify-content:center;">
                                                <?php if (!empty($settings['app_logo']) && file_exists(BASEPATH . '/' . $settings['app_logo'])): ?>
                                                    <img src="<?= e(APP_URL . '/' . $settings['app_logo']) ?>" alt="System Logo"
                                                         class="img-fluid" style="max-height:80px;object-fit:contain;">
                                                <?php else: ?>
                                                    <div class="text-muted" style="font-size:13px;">
                                                        <i class="bi bi-image d-block fs-3 mb-1 opacity-50"></i>
                                                        No custom logo uploaded (using default WMS Pro branding)
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <input type="file" name="app_logo" class="form-control"
                                                   accept="image/jpeg,image/png,image/webp,image/svg+xml">
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                Recommended: PNG or SVG with transparent background (Max 2MB).
                                            </div>
                                        </div>

                                        <!-- Favicon -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" style="color:var(--text-secondary);font-size:13px;">
                                                Browser Favicon
                                            </label>
                                            <div class="p-3 mb-2 rounded text-center" style="background:rgba(255,255,255,0.03);border:1px dashed var(--border-color);min-height:120px;display:flex;align-items:center;justify-content:center;">
                                                <?php if (!empty($settings['app_favicon']) && file_exists(BASEPATH . '/' . $settings['app_favicon'])): ?>
                                                    <img src="<?= e(APP_URL . '/' . $settings['app_favicon']) ?>" alt="Favicon"
                                                         style="width:36px;height:36px;object-fit:contain;">
                                                <?php else: ?>
                                                    <div class="text-muted" style="font-size:13px;">
                                                        <i class="bi bi-app-indicator d-block fs-3 mb-1 opacity-50"></i>
                                                        Default Favicon (📦)
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <input type="file" name="app_favicon" class="form-control"
                                                   accept="image/x-icon,image/png,image/svg+xml,image/jpeg">
                                            <div class="form-text" style="font-size:11.5px;color:var(--text-muted);">
                                                Recommended: 32x32px or 64x64px ICO, PNG, or SVG.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const activeTabInput = document.getElementById('activeTabInput');
    const navPills = document.querySelectorAll('#settingsNavPills button[data-bs-toggle="pill"]');

    // Update active tab input on pill switch
    navPills.forEach(pill => {
        pill.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target').replace('#tab-', '');
            if (activeTabInput) {
                activeTabInput.value = targetId;
            }
            history.replaceState(null, null, '#' + targetId);
        });
    });

    // Restore tab from URL hash if available
    const currentHash = window.location.hash.replace('#', '');
    if (currentHash) {
        const targetBtn = document.getElementById('tab-' + currentHash + '-btn');
        if (targetBtn) {
            const tab = new bootstrap.Tab(targetBtn);
            tab.show();
            if (activeTabInput) {
                activeTabInput.value = currentHash;
            }
        }
    }
});
</script>

<?php require_once INCLUDE_PATH . '/footer.php'; ?>
