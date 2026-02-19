<?php
session_start();
require "../config/db.php";

$errors = [];
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    // ✅ Kiểm tra rỗng
    if ($username == "") {
        $errors['username'] = "Vui lòng nhập tên đăng nhập";
    }

    if ($password == "") {
        $errors['password'] = "Vui lòng nhập mật khẩu";
    }

    // ✅ Nếu không có lỗi mới xử lý DB
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['username']
                ];

         if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../pages/dashboard.php");
            }
            exit();

                exit();
            } else {
                $errors['password'] = "Mật khẩu không đúng";
            }
        } else {
            $errors['username'] = "Tài khoản không tồn tại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link href="../css/css.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="welcome-text">
        <h1 class="hover-gradient-text">Welcome to Bonsai</h1>
    </div>

    <div class="lorem-text">
        <p>Mang đến cho bạn một không gian xanh!</p>
    </div>

    <div class="login-section">
        <h2 class="login-title">ĐĂNG NHẬP</h2>

        <form method="post">

            <!-- USERNAME -->
            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username) ?>">
                <small style="color:red;">
                    <?= $errors['username'] ?? '' ?>
                </small>
            </div>


            <!-- PASSWORD -->
            <div class="form-group password-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" id="login-password">
                <span class="toggle-password" onclick="togglePassword('login-password', this)">
                    <img src="../images/hide.svg" alt="Toggle Password Visibility" style="width: 25px; height: 25px;" />
                </span>
                <small style="color:red;">
                    <?= $errors['password'] ?? '' ?>
                </small>
            </div>

            <div class="remember-forgot">
                <div>
                    <input type="checkbox"> Remember
                </div>
                <a href="forgot.php">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="login-btn">
                Đăng nhập
            </button>
        </form>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <p>Chưa có tài khoản?
            <a href="register.php" class="hover-gradient-text">Đăng ký</a>
        </p>
        <p>
            <a href="../index.html" class="hover-gradient-text">Quay lại</a>
        </p>
    </div>
</div>
<script>
function togglePassword(inputId, wrapper) {
    const input = document.getElementById(inputId);
    const icon = wrapper.querySelector("img");

    if (input.type === "password") {
        input.type = "text";
        icon.src = "../images/show.svg";
    } else {
        input.type = "password";
        icon.src = "../images/hide.svg";
    }
}
</script>
</body>
</html>
