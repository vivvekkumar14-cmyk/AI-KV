<?php include "config.php";

if($_SERVER["REQUEST_METHOD"]=="POST"){
$email=$_POST["email"];
$password=$_POST["password"];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if($user && password_verify($password,$user["password"])){
$_SESSION["user"]=$user["email"];
$_SESSION["role"]=$user["role"];
$_SESSION["access_level"]=$user["access_level"];
header("Location: dashboard.php");
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="assets/style.css">
<title>KV Founder Access</title>
</head>
<body>

<div class="auth-container">
    <h2>Founder Biometric Access</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button>Authenticate</button>
    </form>
</div>

</body>
</html>
