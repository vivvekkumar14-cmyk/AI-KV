<?php
session_start();
include "config.php"; // your DB + session config

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach($cart as $item) $total += $item['price']*$item['quantity'];

// Fetch payment gateway
$gateway = $conn->query("SELECT gateway FROM payment_settings WHERE id=1")->fetch_assoc()['gateway'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KV Mansion Checkout</title>
<link rel="stylesheet" href="assets/css/luxury.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" rel="stylesheet"/>
<style>
/* KV Mansion Cinematic Background */
#kv-bg { position:fixed; top:0; left:0; width:100%; height:100%; z-index:-1; overflow:hidden; background:#000; }
#kv-blur { position:fixed; top:0; left:0; width:100%; height:100%; backdrop-filter: blur(4px); z-index:0; }
.card { position:relative; z-index:1; background: rgba(10,10,10,0.85); border:1px solid #c0c0c0; border-radius:20px; padding:30px; margin-bottom:25px; overflow:hidden; opacity:0; transform:translateY(20px); transition: all 0.6s ease; }
.gradient-text { background: linear-gradient(90deg, #ffd700, #c0c0c0, #ffd700); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; color:transparent; animation: gradientMove 3s infinite; }
@keyframes gradientMove {0%{background-position:0%}50%{background-position:100%}100%{background-position:0%}}
.btn-kvm { padding:12px 25px; border-radius:8px; font-weight:600; background:linear-gradient(145deg,#c0c0c0,#ffd700); color:#000; border:none; cursor:pointer; transition:0.3s; text-decoration:none; display:inline-block; }
.btn-kvm:hover { box-shadow:0 0 15px #ffd700; }
.table { width:100%; border-collapse:collapse; color:#c0c0c0; text-align:left; }
.table th, .table td { padding:12px; border-bottom:1px solid #888; }
.table th { background:#111; }
.table tr:hover { background: rgba(255,215,0,0.05); }
</style>
</head>
<body>

<div id="kv-bg"></div>
<div id="kv-blur"></div>

<div class="container">
<h1 class="gradient-text">KV Mansion Checkout</h1>

<?php if(count($cart)>0): ?>
<div class="card">
<table class="table">
<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
<?php foreach($cart as $item):
$subtotal = $item['price']*$item['quantity'];
?>
<tr>
<td><?= htmlspecialchars($item['title']) ?></td>
<td>$<?= number_format($item['price'],2) ?></td>
<td><?= $item['quantity'] ?></td>
<td>$<?= number_format($subtotal,2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<h3>Total: $<?= number_format($total,2) ?></h3>

<?php if($gateway=='razorpay'): ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<button id="payBtn" class="btn btn-silver">Pay with Razorpay</button>
<script>
var options={key:"YOUR_RAZORPAY_KEY",amount:<?= $total*100 ?>,currency:"INR",name:"KV Mansion",
handler:function(res){ window.location.href="verify_payment.php?tx="+res.razorpay_payment_id; }};
document.getElementById('payBtn').onclick=e=>{new Razorpay(options).open();e.preventDefault();}
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
<?php else: ?>
<div class="card">
<p>Your cart is empty.</p>
<a href="products.html" class="btn btn-silver">Shop Now</a>
</div>
<?php endif; ?>

<h2 class="gradient-text">My Orders</h2>
<div class="card">
<table class="table">
<thead><tr><th>Order ID</th><th>Total</th><th>Date</th><th>Download</th><th>Invoice</th></tr></thead>
<tbody>
<?php
$user_id = $_SESSION['user_id'] ?? 0;
$query = $conn->query("SELECT o.id,o.total_amount,o.created_at,d.download_token,d.expiry_date FROM orders o JOIN downloads d ON o.id=d.order_id WHERE o.user_id=$user_id ORDER BY o.created_at DESC");
while($row=$query->fetch_assoc()):
?>
<tr>
<td><?= $row['id'] ?></td>
<td>$<?= number_format($row['total_amount'],2) ?></td>
<td><?= $row['created_at'] ?></td>
<td><?php if(strtotime($row['expiry_date'])>time()): ?><a href="download.php?token=<?= $row['download_token'] ?>" class="btn btn-silver">Download</a><?php else: ?>Expired<?php endif;?></td>
<td><a href="invoice.php?id=<?= $row['id'] ?>" class="btn btn-silver">Invoice</a></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<script>
// Fade-in cards
document.addEventListener("DOMContentLoaded",()=>{
  document.querySelectorAll(".card").forEach((c,i)=>setTimeout(()=>{c.style.opacity=1;c.style.transform='translateY(0)';},i*150));
});

// Gold/Silver particles
const canvas=document.createElement('canvas');document.getElementById('kv-bg').appendChild(canvas);
canvas.width=window.innerWidth;canvas.height=window.innerHeight;
const ctx=canvas.getContext('2d');
let particles=[];for(let i=0;i<150;i++){particles.push({x:Math.random()*canvas.width,y:Math.random()*canvas.height,r:Math.random()*2+1,dx:(Math.random()-0.5)*0.3,dy:(Math.random()-0.5)*0.3,color:Math.random()>0.5?'#c0c0c0':'#ffd700',alpha:Math.random()*0.5+0.2});}
function animate(){ctx.clearRect(0,0,canvas.width,canvas.height);particles.forEach(p=>{ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle=`rgba(${p.color=='#ffd700'?255:192},${p.color=='#ffd700'?215:192},0,${p.alpha})`;ctx.fill();p.x+=p.dx;p.y+=p.dy;if(p.x>canvas.width)p.x=0;if(p.x<0)p.x=canvas.width;if(p.y>canvas.height)p.y=0;if(p.y<0)p.y=canvas.height;});requestAnimationFrame(animate);}
animate();

// Resize handler
window.addEventListener('resize',()=>{
  canvas.width=window.innerWidth;
  canvas.height=window.innerHeight;
});
</script>
</body>
</html>
