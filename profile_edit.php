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

// อัปเดตข้อมูล
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = $_POST['fullname'];
  $email    = $_POST['email'];
  $password = $_POST['password'];

  if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $fullname, $email, $hashed, $user_id);
  } else {
    $stmt = $conn->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $fullname, $email, $user_id);
  }

  if ($stmt->execute()) {
    echo "<script>alert('✅ อัปเดตข้อมูลสำเร็จ'); window.location='profile.php';</script>";
    exit;
  } else {
    echo "<script>alert('❌ เกิดข้อผิดพลาด');</script>";
  }
}

// ดึงข้อมูลปัจจุบันมาใส่ฟอร์ม
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขข้อมูลส่วนตัว</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-blue">
  <h2>แก้ไขข้อมูลส่วนตัว</h2>
</div>

<div class="w3-container" style="max-width:600px; margin-top:20px;">
  <form method="POST" class="w3-card w3-white w3-padding">
    <label>ชื่อผู้ใช้</label>
    <input class="w3-input w3-border" type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

    <label>อีเมล</label>
    <input class="w3-input w3-border" type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

    <label>รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนให้เว้นว่าง)</label>
    <input class="w3-input w3-border" type="password" name="password">

    <button type="submit" class="w3-button w3-green w3-margin-top">บันทึก</button>
    <a href="profile.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>

</body>
</html>
