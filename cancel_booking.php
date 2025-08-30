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

$user_id   = $_SESSION['user_id'];
$booking_id = $_GET['booking_id'] ?? 0;

// ✅ ตรวจสอบว่าการจองนี้เป็นของ user และยังไม่ถูกยกเลิก/ชำระแล้ว
$sql = "SELECT * FROM bookings 
        WHERE id=? AND user_id=? AND booking_status='confirmed' AND payment_status!='paid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$booking = $res->fetch_assoc();

if (!$booking) {
  echo "<script>alert('❌ ไม่พบการจองนี้ หรือไม่สามารถยกเลิกได้'); window.location='booking_history.php';</script>";
  exit;
}

// ✅ อัปเดตสถานะเป็น cancelled
$stmt = $conn->prepare("UPDATE bookings SET booking_status='cancelled' WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $booking_id, $user_id);

if ($stmt->execute()) {
  echo "<script>alert('✅ ยกเลิกการจองเรียบร้อยแล้ว'); window.location='booking_history.php';</script>";
} else {
  echo "เกิดข้อผิดพลาด: " . $stmt->error;
}
?>
