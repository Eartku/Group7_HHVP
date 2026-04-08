<?php /* app/views/admin/customers/create/index.php */ ?>

<div class="container py-5" style="max-width:580px">

    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-customers">Khách hàng</a>
        <span class="sep">›</span>
        <span>Tạo tài khoản</span>
    </div>

    <h2 class="ui-title">Tạo tài khoản khách hàng</h2>

    <?php if (!empty($errors['general'])): ?>
    <div class="ui-alert danger mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <h5>Thông tin tài khoản</h5>
        </div>
        <div class="ui-card-body">
            <form id="createForm" method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-customers-create">

                <div class="ui-field">
                    <label class="ui-label">Tên đăng nhập <span class="req">*</span></label>
                    <input type="text" name="username" id="username"
                           class="ui-input <?= !empty($errors['username']) ? 'has-error' : '' ?>"
                           value="<?= htmlspecialchars($username ?? '') ?>"
                           placeholder="Nhập tên đăng nhập" required>
                    <?php if (!empty($errors['username'])): ?>
                    <span class="ui-field-error"><?= $errors['username'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Họ và tên</label>
                    <input type="text" name="fullname" id="fullname"
                           class="ui-input"
                           value="<?= htmlspecialchars($fullname ?? '') ?>"
                           placeholder="Tự động theo tên đăng nhập" readonly
                           style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Email <span class="req">*</span></label>
                    <input type="email" name="email" id="email"
                           class="ui-input <?= !empty($errors['email']) ? 'has-error' : '' ?>"
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           placeholder="Nhập email" required>
                    <?php if (!empty($errors['email'])): ?>
                    <span class="ui-field-error"><?= $errors['email'] ?></span>
                    <?php else: ?>
                    <span class="ui-field-error" id="email-error"></span>
                    <?php endif; ?>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Số điện thoại <span class="req">*</span></label>
                    <input type="text" name="phone" id="phone"
                           class="ui-input <?= !empty($errors['phone']) ? 'has-error' : '' ?>"
                           value="<?= htmlspecialchars($phone ?? '') ?>"
                           placeholder="VD: 0912345678" required>
                    <?php if (!empty($errors['phone'])): ?>
                    <span class="ui-field-error"><?= $errors['phone'] ?></span>
                    <?php else: ?>
                    <span class="ui-field-error" id="phone-error"></span>
                    <?php endif; ?>
                </div>

                <div id="password-preview-box" class="ui-alert info mb-3" style="display:none">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Mật khẩu tự động: <strong id="password-preview"></strong>
                </div>

                <!-- ĐỊA CHỈ -->
                <div class="ui-field">
                    <label class="ui-label">Số nhà / Tên đường <span class="req">*</span></label>
                    <input type="text" name="street"
                           class="ui-input <?= !empty($errors['address']) ? 'has-error' : '' ?>"
                           value="<?= htmlspecialchars($street ?? '') ?>"
                           placeholder="VD: 12 Lê Lợi" required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tỉnh / Thành phố <span class="req">*</span></label>
                    <select name="province" id="province-select"
                            class="ui-input" required onchange="loadDistricts(this.value)">
                        <option value="">— Chọn tỉnh / thành phố —</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <div class="ui-field flex-grow-1">
                        <label class="ui-label">Quận / Huyện <span class="req">*</span></label>
                        <div class="ui-select-wrap">
                            <select name="district" id="district-select"
                                    class="ui-input" required disabled onchange="loadWards(this.value)">
                                <option value="">— Chọn quận / huyện —</option>
                            </select>
                            <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ui-field flex-grow-1">
                        <label class="ui-label">Phường / Xã <span class="req">*</span></label>
                        <div class="ui-select-wrap">
                            <select name="ward" id="ward-select"
                                    class="ui-input" required disabled>
                                <option value="">— Chọn phường / xã —</option>
                            </select>
                            <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke-width="2">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="address" id="address-full">
                <?php if (!empty($errors['address'])): ?>
                <span class="ui-field-error"><?= $errors['address'] ?></span>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="ui-btn-outline sm">← Quay lại</a>
                    <button type="button" class="ui-btn sm flex-grow-1"
                            onclick="confirmCreate()">Tạo tài khoản</button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
.ui-select-wrap {
    position: relative;
}
.ui-select-wrap select.ui-input {
    appearance: none;
    -webkit-appearance: none;
    padding-right: 2.2rem;
    cursor: pointer;
}
.ui-select-wrap select.ui-input:disabled {
    background: #f5f5f5;
    color: #888;
    cursor: not-allowed;
}
.select-arrow {
    position: absolute;
    right: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    stroke: #888;
    pointer-events: none;
}
</style>

<script>
const phoneRegex = /^(0[35789])[0-9]{8}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const usernameInput = document.getElementById('username');
const fullnameInput = document.getElementById('fullname');
const phoneInput    = document.getElementById('phone');
const emailInput    = document.getElementById('email');
const previewBox    = document.getElementById('password-preview-box');
const previewSpan   = document.getElementById('password-preview');
const phoneError    = document.getElementById('phone-error');
const emailError    = document.getElementById('email-error');

usernameInput.addEventListener('input', function () {
    fullnameInput.value = this.value;
    updatePasswordPreview();
});

phoneInput.addEventListener('input', function () {
    const clean = this.value.trim().replace(/\s+/g, '');
    if (clean && !phoneRegex.test(clean)) {
        phoneError.textContent = 'Số điện thoại không hợp lệ (10 chữ số, bắt đầu 03/05/07/08/09)';
        this.classList.add('has-error');
    } else {
        phoneError.textContent = '';
        this.classList.remove('has-error');
    }
    updatePasswordPreview();
});

emailInput.addEventListener('input', function () {
    const val = this.value.trim();
    if (val && !emailRegex.test(val)) {
        emailError.textContent = 'Email không hợp lệ (ví dụ: example@gmail.com)';
        this.classList.add('has-error');
    } else {
        emailError.textContent = '';
        this.classList.remove('has-error');
    }
});

function updatePasswordPreview() {
    const u = usernameInput.value.trim().replace(/\s+/g, '');
    const p = phoneInput.value.trim().replace(/\s+/g, '');
    if (u && p) {
        previewSpan.textContent = u + p + '@';
        previewBox.style.display = 'flex';
    } else {
        previewBox.style.display = 'none';
    }
}

// Load tỉnh/thành phố
async function loadProvinces() {
    const res  = await fetch('https://provinces.open-api.vn/api/?depth=1');
    const data = await res.json();
    const sel  = document.getElementById('province-select');
    data.forEach(p => {
        const opt = document.createElement('option');
        opt.value       = p.code;
        opt.textContent = p.name;
        sel.appendChild(opt);
    });
}

async function loadDistricts(provinceCode) {
    const dSel = document.getElementById('district-select');
    const wSel = document.getElementById('ward-select');
    dSel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
    wSel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
    dSel.disabled = true;
    wSel.disabled = true;
    if (!provinceCode) return;

    const res  = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
    const data = await res.json();
    data.districts.forEach(d => {
        const opt = document.createElement('option');
        opt.value       = d.code;
        opt.textContent = d.name;
        dSel.appendChild(opt);
    });
    dSel.disabled = false;
}

async function loadWards(districtCode) {
    const wSel = document.getElementById('ward-select');
    wSel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
    wSel.disabled  = true;
    if (!districtCode) return;

    const res  = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
    const data = await res.json();
    data.wards.forEach(w => {
        const opt = document.createElement('option');
        opt.value       = w.code;
        opt.textContent = w.name;
        wSel.appendChild(opt);
    });
    wSel.disabled = false;
}

loadProvinces();

// confirmCreate duy nhất — gộp validation + ghép address
function confirmCreate() {
    const username = usernameInput.value.trim();
    const phone    = phoneInput.value.trim().replace(/\s+/g, '');
    const email    = emailInput.value.trim();

    if (!username || !phone) {
        alert('Vui lòng nhập đầy đủ tên đăng nhập và số điện thoại.');
        return;
    }
    if (!phoneRegex.test(phone)) {
        phoneError.textContent = 'Số điện thoại không hợp lệ';
        phoneInput.classList.add('has-error');
        phoneInput.focus();
        return;
    }
    if (!emailRegex.test(email)) {
        emailError.textContent = 'Email không hợp lệ';
        emailInput.classList.add('has-error');
        emailInput.focus();
        return;
    }

    // Ghép địa chỉ đầy đủ
    const street   = document.querySelector('[name="street"]').value.trim();
    const province = document.getElementById('province-select');
    const district = document.getElementById('district-select');
    const ward     = document.getElementById('ward-select');
    const pText    = province.options[province.selectedIndex]?.text || '';
    const dText    = district.options[district.selectedIndex]?.text || '';
    const wText    = ward.options[ward.selectedIndex]?.text || '';

    if (!street || !pText || pText.includes('Chọn') || !dText || dText.includes('Chọn') || !wText || wText.includes('Chọn')) {
        alert('Vui lòng chọn đầy đủ địa chỉ.');
        return;
    }

    document.getElementById('address-full').value =
        [street, wText, dText, pText].filter(Boolean).join(', ');

    if (confirm('Bạn có chắc muốn tạo tài khoản này?')) {
        document.getElementById('createForm').submit();
    }
}
</script>