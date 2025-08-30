<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) { die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error); }

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login_index.php");
  exit;
}

// เมื่อกด submit ฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $location = $_POST['location'];
  $price = $_POST['price'];
  
  // ✅ รองรับทั้งอัพโหลดไฟล์และ URL
  $image = "";
  if (!empty($_FILES['image']['name'])) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    $image = "uploads/" . basename($_FILES["image"]["name"]);
  } else {
    $image = $_POST['image_url'] ?? "";
  }

  $stmt = $conn->prepare("INSERT INTO hotels (name, location, price_per_night, image) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssis", $name, $location, $price, $image);
  $stmt->execute();

  header("Location: manage_hotels.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มโรงแรม</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<div class="w3-container w3-blue"><h2>เพิ่มโรงแรมใหม่</h2></div>
<div class="w3-container w3-padding">
  <form method="POST" enctype="multipart/form-data">
    <label>ชื่อโรงแรม</label>
    <input class="w3-input w3-border" type="text" name="name" required>

    <label>ที่อยู่</label>
    <input class="w3-input w3-border" type="text" name="location" required>

    <label>ราคา/คืน</label>
    <input class="w3-input w3-border" type="number" name="price" required>

    <label>เลือกรูปภาพ (Upload)</label>
    <input class="w3-input" type="file" name="image">

    <label>หรือใส่ URL รูปภาพ</label>
    <input class="w3-input w3-border" type="text" name="image_url">

    <button class="w3-button w3-green w3-margin-top">บันทึก</button>
    <a href="manage_hotels.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>
</body>
</html>
