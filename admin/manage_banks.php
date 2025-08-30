<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("❌ Database Failed: " . $conn->connect_error);

// 🔹 ลบธนาคาร
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM banks WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  echo "<script>alert('🗑️ ลบบัญชีธนาคารเรียบร้อย'); window.location='manage_banks.php';</script>";
  exit;
}

// 🔹 ดึงข้อมูลทั้งหมด
$result = $conn->query("SELECT * FROM banks ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการบัญชีธนาคาร</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { font-family: Tahoma, sans-serif; background: #f4f6f9; }
    .container-box {
      max-width: 1000px; margin: 30px auto; background: #fff;
      padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    table img {
      border-radius: 8px; border: 1px solid #ddd; padding: 4px;
      background: #fff;
    }
    .navbar { background:#333; padding:10px; }
    .navbar a { color:white; margin-right:15px; text-decoration:none; }
    .navbar a:hover { text-decoration:underline; }
  </style>
</head>
<body>

<!-- 🔹 Navbar -->
<div class="navbar">
  <a href="dashboard.php"><i class="fa fa-home"></i> แผงควบคุม</a>
  <a href="manage_hotels.php"><i class="fa fa-hotel"></i> โรงแรม</a>
  <a href="manage_bookings.php"><i class="fa fa-calendar"></i> การจอง</a>
  <a href="manage_admins.php"><i class="fa fa-user-shield"></i> ผู้ดูแล</a>
  <a href="manage_banks.php"><i class="fa fa-university"></i> ธนาคาร</a>
  <a href="logout.php" class="w3-right"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
</div>

<div class="container-box w3-animate-top">
  <h2 class="w3-center w3-text-indigo">🏦 จัดการบัญชีธนาคาร</h2>

  <a href="add_bank.php" class="w3-button w3-green w3-round-large w3-margin-bottom">
    <i class="fa fa-plus"></i> เพิ่มบัญชีธนาคาร
  </a>

  <table class="w3-table-all w3-hoverable w3-card-4">
    <tr class="w3-indigo w3-text-white">
      <th>ID</th>
      <th>ธนาคาร</th>
      <th>เลขบัญชี</th>
      <th>ชื่อบัญชี</th>
      <th>QR Code</th>
      <th>การจัดการ</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
          <td><?php echo htmlspecialchars($row['account_number']); ?></td>
          <td><?php echo htmlspecialchars($row['account_name']); ?></td>
          <td>
            <?php if ($row['qr_code']): ?>
              <img src="../<?php echo $row['qr_code']; ?>" width="100">
            <?php else: ?>
              ❌ ไม่มี
            <?php endif; ?>
          </td>
          <td>
            <a href="edit_bank.php?id=<?php echo $row['id']; ?>" class="w3-button w3-yellow w3-round">
              <i class="fa fa-edit"></i> แก้ไข
            </a>
            <a href="manage_banks.php?delete=<?php echo $row['id']; ?>"
               onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบบัญชีนี้?');"
               class="w3-button w3-red w3-round">
              <i class="fa fa-trash"></i> ลบ
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6" class="w3-center w3-text-red">❌ ยังไม่มีข้อมูลธนาคาร</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
