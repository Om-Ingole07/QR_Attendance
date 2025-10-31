<?php
include('db_connect.php');

// === CONFIGURATION ===
// Set teacher username and desired new password
$teacher_username = "teacher1";  // change username as needed
$plain_password = "12345";       // change to the new plain password

// === HASH PASSWORD ===
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// === UPDATE PASSWORD IN DATABASE ===
$sql = "UPDATE teacher SET password = ? WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $teacher_username);
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "✅ <strong>Password updated successfully!</strong><br>";
            echo "Username: <strong>$teacher_username</strong><br>";
            echo "New Password: <strong>$plain_password</strong><br>";
            echo "(Password is stored securely using hashing.)";
        } else {
            echo "⚠️ No matching user found with username '<strong>$teacher_username</strong>'.";
        }
    } else {
        echo "❌ Failed to execute query: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
} else {
    echo "❌ Failed to prepare query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
