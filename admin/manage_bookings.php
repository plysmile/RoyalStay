<?php
session_start();

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
    die("❌ เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ✅ ตรวจสอบสิทธิ์แอดมิน
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login_index.php");
    exit();
}

// ✅ ยืนยันการจอง
if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    $conn->query("UPDATE bookings SET status='confirmed' WHERE id=$id");
    header("Location: manage_bookings.php");
    exit();
}

// ❌ ยกเลิกการจอง
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    $conn->query("UPDATE bookings SET status='cancelled' WHERE id=$id");
    header("Location: manage_bookings.php");
    exit();
}

// 🗑️ ลบการจอง
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM bookings WHERE id=$id");
    header("Location: manage_bookings.php");
    exit();
}

// ✅ ดึงข้อมูลการจองทั้งหมด
$sql = "
    SELECT b.id, u.fullname, h.name AS hotel_name, 
           b.checkin_date, b.checkout_date, b.guests, b.total_price, b.status
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    ORDER BY b.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการการจอง</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-light-grey">

<div class="w3-container w3-blue w3-center">
  <h2>📋 จัดการการจอง</h2>
</div>

<div class="w3-container w3-padding">
  <table class="w3-table w3-bordered w3-striped w3-white">
    <tr class="w3-blue">
      <th>ID</th>
      <th>ผู้จอง</th>
      <th>โรงแรม</th>
      <th>เช็คอิน</th>
      <th>เช็คเอาท์</th>
      <th>ผู้เข้าพัก</th>
      <th>ราคารวม</th>
      <th>สถานะ</th>
      <th>การจัดการ</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
        <td><?php echo htmlspecialchars($row['hotel_name']); ?></td>
        <td><?php echo $row['checkin_date']; ?></td>
        <td><?php echo $row['checkout_date']; ?></td>
        <td><?php echo $row['guests']; ?></td>
        <td>฿<?php echo number_format($row['total_price'], 2); ?></td>
        <td>
          <?php
            if ($row['status'] == "confirmed") {
                echo "<span class='w3-tag w3-green'>ยืนยันแล้ว</span>";
            } elseif ($row['status'] == "cancelled") {
                echo "<span class='w3-tag w3-red'>ถูกยกเลิก</span>";
            } else {
                echo "<span class='w3-tag w3-orange'>รอดำเนินการ</span>";
            }
          ?>
        </td>
        <td>
          <!-- ✅ ปุ่มยืนยัน -->
          <a href="manage_bookings.php?confirm=<?php echo $row['id']; ?>" 
             class="w3-button w3-green w3-small"
             onclick="return confirm('ยืนยันการจองนี้ใช่ไหม?')">ยืนยัน</a>

          <!-- ❌ ปุ่มยกเลิก -->
          <a href="manage_bookings.php?cancel=<?php echo $row['i]()_
