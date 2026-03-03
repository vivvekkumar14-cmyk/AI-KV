<?php
session_start();
include "config.php";

$user_id = $_SESSION['user_id'] ?? 0;
$tx = $_GET['tx'] ?? '';

if(empty($tx)) {
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach($_SESSION['cart'] as $item){
    $total += $item['price'] * $item['quantity'];
}

// Insert order
$stmt = $conn->prepare("INSERT INTO orders (user_id,total_amount,payment_method,payment_status,transaction_id) VALUES (?,?,?,?,?)");
$stmt->bind_param("idsss",$user_id,$total,'razorpay','paid',$tx);
$stmt->execute();
$order_id = $conn->insert_id;

// Insert order items and generate download links
$secureFolder = "../secure_downloads/";
foreach($_SESSION['cart'] as $item){
    $conn->query("INSERT INTO order_items (order_id,product_id,price,quantity) VALUES ($order_id,{$item['id']},{$item['price']},{$item['quantity']})");

    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime("+7 days"));

    $conn->query("INSERT INTO downloads (order_id,product_id,download_token,expiry_date) VALUES ($order_id,{$item['id']},'$token','$expiry')");
}

unset($_SESSION['cart']);

header("Location: orders.php");
exit;
?>
