<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) { 
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error); 
}

// ✅ ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: /Hotel/admin/manage_hotels.php");
    exit;
}

// ✅ ตรวจสอบ id ที่ส่งมา
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // ลบข้อมูล
    $stmt = $conn->prepare("DELETE FROM hotels WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบโรงแรมเรียบร้อย'); window.location='manage_hotels.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบ'); window.location='manage_hotels.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ไม่พบ ID โรงแรมที่ต้องการลบ'); window.location='manage_hotels.php';</script>";
}

$conn->close();
?>
