<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) { die("DB Fail: " . $conn->connect_error); }

if (!isset($_SESSION['user_id'])) {
  die("กรุณาเข้าสู่ระบบก่อนจอง");
}

$user_id = $_SESSION['user_id'];
$hotel_id = $_POST['hotel_id'];
$checkin  = $_POST['checkin'];
$checkout = $_POST['checkout'];
$guests   = $_POST['guests'];

$stmt = $conn->prepare("INSERT INTO bookings (user_id, hotel_id, checkin_date, checkout_date, guests) VALUES (?,?,?,?,?)");
$stmt->bind_param("iissi", $user_id, $hotel_id, $checkin, $checkout, $guests);

if ($stmt->execute()) {
  echo "<script>alert('✅ จองสำเร็จ!'); window.location='booking_history.php';</script>";
} else {
  echo "<script>alert('❌ จองไม่สำเร็จ'); window.location='index.php';</script>";
}
