<?php
$servername = "sql110.infinityfree.com";
$username   = "if0_40235568";
$password   = "QrApp2025";
$dbname     = "if0_40235568_epiz_12345678_mydb";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) die("❌ Database connection failed: " . mysqli_connect_error());
?>
