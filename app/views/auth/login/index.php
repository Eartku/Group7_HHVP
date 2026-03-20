<?php
$noheader = true;
$nolayout = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <link rel="stylesheet" href="../app/css/css.css">
    <title>BonSai | Đăng nhập</title>
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

        <form method="post" action="../app/index.php?url=login">

            <div class="form-group">
                <label>Tên người dùng</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username ?? '') ?>">
                <small style="color:red;"><?= $errors['username'] ?? '' ?></small>
            </div>

            <div class="form-group password-group">
                <label>Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="login-password">
                    <span class="toggle-password"
                          onclick="togglePassword('login-password', this)">
                        <img src="/app/images/hide.svg" style="width:25px;height:25px;" />
                    </span>
                </div>
                <small style="color:red;"><?= $errors['password'] ?? '' ?></small>
            </div>

            <div class="remember-forgot">
                <div><input type="checkbox"> Remember</div>
                <a href="#">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="login-btn">Đăng nhập</button>
        </form>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <p>Chưa có tài khoản?
            <a href="../app/index.php?url=register" class="hover-gradient-text">Đăng ký</a>
        </p>
        <p>
            <a href="../app/index.php" class="hover-gradient-text">Quay lại</a>
        </p>
    </div>

</div>

<script>
function togglePassword(inputId, wrapper) {
    const input = document.getElementById(inputId);
    const icon  = wrapper.querySelector("img");
    if (input.type === "password") {
        input.type = "text";
        icon.src   = "/app/images/show.svg";
    } else {
        input.type = "password";
        icon.src   = "/app/images/hide.svg";
    }
}
</script>
</body>
</html>