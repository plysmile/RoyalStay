<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");

if ($_SERVER['REQUEST_METHOD']=="POST") {
  $booking_id = $_POST['booking_id'];
  $method = $_POST['method'];

  $stmt = $conn->prepare("UPDATE bookings SET payment_status='paid', payment_method=? WHERE id=?");
  $stmt->bind_param("si", $method, $booking_id);
  if ($stmt->execute()) {
    echo "<script>alert('✅ ชำระเงินสำเร็จ'); window.location='booking_history.php';</script>";
  } else {
    echo "❌ เกิดข้อผิดพลาด: " . $stmt->error;
  }
}
?>
