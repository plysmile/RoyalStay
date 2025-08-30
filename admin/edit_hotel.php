<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) { die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error); }

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login_index.php");
  exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hotel = $stmt->get_result()->fetch_assoc();

if (!$hotel) { die("❌ ไม่พบโรงแรม"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $location = $_POST['location'];
  $price = $_POST['price'];

  // ✅ รองรับการแก้ไขรูป (อัปโหลดไฟล์หรือใช้ URL)
  $image = $hotel['image'];
  if (!empty($_FILES['image']['name'])) {
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    $image = "uploads/" . basename($_FILES["image"]["name"]);
  } elseif (!empty($_POST['image_url'])) {
    $image = $_POST['image_url'];
  }

  $stmt = $conn->prepare("UPDATE hotels SET name=?, location=?, price_per_night=?, image=? WHERE id=?");
  $stmt->bind_param("ssisi", $name, $location, $price, $image, $id);
  $stmt->execute();

  header("Location: manage_hotels.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขโรงแรม</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: "Tahoma", sans-serif;
      background: #f5f6fa;
    }
    .form-box {
      max-width: 600px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .preview-img {
      max-width: 100%;
      border-radius: 8px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="w3-container w3-orange w3-center">
  <h2>✏️ แก้ไขโรงแรม</h2>
</div>

<div class="form-box">
  <form method="POST" enctype="multipart/form-data">
    <label>ชื่อโรงแรม</label>
    <input class="w3-input w3-border w3-round" type="text" name="name"
           value="<?php echo htmlspecialchars($hotel['name']); ?>" required>

    <label>ที่อยู่</label>
    <input class="w3-input w3-border w3-round" type="text" name="location"
           value="<?php echo htmlspecialchars($hotel['location']); ?>" required>

    <label>ราคา/คืน</label>
    <input class="w3-input w3-border w3-round" type="number" name="price"
           value="<?php echo $hotel['price_per_night']; ?>" required>

    <label>อัปโหลดรูปภาพ</label>
    <input class="w3-input w3-border w3-round" type="file" name="image" accept="image/*">

    <label>หรือใช้ URL รูปภาพ</label>
    <input class="w3-input w3-border w3-round" type="text" name="image_url"
           value="<?php echo htmlspecialchars($hotel['image']); ?>">

    <?php if (!empty($hotel['image'])): ?>
      <p>📸 รูปปัจจุบัน:</p>
      <img src="../<?php echo $hotel['image']; ?>" class="preview-img">
    <?php endif; ?>

    <button class="w3-button w3-green w3-round w3-margin-top w3-block">บันทึก</button>
    <a href="manage_hotels.php" class="w3-button w3-grey w3-round w3-margin-top w3-block">ยกเลิก</a>
  </form>
</div>

</body>
</html>
