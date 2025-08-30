<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ถ้ามีการ submit ฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $new_password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if ($new_password !== $confirm_password) {
    echo "<script>alert('❌ รหัสผ่านไม่ตรงกัน'); window.history.back();</script>";
    exit;
  }

  // เข้ารหัสรหัสผ่านใหม่
  $hashed = password_hash($new_password, PASSWORD_DEFAULT);

  // ตรวจสอบว่ามี email อยู่ในระบบไหม
  $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    // อัปเดตรหัสผ่าน
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt->bind_param("ss", $hashed, $email);
    if ($stmt->execute()) {
      echo "<script>alert('✅ เปลี่ยนรหัสผ่านสำเร็จ'); window.location='login.html';</script>";
      exit;
    } else {
      echo "<script>alert('❌ เกิดข้อผิดพลาด'); window.history.back();</script>";
    }
  } else {
    echo "<script>alert('❌ ไม่พบบัญชีอีเมลนี้'); window.history.back();</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ลืมรหัสผ่าน</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    .form-box {
      max-width: 500px;
      margin: auto;
      margin-top: 60px;
    }
  </style>
</head>
<body>

<div class="w3-container w3-blue w3-center">
  <h2>ลืมรหัสผ่าน</h2>
</div>

<div class="w3-container w3-card w3-white w3-padding form-box">
  <form method="POST">
    <label>อีเมล</label>
    <input class="w3-input w3-border" type="email" name="email" required>

    <label class="w3-margin-top">รหัสผ่านใหม่</label>
    <input class="w3-input w3-border" type="password" name="password" required>

    <label class="w3-margin-top">ยืนยันรหัสผ่านใหม่</label>
    <input class="w3-input w3-border" type="password" name="confirm_password" required>

    <button class="w3-button w3-green w3-margin-top w3-block">เปลี่ยนรหัสผ่าน</button>
    <a href="login.html" class="w3-button w3-block w3-border w3-margin-top">กลับไปหน้าเข้าสู่ระบบ</a>
  </form>
</div>

</body>
</html>
