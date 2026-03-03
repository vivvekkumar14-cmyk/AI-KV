<?php
include "config.php";
session_start();

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach($_SESSION['cart'] as $item) $total += $item['price']*$item['quantity'];
$gateway = $conn->query("SELECT gateway FROM payment_settings WHERE id=1")->fetch_assoc()['gateway'];
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KV Checkout</title>
<link rel="stylesheet" href="assets/css/luxury.css">
</head>
<body>

<div class="container">
<h1 class="gradient-text">Checkout</h1>
<div class="card">
<p>Total Amount: <strong>$<?= number_format($total,2) ?></strong></p>

<?php if($gateway=='razorpay'): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<button id="payBtn" class="btn btn-silver">Pay with Razorpay</button>

<script>
var options = {
    "key":"YOUR_RAZORPAY_KEY",
    "amount":<?= $total*100 ?>,
    "currency":"INR",
    "name":"KV Western Luxury",
    "handler":function(res){ window.location.href="verify_payment.php?tx="+res.razorpay_payment_id; }
};
document.getElementById('payBtn').onclick = e => { new Razorpay(options).open(); e.preventDefault(); }
</script>

<?php elseif($gateway=='stripe'): ?>
<a href="stripe_checkout.php" class="btn btn-silver">Pay with Stripe</a>
<?php elseif($gateway=='paypal'): ?>
<form action="paypal_checkout.php" method="POST">
<input type="hidden" name="amount" value="<?= $total ?>">
<button type="submit" class="btn btn-silver">Pay with PayPal</button>
</form>
<?php endif; ?>
</div>
</div>

<script src="assets/js/luxury.js"></script>
</body>
</html>
