<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการจอง'); window.location='login.html';</script>";
  exit;
}

$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$hotel_id = $_GET['hotel_id'];
$checkin = $_GET['checkin'];
$checkout = $_GET['checkout'];
$guests = $_GET['guests'];

// ดึงข้อมูลโรงแรม
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

// คำนวณจำนวนคืน
$days = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
if ($days <= 0) $days = 1;
$total_price = $hotel['price_per_night'] * $days;
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ยืนยันการจอง</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

<div class="w3-container w3-blue">
  <h2>ยืนยันการจอง</h2>
</div>

<div class="w3-container w3-padding">
  <div class="w3-card w3-white w3-padding">
    <h3><?php echo $hotel['name']; ?></h3>
    <p>ที่ตั้ง: <?php echo $hotel['location']; ?></p>
    <p>ราคา: ฿<?php echo number_format($hotel['price_per_night'], 2); ?> ต่อคืน</p>
    <p>วันที่เข้าพัก: <?php echo $checkin; ?> ถึง <?php echo $checkout; ?></p>
    <p>จำนวนคืน: <?php echo $days; ?></p>
    <p>จำนวนผู้เข้าพัก: <?php echo $guests; ?></p>
    <p><strong>รวมทั้งหมด: ฿<?php echo number_format($total_price, 2); ?></strong></p>

    <form action="confirm_booking.php" method="POST">
      <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
      <input type="hidden" name="checkin" value="<?php echo $checkin; ?>">
      <input type="hidden" name="checkout" value="<?php echo $checkout; ?>">
      <input type="hidden" name="guests" value="<?php echo $guests; ?>">
      <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

      <button class="w3-button w3-blue w3-margin-top">ยืนยันการจอง</button>
    </form>
  </div>
</div>

</body>
</html>
