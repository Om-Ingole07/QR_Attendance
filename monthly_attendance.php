<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['teacher_logged_in'])) {
    header("Location: login.php");
    exit();
}

// ---- Handle CSV Download for All Records ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
    $month = intval($_POST['month']);
    $year = intval($_POST['year']);
    $filename = date("F", mktime(0, 0, 0, $month, 10)) . "-$year.csv";

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");
    fputcsv($output, ["Roll No", "Name", "Lectures Scheduled", "Lectures Attended", "Percent (%)", "Remark"]);

    $sql = "
        SELECT s.roll_no, s.name,
            COUNT(a.attendance_id) AS lectures_scheduled,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS lectures_attended,
            ROUND(
              (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.attendance_id),0)) * 100, 2
            ) AS percent
        FROM student s
        LEFT JOIN attendance a ON s.roll_no = a.roll_no
            AND MONTH(a.date) = {$month} AND YEAR(a.date) = {$year}
        GROUP BY s.roll_no, s.name
        ORDER BY s.roll_no ASC
    ";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $percent = $row['percent'] ?? 0;
            if ($percent >= 90) $remark = "Excellent";
            elseif ($percent >= 75) $remark = "Average";
            else $remark = "Less";
            fputcsv($output, [
                $row['roll_no'],
                $row['name'],
                $row['lectures_scheduled'],
                $row['lectures_attended'],
                $percent,
                $remark
            ]);
        }
    }
    fclose($output);
    exit();
}

// ---- Handle CSV Download for Below 75% ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_below75'])) {
    $month = intval($_POST['month']);
    $year  = intval($_POST['year']);
    $filename = "Below75_" . date("F", mktime(0,0,0,$month,10)) . "-$year.csv";

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen("php://output", "w");
    fputcsv($output, ["Roll No", "Name", "Lectures Scheduled", "Lectures Attended", "Percent (%)", "Remark"]);

    $sql = "
        SELECT s.roll_no, s.name,
            COUNT(a.attendance_id) AS lectures_scheduled,
            SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) AS lectures_attended,
            ROUND(
                (SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.attendance_id),0)) * 100, 2
            ) AS percent
        FROM student s
        LEFT JOIN attendance a ON s.roll_no = a.roll_no
            AND MONTH(a.date) = {$month} AND YEAR(a.date) = {$year}
        GROUP BY s.roll_no, s.name
        HAVING percent < 75
        ORDER BY s.roll_no ASC
    ";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $percent = $row['percent'] ?? 0;
            $remark = "Less";
            fputcsv($output, [
                $row['roll_no'],
                $row['name'],
                $row['lectures_scheduled'],
                $row['lectures_attended'],
                $percent,
                $remark
            ]);
        }
    }
    fclose($output);
    exit();
}

// ---- Default month/year ----
$selected_month = intval(date('m'));
$selected_year  = intval(date('Y'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['download']) && !isset($_POST['download_below75'])) {
    $selected_month = intval($_POST['month']);
    $selected_year  = intval($_POST['year']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Monthly Attendance</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>📆 Monthly Attendance Record</h2>

    <form method="post" class="form-box" style="text-align:center;">
        <label>Select Month:</label>
        <select name="month" required>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date('F', mktime(0, 0, 0, $m, 10));
                $sel = ($m === $selected_month) ? 'selected' : '';
                echo "<option value='{$m}' {$sel}>{$monthName}</option>";
            }
            ?>
        </select>

        <label>Select Year:</label>
        <select name="year" required>
            <?php
            for ($y = 2023; $y <= date('Y'); $y++) {
                $sel = ($y === $selected_year) ? 'selected' : '';
                echo "<option value='{$y}' {$sel}>{$y}</option>";
            }
            ?>
        </select>

        <button type="submit" class="btn">View Record</button>
    </form>

    <br>

    <table>
        <tr>
            <th>Roll No</th>
            <th>Name</th>
            <th>Lectures Scheduled</th>
            <th>Lectures Attended</th>
            <th>Percent (%)</th>
            <th>Remark</th>
        </tr>
        <?php
        $query = "
            SELECT s.roll_no, s.name,
                COUNT(a.attendance_id) AS lectures_scheduled,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS lectures_attended,
                ROUND(
                  (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.attendance_id),0)) * 100, 2
                ) AS percent
            FROM student s
            LEFT JOIN attendance a ON s.roll_no = a.roll_no
                AND MONTH(a.date) = {$selected_month} AND YEAR(a.date) = {$selected_year}
            GROUP BY s.roll_no, s.name
            ORDER BY s.roll_no ASC
        ";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $percent = $row['percent'] ?? 0;
                if ($percent >= 90) $remark = "Excellent";
                elseif ($percent >= 75) $remark = "Average";
                else $remark = "Less";
                echo "<tr>
                        <td>{$row['roll_no']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['lectures_scheduled']}</td>
                        <td>{$row['lectures_attended']}</td>
                        <td>{$percent}</td>
                        <td>{$remark}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No records found.</td></tr>";
        }
        ?>
    </table>

    <br>
    <!-- Existing Download Button -->
    <form method="post">
        <input type="hidden" name="month" value="<?php echo $selected_month; ?>">
        <input type="hidden" name="year" value="<?php echo $selected_year; ?>">
        <button type="submit" name="download" value="1" class="btn">⬇ Download Record</button>
    </form>

    <br>
    <!-- New Below 75% Button -->
    <form method="post">
        <input type="hidden" name="month" value="<?php echo $selected_month; ?>">
        <input type="hidden" name="year" value="<?php echo $selected_year; ?>">
        <button type="submit" name="download_below75" value="1" class="btn" style="background:#dc3545;">
            ⬇ Download Below 75% Attendance
        </button>
    </form>

    <br>
    <a href="view_attendance.php" class="btn">⬅ Back</a>
</div>
</body>
</html>
