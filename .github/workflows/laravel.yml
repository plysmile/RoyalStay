<?php
session_start();

/* ── เชื่อมต่อฐานข้อมูล ───────────────────────────── */
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
  die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

/* ── ถ้าเข้าสู่ระบบอยู่ ให้ดึงข้อมูลผู้ใช้ ─────────── */
$user = null;
if (isset($_SESSION['user_id'])) {
  $user_id = (int)$_SESSION['user_id'];
  $stmtUser = $conn->prepare("SELECT fullname, email FROM users WHERE id=?");
  $stmtUser->bind_param("i", $user_id);
  $stmtUser->execute();
  $user = $stmtUser->get_result()->fetch_assoc();
  $stmtUser->close();
}

/* ── สำหรับ dropdown รายชื่อโรงแรม ────────────────── */
$hotels = $conn->query("SELECT id, name FROM hotels ORDER BY name");

/* ── โรงแรมแนะนำ (แสดงในสไลด์) ───────────────────── */
$stmtRec = $conn->prepare("SELECT id, name, location, price_per_night, image FROM hotels ORDER BY id DESC LIMIT 12");
$stmtRec->execute();
$recommended = $stmtRec->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>RoyalStay | จองโรงแรมเชียงใหม่</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- รองรับมือถือ -->
  <meta name="description" content="RoyalStay ระบบจองโรงแรมเชียงใหม่ ค้นหา เปรียบเทียบ และจองโรงแรมได้ง่าย ๆ">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background-image: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb');
      background-position: center;
      background-size: cover;
      background-attachment: fixed;
      font-family: "Tahoma", sans-serif;
    }
    .main-box {
      max-width: 700px;
      margin: 80px auto;
      padding: 20px;
      background: rgba(255,255,255,0.95);
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .hotel-card {
      width: 350px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .hotel-card img { width:100%; height:220px; object-fit:cover; }
    .hotel-info { padding:15px; }
    .hotel-info .price { font-weight:bold; color:#007bff; margin-top:5px; }
    .swiper-container-wrapper { max-width:1200px; margin:40px auto; }
    .swiper-button-next, .swiper-button-prev {
      color: #007bff;
      background: rgba(255,255,255,0.8);
      border-radius: 50%;
      width: 45px;
      height: 45px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .swiper-button-next:hover, .swiper-button-prev:hover { background: #fff; }
    .highlight-title {
      display: inline-block;
      background: linear-gradient(135deg, #007bff, #6610f2);
      color: #fff;
      padding: 12px 30px;
      border-radius: 30px;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
      text-align: center;
    }
    /* ทำการ์ดสไลด์ยืดหยุ่นในมือถือ */
    @media (max-width: 600px) {
      .hotel-card { width: 100%; }
      .hotel-card img { height: 200px; }
      .main-box { margin: 30px 10px; }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<div class="w3-bar w3-blue">
  <?php if ($user): ?>
    <a href="booking_history.php" class="w3-bar-item w3-button w3-right"><i class="fa fa-history"></i> ประวัติการจอง</a>
    <div class="w3-dropdown-hover w3-right">
      <button class="w3-button">
        <i class="fa fa-user"></i> <?php echo htmlspecialchars($user['fullname']); ?> ▼
      </button>
      <div class="w3-dropdown-content w3-bar-block w3-card-4" style="right:0; min-width:200px;">
        <a href="profile.php" class="w3-bar-item w3-button"><i class="fa fa-id-card"></i> ข้อมูลส่วนตัว</a>
        <a href="logout.php" class="w3-bar-item w3-button w3-red"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
      </div>
    </div>
  <?php else: ?>
    <a href="register.html" class="w3-bar-item w3-button w3-right">สมัครสมาชิก</a>
    <a href="login.html" class="w3-bar-item w3-button w3-right">เข้าสู่ระบบ</a>
  <?php endif; ?>
</div>

<!-- Hero Title -->
<header class="w3-center" style="padding:60px 20px;">
  <h1 style="
    font-size:48px;
    font-weight:bold;
    color:#fff;
    text-shadow:2px 2px 8px rgba(0,0,0,0.6);
  ">
    RoyalStay
  </h1>
  <p style="font-size:20px; color:#f1f1f1; text-shadow:1px 1px 5px rgba(0,0,0,0.5);">
    🏨 ระบบจองโรงแรมเชียงใหม่ สะดวก รวดเร็ว ปลอดภัย
  </p>
</header>


<!-- ฟอร์มค้นหาโรงแรม -->
<div class="main-box">
  <h3>ค้นหาโรงแรม</h3>
  <form action="search_results.php" method="GET">
    <label>เลือกโรงแรม</label>
    <select class="w3-select w3-border" name="hotel" required>
      <option value="" disabled selected>-- เลือกโรงแรม --</option>
      <?php if ($hotels && $hotels->num_rows > 0): ?>
        <?php while($h = $hotels->fetch_assoc()): ?>
          <option value="<?php echo (int)$h['id']; ?>">
            <?php echo htmlspecialchars($h['name']); ?>
          </option>
        <?php endwhile; ?>
      <?php else: ?>
        <option disabled>ยังไม่มีโรงแรมในระบบ</option>
      <?php endif; ?>
    </select>

    <div class="w3-row-padding w3-margin-top">
      <div class="w3-half">
        <label>วันที่เช็คอิน</label>
        <input class="w3-input w3-border" type="date" name="checkin" required>
      </div>
      <div class="w3-half">
        <label>วันที่เช็คเอาท์</label>
        <input class="w3-input w3-border" type="date" name="checkout" required>
      </div>
    </div>

    <div class="w3-row-padding w3-margin-top">
      <div class="w3-half">
        <label>จำนวนผู้เข้าพัก</label>
        <input class="w3-input w3-border" type="number" name="guests" value="2" min="1" required>
      </div>
    </div>

    <button class="w3-button w3-blue w3-margin-top w3-block">
      <i class="fa fa-search"></i> ค้นหา
    </button>
  </form>
</div>

<!-- โรงแรมแนะนำ -->
<div class="swiper-container-wrapper">
  <div class="w3-center">
    <h2 class="highlight-title">🏨 โรงแรมแนะนำ</h2>
  </div>

  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <?php if ($recommended && $recommended->num_rows > 0): ?>
        <?php while($row = $recommended->fetch_assoc()): ?>
          <div class="swiper-slide">
            <div class="hotel-card">
              <img src="<?php echo htmlspecialchars($row['image'] ?: 'https://via.placeholder.com/800x450?text=RoyalStay'); ?>"
                   alt="<?php echo htmlspecialchars($row['name']); ?>">
              <div class="hotel-info">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p><?php echo htmlspecialchars($row['location']); ?></p>
                <p class="price">฿<?php echo number_format((float)$row['price_per_night'], 2); ?> / คืน</p>
                <a href="confirm_booking.php?hotel_id=<?php echo (int)$row['id']; ?>"
                   class="w3-button w3-blue w3-margin-top w3-block">จองเลย</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="swiper-slide">
          <div class="w3-panel w3-white w3-round w3-padding">
            ยังไม่มีโรงแรมสำหรับแนะนำในตอนนี้
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
var swiper = new Swiper(".mySwiper", {
  slidesPerView: 3,
  spaceBetween: 20,
  loop: true,
  pagination: { el: ".swiper-pagination", clickable: true },
  navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
  breakpoints: {
    0:   { slidesPerView: 1 },
    768: { slidesPerView: 2 },
    1024:{ slidesPerView: 3 }
  }
});
</script>

</body>
</html>
<?php
/* ปิด statement ที่เปิดไว้ */
$stmtRec->close();
/* ปิดการเชื่อมต่อถ้าอยากปิด */
$conn->close();
?>
