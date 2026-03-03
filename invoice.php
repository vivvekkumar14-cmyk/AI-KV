<?php
include "config.php";
$order_id = $_GET['id'];

if(empty($order_id)) {
    die("Invalid order ID.");
}

$order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
$items = $conn->query("SELECT * FROM order_items WHERE order_id=$order_id");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KV Invoice</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <div class="invoice">
        <h1>KV Western Luxury Invoice</h1>
        
        <div class="invoice-header">
            <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
            <p><strong>Payment Status:</strong> <?= ucfirst($order['payment_status']) ?></p>
            <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
            <p><strong>Transaction ID:</strong> <?= $order['transaction_id'] ?></p>
        </div>

        <hr>

        <div class="invoice-items">
            <h3>Items:</h3>
            <table class="table">
                <thead>
                    <tr><th>Product ID</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <?php while($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?= $item['product_id'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'],2) ?></td>
                        <td>$<?= number_format($item['price'] * $item['quantity'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="invoice-total">
            <h3>Total Amount: $<?= number_format($order['total_amount'],2) ?></h3>
        </div>

        <div class="invoice-footer">
            <p>Thank you for your purchase!</p>
            <a href="javascript:window.print()" class="btn-small">Print Invoice</a>
        </div>
    </div>
</div>

</body>
</html>
