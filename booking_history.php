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

$sql = "SELECT b.*, h.name AS hotel_name, h.location 
        FROM bookings b 
        LEFT JOIN hotels h ON b.hotel_id = h.id 
        WHERE b.user_id = ? 
        ORDER BY b.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ประวัติการจอง</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-blue">
  <h2>ประวัติการจองของฉัน</h2>
</div>

<div class="w3-container" style="margin-top:20px; max-width:1000px;">
  <?php if ($result->num_rows > 0): ?>
    <table class="w3-table w3-striped w3-bordered w3-white">
      <tr>
        <th>โรงแรม</th>
        <th>เช็คอิน</th>
        <th>เช็คเอาท์</th>
        <th>ผู้เข้าพัก</th>
        <th>ราคารวม</th>
        <th>วันที่จอง</th>
        <th>สถานะ</th>
      </tr>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td>
          <?php echo $row['hotel_name'] ? $row['hotel_name'] : "❌ โรงแรมถูกลบ"; ?>
          <br><small><?php echo $row['location']; ?></small>
        </td>
        
        <td><?php echo $row['checkin_date']; ?></td>
        <td><?php echo $row['checkout_date']; ?></td>
        <td><?php echo $row['guests']; ?></td>
        <td>฿<?php echo number_format($row['total_price'],2); ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
          <?php if ($row['payment_status']=="paid"): ?>
            ✅ ชำระแล้ว
          <?php elseif (isset($row['booking_status']) && $row['booking_status']=="cancelled"): ?>
            ❌ ยกเลิกแล้ว
          <?php else: ?>
            ⏳ รอชำระ <br>
            <a href="payment.php?booking_id=<?php echo $row['id']; ?>" 
               class="w3-button w3-green w3-small" style="margin-top:5px;">ชำระเงิน</a>
            <a href="cancel_booking.php?booking_id=<?php echo $row['id']; ?>" 
               class="w3-button w3-red w3-small" style="margin-top:5px;"
               onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?');">
               ยกเลิก
            </a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>❌ คุณยังไม่มีประวัติการจอง</p>
  <?php endif; ?>
</div>

</body>
</html>
