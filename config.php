<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=kv;charset=utf8mb4","root","");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Secure connection failed.");
}
?>
