<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Check if dynamic attendance table is already empty
$check = mysqli_query($conn, "SELECT * FROM dynamic_attendance");
if (mysqli_num_rows($check) > 0) {
    // Truncate the table
    mysqli_query($conn, "TRUNCATE TABLE dynamic_attendance");
    $message = "<div class='message success'>✅ Dynamic attendance cleared successfully!</div>";
} else {
    $message = "<div class='message warning'>⚠️ Dynamic attendance is already cleared.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clear Dynamic Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>🗑 Clear Dynamic Attendance</h2>
    <?php echo $message; ?>
    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
