<?php /* app/views/admin/customers/edit/index.php */ ?>

<div class="container py-5" style="max-width:580px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-customers">Khách hàng</a>
        <span class="sep">›</span>
        <span>Chỉnh sửa #C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?></span>
    </div>

    <h2 class="ui-title">Chỉnh sửa khách hàng</h2>

    <?php if (!empty($success)): ?>
    <div class="ui-alert success mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="ui-alert danger mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Avatar card -->
    <div class="ui-avatar-card mb-4">
        <div class="ui-avatar-wrap is-default">
            <img src="<?= htmlspecialchars($avatarPath ?? BASE_URL . '/images/user.png') ?>"
                 class="ui-avatar-img" alt="avatar">
        </div>
        <div class="ui-avatar-name"><?= htmlspecialchars($user['fullname']) ?></div>
        <div class="ui-avatar-role">
            #C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>
        </div>

        <!-- Status toggle -->
        <div class="d-flex justify-content-center gap-2 mt-3">
            <?php
            $statuses = [
                'active'   => ['label' => 'Hoạt động', 'cls' => 'ui-btn sm', 'style' => ''],
                'warning'  => ['label' => 'Cảnh báo',  'cls' => 'ui-btn sm', 'style' => 'background:linear-gradient(135deg,#f7c948,#d97706)'],
                'inactive' => ['label' => 'Bị khóa',   'cls' => 'ui-btn sm', 'style' => 'background:linear-gradient(135deg,#f76f8e,#db2777)'],
            ];
            foreach ($statuses as $val => $cfg):
                $isActive = ($user['status'] === $val);
            ?>
            <a href="<?= BASE_URL ?>/index.php?url=admin-customers-edit&id=<?= $user['id'] ?>&set_status=<?= $val ?>"
               class="<?= $cfg['cls'] ?>"
               style="<?= $cfg['style'] ?>;<?= $isActive ? '' : 'opacity:.4;filter:grayscale(.5)' ?>"
               onclick="return confirm('Đổi trạng thái sang <?= $cfg['label'] ?>?')">
                <?= $cfg['label'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Form card -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <h5>Thông tin chi tiết</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-customers-edit&id=<?= $user['id'] ?>">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <div class="ui-field">
                    <label class="ui-label">Mã khách hàng</label>
                    <input type="text" class="ui-input"
                           value="#C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Họ và tên</label>
                    <input type="text" name="fullname" class="ui-input"
                           value="<?= htmlspecialchars($user['fullname']) ?>"
                           placeholder="Nhập họ tên">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tên đăng nhập</label>
                    <input type="text" name="username" class="ui-input"
                           value="<?= htmlspecialchars($user['username']) ?>"
                           placeholder="Nhập username">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Mật khẩu mới</label>
                    <div class="ui-pwd-wrap">
                        <input type="password" name="password" id="pwdInput"
                               class="ui-input" placeholder="Để trống nếu không đổi">
                        <button type="button" class="ui-pwd-toggle" onclick="togglePwd()">
                            <img id="eyeIcon"
                                 src="<?= BASE_URL ?>/images/show.svg"
                                 alt="toggle"
                                 style='background-color:green;width:25px;height:25px'>
                        </button>
                    </div>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Email</label>
                    <input type="email" name="email" class="ui-input"
                           value="<?= htmlspecialchars($user['email']) ?>"
                           placeholder="Nhập email">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Số điện thoại</label>
                    <input type="text" name="phone" class="ui-input"
                           value="<?= htmlspecialchars($user['phone']) ?>"
                           placeholder="Nhập số điện thoại">
                </div>

                <!-- ── Địa chỉ: 4 phần tách riêng, join qua JS ── -->
                <div class="ui-field">
                    <label class="ui-label">Số nhà / Tên đường</label>
                    <input type="text" id="addr-street" class="ui-input"
                           placeholder="VD: 12 Lê Lợi"
                           oninput="joinAddress()">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tỉnh / Thành phố</label>
                    <div style="position:relative">
                        <select id="addr-province" class="ui-input"
                                onchange="loadDistricts()"
                                style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                            <option value="">— Đang tải... —</option>
                        </select>
                        <span id="spin-province" class="addr-spinner"></span>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <div class="ui-field">
                        <label class="ui-label">Quận / Huyện</label>
                        <div style="position:relative">
                            <select id="addr-district" class="ui-input"
                                    onchange="loadWards()" disabled
                                    style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                                <option value="">— Chọn quận / huyện —</option>
                            </select>
                            <span id="spin-district" class="addr-spinner"></span>
                        </div>
                    </div>
                    <div class="ui-field">
                        <label class="ui-label">Phường / Xã</label>
                        <div style="position:relative">
                            <select id="addr-ward" class="ui-input"
                                    onchange="joinAddress()" disabled
                                    style="appearance:none;-webkit-appearance:none;padding-right:32px;cursor:pointer">
                                <option value="">— Chọn phường / xã —</option>
                            </select>
                            <span id="spin-ward" class="addr-spinner"></span>
                        </div>
                    </div>
                </div>

                <!-- Hidden field thực sự submit lên server -->
                <input type="hidden" name="address" id="addr-full"
                       value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                <!-- ── end địa chỉ ── -->

                <div class="ui-field">
                    <label class="ui-label">Ngày tham gia</label>
                    <input type="date" class="ui-input"
                           value="<?= date('Y-m-d', strtotime($user['created_at'])) ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="ui-btn-outline sm">
                        ← Quay lại
                    </a>
                    <button type="submit" class="ui-btn sm flex-grow-1">
                        Lưu thay đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
.addr-spinner {
    display: none;
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    width: 14px; height: 14px;
    border: 2px solid #ddd; border-top-color: #3b82f6;
    border-radius: 50%; animation: addrSpin .6s linear infinite;
}
@keyframes addrSpin { to { transform: translateY(-50%) rotate(360deg); } }
</style>

<script>
const ADDR_API = 'https://provinces.open-api.vn/api';

/* ── Helpers ── */
function addrSpinner(id, show) {
    document.getElementById(id).style.display = show ? 'block' : 'none';
}
function resetSelect(id, label) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${label}</option>`;
    el.disabled = true;
}
function getText(id) {
    const el  = document.getElementById(id);
    const txt = el.options[el.selectedIndex]?.text || '';
    return txt.startsWith('—') ? '' : txt;
}

/* ── Join → hidden field ── */
function joinAddress() {
    const parts = [
        document.getElementById('addr-street').value.trim(),
        getText('addr-ward'),
        getText('addr-district'),
        getText('addr-province'),
    ].filter(Boolean);
    document.getElementById('addr-full').value = parts.join(', ');
}

/* ── Load tỉnh ── */
async function loadProvinces() {
    addrSpinner('spin-province', true);
    try {
        const data = await fetch(`${ADDR_API}/p/`).then(r => r.json());
        const sel  = document.getElementById('addr-province');
        sel.innerHTML = '<option value="">— Chọn tỉnh / thành phố —</option>';
        data.forEach(p => sel.insertAdjacentHTML('beforeend',
            `<option value="${p.code}">${p.name}</option>`));
        sel.disabled = false;

        // Nếu đã có địa chỉ cũ → parse và pre-fill
        prefillAddress();
    } catch(e) { console.error(e); }
    finally { addrSpinner('spin-province', false); }
}

/* ── Chọn tỉnh → load quận ── */
async function loadDistricts() {
    const code = document.getElementById('addr-province').value;
    resetSelect('addr-district', '— Chọn quận / huyện —');
    resetSelect('addr-ward',     '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    addrSpinner('spin-district', true);
    try {
        const data = await fetch(`${ADDR_API}/p/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-district');
        sel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
        (data.districts || []).forEach(d => sel.insertAdjacentHTML('beforeend',
            `<option value="${d.code}">${d.name}</option>`));
        sel.disabled = false;
    } catch(e) { console.error(e); }
    finally { addrSpinner('spin-district', false); }
}

/* ── Chọn quận → load phường ── */
async function loadWards() {
    const code = document.getElementById('addr-district').value;
    resetSelect('addr-ward', '— Chọn phường / xã —');
    joinAddress();
    if (!code) return;

    addrSpinner('spin-ward', true);
    try {
        const data = await fetch(`${ADDR_API}/d/${code}?depth=2`).then(r => r.json());
        const sel  = document.getElementById('addr-ward');
        sel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
        (data.wards || []).forEach(w => sel.insertAdjacentHTML('beforeend',
            `<option value="${w.code}">${w.name}</option>`));
        sel.disabled = false;
    } catch(e) { console.error(e); }
    finally { addrSpinner('spin-ward', false); }
}

/* ── Pre-fill từ địa chỉ cũ (format: "đường, phường, quận, tỉnh") ── */
async function prefillAddress() {
    const existing = document.getElementById('addr-full').value.trim();
    if (!existing) return;

    const parts = existing.split(',').map(s => s.trim());
    // parts[0]=đường, parts[1]=phường, parts[2]=quận, parts[3]=tỉnh
    if (parts[0]) document.getElementById('addr-street').value = parts[0];

    if (!parts[3] && !parts[2]) return; // không đủ thông tin để match

    // Match tỉnh
    const provSel = document.getElementById('addr-province');
    const provOpt = Array.from(provSel.options).find(o => parts[3] && o.text === parts[3]);
    if (provOpt) {
        provSel.value = provOpt.value;
        // Load quận rồi match
        await loadDistricts();
        const distSel = document.getElementById('addr-district');
        const distOpt = Array.from(distSel.options).find(o => parts[2] && o.text === parts[2]);
        if (distOpt) {
            distSel.value = distOpt.value;
            // Load phường rồi match
            await loadWards();
            const wardSel = document.getElementById('addr-ward');
            const wardOpt = Array.from(wardSel.options).find(o => parts[1] && o.text === parts[1]);
            if (wardOpt) wardSel.value = wardOpt.value;
        }
    }
    joinAddress();
}

/* ── Toggle password ── */
function togglePwd() {
    const input = document.getElementById('pwdInput');
    input.type  = input.type === 'password' ? 'text' : 'password';
}

loadProvinces();
</script>