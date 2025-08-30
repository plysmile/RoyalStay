<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM admins WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();
    header("Location: manage_admins.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขผู้ดูแล</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-container w3-orange"><h2>✏️ แก้ไขผู้ดูแล</h2></div>
<div class="w3-container w3-padding">
  <form method="POST">
    <label>ชื่อผู้ใช้</label>
    <input class="w3-input w3-border" type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>

    <label>รหัสผ่านใหม่ (ถ้าไม่เปลี่ยนปล่อยว่าง)</label>
    <input class="w3-input w3-border" type="password" name="password">

    <label>สิทธิ์</label>
    <select class="w3-select w3-border" name="role">
      <option value="admin" <?php if($admin['role']=='admin') echo 'selected'; ?>>Admin</option>
      <option value="superadmin" <?php if($admin['role']=='superadmin') echo 'selected'; ?>>Super Admin</option>
    </select>

    <button class="w3-button w3-green w3-margin-top">บันทึก</button>
    <a href="manage_admins.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>
</body>
</html>
