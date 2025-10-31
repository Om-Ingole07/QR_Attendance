<?php
session_start();

// Destroy all sessions and redirect to login page
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>You have been logged out successfully.</h2>
        <a href="login.php" class="btn">Return to Login</a>
    </div>
</body>
</html>
