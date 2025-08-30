<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body { font-family: Tahoma, sans-serif; background:#f5f5f5; }
    .w3-container { background:#fff; border-radius:10px; padding:20px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    .w3-ul li a { text-decoration:none; display:block; padding:10px; }
    .w3-ul li a:hover { background:#f1f1f1; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <div class="w3-bar w3-blue">
    <span class="w3-bar-item">ยินดีต้อนรับ, <?php echo $_SESSION['admin_name']; ?></span>
    <a href="logout.php" class="w3-bar-item w3-button w3-right">ออกจากระบบ</a>
  </div>

  <!-- Dashboard -->
  <div class="w3-container" style="margin-top:20px; max-width:600px; margin:auto;">
    <h2>📊 แผงควบคุมแอดมิน</h2>
    <ul class="w3-ul w3-card-4">
      <li><a href="manage_hotels.php">🏨 จัดการโรงแรม</a></li>
      <li><a href="manage_bookings.php">📅 จัดการการจอง</a></li>
      <li><a href="manage_admins.php">👨‍💻 จัดการผู้ดูแล</a></li>
      <li><a href="manage_banks.php">🏦 จัดการบัญชีธนาคาร</a></li>
      <li><a href="payments.php">💳 จัดการการชำระเงิน</a></li> 
    </ul>
  </div>
</body>
</html>
