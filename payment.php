<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('กรุณาเข้าสู่ระบบก่อนชำระเงิน'); window.location='login.html';</script>";
  exit;
}

// ✅ เชื่อมต่อฐานข้อมูล ก่อนใช้ $conn
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) die("DB Error");

$user_id = $_SESSION['user_id'];
$booking_id = $_GET['booking_id'] ?? 0;

// ✅ ดึงข้อมูลการจอง
$sql = "SELECT b.*, h.name AS hotel_name, h.location, h.price_per_night 
        FROM bookings b 
        JOIN hotels h ON b.hotel_id = h.id 
        WHERE b.id=? AND b.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) die("❌ ไม่พบการจองนี้");

// ✅ เมื่อกด Submit ชำระเงิน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = $_POST['payment_method'];
  $bank_id = $_POST['bank_id'] ?? null;
  $slip = null;

  // ✅ อัปโหลดสลิป
  if ($method === "bank_transfer" && !empty($_FILES['slip']['name'])) {
    $targetDir = "uploads/slips/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES["slip"]["name"]);
    $targetFile = $targetDir . $fileName;
    move_uploaded_file($_FILES["slip"]["tmp_name"], $targetFile);
    $slip = $targetFile;
  }

  // ✅ อัปเดตสถานะการจอง
  $stmt = $conn->prepare("UPDATE bookings 
                          SET payment_status='pending', payment_method=?, payment_slip=?, bank_id=? 
                          WHERE id=? AND user_id=?");
  $stmt->bind_param("ssiii", $method, $slip, $bank_id, $booking_id, $user_id);

  if ($stmt->execute()) {
    echo "<script>alert('📤 ส่งหลักฐานชำระเงินเรียบร้อย! รอตรวจสอบจากแอดมิน'); window.location='booking_history.php';</script>";
    exit;
  } else {
    echo "Error: " . $stmt->error;
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ชำระเงิน</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
    body { font-family: Tahoma, sans-serif; background:#f5f5f5; }
    .pay-card { max-width:650px; margin:40px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    .price { color:#007bff; font-size:20px; font-weight:bold; }
    .bank-box { background:#fafafa; border:1px solid #ddd; padding:15px; margin-top:10px; border-radius:8px; }
  </style>
</head>
<body>

<div class="pay-card">
  <h2>💳 ชำระเงินสำหรับการจอง</h2>
  <p><b>โรงแรม:</b> <?=htmlspecialchars($booking['hotel_name']);?></p>
  <p><b>ที่ตั้ง:</b> <?=htmlspecialchars($booking['location']);?></p>
  <p><b>เช็คอิน:</b> <?=$booking['checkin_date'];?></p>
  <p><b>เช็คเอาท์:</b> <?=$booking['checkout_date'];?></p>
  <p><b>จำนวนผู้เข้าพัก:</b> <?=$booking['guests'];?> คน</p>
  <p class="price">ยอดชำระ: ฿<?=number_format($booking['total_price'],2);?></p>

  <form method="POST" enctype="multipart/form-data">
    <label><b>เลือกวิธีชำระเงิน:</b></label>
    <select name="payment_method" id="payment_method" class="w3-select w3-border" required onchange="toggleBank()">
      <option value="">-- เลือก --</option>
      <option value="credit_card">💳 บัตรเครดิต/เดบิต</option>
      <option value="bank_transfer">🏦 โอนผ่านธนาคาร / PromptPay</option>
      <option value="cash">💵 ชำระเงินสด</option>
    </select>

    <!-- ✅ เลือกบัญชีธนาคาร -->
    <div id="bankBox" class="bank-box" style="display:none;">
      <h3>🏦 เลือกบัญชีสำหรับโอนเงิน</h3>
      <?php 
      $banks=$conn->query("SELECT * FROM banks");
      while($b=$banks->fetch_assoc()): ?>
        <label class="w3-block w3-padding w3-border w3-round w3-margin-bottom">
          <input type="radio" name="bank_id" value="<?=$b['id']?>" required>
          <b><?=$b['bank_name']?></b><br>
          เลขบัญชี: <?=$b['account_number']?><br>
          ชื่อบัญชี: <?=$b['account_name']?><br>
          <?php if($b['qr_code']): ?>
            <img src="<?=$b['qr_code'];?>" width="150" style="margin-top:5px;">
          <?php endif; ?>
        </label>
      <?php endwhile; ?>

      <label>📤 อัปโหลดสลิปการโอนเงิน</label>
      <input type="file" class="w3-input" name="slip" accept="image/*">
    </div>

    <button type="submit" class="w3-button w3-green w3-margin-top">ยืนยันการชำระเงิน</button>
    <a href="booking_history.php" class="w3-button w3-grey w3-margin-top">ยกเลิก</a>
  </form>
</div>

<script>
function toggleBank(){
  let method=document.getElementById("payment_method").value;
  document.getElementById("bankBox").style.display=(method==="bank_transfer")?"block":"none";
}
</script>

</body>
</html>
