<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM student ORDER BY roll_no ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Students</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📋 Student Records</h2>
    <table>
        <tr>
            <th>Roll No</th>
            <th>Name</th>
            <th>Father's Name</th>
            <th>Mother's Name</th>
            <th>Mobile No</th>
            <th>Email</th>
            <th>DOB</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['roll_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['father_name']) ?></td>
                    <td><?= htmlspecialchars($row['mother_name']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['dob'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No student records found.</td></tr>
        <?php endif; ?>
    </table>
    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
</div>
</body>
</html>
