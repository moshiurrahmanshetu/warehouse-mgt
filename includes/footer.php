<?php
/**
 * Footer Include
 */
if (!defined('BASEPATH')) exit('No direct script access');
?>
<footer class="wms-footer">
    <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</span>
    <span>Version <?= e(APP_VERSION) ?> &mdash; <?= e(APP_ENV === 'development' ? '🔧 Development' : '🚀 Production') ?></span>
</footer>
