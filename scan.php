<?php // scan.php ?>
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Permissions-Policy" content="camera=(self)">
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>QR Attendance Scanner</title>

<!-- ✅ Use stable CDN version -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<style>
  html, body {
    margin: 0; padding: 0;
    width: 100%; height: 100%;
    background: #000;
    color: #fff;
    font-family: Arial, sans-serif;
    overflow: hidden;
  }
  #reader {
    width: 100%;
    height: 100vh;
  }
  #overlay {
    position: fixed;
    top: 12px;
    left: 12px;
    background: rgba(0,0,0,0.6);
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 1000;
  }
</style>
</head>
<body>
<div id="overlay">📷 Scanning... Allow camera access</div>
<div id="reader"></div>

<script>
  // ✅ Set your domain whitelist for safety
  const allowedHosts = ["qrattendancesystem.42web.io"];

  function isAllowedUrl(url) {
    try {
      const u = new URL(url);
      return (u.protocol === "https:" || u.protocol === "http:") &&
             allowedHosts.includes(u.hostname);
    } catch (e) { return false; }
  }

  function onScanSuccess(decodedText, decodedResult) {
    console.log("Decoded:", decodedText);
    const overlay = document.getElementById('overlay');
    overlay.textContent = "✅ QR Scanned Successfully! Redirecting...";

    html5QrCode.stop().then(() => {
      if (isAllowedUrl(decodedText)) {
        window.location.href = decodedText;
      } else {
        overlay.textContent = "⚠️ Invalid QR Code Domain!";
        setTimeout(() => overlay.textContent = "📷 Scanning...", 2000);
        startScanner();
      }
    }).catch(err => console.error("Stop failed:", err));
  }

  function onScanFailure(error) {
    // Do nothing on failed scans, library continuously tries again.
  }

  let html5QrCode;
  function startScanner() {
    const qrRegion = document.getElementById("reader");
    html5QrCode = new Html5Qrcode("reader");
    const config = { fps: 10, qrbox: { width: 300, height: 300 } };

    Html5Qrcode.getCameras().then(cameras => {
      if (cameras && cameras.length) {
        const backCam = cameras.find(c => /back|rear|environment/i.test(c.label)) || cameras[0];
        html5QrCode.start(backCam.id, config, onScanSuccess, onScanFailure)
          .catch(err => {
            console.error("Camera start error:", err);
            document.getElementById("overlay").textContent = "⚠️ Unable to access camera.";
          });
      } else {
        document.getElementById("overlay").textContent = "No camera found.";
      }
    }).catch(err => {
      console.error("Camera list error:", err);
      document.getElementById("overlay").textContent = "⚠️ Camera access denied.";
    });
  }

  // Start automatically
  startScanner();
</script>
</body>
</html>
