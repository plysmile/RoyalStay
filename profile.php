<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location='login.html';</script>";
  exit;
}

$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fullname, email, created_at FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลส่วนตัว</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-blue">
  <h2>ข้อมูลส่วนตัว</h2>
</div>

<div class="w3-container" style="max-width:600px; margin-top:20px;">
  <div class="w3-card w3-white w3-padding">
    <p><b>ชื่อผู้ใช้:</b> <?php echo htmlspecialchars($user['fullname']); ?></p>
    <p><b>อีเมล:</b> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><b>สมัครเมื่อ:</b> <?php echo $user['created_at']; ?></p>
    <a href="profile_edit.php" class="w3-button w3-green">แก้ไขข้อมูล</a>
    <a href="index.php" class="w3-button w3-grey">กลับหน้าหลัก</a>
  </div>
</div>

</body>
</html>
