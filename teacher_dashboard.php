<?php
session_start();
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Teacher Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>👩‍🏫 Teacher Dashboard</h2>
    <p>Welcome, Teacher! Choose an action below:</p>

    <div style="display:flex; flex-direction: column; align-items: center; gap: 15px; margin-top: 20px;">

        <a href="add_student.php" class="btn">➕ Add Student</a>
        <a href="view_student.php" class="btn">📋 View Student Record</a>
        <a href="genrate_qr.php" class="btn">� Genrate QR </a>
        <a href="mark_attendance.php" class="btn">📝 Mark Attendance</a>
        <a href="edit_attendance.php" class="btn">✏️ Edit Attendance</a>
        <a href="view_attendance.php" class="btn">📊 View Attendance</a>
        <a href="clear_dynamic.php" class="btn">🗑 Clear Dynamic Attendance</a>
        <a href="logout.php" class="btn">🚪 Logout</a>
        


    </div>
</div>
</body>
</html>
