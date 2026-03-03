<?php
include "config.php";
header("Content-Type: application/json");
$stmt = $pdo->query("SELECT asset_name,value FROM treasury");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
