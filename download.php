<?php
include "config.php";
$token = $_GET['token'];

if(empty($token)) {
    die("Invalid download link.");
}

$q = $conn->query("
SELECT p.file_path FROM downloads d
JOIN products p ON d.product_id = p.id
WHERE d.download_token='$token' AND d.expiry_date > NOW()
");

if($q->num_rows > 0){
    $file = $q->fetch_assoc()['file_path'];
    $filePath = "../secure_downloads/".$file;
    
    if(file_exists($filePath)) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".basename($file));
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
    }
} else {
    echo "Invalid or Expired Link.";
}
?>
