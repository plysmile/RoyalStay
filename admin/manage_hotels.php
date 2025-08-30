<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// ลบโรงแรม
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM hotels WHERE id=$id");
    echo "<script>alert('ลบโรงแรมเรียบร้อย'); window.location='manage_hotels.php';</script>";
    exit();
}

// ดึงข้อมูลโรงแรมทั้งหมด
$result = $conn->query("SELECT * FROM hotels ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการโรงแรม</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: "Tahoma", sans-serif;
            background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
        }
        .container-box {
            max-width: 1100px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        table img {
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        .navbar {
            background: #333;
            color: white;
            padding: 10px;
        }
        .navbar a {
            color: white;
            padding: 8px 16px;
            text-decoration: none;
        }
        .navbar a:hover {
            background: #555;
        }
    </style>
</head>
<body>

<!-- 🔹 Navbar Admin -->
<div class="navbar">
    <a href="manage_hotels.php"><i class="fa fa-hotel"></i> จัดการโรงแรม</a>
    <a href="manage_bookings.php"><i class="fa fa-calendar-check"></i> จัดการการจอง</a>
    <a href="admin_logout.php" class="w3-right w3-red"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
</div>

<div class="container-box w3-animate-top">
    <h2 class="w3-center w3-text-indigo">📌 จัดการโรงแรม</h2>
    <a href="add_hotel.php" class="w3-button w3-green w3-round-large w3-margin-bottom">
        <i class="fa fa-plus"></i> เพิ่มโรงแรม
    </a>

    <table class="w3-table-all w3-hoverable w3-card-4">
        <tr class="w3-indigo w3-text-white">
            <th>ID</th>
            <th>ชื่อโรงแรม</th>
            <th>ที่อยู่</th>
            <th>ราคา/คืน</th>
            <th>รูปภาพ</th>
            <th>การจัดการ</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><b><?php echo htmlspecialchars($row['name']); ?></b></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td class="w3-text-green">฿<?php echo number_format($row['price_per_night'],2); ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../<?php echo $row['image']; ?>" width="100">
                    <?php else: ?>
                        <span class="w3-text-red">ไม่มีรูป</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_hotel.php?id=<?php echo $row['id']; ?>" class="w3-button w3-yellow w3-round">
                        <i class="fa fa-edit"></i> แก้ไข
                    </a>
                    <a href="manage_hotels.php?delete=<?php echo $row['id']; ?>" 
                       class="w3-button w3-red w3-round"
                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบโรงแรมนี้?');">
                       <i class="fa fa-trash"></i> ลบ
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="w3-center w3-text-red">❌ ยังไม่มีข้อมูลโรงแรม</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
