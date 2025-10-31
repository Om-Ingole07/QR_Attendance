<?php
include('db_connect.php');
session_start();

// Ensure student is logged in
if (!isset($_SESSION['student_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Generate unique device ID based on browser + IP
$device_id = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

if (isset($_POST['submit'])) {
    $roll_no = mysqli_real_escape_string($conn, trim($_POST['roll_no']));
    $today = date('Y-m-d');

    // ✅ 1. Validate roll number format
    if (!preg_match("/^[0-9]{2}[A-Z]{2}[0-9]{3}$/", $roll_no)) {
        $message = "<div class='message error'>❌ Invalid Roll Number Format! (Example: 23CM001)</div>";
    } else {
        // ✅ 2. Check if roll number exists
        $check_student = mysqli_query($conn, "SELECT * FROM student WHERE roll_no='$roll_no'");
        if (mysqli_num_rows($check_student) == 0) {
            $message = "<div class='message error'>❌ Roll Number not found in student records!</div>";
        } else {
            // ✅ 3. Check if roll or device already submitted today
            $check = mysqli_query($conn, "
                SELECT * FROM dynamic_attendance 
                WHERE (roll_no='$roll_no' OR device_id='$device_id')
                AND submit_date = CURDATE()
            ");

            if (mysqli_num_rows($check) > 0) {
                $existing = mysqli_fetch_assoc($check);
                if ($existing['roll_no'] === $roll_no) {
                    $message = "<div class='message warning'>⚠️ Roll No <strong>$roll_no</strong> has already submitted attendance today!</div>";
                } else {
                    $message = "<div class='message warning'>⚠️ This device has already been used to submit attendance for Roll No: <strong>{$existing['roll_no']}</strong> today!</div>";
                }
            } else {
                // ✅ 4. Try inserting attendance
                $insert = mysqli_query($conn, "
                    INSERT INTO dynamic_attendance (roll_no, device_id, submit_time)
                    VALUES ('$roll_no', '$device_id', NOW())
                ");

                if ($insert) {
                    $message = "<div class='message success'>✅ Attendance submitted successfully for Roll No: <strong>$roll_no</strong>!</div>";
                } else {
                    // ✅ Handle DB duplicate error gracefully
                    if (mysqli_errno($conn) == 1062) {
                        $message = "<div class='message warning'>⚠️ You have already submitted attendance today!</div>";
                    } else {
                        $message = "<div class='message error'>❌ Database error while submitting attendance. Please try again.</div>";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Attendance</title>
<link rel="stylesheet" href="style.css">
<style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; }
    .container { max-width: 400px; margin: 80px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    h2 { text-align: center; margin-bottom: 20px; }
    label { display: block; margin-bottom: 6px; font-weight: bold; }
    input[type="text"] { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; }
    .btn { display: inline-block; width: 100%; text-align: center; padding: 10px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; }
    .btn:hover { background: #0056b3; }
    .message { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-weight: bold; }
    .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
</head>
<body>
<div class="container">
    <h2>📝 Submit Attendance</h2>
    <?php echo $message; ?>

    <form method="post">
        <label for="roll_no">Enter Roll No (e.g. 23CM001):</label>
        <input type="text" name="roll_no" id="roll_no" pattern="[0-9]{2}[A-Z]{2}[0-9]{3}" title="Format: 23CM001" required>

        <button type="submit" name="submit" class="btn">Submit Attendance</button>
    </form>

    <br>
    <a href="logout.php" class="btn" style="background:#dc3545;">🚪 Logout</a>
</div>
</body>
</html>
