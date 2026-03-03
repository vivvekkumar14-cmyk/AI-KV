<?php
include "config.php";

function calculateScore($userId, $pdo){

    $wealth = rand(50,100); // Replace with real metric
    $holding = rand(20,80);
    $voting = rand(10,50);
    $invite = rand(30,90);

    $score = ($wealth * 0.35) +
             ($holding * 0.25) +
             ($voting * 0.15) +
             ($invite * 0.25);

    return round($score,2);
}

if(isset($_GET['user_id'])){
    $userId = $_GET['user_id'];
    $score = calculateScore($userId,$pdo);
    $stmt = $pdo->prepare("REPLACE INTO user_metrics (user_id,allocation_score) VALUES (?,?)");
    $stmt->execute([$userId,$score]);
    echo "Score updated: $score";
}
?>
