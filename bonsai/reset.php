<?php
session_start();
require "db.php";

$errors = [];
$success = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $newPass  = $_POST["new_password"];

    // ✅ Validate
    if ($username === "") {
        $errors['username'] = "Vui lòng nhập tên đăng nhập";
    }

    if ($email === "") {
        $errors['email'] = "Vui lòng nhập email";
    }

    if ($newPass === "") {
        $errors['new_password'] = "Vui lòng nhập mật khẩu mới";
    }

    if (empty($errors)) {
        // ✅ Kiểm tra user + email có khớp không
        $sql = "SELECT id FROM users WHERE username = ? AND email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // ✅ Hash mật khẩu mới
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);

            // ✅ Update mật khẩu
            $update = "UPDATE users SET password = ? WHERE id = ?";
            $stmt2 = $conn->prepare($update);
            $stmt2->bind_param("si", $hashed, $user['id']);
            $stmt2->execute();

            $success = "Đổi mật khẩu thành công! Bạn có thể đăng nhập lại.";
        } else {
            $errors['general'] = "Tên đăng nhập hoặc email không đúng";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="css/css.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="welcome-text">
        <h1 class="hover-gradient-text">Bonsai</h1>
    </div>

    <div class="login-section">
        <h2 class="login-title">QUÊN MẬT KHẨU</h2>

        <?php if ($success): ?>
            <p style="color:green; text-align:center;">
                <?= $success ?>
            </p>
            <p style="text-align:center;">
                <a href="login.php" class="hover-gradient-text">Quay lại đăng nhập</a>
            </p>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="post">

            <?php if (isset($errors['general'])): ?>
                <p style="color:red; text-align:center;">
                    <?= $errors['general'] ?>
                </p>
            <?php endif; ?>

            <!-- USERNAME -->
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username) ?>">
                <small style="color:red;">
                    <?= $errors['username'] ?? '' ?>
                </small>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($email) ?>">
                <small style="color:red;">
                    <?= $errors['email'] ?? '' ?>
                </small>
            </div>

            <!-- NEW PASSWORD -->
            <div class="form-group password-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="new_password" id="new-password">
                <span class="toggle-password"
                      onclick="togglePassword('new-password', this)">
                    <img src="images/hide.svg" style="width:25px;height:25px;">
                </span>
                <small style="color:red;">
                    <?= $errors['new_password'] ?? '' ?>
                </small>
            </div>

            <button type="submit" class="login-btn">
                Đổi mật khẩu
            </button>
        </form>
        <?php endif; ?>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <a href="login.php" class="hover-gradient-text">Quay lại đăng nhập</a>
    </div>
</div>

<script>
function togglePassword(inputId, wrapper) {
    const input = document.getElementById(inputId);
    const icon = wrapper.querySelector("img");

    if (input.type === "password") {
        input.type = "text";
        icon.src = "images/show.svg";
    } else {
        input.type = "password";
        icon.src = "images/hide.svg";
    }
}
</script>
</body>
</html>
