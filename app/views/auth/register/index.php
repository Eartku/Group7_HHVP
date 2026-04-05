<?php
$noheader = true;
$nolayout = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BonSai | Đăng ký</title>
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
            padding: 28px 20px;
        }

        /* ── Card ── */
        .reg-card {
            background: rgba(10, 28, 18, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(163, 198, 57, 0.2);
            width: 100%;
            max-width: 460px;
            padding: 40px 40px 32px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }
        .reg-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #a3c639, #6b7c2e);
        }

        /* ── Brand ── */
        .brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand-logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #a3c639, #6b7c2e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 3px;
        }
        .brand-tagline {
            font-size: 12px;
            color: #7a9a50;
        }

        /* ── Title ── */
        .form-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #a3c639;
            text-align: center;
            margin-bottom: 22px;
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

        /* ── 2-col row ── */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        @media (max-width: 480px) { .field-row { grid-template-columns: 1fr; } }

        /* ── Fields ── */
        .field { margin-bottom: 14px; }
        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #7a9a50;
            margin-bottom: 5px;
        }
        .field .required { color: #f87171; margin-left: 2px; }

        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            padding: 10px 13px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(163,198,57,0.2);
            border-radius: 9px;
            color: #56731e;
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
        .field input.has-error { border-color: rgba(248,113,113,.5); }

        .field-error {
            font-size: 11px;
            color: #f87171;
            margin-top: 4px;
            display: block;
        }

        /* ── Password wrap ── */
        .pwd-wrap { position: relative; }
        .pwd-wrap input { padding-right: 40px; }
        .toggle-pwd {
            position: absolute;
            right: 11px;
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
        .toggle-pwd img { width: 17px; pointer-events: none; }

        /* ── Password strength ── */
        .pwd-strength {
            display: flex;
            gap: 4px;
            margin-top: 6px;
        }
        .pwd-strength-bar {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: rgba(255,255,255,.1);
            transition: background .3s;
        }
        .pwd-strength-bar.weak   { background: #f87171; }
        .pwd-strength-bar.medium { background: #fbbf24; }
        .pwd-strength-bar.strong { background: #a3c639; }
        .pwd-strength-label {
            font-size: 11px;
            color: #7a9a50;
            margin-top: 4px;
            min-height: 16px;
        }

        /* ── Alert general ── */
        .alert-err {
            background: rgba(248,113,113,.12);
            border: 1px solid rgba(248,113,113,.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #f87171;
            margin-bottom: 16px;
            text-align: center;
        }

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
            margin-top: 6px;
            transition: opacity .2s, transform .15s;
        }
        .btn-submit:hover { opacity: .9; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        /* ── Footer ── */
        .card-footer {
            margin-top: 20px;
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

        /* ── Divider ── */
        .section-sep {
            border: none;
            border-top: 1px solid rgba(163,198,57,.12);
            margin: 16px 0 14px;
        }
        .section-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #4a6030;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<div class="reg-card">

    <!-- Brand -->
    <div class="brand">
        <span class="brand-logo">BonSai 🌱</span>
        <span class="brand-tagline">Tạo tài khoản miễn phí</span>
    </div>

    <div class="form-title">Đăng ký tài khoản</div>

    <?php if (!empty($errors['general'])): ?>
    <div class="alert-err"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_REGISTER_PATH ?>"
          onsubmit="return checkForm()">

        <!-- Row 1: username + fullname -->
        <div class="field-row">
            <div class="field">
                <label>Tên đăng nhập <span class="required">*</span></label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username ?? '') ?>"
                       placeholder="username"
                       class="<?= !empty($errors['username']) ? 'has-error' : '' ?>"
                       required autocomplete="username">
                <?php if (!empty($errors['username'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['username']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label>Họ và tên <span class="required">*</span></label>
                <input type="text" name="fullname"
                       value="<?= htmlspecialchars($fullname ?? '') ?>"
                       placeholder="Nguyễn Văn A"
                       class="<?= !empty($errors['fullname']) ? 'has-error' : '' ?>"
                       required>
                <?php if (!empty($errors['fullname'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['fullname']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Row 2: email + phone -->
        <div class="field-row">
            <div class="field">
                <label>Email <span class="required">*</span></label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($email ?? '') ?>"
                       placeholder="email@example.com"
                       class="<?= !empty($errors['email']) ? 'has-error' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label>Số điện thoại</label>
                <input type="text" name="phone"
                       value="<?= htmlspecialchars($phone ?? '') ?>"
                       placeholder="09xxxxxxxx">
            </div>
        </div>

        <!-- Address full width -->
        <div class="field">
            <label>Địa chỉ <span class="required">*</span></label>
            <input type="text" name="address"
                   value="<?= htmlspecialchars($address ?? '') ?>"
                   placeholder="Số nhà, đường, quận, tỉnh/thành"
                   class="<?= !empty($errors['address']) ? 'has-error' : '' ?>"
                   required>
            <?php if (!empty($errors['address'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['address']) ?></span>
            <?php endif; ?>
        </div>

        <hr class="section-sep">
        <div class="section-label">Bảo mật</div>

        <!-- Password -->
        <div class="field">
            <label>Mật khẩu <span class="required">*</span></label>
            <div class="pwd-wrap">
                <input type="password" name="password" id="reg-password"
                       placeholder="Tối thiểu 8 ký tự"
                       class="<?= !empty($errors['password']) ? 'has-error' : '' ?>"
                       required oninput="checkStrength(this.value)">
                <button type="button" class="toggle-pwd"
                        onclick="togglePwd('reg-password','ico-pwd1')" style="background-color: #4a6030;">
                    <img src="../app/images/hide.svg" id="ico-pwd1">
                </button>
            </div>
            <div class="pwd-strength">
                <div class="pwd-strength-bar" id="bar1"></div>
                <div class="pwd-strength-bar" id="bar2"></div>
                <div class="pwd-strength-bar" id="bar3"></div>
                <div class="pwd-strength-bar" id="bar4"></div>
            </div>
            <div class="pwd-strength-label" id="strength-label"></div>
            <?php if (!empty($errors['password'])): ?>
                <span class="field-error" id="password-error">
                    <?= htmlspecialchars($errors['password']) ?>
                </span>
            <?php else: ?>
                <span class="field-error" id="password-error"></span>
            <?php endif; ?>
        </div>

        <!-- Confirm password -->
        <div class="field">
            <label>Xác nhận mật khẩu <span class="required">*</span></label>
            <div class="pwd-wrap">
                <button type="button" class="toggle-pwd"
                        onclick="togglePwd('reg-confirm','ico-pwd2')" style="background-color: #4a6030;">
                    <img src="../app/images/hide.svg" id="ico-pwd2">
                </button>
                <input type="password" name="confirm_password" id="reg-confirm"
                       placeholder="Nhập lại mật khẩu"
                       class="<?= !empty($errors['confirm']) ? 'has-error' : '' ?>"
                       required>
                
            </div>
            <?php if (!empty($errors['confirm'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['confirm']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-submit">Tạo tài khoản</button>
    </form>

    <div class="card-footer">
        Đã có tài khoản?
        <a href="<?= BASE_LOGIN_PATH ?>">Đăng nhập ngay</a>
    </div>

</div>

<script>
function togglePwd(inputId, icoId) {
    const input = document.getElementById(inputId);
    const ico   = document.getElementById(icoId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    ico.src    = isHidden ? '../app/images/show.svg' : '../app/images/hide.svg';
}

function checkStrength(p) {
    const bars   = [document.getElementById('bar1'),document.getElementById('bar2'),
                    document.getElementById('bar3'),document.getElementById('bar4')];
    const label  = document.getElementById('strength-label');
    const levels = [
        /[A-Z]/.test(p),
        /[a-z]/.test(p),
        /[0-9]/.test(p),
        /[!@#$%^&*]/.test(p)
    ];
    const len   = p.length >= 8;
    const score = levels.filter(Boolean).length + (len ? 1 : 0);

    bars.forEach(b => b.className = 'pwd-strength-bar');
    if (!p) { label.textContent = ''; return; }

    if (score <= 2) {
        bars[0].classList.add('weak');
        label.textContent = 'Yếu';  label.style.color = '#f87171';
    } else if (score === 3) {
        bars[0].classList.add('medium'); bars[1].classList.add('medium');
        label.textContent = 'Trung bình'; label.style.color = '#fbbf24';
    } else if (score === 4) {
        bars[0].classList.add('strong'); bars[1].classList.add('strong');
        bars[2].classList.add('strong');
        label.textContent = 'Khá mạnh'; label.style.color = '#a3c639';
    } else {
        bars.forEach(b => b.classList.add('strong'));
        label.textContent = 'Mạnh'; label.style.color = '#a3c639';
    }
}

function checkForm() {
    const p   = document.getElementById('reg-password').value;
    const c   = document.getElementById('reg-confirm').value;
    const err = document.getElementById('password-error');

    // Kiểm tra số điện thoại (nếu có nhập)

    if (phone === '') return true; // không bắt buộc
    // Định dạng VN: 10 số, đầu 03x|05x|07x|08x|09x
    const vnPhone = /^(0[3|5|7|8|9])+([0-9]{8})$/;
        return vnPhone.test(phone);

    if (p.length < 8) {
        err.textContent = 'Mật khẩu phải có ít nhất 8 ký tự';
        return false;
    }
    if (!/[A-Z]/.test(p)||!/[a-z]/.test(p)||!/[0-9]/.test(p)||!/[!@#$%^&*]/.test(p)) {
        err.textContent = 'Cần chữ hoa, thường, số và ký tự đặc biệt (!@#$%^&*)';
        return false;
    }
    if (p !== c) {
        alert('Mật khẩu không khớp');
        return false;
    }
    err.textContent = '';
    return true;
}
</script>
</body>
</html>