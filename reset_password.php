<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hotel_booking");

$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && strtotime($user['reset_expires']) > time()) {
        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
        $stmt->bind_param("si", $password, $user['id']);
        $stmt->execute();
        echo "✅ เปลี่ยนรหัสผ่านสำเร็จ <a href='login.html'>เข้าสู่ระบบ</a>";
    } else {
        echo "❌ ลิงก์ไม่ถูกต้องหรือหมดอายุแล้ว";
    }
}
?>

<form method="POST">
    <h3>ตั้งรหัสผ่านใหม่</h3>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="password" placeholder="รหัสผ่านใหม่" required>
    <button type="submit">เปลี่ยนรหัสผ่าน</button>
</form>
