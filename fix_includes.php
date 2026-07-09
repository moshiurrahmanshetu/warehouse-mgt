<?php
$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("views"));
foreach ($dir as $file) {
    if ($file->isFile() && $file->getExtension() === "php") {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, "VIEW_PATH . \x27/includes/") !== false) {
            $content = str_replace("VIEW_PATH . \x27/includes/", "INCLUDE_PATH . \x27/", $content);
            file_put_contents($file->getPathname(), $content);
            echo "Fixed: " . $file->getPathname() . "\n";
        }
    }
}
?>
