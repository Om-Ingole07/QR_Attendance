<?php
include('phpqrcode/qrlib.php');

// === Folder to store generated QR images ===
$path = 'temp/';
if (!file_exists($path)) {
    mkdir($path);
}

// Optional: Clean up old QR images to avoid clutter
foreach (glob($path . "qr_*.png") as $oldFile) {
    unlink($oldFile);
}

$qrImage = "";
if (isset($_POST['generate_qr'])) {
    // === Base URL of your student_attendance.php page ===
    $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/student_attendance.php";

    // === Optional unique token (if you want each QR to be unique) ===
    $uniqueCode = bin2hex(random_bytes(5));
    $qrData = $baseUrl . "?token=" . $uniqueCode;

    // === File name for generated QR ===
    $fileName = 'qr_' . time() . '_' . rand(1000, 9999) . '.png';
    $filePath = $path . $fileName;

    // === Generate QR Code ===
    QRcode::png($qrData, $filePath, 'L', 6, 2);

    $qrImage = $filePath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance QR Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
            background-color: #f9f9f9;
        }
        .btn, button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover, button:hover {
            background-color: #0056b3;
        }
        .qr-container {
            margin-top: 30px;
        }
        img {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 10px;
            background: white;
            width: 250px;
            height: 250px;
        }
        #backBtnWrapper {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <div id="backBtnWrapper">
        <a href="teacher_dashboard.php" class="btn">⬅ Back to Dashboard</a>
    </div>

    <h2>Generate QR for Student Attendance</h2>
    <form method="post">
        <button type="submit" name="generate_qr">Generate QR Code</button>
    </form>

    <div class="qr-container">
        <?php if (!empty($qrImage)): ?>
            <p><strong>Scan this QR to open Student Attendance Page</strong></p>
            <img src="<?= $qrImage ?>?v=<?= time() ?>" alt="QR Code"><br><br>
            <a href="<?= $qrImage ?>" class="btn" download>Download QR</a>
        <?php endif; ?>
    </div>

</body>
</html>
