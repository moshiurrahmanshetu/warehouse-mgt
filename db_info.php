<?php
require_once "includes/bootstrap.php";
$db = Database::getInstance()->getConnection();
$tables = ["users", "roles", "user_roles"];
foreach ($tables as $t) {
    echo "TABLE: $t\n";
    $stmt = $db->query("DESCRIBE $t");
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $r) {
        echo $r["Field"] . " | " . $r["Type"] . "\n";
    }
    echo "---------------------------\n";
}
?>
