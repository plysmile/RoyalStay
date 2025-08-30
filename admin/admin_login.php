<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, username, password, fullname FROM admins WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
   if ($password === $admin['password']) {
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_name'] = $admin['fullname'];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "รหัสผ่านไม่ถูกต้อง";
    }
  } else {
    $error = "ไม่พบบัญชีนี้";
  }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เข้าสู่ระบบแอดมิน</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
  <div class="w3-container w3-blue w3-center">
    <h2>เข้าสู่ระบบผู้ดูแล</h2>
  </div>

  <div class="w3-container w3-card w3-white w3-padding" style="max-width:400px; margin:50px auto;">
    <?php if (!empty($error)) echo "<p class='w3-text-red'>$error</p>"; ?>
    <form method="POST">
      <label>ชื่อผู้ใช้</label>
      <input class="w3-input w3-border" type="text" name="username" required>

      <label class="w3-margin-top">รหัสผ่าน</label>
      <input class="w3-input w3-border" type="password" name="password" required>

      <button class="w3-button w3-blue w3-margin-top w3-block">เข้าสู่ระบบ</button>
    </form>
  </div>
</body>
</html>
