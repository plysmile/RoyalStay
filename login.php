<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$email = $_POST['email'];
$password = $_POST['password'];

// ตรวจสอบข้อมูลในฐานข้อมูล
$stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();

  if (password_verify($password, $user['password'])) {
    // เข้าสู่ระบบสำเร็จ
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
   header("Location: index.php");
exit();

  } else {
    echo "<script>alert('รหัสผ่านไม่ถูกต้อง'); window.location='login.html';</script>";
  }
} else {
  echo "<script>alert('ไม่พบบัญชีนี้ในระบบ'); window.location='login.html';</script>";
}

$conn->close();
?>
