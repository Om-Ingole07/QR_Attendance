<?php
include('db_connect.php'); // your database connection
session_start();

// Optional: only teacher can access
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle mark attendance action
if (isset($_POST['mark'])) {

    // 1️⃣ Get all students
    $students = mysqli_query($conn, "SELECT roll_no FROM student");
    if (!$students) {
        $message = "<div class='message error'>❌ Error fetching students.</div>";
    } else {
        // 2️⃣ Get all submitted roll numbers from dynamic_attendance
        $dynamic = mysqli_query($conn, "SELECT roll_no FROM dynamic_attendance");
        $present_rolls = [];
        while ($row = mysqli_fetch_assoc($dynamic)) {
            $present_rolls[] = $row['roll_no'];
        }

        $date = date('Y-m-d');
        $time = date('H:i:s');

        // 3️⃣ Loop through all students and insert into attendance
        while ($stu = mysqli_fetch_assoc($students)) {
            $roll = $stu['roll_no'];
            $status = in_array($roll, $present_rolls) ? 'Present' : 'Absent';

            $insert = mysqli_query($conn, "INSERT INTO attendance (roll_no, date, time, status) 
                                           VALUES ('$roll', '$date', '$time', '$status')");
        }

        // 4️⃣ Clear dynamic attendance
        mysqli_query($conn, "TRUNCATE TABLE dynamic_attendance");

        $message = "<div class='message success'>✅ Attendance marked successfully for ".date('d-m-Y H:i:s')."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mark Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📝 Mark Attendance</h2>
    <?php echo $message; ?>

    <form method="post">
        <p>Click the button below to mark attendance from submitted dynamic data.</p>
        <button type="submit" name="mark" class="btn">Mark Attendance Now</button>
    </form>

    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
