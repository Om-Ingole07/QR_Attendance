<?php
include('db_connect.php');
session_start();

// Only teacher can access
if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch attendance with student name
$query = "
SELECT a.attendance_id, a.roll_no, s.name, a.date, a.time, a.status
FROM attendance a
LEFT JOIN student s ON a.roll_no = s.roll_no
ORDER BY a.date DESC, a.roll_no ASC
";
$result = mysqli_query($conn, $query);

// Fetch total lectures per student for percent calculation
$students_query = mysqli_query($conn, "SELECT roll_no, name FROM student ORDER BY roll_no ASC");
$student_totals = [];
while ($stu = mysqli_fetch_assoc($students_query)) {
    $roll = $stu['roll_no'];
    $total_lectures = mysqli_query($conn, "SELECT COUNT(*) as total FROM attendance WHERE roll_no='$roll'");
    $total = mysqli_fetch_assoc($total_lectures)['total'];
    $present_lectures = mysqli_query($conn, "SELECT COUNT(*) as present_count FROM attendance WHERE roll_no='$roll' AND status='Present'");
    $present = mysqli_fetch_assoc($present_lectures)['present_count'];
    
    $percent = $total > 0 ? round(($present / $total) * 100, 2) : 0;
    
    if ($percent > 90) {
        $remark = "Excellent";
    } elseif ($percent >= 75) {
        $remark = "Average";
    } else {
        $remark = "Less";
    }
    
    $student_totals[$roll] = [
        'name' => $stu['name'],
        'total' => $total,
        'present' => $present,
        'percent' => $percent,
        'remark' => $remark
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📊 Attendance Records</h2>

    <table>
        <tr>
            <th>Roll No</th>
            <th>Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['roll_no']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No attendance records found.</td></tr>
        <?php endif; ?>
    </table>

    <h3>📌 Attendance Percent Summary</h3>
    <table>
        <tr>
            <th>Roll No</th>
            <th>Name</th>
            <th>Total Lectures</th>
            <th>Lectures Attended</th>
            <th>Percent</th>
            <th>Remark</th>
        </tr>
        <?php foreach($student_totals as $roll => $data): ?>
            <tr>
                <td><?= $roll ?></td>
                <td><?= htmlspecialchars($data['name']) ?></td>
                <td><?= $data['total'] ?></td>
                <td><?= $data['present'] ?></td>
                <td><?= $data['percent'] ?>%</td>
                <td><?= $data['remark'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
    <a href="monthly_attendance.php" class="btn">📅 Monthly Record</a>
</div>
</body>
</html>
