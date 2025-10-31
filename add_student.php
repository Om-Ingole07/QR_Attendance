<?php
include('db_connect.php');
session_start();

// Optional: only teacher can access
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['submit'])) {
    $roll_no = mysqli_real_escape_string($conn, $_POST['roll_no']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $mother_name = mysqli_real_escape_string($conn, $_POST['mother_name']);
    $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = $_POST['dob'];

    $full_name = $surname . " " . $name;

    $check = mysqli_query($conn, "SELECT * FROM student WHERE roll_no='$roll_no'");
    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='message warning'>⚠️ Student with this Roll No already exists.</div>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO student 
            (roll_no, name, father_name, mother_name, mobile_no, email, dob) 
            VALUES ('$roll_no', '$full_name', '$father_name', '$mother_name', '$mobile_no', '$email', '$dob')");
        if ($insert) {
            $message = "<div class='message success'>✅ Student added successfully!</div>";
        } else {
            $message = "<div class='message error'>❌ Failed to add student.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>➕ Add New Student</h2>
    <?php echo $message; ?>
    <form method="post">
        <label>Roll No:</label>
        <input type="text" name="roll_no" required>

        <label>Surname:</label>
        <input type="text" name="surname" required>

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Father's Name:</label>
        <input type="text" name="father_name" required>

        <label>Mother's Name:</label>
        <input type="text" name="mother_name" required>

        <label>Mobile No:</label>
        <input type="text" name="mobile_no" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Date of Birth:</label>
        <input type="date" name="dob" required>

        <button type="submit" name="submit" class="btn">Add Student</button>
    </form>
    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
