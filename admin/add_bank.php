<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("❌ Database Failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bank_name = $_POST['bank_name'];
  $account_number = $_POST['account_number'];
  $account_name = $_POST['account_name'];

  // 📌 อัปโหลด QR Code
  $qr_code = null;
  if (!empty($_FILES['qr_code']['name'])) {
    $targetDir = "../uploads/banks/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $filename = time() . "_" . basename($_FILES["qr_code"]["name"]);
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($_FILES["qr_code"]["tmp_name"], $targetFile)) {
      $qr_code = "uploads/banks/" . $filename;
    }
  }

  $stmt = $conn->prepare("INSERT INTO banks (bank_name, account_number, account_name, qr_code) VALUES (?,?,?,?)");
  $stmt->bind_param("ssss", $bank_name, $account_number, $account_name, $qr_code);
  $stmt->execute();

  echo "<script>alert('✅ เพิ่มบัญชีธนาคารเรียบร้อย'); window.location='manage_banks.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มบัญชีธนาคาร</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-container w3-blue"><h2>➕ เพิ่มบัญชีธนาคาร</h2></div>
<div class="w3-container w3-padding">
  <form method="POST" enctype="multipart/form-data">
    <label>🏦 ชื่อธนาคาร</label>
    <input class="w3-input w3-border" type="text" name="bank_name" required>

    <label>💳 เลขบัญชี</label>
    <input class="w3-input w3-border" type="text" name="account_number" required>

    <label>👤 ชื่อบัญชี</label>
    <input class="w3-input w3-border" type="text" name="account_name" required>

    <label>📷 อัปโหลด QR Code</label>
    <input class="w3-input w3-border" type="file" name="qr_code" accept="image/*">

    <button class="w3-button w3-green w3-margin-top">บันทึก</button>
    <a href="manage_banks.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>
</body>
</html>
