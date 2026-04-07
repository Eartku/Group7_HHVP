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
            display: flex; align-items: center; justify-content: center;
            padding: 28px 20px;
        }
        .reg-card {
            background: rgba(10, 28, 18, 0.92);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(163, 198, 57, 0.2);
            width: 100%; max-width: 460px;
            padding: 40px 40px 32px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
            position: relative; overflow: hidden;
        }
        .reg-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #a3c639, #6b7c2e);
        }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand-logo {
            font-size: 28px; font-weight: 900; letter-spacing: -1px;
            background: linear-gradient(135deg, #a3c639, #6b7c2e);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; display: block; margin-bottom: 3px;
        }
        .brand-tagline { font-size: 12px; color: #7a9a50; }
        .form-title {
            font-size: 13px; font-weight: 700; letter-spacing: 3px;
            text-transform: uppercase; color: #a3c639;
            text-align: center; margin-bottom: 22px; position: relative;
        }
        .form-title::after {
            content: ''; display: block; width: 36px; height: 2px;
            background: linear-gradient(90deg, #a3c639, #6b7c2e);
            border-radius: 2px; margin: 8px auto 0;
        }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 480px) { .field-row { grid-template-columns: 1fr; } }
        .field { margin-bottom: 14px; }
        .field label {
            display: block; font-size: 11px; font-weight: 700;
            letter-spacing: 1px; text-transform: uppercase;
            color: #7a9a50; margin-bottom: 5px;
        }
        .field .required { color: #f87171; margin-left: 2px; }
        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"],
        .field select {
            width: 100%; padding: 10px 13px;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(163,198,57,0.2);
            border-radius: 9px; color: #56731e;
            font-family: Arial, sans-serif; font-size: 14px;
            outline: none; transition: border-color .2s, background .2s;
        }
        .field select {
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%237a9a50' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            background-color: rgba(255,255,255,0.06); padding-right: 32px; cursor: pointer;
        }
        .field select option { background: #0a1c12; color: #a3c639; }
        .field select:disabled { opacity: 0.4; cursor: not-allowed; }
        .field input:focus, .field select:focus {
            border-color: #a3c639; background: rgba(163,198,57,0.07);
        }
        .field input::placeholder { color: #4a6030; }
        .field input.has-error, .field select.has-error { border-color: rgba(248,113,113,.5); }

        /* Spinner loading */
        .select-wrap { position: relative; }
        .select-spinner {
            position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
            width: 14px; height: 14px;
            border: 2px solid rgba(163,198,57,0.2); border-top-color: #a3c639;
            border-radius: 50%; animation: spin .6s linear infinite; display: none;
        }
        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

        .field-error { font-size: 11px; color: #f87171; margin-top: 4px; display: block; }
        .pwd-wrap { position: relative; }
        .pwd-wrap input { padding-right: 40px; }
        .toggle-pwd {
            position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
            cursor: pointer; opacity: .5; transition: opacity .2s;
            background: none; border: none; padding: 0; display: flex; align-items: center;
        }
        .toggle-pwd:hover { opacity: 1; }
        .toggle-pwd img { width: 17px; pointer-events: none; }
        .pwd-strength { display: flex; gap: 4px; margin-top: 6px; }
        .pwd-strength-bar {
            flex: 1; height: 3px; border-radius: 2px;
            background: rgba(255,255,255,.1); transition: background .3s;
        }
        .pwd-strength-bar.weak   { background: #f87171; }
        .pwd-strength-bar.medium { background: #fbbf24; }
        .pwd-strength-bar.strong { background: #a3c639; }
        .pwd-strength-label { font-size: 11px; color: #7a9a50; margin-top: 4px; min-height: 16px; }
        .alert-err {
            background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.3);
            border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #f87171;
            margin-bottom: 16px; text-align: center;
        }
        .btn-submit {
            width: 100%; padding: 13px; border: none; border-radius: 9px;
            background: linear-gradient(135deg, #a3c639 0%, #6b7c2e 100%);
            color: #fff; font-family: Arial, sans-serif; font-size: 14px;
            font-weight: 700; letter-spacing: 1px; cursor: pointer;
            margin-top: 6px; transition: opacity .2s, transform .15s;
        }
        .btn-submit:hover { opacity: .9; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }
        .card-footer { margin-top: 20px; text-align: center; font-size: 13px; color: #4a6030; }
        .card-footer a { color: #a3c639; text-decoration: none; font-weight: 700; }
        .card-footer a:hover { text-decoration: underline; }
        .section-sep { border: none; border-top: 1px solid rgba(163,198,57,.12); margin: 16px 0 14px; }
        .section-label { font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #4a6030; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="reg-card">

    <div class="brand">
        <span class="brand-logo">BonSai 🌱</span>
        <span class="brand-tagline">Tạo tài khoản miễn phí</span>
    </div>
    <div class="form-title">Đăng ký tài khoản</div>

    <?php if (!empty($errors['general'])): ?>
    <div class="alert-err"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_REGISTER_PATH ?>" onsubmit="return checkForm()">

        <!-- username + fullname -->
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

        <!-- email + phone -->
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

        <!-- Số nhà / đường -->
        <div class="field">
            <label>Số nhà / Tên đường <span class="required">*</span></label>
            <input type="text" id="addr-street"
                   placeholder="VD: 12 Lê Lợi"
                   oninput="joinAddress()" required>
        </div>

        <!-- Tỉnh/thành -->
        <div class="field">
            <label>Tỉnh / Thành phố <span class="required">*</span></label>
            <div class="select-wrap">
                <select id="addr-province" onchange="loadDistricts()" required>
                    <option value="">— Đang tải... —</option>
                </select>
                <span class="select-spinner" id="spin-province"></span>
            </div>
        </div>

        <!-- Quận + Phường -->
        <div class="field-row">
            <div class="field">
                <label>Quận / Huyện <span class="required">*</span></label>
                <div class="select-wrap">
                    <select id="addr-district" onchange="loadWards()" disabled required>
                        <option value="">— Chọn quận / huyện —</option>
                    </select>
                    <span class="select-spinner" id="spin-district"></span>
                </div>
            </div>
            <div class="field">
                <label>Phường / Xã <span class="required">*</span></label>
                <div class="select-wrap">
                    <select id="addr-ward" onchange="joinAddress()" disabled required>
                        <option value="">— Chọn phường / xã —</option>
                    </select>
                    <span class="select-spinner" id="spin-ward"></span>
                </div>
            </div>
        </div>

        <!-- Hidden field gửi server -->
        <input type="hidden" name="address" id="addr-full"
               value="<?= htmlspecialchars($address ?? '') ?>">
        <?php if (!empty($errors['address'])): ?>
            <span class="field-error"><?= htmlspecialchars($errors['address']) ?></span>
        <?php endif; ?>

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
                        onclick="togglePwd('reg-password','ico-pwd1')" style="background-color:#4a6030;">
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
                <span class="field-error" id="password-error"><?= htmlspecialchars($errors['password']) ?></span>
            <?php else: ?>
                <span class="field-error" id="password-error"></span>
            <?php endif; ?>
        </div>

        <!-- Confirm password -->
        <div class="field">
            <label>Xác nhận mật khẩu <span class="required">*</span></label>
            <div class="pwd-wrap">
                <button type="button" class="toggle-pwd"
                        onclick="togglePwd('reg-confirm','ico-pwd2')" style="background-color:#4a6030;">
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
        Đã có tài khoản? <a href="<?= BASE_LOGIN_PATH ?>">Đăng nhập ngay</a>
    </div>
</div>

<script>
const API = 'https://provinces.open-api.vn/api';

function spinner(id, show) {
    document.getElementById(id).style.display = show ? 'block' : 'none';
}

function resetSelect(id, label) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${label}</option>`;
    el.disabled = true;
}

/* Lấy text của option đang chọn, bỏ nếu là placeholder (bắt đầu bằng —) */
function getText(id) {
    const el  = document.getElementById(id);
    const txt = el.options[el.selectedIndex]?.text || '';
    return txt.startsWith('—') ? '' : txt;
}

/* Join 4 phần → hidden field */
function joinAddress() {
    const parts = [
        document.getElementById('addr-street').value.trim(),
        getText('addr-ward'),
        getText('addr-district'),
        getText('addr-province'),
    ].filter(Boolean);
    document.getElementById('addr-full').value = parts.join(', ');
}

/* Load tỉnh/thành khi trang mở */
async function loadProvinces() {
    spinner('spin-province', true);
    try {
        const data = await fetch(`${API}/p/`).then(r => r.json());
        const sel  = document.getElementById('addr-province');
        sel.innerHTML = '<option value="">— Chọn tỉnh / thành phố —</option>';
        data.forEach(p => {
            sel.insertAdjacentHTML('beforeend', `<option value="${p.code}">${p.name}</option>`);
        });
        sel.disabled = false;
    } catch { sel.innerHTML = '<option value="">Lỗi tải dữ liệu</option>'; }
    finally  { spinner('spin-province', false); }
}

/* Chọn tỉnh → load quận */
async function loadDistricts() {
    const code = document.getElementById('addr-province').value;
    resetSelect('addr-district', '— Chọn quận / huyện —');
    resetSelect('addr-ward',     '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    spinner('spin-district', true);
    try {
        const data = await fetch(`${API}/p/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-district');
        sel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
        (data.districts || []).forEach(d => {
            sel.insertAdjacentHTML('beforeend', `<option value="${d.code}">${d.name}</option>`);
        });
        sel.disabled = false;
    } catch { /* giữ disabled */ }
    finally  { spinner('spin-district', false); }
}

/* Chọn quận → load phường */
async function loadWards() {
    const code = document.getElementById('addr-district').value;
    resetSelect('addr-ward', '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    spinner('spin-ward', true);
    try {
        const data = await fetch(`${API}/d/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-ward');
        sel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
        (data.wards || []).forEach(w => {
            sel.insertAdjacentHTML('beforeend', `<option value="${w.code}">${w.name}</option>`);
        });
        sel.disabled = false;
    } catch { /* giữ disabled */ }
    finally  { spinner('spin-ward', false); }
}

/* Toggle password */
function togglePwd(inputId, icoId) {
    const input    = document.getElementById(inputId);
    const ico      = document.getElementById(icoId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    ico.src    = isHidden ? '../app/images/show.svg' : '../app/images/hide.svg';
}

/* Password strength */
function checkStrength(p) {
    const bars  = ['bar1','bar2','bar3','bar4'].map(id => document.getElementById(id));
    const label = document.getElementById('strength-label');
    const score = [/[A-Z]/.test(p),/[a-z]/.test(p),/[0-9]/.test(p),/[!@#$%^&*]/.test(p)]
                    .filter(Boolean).length + (p.length >= 8 ? 1 : 0);
    bars.forEach(b => b.className = 'pwd-strength-bar');
    if (!p) { label.textContent = ''; return; }
    if      (score <= 2) { bars[0].classList.add('weak');   label.textContent='Yếu';        label.style.color='#f87171'; }
    else if (score === 3) { bars[0].classList.add('medium'); bars[1].classList.add('medium'); label.textContent='Trung bình'; label.style.color='#fbbf24'; }
    else if (score === 4) { [0,1,2].forEach(i=>bars[i].classList.add('strong')); label.textContent='Khá mạnh'; label.style.color='#a3c639'; }
    else                  { bars.forEach(b=>b.classList.add('strong')); label.textContent='Mạnh'; label.style.color='#a3c639'; }
}

/* Validate form */
function checkForm() {
    if (!document.getElementById('addr-street').value.trim())          { alert('Vui lòng nhập số nhà / tên đường'); return false; }
    if (!document.getElementById('addr-province').value)               { alert('Vui lòng chọn tỉnh / thành phố');  return false; }
    if (!document.getElementById('addr-district').value)               { alert('Vui lòng chọn quận / huyện');       return false; }
    if (!document.getElementById('addr-ward').value)                   { alert('Vui lòng chọn phường / xã');        return false; }
    joinAddress();

    const phone = document.querySelector('input[name="phone"]').value.trim();
    if (phone && !/^(0[35789])[0-9]{8}$/.test(phone)) {
        alert('Số điện thoại không đúng định dạng (VD: 0912345678)'); return false;
    }

    const p   = document.getElementById('reg-password').value;
    const c   = document.getElementById('reg-confirm').value;
    const err = document.getElementById('password-error');
    if (p.length < 8)                                                           { err.textContent = 'Mật khẩu phải có ít nhất 8 ký tự'; return false; }
    if (!/[A-Z]/.test(p)||!/[a-z]/.test(p)||!/[0-9]/.test(p)||!/[!@#$%^&*]/.test(p)) { err.textContent = 'Cần chữ hoa, thường, số và ký tự đặc biệt (!@#$%^&*)'; return false; }
    if (p !== c)                                                                 { alert('Mật khẩu không khớp'); return false; }
    err.textContent = '';
    return true;
}

loadProvinces();
</script>
</body>
</html>