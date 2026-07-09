<?php
session_start();
$_SESSION["user_id"] = 1;
$_SESSION["user"] = ["id" => 1, "name" => "Test Admin"];
$_SESSION["roles"] = ["admin"];
// Give all permissions
$_SESSION["permissions"] = [
    "warehouses.view", "zones.view", "racks.view", "shelves.view", "bins.view",
    "suppliers.view", "customers.view",
    "categories.view", "brands.view", "units.view", "tax_rates.view", 
    "currencies.view", "attributes.view", "attribute_values.view", "product_tags.view"
];

$pages = [
    "dashboard.php",
    "warehouses.php", "zones.php", "racks.php", "shelves.php", "bins.php",
    "suppliers.php", "customers.php",
    "categories.php", "brands.php", "units.php", "tax_rates.php",
    "currencies.php", "attributes.php", "attribute_values.php", "product_tags.php"
];

$errors = [];
foreach ($pages as $page) {
    if (!file_exists($page)) {
        $errors[] = "$page - File not found";
        continue;
    }
    // Run via exec to capture output and isolate
    $cmd = "php -d display_errors=1 -d error_reporting=E_ALL " . escapeshellarg($page);
    exec($cmd, $output, $return_var);
    $outputStr = implode("\n", $output);
    
    if (strpos($outputStr, "Fatal error") !== false || strpos($outputStr, "Parse error") !== false) {
        $errors[] = "$page - PHP Error found:\n" . substr($outputStr, 0, 500);
    } else {
        echo "$page - OK\n";
    }
    // reset output
    unset($output);
}

if (count($errors) > 0) {
    echo "\nERRORS FOUND:\n";
    foreach ($errors as $e) {
        echo $e . "\n\n";
    }
} else {
    echo "\nAll pages loaded successfully without fatal errors!\n";
}
?>
