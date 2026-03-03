<?php include "config.php";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_ARGON2ID);
$invite = $_POST["invite"];

$stmt = $pdo->prepare("SELECT id FROM invite_codes WHERE code = ? AND used = 0");
$stmt->execute([$invite]);

if($stmt->rowCount() > 0){

    $insert = $pdo->prepare("INSERT INTO users (email,password,access_level) VALUES (?,?,?)");
    $insert->execute([$email,$password,1]);

    $update = $pdo->prepare("UPDATE invite_codes SET used = 1 WHERE code = ?");
    $update->execute([$invite]);

    header("Location: founder.php");
    exit();

}else{
echo "Invalid Invite Code";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="assets/style.css">
<title>KV Access Request</title>
</head>
<body>

<div class="auth-container">
    <h2>Request Imperial Access</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="invite" placeholder="Invite Code" required>
        <button type="submit">Request Allocation</button>
    </form>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
</div>

</body>
</html>
