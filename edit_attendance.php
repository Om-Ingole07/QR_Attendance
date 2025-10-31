<?php
include('db_connect.php'); // Your database connection
session_start();

// Optional: only teacher can access
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Handle toggle request
if (isset($_GET['toggle_id'])) {
    $id = intval($_GET['toggle_id']);
    // Toggle attendance
    $toggle_query = "
        UPDATE attendance
        SET status = CASE 
                        WHEN status='Present' THEN 'Absent' 
                        ELSE 'Present' 
                     END
        WHERE attendance_id = $id
    ";
    mysqli_query($conn, $toggle_query);
    header("Location: edit_attendance.php");
    exit();
}

// Fetch all attendance records
$query = "SELECT a.attendance_id, a.roll_no, a.date, a.time, a.status 
          FROM attendance a
          ORDER BY a.date DESC, a.roll_no ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>✏️ Edit Attendance Records</h2>
    <table>
        <tr>
            <th>Roll No</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['roll_no']) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <a class="btn" href="edit_attendance.php?toggle_id=<?= $row['attendance_id'] ?>">
                            Toggle
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No attendance records found.</td></tr>
        <?php endif; ?>
    </table>
    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back</a>
</div>
</body>
</html>
