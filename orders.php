<?php
session_start();
include "config.php";
$user_id = $_SESSION['user_id'] ?? 0;

$query = $conn->query("
SELECT o.id,o.total_amount,o.created_at,d.download_token,d.expiry_date
FROM orders o
JOIN downloads d ON o.id=d.order_id
WHERE o.user_id=$user_id
ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KV Orders</title>
<link rel="stylesheet" href="assets/css/luxury.css">
</head>
<body>

<div class="container">
<h1 class="gradient-text">My Orders</h1>
<div class="card">
<table class="table">
<thead><tr><th>Order ID</th><th>Total</th><th>Date</th><th>Download</th><th>Invoice</th></tr></thead>
<tbody>
<?php while($row=$query->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td>$<?= number_format($row['total_amount'],2) ?></td>
<td><?= $row['created_at'] ?></td>
<td>
<?php if(strtotime($row['expiry_date'])>time()): ?>
<a href="download.php?token=<?= $row['download_token'] ?>" class="btn btn-silver">Download</a>
<?php else: ?>Expired<?php endif; ?>
</td>
<td><a href="invoice.php?id=<?= $row['id'] ?>" class="btn btn-silver">Invoice</a></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

<script src="assets/js/luxury.js"></script>
</body>
</html>
