<?php
$appendix = false;
$pageTitle = 'Hồ sơ cá nhân';

// Tách địa chỉ đã lưu thành các phần để pre-fill dropdown
// Format: "số nhà/đường, phường, quận, tỉnh"
$addrRaw   = $user['address'] ?? '';
$addrParts = array_map('trim', explode(',', $addrRaw));
// Đảm bảo luôn có đủ 4 phần (tránh undefined index)
while (count($addrParts) < 4) $addrParts[] = '';
?>
<div class="profile-page">
    <div style="max-width:900px;margin:0 auto;padding:0 20px">
        <h2 class="profile-title" style="text-align:center">Hồ sơ cá nhân</h2>
    </div>

    <?php
    $hasAvatar  = !empty($user['avatar']);
    $avatarSrc  = $hasAvatar
        ? BASE_URL . '/uploads/avatars/' . htmlspecialchars($user['avatar'])
        : BASE_URL . '/images/avatar.svg';
    $defaultClass = $hasAvatar ? '' : 'is-default';
    ?>

    <div class="profile-grid">

        <!-- ── LEFT PANEL ── -->
        <div class="profile-left">

            <!-- Avatar card -->
            <div class="ui-avatar-card">
                <div class="ui-avatar-wrap <?= $defaultClass ?>" id="avatarWrap">

                    <img src="<?= $avatarSrc ?>"
                         class="avatar-img" id="avatarPreview" alt="Avatar">

                    <!-- Nút xóa ảnh -->
                    <button type="button"
                            class="btn-del-avatar"
                            id="btnDelAvatar"
                            title="Xóa ảnh">✕</button>

                    <!-- Click overlay để chọn ảnh mới -->
                    <label for="avatarFileInput" class="avatar-overlay">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="#fff" stroke-width="2" stroke-linecap="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8
                                     a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Đổi ảnh</span>
                    </label>

                </div>

                <div class="avatar-name"><?= htmlspecialchars($user['fullname'] ?? 'Người dùng') ?></div>
                <div class="avatar-role">Khách hàng</div>
            </div>

            <!-- Info summary -->
            <div class="info-card">
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Email</div>
                        <div class="val"><?= htmlspecialchars($user['email'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                                     19.79 19.79 0 0 1-8.63-3.07
                                     19.5 19.5 0 0 1-6-6
                                     19.79 19.79 0 0 1-3.07-8.67
                                     A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72
                                     c.127.96.361 1.903.7 2.81
                                     a2 2 0 0 1-.45 2.11L8.09 9.91
                                     a16 16 0 0 0 6 6l1.27-1.27
                                     a2 2 0 0 1 2.11-.45
                                     c.907.339 1.85.573 2.81.7
                                     A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Điện thoại</div>
                        <div class="val"><?= htmlspecialchars($user['phone'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13
                                     a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Địa chỉ</div>
                        <div class="val" id="sidebarAddress">
                            <?= htmlspecialchars($addrRaw ?: '—') ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── RIGHT PANEL ── -->
        <div class="profile-right">

            <!-- Alerts -->
            <?php if (!empty($errors)): ?>
            <div class="ui-alert alert-danger-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . ' '; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
            <div class="ui-alert alert-success-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?= $_GET['success'] == 2 ? 'Đổi mật khẩu thành công!' : 'Cập nhật thông tin thành công!' ?>
            </div>
            <?php endif; ?>

            <!-- Form cập nhật thông tin -->
            <div class="ui-card">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <h5>Thông tin cá nhân</h5>
                </div>
                <div class="ui-card-body">
                    <form method="POST"
                          enctype="multipart/form-data"
                          action="<?= BASE_URL ?>/index.php?url=profile"
                          id="profileForm">

                        <!-- Avatar file input ẩn -->
                        <input type="file" name="avatar" id="avatarFileInput"
                               accept="image/jpeg,image/png,image/gif,image/webp">

                        <!-- Hidden field xóa avatar -->
                        <input type="hidden" name="delete_avatar" id="deleteAvatarField" value="0">

                        <div class="field-group">
                            <label class="ui-label">Họ và tên</label>
                            <input type="text" name="fullname" class="ui-input"
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                   placeholder="Nhập họ và tên">
                        </div>

                        <div class="field-group">
                            <label class="ui-label">Email</label>
                            <input type="email" name="email" class="ui-input"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   placeholder="Nhập email">
                        </div>

                        <div class="field-group">
                            <label class="ui-label">Số điện thoại</label>
                            <input type="text" name="phone" class="ui-input"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   placeholder="Nhập số điện thoại">
                        </div>

                        <!-- ── ĐỊA CHỈ — 4 phần tách biệt ── -->
                        <div class="field-group">
                            <label class="ui-label">Số nhà / Tên đường</label>
                            <input type="text" id="addr-street" class="ui-input"
                                   placeholder="VD: 12 Lê Lợi"
                                   oninput="joinAddress()"
                                   value="<?= htmlspecialchars($addrParts[0]) ?>">
                        </div>

                        <div class="field-group">
                            <label class="ui-label">Tỉnh / Thành phố</label>
                            <div class="addr-select-wrap">
                                <select id="addr-province" class="ui-input addr-select"
                                        onchange="loadDistricts()">
                                    <option value="">— Đang tải... —</option>
                                </select>
                                <span class="addr-spinner" id="spin-province"></span>
                            </div>
                        </div>

                        <div class="addr-two-col">
                            <div class="field-group">
                                <label class="ui-label">Quận / Huyện</label>
                                <div class="addr-select-wrap">
                                    <select id="addr-district" class="ui-input addr-select"
                                            onchange="loadWards()" disabled>
                                        <option value="">— Chọn quận / huyện —</option>
                                    </select>
                                    <span class="addr-spinner" id="spin-district"></span>
                                </div>
                            </div>
                            <div class="field-group">
                                <label class="ui-label">Phường / Xã</label>
                                <div class="addr-select-wrap">
                                    <select id="addr-ward" class="ui-input addr-select"
                                            onchange="joinAddress()" disabled>
                                        <option value="">— Chọn phường / xã —</option>
                                    </select>
                                    <span class="addr-spinner" id="spin-ward"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field gửi server — giữ nguyên name="address" -->
                        <input type="hidden" name="address" id="addr-full"
                               value="<?= htmlspecialchars($addrRaw) ?>">
                        <!-- ── /ĐỊA CHỈ ── -->

                        <button type="submit" name="update_profile" class="ui-btn">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form đổi mật khẩu -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <h5>Đổi mật khẩu</h5>
                </div>
                <div class="ui-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?url=profile">

                        <div class="field-group">
                            <label class="ui-label">Mật khẩu hiện tại</label>
                            <div class="input-group-custom">
                                <input type="password" name="current_password" id="currentPassword"
                                       placeholder="Nhập mật khẩu hiện tại">
                                <button type="button" class="toggle-pwd" data-target="currentPassword">
                                    <img src="<?= BASE_URL ?>/images/hide.svg" id="ico-currentPassword">
                                </button>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="ui-label">Mật khẩu mới</label>
                            <div class="input-group-custom">
                                <input type="password" name="new_password" id="newPassword"
                                       placeholder="Nhập mật khẩu mới">
                                <button type="button" class="toggle-pwd" data-target="newPassword">
                                    <img src="<?= BASE_URL ?>/images/hide.svg" id="ico-newPassword">
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="change_password" class="ui-btn">
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>

        </div><!-- /profile-right -->
    </div><!-- /profile-grid -->
</div><!-- /profile-page -->

<!-- ── Modal xác nhận xóa ảnh ── -->
<div class="modal-overlay" id="delModal">
    <div class="modal-box">
        <h6>Xóa ảnh đại diện?</h6>
        <p>Ảnh sẽ được đặt lại về mặc định. Hành động này không thể hoàn tác.</p>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-cancel" id="modalCancel">Hủy</button>
            <button class="modal-btn modal-btn-confirm" id="modalConfirm">Xóa ảnh</button>
        </div>
    </div>
</div>

<style>
/* ── Địa chỉ dropdown ── */
.addr-two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media (max-width: 480px) {
    .addr-two-col { grid-template-columns: 1fr; }
}
.addr-select-wrap {
    position: relative;
}
.addr-select {
    appearance: none;
    -webkit-appearance: none;
    padding-right: 32px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7c8a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
}
.addr-select:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}
.addr-spinner {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    border: 2px solid rgba(128,128,128,.2);
    border-top-color: #6b7c8a;
    border-radius: 50%;
    animation: addrSpin .6s linear infinite;
    display: none;
    pointer-events: none;
}
@keyframes addrSpin { to { transform: translateY(-50%) rotate(360deg); } }
</style>

<script>
/* ═══════════════════════════════════════════════
   Địa chỉ đã lưu — truyền từ PHP sang JS
   ═══════════════════════════════════════════════ */
const SAVED = {
    street:   <?= json_encode($addrParts[0]) ?>,
    ward:     <?= json_encode($addrParts[1]) ?>,
    district: <?= json_encode($addrParts[2]) ?>,
    province: <?= json_encode($addrParts[3]) ?>,
};

const PROV_API = 'https://provinces.open-api.vn/api';

/* ── Helpers ── */
function addrSpinner(id, show) {
    const el = document.getElementById(id);
    if (el) el.style.display = show ? 'inline-block' : 'none';
}

function resetSelect(id, label) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${label}</option>`;
    el.disabled  = true;
}

function getSelText(id) {
    const el  = document.getElementById(id);
    const txt = el.options[el.selectedIndex]?.text || '';
    return txt.startsWith('—') ? '' : txt;
}

/* Ghép 4 phần → hidden field + cập nhật sidebar */
function joinAddress() {
    const parts = [
        document.getElementById('addr-street').value.trim(),
        getSelText('addr-ward'),
        getSelText('addr-district'),
        getSelText('addr-province'),
    ].filter(Boolean);
    const full = parts.join(', ');
    document.getElementById('addr-full').value = full;

    // Đồng bộ text hiển thị ở left panel
    const sidebar = document.getElementById('sidebarAddress');
    if (sidebar) sidebar.textContent = full || '—';
}

/* So khớp tên tỉnh/quận/phường (bỏ qua khoảng trắng thừa, không phân biệt hoa/thường) */
function namMatch(a, b) {
    return a.trim().toLowerCase() === b.trim().toLowerCase();
}

/* ── Load tỉnh/thành, tự restore giá trị đã lưu ── */
async function loadProvinces() {
    addrSpinner('spin-province', true);
    try {
        const data = await fetch(`${PROV_API}/p/`).then(r => r.json());
        const sel  = document.getElementById('addr-province');
        sel.innerHTML = '<option value="">— Chọn tỉnh / thành phố —</option>';
        let savedCode = null;

        data.forEach(p => {
            const opt = document.createElement('option');
            opt.value       = p.code;
            opt.textContent = p.name;
            if (SAVED.province && namMatch(p.name, SAVED.province)) {
                opt.selected = true;
                savedCode    = p.code;
            }
            sel.appendChild(opt);
        });
        sel.disabled = false;

        // Cascade: nếu có tỉnh đã lưu → load quận
        if (savedCode) await loadDistricts(true);

    } catch (e) {
        document.getElementById('addr-province').innerHTML =
            '<option value="">Lỗi tải dữ liệu — thử lại</option>';
    } finally {
        addrSpinner('spin-province', false);
    }
}

/* ── Load quận/huyện ── */
async function loadDistricts(isRestore = false) {
    const code = document.getElementById('addr-province').value;
    resetSelect('addr-district', '— Chọn quận / huyện —');
    resetSelect('addr-ward',     '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    addrSpinner('spin-district', true);
    try {
        const data = await fetch(`${PROV_API}/p/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-district');
        sel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
        let savedCode = null;

        (data.districts || []).forEach(d => {
            const opt = document.createElement('option');
            opt.value       = d.code;
            opt.textContent = d.name;
            if (isRestore && SAVED.district && namMatch(d.name, SAVED.district)) {
                opt.selected = true;
                savedCode    = d.code;
            }
            sel.appendChild(opt);
        });
        sel.disabled = false;

        if (savedCode) await loadWards(true);

    } catch (e) { /* giữ disabled */ }
    finally { addrSpinner('spin-district', false); }
}

/* ── Load phường/xã ── */
async function loadWards(isRestore = false) {
    const code = document.getElementById('addr-district').value;
    resetSelect('addr-ward', '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    addrSpinner('spin-ward', true);
    try {
        const data = await fetch(`${PROV_API}/d/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-ward');
        sel.innerHTML = '<option value="">— Chọn phường / xã —</option>';

        (data.wards || []).forEach(w => {
            const opt = document.createElement('option');
            opt.value       = w.code;
            opt.textContent = w.name;
            if (isRestore && SAVED.ward && namMatch(w.name, SAVED.ward)) {
                opt.selected = true;
            }
            sel.appendChild(opt);
        });
        sel.disabled = false;

        // Sau khi restore xong phường → cập nhật hidden + sidebar
        if (isRestore) joinAddress();

    } catch (e) { /* giữ disabled */ }
    finally { addrSpinner('spin-ward', false); }
}

/* ═══════════════════════════════════════════════
   Toggle password visibility
   ═══════════════════════════════════════════════ */
document.querySelectorAll('.toggle-pwd').forEach(btn => {
    btn.addEventListener('click', () => {
        const id    = btn.dataset.target;
        const input = document.getElementById(id);
        const ico   = document.getElementById('ico-' + id);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        ico.src    = isHidden
            ? '<?= BASE_URL ?>/images/show.svg'
            : '<?= BASE_URL ?>/images/hide.svg';
    });
});

/* ═══════════════════════════════════════════════
   Preview avatar khi chọn file mới
   ═══════════════════════════════════════════════ */
document.getElementById('avatarFileInput').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').src = e.target.result;
        document.getElementById('avatarWrap').classList.remove('is-default');
        document.getElementById('deleteAvatarField').value = '0';
    };
    reader.readAsDataURL(this.files[0]);
});

/* ═══════════════════════════════════════════════
   Modal xóa avatar
   ═══════════════════════════════════════════════ */
document.getElementById('btnDelAvatar').addEventListener('click', () => {
    document.getElementById('delModal').classList.add('active');
});
document.getElementById('modalCancel').addEventListener('click', () => {
    document.getElementById('delModal').classList.remove('active');
});
document.getElementById('modalConfirm').addEventListener('click', () => {
    document.getElementById('deleteAvatarField').value = '1';
    document.getElementById('avatarFileInput').value   = '';
    document.getElementById('avatarPreview').src = '<?= BASE_URL ?>/images/avatar.svg';
    document.getElementById('avatarWrap').classList.add('is-default');
    document.getElementById('delModal').classList.remove('active');
    document.getElementById('profileForm').submit();
});
document.getElementById('delModal').addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});

/* ═══════════════════════════════════════════════
   Khởi động
   ═══════════════════════════════════════════════ */
loadProvinces();
</script>