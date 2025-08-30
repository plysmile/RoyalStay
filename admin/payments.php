<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("DB Error");

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// ✅ อนุมัติ / ปฏิเสธการชำระเงิน
if (isset($_GET['action'], $_GET['id'])) {
  $id = intval($_GET['id']);
  if ($_GET['action'] === 'approve') {
    $conn->query("UPDATE bookings SET payment_status='paid' WHERE id=$id");
  } elseif ($_GET['action'] === 'reject') {
    $conn->query("UPDATE bookings SET payment_status='rejected' WHERE id=$id");
  }
  header("Location: payments.php");
  exit;
}

// ✅ ดึงข้อมูลการชำระเงินทั้งหมด
$sql = "SELECT b.*, u.fullname, h.name AS hotel_name, ba.bank_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN hotels h ON b.hotel_id = h.id
        LEFT JOIN bank_accounts ba ON b.bank_id = ba.id
        WHERE b.payment_status IN ('pending','paid','rejected')
        ORDER BY b.id DESC";
$payments = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการการชำระเงิน</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body { font-family: Tahoma, sans-serif; background:#f5f5f5; }
    .status-pending { color: orange; font-weight: bold; }
    .status-paid { color: green; font-weight: bold; }
    .status-rejected { color: red; font-weight: bold; }
  </style>
</head>
<body>
<div class="w3-bar w3-blue">
  <span class="w3-bar-item">📑 จัดการการชำระเงิน</span>
  <a href="dashboard.php" class="w3-bar-item w3-button w3-right">⬅ กลับ</a>
</div>

<div class="w3-container" style="margin-top:20px;">
  <h2>💳 การชำระเงินทั้งหมด</h2>
  <table class="w3-table-all w3-card-4 w3-small">
    <tr class="w3-blue">
      <th>ลูกค้า</th>
      <th>โรงแรม</th>
      <th>จำนวนเงินที่ต้องชำระ</th>
      <th>จำนวนเงินที่โอน</th>
      <th>วิธีชำระเงิน</th>
      <th>บัญชีธนาคาร</th>
      <th>เวลาโอน</th>
      <th>วันที่อัปโหลด</th>
      <th>สลิป</th>
      <th>สถานะ</th>
      <th>การจัดการ</th>
    </tr>
    <?php while($p = $payments->fetch_assoc()): ?>
    <tr>
      <td><?=htmlspecialchars($p['fullname'])?></td>
      <td><?=htmlspecialchars($p['hotel_name'])?></td>
      <td>฿<?=number_format($p['total_price'],2)?></td>
      <td>
        <?php if($p['transfer_amount']): ?>
          ฿<?=number_format($p['transfer_amount'],2)?>
          <?php if($p['transfer_amount'] == $p['total_price']): ?>
            ✅
          <?php else: ?>
            ❌
          <?php endif; ?>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td><?=$p['payment_method']?></td>
      <td><?=$p['bank_name'] ?: '-'?></td>
      <td><?=$p['transfer_time'] ?: '-'?></td>
      <td><?=$p['payment_uploaded_at'] ?: '-'?></td>
      <td>
        <?php if($p['payment_slip']): ?>
          <a href="../<?=$p['payment_slip']?>" target="_blank">📷 ดูสลิป</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td class="<?='status-'.$p['payment_status']?>">
        <?php if($p['payment_status']=='pending'): ?>
          ⏳ รอตรวจสอบ
        <?php elseif($p['payment_status']=='paid'): ?>
          ✅ ชำระแล้ว
        <?php else: ?>
          ❌ ถูกปฏิเสธ
        <?php endif; ?>
      </td>
      <td>
        <?php if($p['payment_status']=='pending'): ?>
          <a href="payments.php?action=approve&id=<?=$p['id']?>" class="w3-button w3-green w3-small">อนุมัติ</a>
          <a href="payments.php?action=reject&id=<?=$p['id']?>" class="w3-button w3-red w3-small">ปฏิเสธ</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
