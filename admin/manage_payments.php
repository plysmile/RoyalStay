<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ต้องเป็นแอดมินเท่านั้น
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login_index.php");
    exit;
}

// ✅ ถ้ามีการอนุมัติการชำระเงิน
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE bookings SET payment_status='approved' WHERE id=$id");
    echo "<script>alert('✅ อนุมัติการชำระเงินเรียบร้อย'); window.location='manage_payments.php';</script>";
    exit;
}

// ✅ ถ้ามีการปฏิเสธการชำระเงิน
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE bookings SET payment_status='rejected' WHERE id=$id");
    echo "<script>alert('❌ ปฏิเสธการชำระเงินแล้ว'); window.location='manage_payments.php';</script>";
    exit;
}

// ✅ ดึงข้อมูลการจองที่มีการชำระเงิน
$sql = "SELECT b.*, u.fullname, h.name AS hotel_name 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN hotels h ON b.hotel_id = h.id
        ORDER BY b.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการการชำระเงิน</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    body { font-family: Tahoma, sans-serif; background:#f5f5f5; }
    .container-box {
        max-width: 1300px; margin: 30px auto; background: #fff;
        padding: 20px; border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    table img { max-width:120px; border:1px solid #ddd; border-radius:8px; }
</style>
</head>
<body>

<div class="w3-bar w3-blue">
  <a href="manage_hotels.php" class="w3-bar-item w3-button"><i class="fa fa-hotel"></i> โรงแรม</a>
  <a href="manage_bookings.php" class="w3-bar-item w3-button"><i class="fa fa-calendar"></i> การจอง</a>
  <a href="manage_payments.php" class="w3-bar-item w3-button w3-green"><i class="fa fa-credit-card"></i> การชำระเงิน</a>
  <a href="admin_logout.php" class="w3-bar-item w3-button w3-red w3-right"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
</div>

<div class="container-box">
  <h2 class="w3-center w3-text-indigo">💰 จัดการการชำระเงิน</h2>

  <table class="w3-table-all w3-hoverable">
    <tr class="w3-indigo w3-text-white">
      <th>ID</th>
      <th>ผู้จอง</th>
      <th>โรงแรม</th>
      <th>ยอดที่ต้องชำระ</th>
      <th>ยอดที่โอนมา</th>
      <th>เวลาโอน</th>
      <th>วันที่อัปโหลด</th>
      <th>วิธีชำระ</th>
      <th>สถานะ</th>
      <th>หลักฐาน</th>
      <th>การจัดการ</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= htmlspecialchars($row['fullname']); ?></td>
          <td><?= htmlspecialchars($row['hotel_name']); ?></td>

          <!-- ยอดที่ต้องชำระ -->
          <td class="w3-text-blue">฿<?= number_format($row['total_price'],2); ?></td>

          <!-- ยอดที่ลูกค้าโอน -->
          <td class="<?= ($row['transfer_amount'] < $row['total_price']) ? 'w3-text-red' : 'w3-text-green' ?>">
            ฿<?= $row['transfer_amount'] ? number_format($row['transfer_amount'],2) : '-'; ?>
          </td>

          <!-- เวลาโอน -->
          <td><?= $row['transfer_time'] ?? '-'; ?></td>

          <!-- วันที่อัปโหลด -->
          <td><?= $row['payment_uploaded_at'] ?? '-'; ?></td>

          <!-- วิธีชำระ -->
          <td><?= ucfirst($row['payment_method']); ?></td>

          <!-- สถานะ -->
          <td>
            <?php if($row['payment_status']=='paid'): ?>
              <span class="w3-tag w3-yellow">รอตรวจสอบ</span>
            <?php elseif($row['payment_status']=='approved'): ?>
              <span class="w3-tag w3-green">อนุมัติแล้ว</span>
            <?php elseif($row['payment_status']=='rejected'): ?>
              <span class="w3-tag w3-red">ถูกปฏิเสธ</span>
            <?php else: ?>
              <span class="w3-tag">-</span>
            <?php endif; ?>
          </td>

          <!-- สลิป -->
          <td>
            <?php if ($row['payment_slip']): ?>
              <a href="../<?= $row['payment_slip']; ?>" target="_blank">
                <img src="../<?= $row['payment_slip']; ?>" alt="Slip">
              </a>
            <?php else: ?>
              <span class="w3-text-grey">ไม่มีสลิป</span>
            <?php endif; ?>
          </td>

          <!-- ปุ่มอนุมัติ/ปฏิเสธ -->
          <td>
            <?php if($row['payment_status']=='paid'): ?>
              <a href="?approve=<?= $row['id']; ?>" class="w3-button w3-green w3-round">✔ อนุมัติ</a>
              <a href="?reject=<?= $row['id']; ?>" class="w3-button w3-red w3-round">✖ ปฏิเสธ</a>
            <?php else: ?>
              <span>-</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="11" class="w3-center w3-text-red">❌ ไม่มีข้อมูลการชำระเงิน</td></tr>
    <?php endif; ?>
  </table>
</div>

</body>
</html>
