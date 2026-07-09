<?php
$footer = file_get_contents("includes/footer.php");
$bottom = file_get_contents("includes/layout_bottom.php");
// The layout_bottom has <?php require_once INCLUDE_PATH . "/footer.php"; ?>. We replace that with the $footer content.
$newFooter = str_replace("<?php require_once INCLUDE_PATH . \x27/footer.php\x27; ?>", $footer, $bottom);
file_put_contents("includes/footer.php", $newFooter);
unlink("includes/layout_bottom.php");
echo "Updated footer.php\n";
?>
