<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");

if (!isset($_SESSION['admin_id'])) { header("Location: admin_login_index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO admins (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();

    header("Location: manage_admins.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มผู้ดูแล</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-container w3-blue"><h2>➕ เพิ่มผู้ดูแล</h2></div>
<div class="w3-container w3-padding">
  <form method="POST">
    <label>ชื่อผู้ใช้</label>
    <input class="w3-input w3-border" type="text" name="username" required>

    <label>รหัสผ่าน</label>
    <input class="w3-input w3-border" type="password" name="password" required>

    <label>สิทธิ์</label>
    <select class="w3-select w3-border" name="role">
      <option value="admin">Admin</option>
      <option value="superadmin">Super Admin</option>
    </select>

    <button class="w3-button w3-green w3-margin-top">บันทึก</button>
    <a href="manage_admins.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>
</body>
</html>
