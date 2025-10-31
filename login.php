<?php
include('db_connect.php');
session_start();

$message = "";

if (isset($_POST['login'])) {
    $role = $_POST['role'] ?? '';

    if ($role == "teacher") {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        // Fetch teacher from DB
        $query = mysqli_query($conn, "SELECT * FROM teacher WHERE username='$username'");
        if (mysqli_num_rows($query) > 0) {
            $teacher = mysqli_fetch_assoc($query);
            // Verify hashed password
            if (password_verify($password, $teacher['password'])) {
                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_name'] = $teacher['username'];
                header("Location: teacher_dashboard.php");
                exit();
            } else {
                $message = "❌ Invalid password!";
            }
        } else {
            $message = "❌ Teacher not found!";
        }

    } elseif ($role == "student") {
        // Directly go to scan page, no input required
        $_SESSION['student_logged_in'] = true;
        header("Location: scan.php");
        exit();
    } else {
        $message = "❌ Please select a role!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>🔐 Login</h2>
    <?php if ($message != "") echo "<div class='message warning'>$message</div>"; ?>

    <!-- Notice the hidden 'login' input below -->
    <form method="post" id="loginForm">
        <label>Login As:</label>
        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select>

        <div id="teacher_fields" style="display:none;">
            <label>Username:</label>
            <input type="text" name="username">

            <label>Password:</label>
            <input type="password" name="password">
        </div>

        <!-- Hidden login flag so server-side detects submissions made by JS -->
        <input type="hidden" name="login" id="loginHidden" value="">

        <button type="submit" class="btn">Login</button>
    </form>
</div>

<script>
const roleSelect = document.getElementById('role');
const teacherFields = document.getElementById('teacher_fields');
const loginForm = document.getElementById('loginForm');
const loginHidden = document.getElementById('loginHidden');

roleSelect.addEventListener('change', function() {
    if (this.value === "teacher") {
        teacherFields.style.display = "block";
    } else {
        teacherFields.style.display = "none";
    }

    if (this.value === "student") {
        // Set hidden login field so PHP sees the submission
        loginHidden.value = "1";
        // Slight delay to allow the change event to finish (optional)
        setTimeout(() => loginForm.submit(), 50);
    } else {
        // clear hidden value for normal teacher flow
        loginHidden.value = "";
    }
});
</script>

</body>
</html>
