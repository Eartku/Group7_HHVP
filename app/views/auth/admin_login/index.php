<?php $pageTitle = 'Admin Login'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/admin.css">
    <title>Admin Login</title>
</head>
<body>
    <div class="container">
        <div class="welcome-text">
            <h1 class="hover-gradient-text">Welcome to Bonsai - Admin</h1>
        </div>
        <div class="lorem-text">
            <p>Trang quản trị hệ thống</p>
        </div>
        <div class="login-section">
            <h2 class="login-title">ĐĂNG NHẬP</h2>

            <form method="POST" action="<?= BASE_URL ?>/index.php?url=admin-login">

                <div class="form-group">
                    <label>Tên người dùng</label>
                    <input type="text" name="username"
                           placeholder="Nhập tên người dùng"
                           value="<?= htmlspecialchars($username ?? '') ?>">
                    <small style="color:red;"><?= $errors['username'] ?? '' ?></small>
                </div>

                <div class="form-group password-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" id="admin-password">
                    <span class="toggle-password"
                          onclick="togglePassword('admin-password', this)">
                        <img src="<?= BASE_URL ?>/images/hide.svg" style="width:25px;height:25px;">
                    </span>
                    <small style="color:red;"><?= $errors['password'] ?? '' ?></small>
                </div>

                <div class="remember-forgot">
                    <div><input type="checkbox"> Remember</div>
                </div>

                <button type="submit" class="login-btn">Đăng nhập</button>
            </form>
        </div>
    </div>

    <script>
    function togglePassword(inputId, wrapper) {
        const input = document.getElementById(inputId);
        const icon  = wrapper.querySelector("img");
        if (input.type === "password") {
            input.type = "text";
            icon.src   = "<?= BASE_URL ?>/images/show.svg";
        } else {
            input.type = "password";
            icon.src   = "<?= BASE_URL ?>/images/hide.svg";
        }
    }
    </script>
</body>
</html>