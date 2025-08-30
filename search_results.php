<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รับค่าจาก index.php
$hotel_id = isset($_GET['hotel']) ? intval($_GET['hotel']) : 0;
$checkin = $_GET['checkin'] ?? '';
$checkout = $_GET['checkout'] ?? '';
$guests = $_GET['guests'] ?? 1;

// ดึงข้อมูลโรงแรม
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id=?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ผลการค้นหาโรงแรม</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body {
      margin: 0;
      font-family: "Tahoma", sans-serif;
      background-color: #f5f5f5;
    }
    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
    }
    .hotel-card {
      display: flex;
      flex-direction: row;
      border: 1px solid #ddd;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      background-color: #fff;
    }
    .hotel-card img {
      width: 320px;
      height: 220px;
      object-fit: cover;
    }
    .hotel-info {
      padding: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .hotel-info h3 {
      margin-top: 0;
      font-size: 22px;
      color: #333;
    }
    .hotel-info p { margin: 5px 0; color: #555; }
    .price {
      color: #007bff;
      font-weight: bold;
      margin: 10px 0;
    }
    .btn-book {
      display: inline-block;
      padding: 10px;
      background: #007bff;
      color: #fff;
      text-align: center;
      border-radius: 5px;
      text-decoration: none;
      width: 160px;
      margin-top: 10px;
    }
    .btn-book:hover { background: #0056b3; }
    @media screen and (max-width: 768px) {
      .hotel-card { flex-direction: column; }
      .hotel-card img { width: 100%; height: auto; }
    }
  </style>
</head>
<body>

<div class="w3-container w3-blue w3-center">
  <h2>ผลการค้นหาโรงแรม</h2>
</div>

<div class="container">
  <?php if ($hotel): ?>
    <div class="hotel-card">
      <img src="<?php echo $hotel['image'] ?: 'https://via.placeholder.com/300x200'; ?>" 
           alt="<?php echo htmlspecialchars($hotel['name']); ?>">

      <div class="hotel-info">
        <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
        <p>📍 <?php echo htmlspecialchars($hotel['location']); ?></p>
        <p class="price">฿<?php echo number_format($hotel['price_per_night'], 2); ?> / คืน</p>

        <!-- แสดงข้อมูลที่ผู้ใช้เลือก -->
        <p>เช็คอิน: <?php echo htmlspecialchars($checkin); ?></p>
        <p>เช็คเอาท์: <?php echo htmlspecialchars($checkout); ?></p>
        <p>จำนวนผู้เข้าพัก: <?php echo htmlspecialchars($guests); ?> คน</p>

        <!-- ปุ่มจอง -->
        <a href="confirm_booking.php?hotel_id=<?php echo $hotel['id']; ?>&checkin=<?php echo $checkin; ?>&checkout=<?php echo $checkout; ?>&guests=<?php echo $guests; ?>" 
           class="btn-book">จองเลย</a>
      </div>
    </div>
  <?php else: ?>
    <p>❌ ไม่พบโรงแรมที่เลือก</p>
  <?php endif; ?>
</div>

</body>
</html>
