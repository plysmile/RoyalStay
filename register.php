<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// ตรวจสอบว่า email ซ้ำหรือไม่
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
  echo "<script>alert('อีเมลนี้มีในระบบแล้ว!'); window.location='register.html';</script>";
  exit;
}

// บันทึกลงฐานข้อมูล
$stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullname, $email, $password);

if ($stmt->execute()) {
  echo "<script>alert('สมัครสมาชิกเรียบร้อย! กรุณาเข้าสู่ระบบ'); window.location='login.html';</script>";
} else {
  echo "เกิดข้อผิดพลาด: " . $stmt->error;
}

$conn->close();
?>
