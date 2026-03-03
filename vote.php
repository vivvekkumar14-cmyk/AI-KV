<?php include "config.php";
if(!isset($_SESSION["user"])) header("Location:index.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){
$proposal=$_POST["proposal"];
$vote=$_POST["vote"];
$user=$_SESSION["user"];

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$user]);
$userId = $stmt->fetch()["id"];

$insert = $pdo->prepare("INSERT INTO votes (user_id,proposal,vote) VALUES (?,?,?)");
$insert->execute([$userId,$proposal,$vote]);

$log = $pdo->prepare("INSERT INTO audit_logs (user_id,action) VALUES (?,?)");
$log->execute([$userId,"Submitted Governance Vote"]);

echo "Vote Recorded.";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="assets/style.css">
<title>KV Governance Chamber</title>
</head>
<body>

<div class="governance">
    <h2>Governance Chamber</h2>
    
    <div class="vote-weight">
        <p>Your Vote Weight: <?= $_SESSION["access_level"] ?></p>
    </div>

    <form method="POST" class="vote-form">
        <input type="text" name="proposal" placeholder="Proposal Title" required>
        <select name="vote">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
        <button>Submit Vote</button>
    </form>

    <div class="actions">
        <a href="dashboard.php" class="btn">Back to Command</a>
    </div>
</div>

</body>
</html>
