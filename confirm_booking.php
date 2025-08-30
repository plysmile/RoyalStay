<?php
session_start();

// ถ้าไม่ได้ล็อกอินให้กลับไปหน้า login
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('กรุณาเข้าสู่ระบบก่อนจอง'); window.location='login.html';</script>";
  exit;
}

$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$hotel_id = $_GET['hotel_id'] ?? 0;

// ดึงข้อมูลโรงแรม
$stmt = $conn->prepare("SELECT name, location, price_per_night, image FROM hotels WHERE id=?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

if (!$hotel) {
  die("❌ ไม่พบโรงแรมที่เลือก");
}

// ดึงค่าที่ส่งมาจาก search_results.php
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests   = $_GET['guests']   ?? 1;

// ถ้าผู้ใช้กดยืนยันการจอง (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $checkin  = $_POST['checkin'];
  $checkout = $_POST['checkout'];
  $guests   = $_POST['guests'];

  $days = (strtotime($checkout) - strtotime($checkin)) / (60 * 60 * 24);
  if ($days <= 0) {
    echo "<script>alert('❌ วันที่เช็คเอาท์ต้องหลังจากเช็คอิน'); window.history.back();</script>";
    exit;
  }

  $total_price = $hotel['price_per_night'] * $days;

  $stmt = $conn->prepare("INSERT INTO bookings 
    (user_id, hotel_id, checkin_date, checkout_date, guests, total_price, payment_status) 
    VALUES (?, ?, ?, ?, ?, ?, 'pending')");
  $stmt->bind_param("iissid", $user_id, $hotel_id, $checkin, $checkout, $guests, $total_price);

  if ($stmt->execute()) {
    echo "<script>alert('✅ จองสำเร็จ!'); window.location='booking_history.php';</script>";
    exit;
  } else {
    echo "เกิดข้อผิดพลาด: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ยืนยันการจอง</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body { background: #f5f5f5; font-family: "Tahoma", sans-serif; }
    .card {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      overflow: hidden;
    }
    .card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
    }
    .card-body { padding: 20px; }
    .price { font-weight: bold; color: #007bff; }
  </style>
</head>
<body>

<div class="card">
  <img src="<?php echo $hotel['image'] ?: 'https://via.placeholder.com/600x250'; ?>" alt="">
  <div class="card-body">
    <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
    <p>📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
    <p class="price">฿<?php echo number_format($hotel['price_per_night'], 2); ?> / คืน</p>

    <!-- ฟอร์มยืนยันการจอง -->
    <form method="POST">
      <label>วันที่เช็คอิน</label>
      <input class="w3-input w3-border" type="date" name="checkin" 
             value="<?php echo htmlspecialchars($checkin); ?>" required>

      <label>วันที่เช็คเอาท์</label>
      <input class="w3-input w3-border" type="date" name="checkout" 
             value="<?php echo htmlspecialchars($checkout); ?>" required>

      <label>จำนวนผู้เข้าพัก</label>
      <input class="w3-input w3-border" type="number" name="guests" 
             value="<?php echo htmlspecialchars($guests); ?>" min="1" required>

      <button type="submit" class="w3-button w3-green w3-margin-top">ยืนยันการจอง</button>
      <a href="index.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
    </form>
  </div>
</div>

</body>
</html>
