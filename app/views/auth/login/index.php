<?php
$noheader = true;
$nolayout = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BonSai | Đăng nhập</title>
    <link rel="stylesheet" href="../app/css/css.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: url("../app/images/background_auth.jpg") no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── Card ── */
        .login-card {
            background: rgba(10, 28, 18, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(163, 198, 57, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 44px 40px 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }

        /* top gradient bar */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #a3c639, #6b7c2e);
        }

        /* ── Brand ── */
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-logo {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #a3c639, #6b7c2e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 4px;
        }
        .brand-tagline {
            font-size: 13px;
            color: #7a9a50;
            letter-spacing: 0.3px;
        }

        /* ── Title ── */
        .form-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #a3c639;
            text-align: center;
            margin-bottom: 24px;
            position: relative;
        }
        .form-title::after {
            content: '';
            display: block;
            width: 36px;
            height: 2px;
            background: linear-gradient(90deg, #a3c639, #6b7c2e);
            border-radius: 2px;
            margin: 8px auto 0;
        }

        /* ── Fields ── */
        .field {
            margin-bottom: 16px;
        }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #7a9a50;
            margin-bottom: 6px;
        }
        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(163,198,57,0.2);
            border-radius: 9px;
            color: #e2ebd0;
            font-family: Arial, sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .field input:focus {
            border-color: #a3c639;
            background: rgba(163,198,57,0.07);
        }
        .field input::placeholder { color: #4a6030; }

        .field-error {
            font-size: 12px;
            color: #f87171;
            margin-top: 5px;
            display: block;
        }

        /* ── Password toggle ── */
        .pwd-wrap {
            position: relative;
        }
        .pwd-wrap input { padding-right: 44px; }
        .toggle-pwd {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: .5;
            transition: opacity .2s;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .toggle-pwd:hover { opacity: 1; }
        .toggle-pwd img { width: 18px; pointer-events: none; }

        /* ── Remember / forgot ── */
        .row-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 12px;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #7a9a50;
            cursor: pointer;
        }
        .remember input[type="checkbox"] {
            accent-color: #a3c639;
            width: 14px;
            height: 14px;
        }
        .forgot-link {
            color: #a3c639;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #a3c639 0%, #6b7c2e 100%);
            color: #fff;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
        }
        .btn-submit:hover { opacity: .9; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        /* ── Footer links ── */
        .card-footer {
            margin-top: 22px;
            text-align: center;
            font-size: 13px;
            color: #4a6030;
        }
        .card-footer a {
            color: #a3c639;
            text-decoration: none;
            font-weight: 700;
        }
        .card-footer a:hover { text-decoration: underline; }
        .card-footer .sep {
            margin: 0 10px;
            opacity: .4;
        }

        /* ── Alert ── */
        .alert-err {
            background: rgba(248,113,113,.12);
            border: 1px solid rgba(248,113,113,.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #f87171;
            margin-bottom: 18px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">

    <!-- Brand -->
    <div class="brand">
        <span class="brand-logo">BonSai 🌱</span>
        <span class="brand-tagline">Mang đến cho bạn một không gian xanh!</span>
    </div>

    <div class="form-title">Đăng nhập</div>

    <?php if (!empty($errors['username']) && empty($errors['password'])): ?>
    <div class="alert-err"><?= htmlspecialchars($errors['username']) ?></div>
    <?php endif; ?>

    <form method="post" action="../app/index.php?url=login">

        <div class="field">
            <label>Tên đăng nhập</label>
            <input type="text" name="username"
                   value="<?= htmlspecialchars($username ?? '') ?>"
                   placeholder="Nhập tên đăng nhập"
                   autocomplete="username">
            <?php if (!empty($errors['username'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['username']) ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label>Mật khẩu</label>
            <div class="pwd-wrap">
                <input type="password" name="password" id="login-password"
                       placeholder="Nhập mật khẩu"
                       autocomplete="current-password">
                <button type="button" class="toggle-pwd"
                        onclick="togglePwd('login-password', this)" style="background-color: #4a6030;">
                    <img src="../app/images/hide.svg" id="ico-login">
                </button>
            </div>
            <?php if (!empty($errors['password'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="row-meta">
            <label class="remember">
                <input type="checkbox"> Ghi nhớ đăng nhập
            </label>
            <a href="#" class="forgot-link">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn-submit">Đăng nhập</button>
    </form>

    <div class="card-footer">
        <a href="../app/index.php?url=register">Tạo tài khoản mới</a>
        <span class="sep">|</span>
        <a href="../app/index.php">Trang chủ</a>
    </div>

</div>

<script>
function togglePwd(id, btn) {
    const input = document.getElementById(id);
    const ico   = btn.querySelector('img');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    ico.src    = isHidden ? '../app/images/show.svg' : '../app/images/hide.svg';
}
</script>
</body>
</html>