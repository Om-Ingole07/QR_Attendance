<?php
// Database connection details (use your Render credentials)
$host = "dpg-abc12345.render.com";  // change this
$port = "5432";
$dbname = "attendance_system";      // your database name
$user = "attendance_user";          // your database user
$password = "Abc12345XYZ";          // your database password

// Create connection
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

// Check connection
if (!$conn) {
    die("Database connection failed: " . pg_last_error());
} else {
    // echo "Connected successfully!";  // optional
}
?>
