<?php
session_start();
include "config.php";
$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KV Cart</title>
<link rel="stylesheet" href="assets/css/luxury.css">
</head>
<body>

<div class="container">
<h1 class="gradient-text">Your Cart</h1>

<?php if(count($cart) > 0): ?>
<div class="card">
<table class="table">
<thead>
<tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
</thead>
<tbody>
<?php foreach($cart as $item):
    $subtotal = $item['price']*$item['quantity'];
    $total += $subtotal;
?>
<tr>
<td><?= htmlspecialchars($item['title']); ?></td>
<td>$<?= number_format($item['price'],2) ?></td>
<td><?= $item['quantity'] ?></td>
<td>$<?= number_format($subtotal,2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<h3>Total: $<?= number_format($total,2) ?></h3>
<a href="checkout.php" class="btn btn-silver">Proceed to Checkout</a>
</div>
<?php else: ?>
<div class="card">
<p>Your cart is empty.</p>
<a href="products.html" class="btn btn-silver">Shop Now</a>
</div>
<?php endif; ?>
</div>

<script src="assets/js/luxury.js"></script>
</body>
</html>
