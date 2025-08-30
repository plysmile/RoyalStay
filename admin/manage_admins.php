<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) { die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error); }

// ตรวจสอบสิทธิ์
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login_index.php"); exit;
}

// ดึงข้อมูลแอดมินคนที่ล็อกอิน
$admin_id = $_SESSION['admin_id'];
$res = $conn->query("SELECT role FROM admins WHERE id=$admin_id");
$currentAdmin = $res->fetch_assoc();
if ($currentAdmin['role'] !== 'superadmin') {
    die("❌ คุณไม่มีสิทธิ์เข้าถึงหน้านี้");
}

// ลบแอดมิน
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $admin_id) { // กันไม่ให้ลบตัวเอง
        $conn->query("DELETE FROM admins WHERE id=$id");
        echo "<script>alert('ลบผู้ดูแลแล้ว'); window.location='manage_admins.php';</script>";
        exit;
    } else {
        echo "<script>alert('ไม่สามารถลบบัญชีของคุณเองได้');</script>";
    }
}

// ดึงรายชื่อแอดมินทั้งหมด
$result = $conn->query("SELECT * FROM admins ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการผู้ดูแลระบบ</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-container w3-indigo w3-padding">
  <h2>👨‍💻 จัดการผู้ดูแลระบบ</h2>
</div>
<div class="w3-container w3-padding">
  <a href="add_admin.php" class="w3-button w3-green w3-margin-bottom">➕ เพิ่มผู้ดูแล</a>
  <table class="w3-table-all w3-hoverable">
    <tr class="w3-indigo">
      <th>ID</th>
      <th>ชื่อผู้ใช้</th>
      <th>สิทธิ์</th>
      <th>การจัดการ</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
          <a href="edit_admin.php?id=<?php echo $row['id']; ?>" class="w3-button w3-yellow">✏️ แก้ไข</a>
          <?php if ($row['id'] != $admin_id): ?>
            <a href="manage_admins.php?delete=<?php echo $row['id']; ?>" 
               onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ดูแลนี้?');"
               class="w3-button w3-red">🗑️ ลบ</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
