<?php
$dashboard = file_get_contents("views/dashboard/index.php");
// Remove the footer block from dashboard
$dashboard = preg_replace("/<!-- Footer -->.*?<\/html>/s", "<?php require_once INCLUDE_PATH . \x27/footer.php\x27; ?>", $dashboard);
file_put_contents("views/dashboard/index.php", $dashboard);
echo "Updated dashboard/index.php\n";
?>
