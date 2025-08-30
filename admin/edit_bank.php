<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("❌ Database Failed: " . $conn->connect_error);

// รับ id ของบัญชีธนาคาร
$id = $_GET['id'] ?? 0;

// ดึงข้อมูลเดิม
$stmt = $conn->prepare("SELECT * FROM banks WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$bank = $stmt->get_result()->fetch_assoc();

if (!$bank) {
  die("❌ ไม่พบบัญชีธนาคารนี้");
}

// ถ้ามีการกดบันทึก (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bank_name      = $_POST['bank_name'];
  $account_number = $_POST['account_number'];
  $account_name   = $_POST['account_name'];
  $qr_code        = $bank['qr_code']; // เก็บค่าเดิมก่อน

  // ✅ ถ้ามีอัปโหลดใหม่
  if (!empty($_FILES['qr_code']['name'])) {
    $targetDir = "../uploads/banks/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $targetFile = $targetDir . time() . "_" . basename($_FILES["qr_code"]["name"]);
    if (move_uploaded_file($_FILES["qr_code"]["tmp_name"], $targetFile)) {
      $qr_code = "uploads/banks/" . basename($targetFile);
    }
  }

  $stmt = $conn->prepare("UPDATE banks SET bank_name=?, account_number=?, account_name=?, qr_code=? WHERE id=?");
  $stmt->bind_param("ssssi", $bank_name, $account_number, $account_name, $qr_code, $id);
  $stmt->execute();

  echo "<script>alert('✅ แก้ไขข้อมูลเรียบร้อย'); window.location='manage_banks.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขบัญชีธนาคาร</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
  <div class="w3-container w3-blue"><h2>✏️ แก้ไขบัญชีธนาคาร</h2></div>
  <div class="w3-container w3-padding">

    <form method="POST" enctype="multipart/form-data">
      <label>🏦 ธนาคาร</label>
      <input class="w3-input w3-border" type="text" name="bank_name"
             value="<?php echo htmlspecialchars($bank['bank_name']); ?>" required>

      <label class="w3-margin-top">🔢 เลขบัญชี</label>
      <input class="w3-input w3-border" type="text" name="account_number"
             value="<?php echo htmlspecialchars($bank['account_number']); ?>" required>

      <label class="w3-margin-top">👤 ชื่อบัญชี</label>
      <input class="w3-input w3-border" type="text" name="account_name"
             value="<?php echo htmlspecialchars($bank['account_name']); ?>" required>

      <label class="w3-margin-top">📷 QR Code ใหม่ (ถ้ามี)</label>
      <input class="w3-input w3-border" type="file" name="qr_code" accept="image/*">

      <?php if ($bank['qr_code']): ?>
        <p>📌 QR Code ปัจจุบัน:</p>
        <img src="../<?php echo $bank['qr_code']; ?>" width="150" style="border:1px solid #ddd; border-radius:8px;">
      <?php endif; ?>

      <button class="w3-button w3-green w3-margin-top">บันทึก</button>
      <a href="manage_banks.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
    </form>

  </div>
</body>
</html>
