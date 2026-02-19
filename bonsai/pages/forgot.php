<?php
require "../config/db.php";

$errors = [];
$success = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {
        $errors['email'] = "Vui lòng nhập email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ";
    }

    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + 3600); // 1 giờ

            $update = $conn->prepare(
                "UPDATE users SET reset_token=?, reset_expires=? WHERE email=?"
            );
            $update->bind_param("sss", $token, $expires, $email);
            $update->execute();

            // DEMO link (sau này gửi mail)
            $resetLink = "http://localhost/bonsai/reset.php?token=$token";

            $success = "✅ Link đặt lại mật khẩu (demo):<br>
                        <a href='$resetLink'>$resetLink</a>";
        } else {
            $errors['email'] = "Email không tồn tại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quên mật khẩu</title>
<link href="../css/css.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2 class="login-title">QUÊN MẬT KHẨU</h2>

    <?php if ($success): ?>
        <p style="color:green;text-align:center"><?= $success ?></p>
    <?php endif; ?>

    <form method="post">

        <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <input type="email" name="email"
                   value="<?= htmlspecialchars($email) ?>"
                   placeholder="Nhập email đã đăng ký"
                   required>
            <small style="color:red">
                <?= $errors['email'] ?? '' ?>
            </small>
        </div>

        <button class="login-btn">Gửi yêu cầu</button>
    </form>

    <p style="text-align:center;margin-top:15px">
        <a href="login.php">← Quay lại đăng nhập</a>
    </p>
</div>

</body>
</html>
