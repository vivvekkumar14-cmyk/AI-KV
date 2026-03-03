<?php
include "config.php";

if($_SESSION["access_level"] != 5){
    die("Founder Clearance Required.");
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/style.css">
<title>KV Founder Command</title>
</head>
<body>

<h1>KV Founder Command Authority</h1>

<div class="panel auth-container">

<h2>Generate Invite Code</h2>
<form method="POST" action="generate_invite.php">
<button>Create Imperial Invite</button>
</form>

<h2>Modify Treasury</h2>
<form method="POST" action="update_treasury.php">
<input type="text" name="asset" placeholder="Asset Name">
<input type="number" name="value" placeholder="New Value">
<button>Override Value</button>
</form>

<h2>Adjust User Access</h2>
<form method="POST" action="update_access.php">
<input type="email" name="email" placeholder="User Email">
<select name="level">
<option value="1">Member</option>
<option value="2">Elite</option>
<option value="3">Institutional</option>
<option value="4">Council</option>
<option value="5">Founder</option>
</select>
<button>Update Access</button>
</form>

</div>

</body>
</html>
