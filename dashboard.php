<?php include "config.php";
if(!isset($_SESSION["user"])) header("Location:index.php");

if($_SESSION["access_level"] < 3){
   die("Insufficient Sovereign Clearance.");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<link rel="stylesheet" href="assets/style.css">
<title>KV Treasury Command</title>
</head>
<body>

<div class="dashboard">
    <h1>KV Treasury Command</h1>
    
    <div class="treasury-section">
        <h2>Imperial Treasury</h2>
        <?php
        $stmt = $pdo->query("SELECT * FROM treasury");
        while($row = $stmt->fetch()){
            echo "<div class='treasury-item'>";
            echo "<h3>".$row["asset_name"]." : $".$row["value"]."</h3>";
            echo "</div>";
        }
        ?>
    </div>

    <div class="actions">
        <a href="vote.php" class="btn">Enter Governance Chamber</a>
        <a href="logout.php" class="btn">Exit</a>
        <?php if($_SESSION["access_level"] == 5): ?>
            <a href="founder_control.php" class="btn">Founder Override Panel</a>
        <?php endif; ?>
    </div>
</div>

<script>
setInterval(()=>{
 fetch("treasury_api.php")
 .then(res=>res.json())
 .then(data=>{
    console.log(data);
 });
},5000);
</script>

</body>
</html>
