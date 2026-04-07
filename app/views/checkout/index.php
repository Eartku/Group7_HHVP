<div class="container py-5">

    <?php if ($error): ?>
        <div class="ui-alert danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?url=checkout">
        <div class="checkout-layout">

            <!-- THÔNG TIN KHÁCH HÀNG -->
            <div>
                <div class="ui-card">
                    <div class="ui-card-head">
                        <h5>Thông tin khách hàng</h5>
                    </div>
                    <div class="ui-card-body">

                        <div class="ui-field">
                            <label class="ui-label">Họ và Tên <span class="req">*</span></label>
                            <input type="text" name="fullname" class="ui-input"
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label">Email <span class="req">*</span></label>
                            <input type="email" name="email" class="ui-input"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label">Số điện thoại <span class="req">*</span></label>
                            <input type="text" name="phone" class="ui-input"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>

                        <hr>

                        <h5 class="ui-title sm">Địa chỉ giao hàng</h5>

                        <!-- Choice 1: Địa chỉ đã lưu -->
                        <div class="ui-field">
                            <label class="ui-label">
                                <input type="radio" name="address_option" value="saved" checked>
                                Dùng địa chỉ đã lưu
                            </label>
                            <select name="saved_address" id="saved_address" class="ui-input">
                                <option value="">-- Chọn địa chỉ đã lưu --</option>
                                <?php if (!empty($user['address'])): ?>
                                <option value="<?= htmlspecialchars($user['address']) ?>">
                                    <?= htmlspecialchars($user['address']) ?>
                                </option>
                                <?php endif; ?>
                                <?php if (!empty($user['street']) && !empty($user['ward']) && !empty($user['district']) && !empty($user['province'])): ?>
                                <option value="<?= htmlspecialchars($user['street'] . ', ' . $user['ward'] . ', ' . $user['district'] . ', ' . $user['province']) ?>">
                                    <?= htmlspecialchars($user['street'] . ', ' . $user['ward'] . ', ' . $user['district'] . ', ' . $user['province']) ?>
                                </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Choice 2: Nhập địa chỉ mới -->
                        <div class="ui-field">
                            <label class="ui-label">
                                <input type="radio" name="address_option" value="new">
                                Nhập địa chỉ mới
                            </label>
                        </div>

                        <!-- Form địa chỉ mới (4 input dropdown) -->
                        <div id="new_address_form" style="margin-top: 15px; display: none;">
                            <!-- Số nhà / đường -->
                            <div class="ui-field">
                                <label class="ui-label">Số nhà / Tên đường <span class="req">*</span></label>
                                <input type="text" id="addr-street" name="street" class="ui-input" 
                                       placeholder="VD: 12 Lê Lợi"
                                       value="<?= htmlspecialchars($street ?? '') ?>">
                            </div>

                            <!-- Tỉnh/thành -->
                            <div class="ui-field">
                                <label class="ui-label">Tỉnh / Thành phố <span class="req">*</span></label>
                                <div class="select-wrap">
                                    <select id="addr-province" name="province" class="ui-input" style="width:100%">
                                        <option value="">— Đang tải... —</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Quận / Huyện -->
                            <div class="ui-field">
                                <label class="ui-label">Quận / Huyện <span class="req">*</span></label>
                                <div class="select-wrap">
                                    <select id="addr-district" name="district" class="ui-input" style="width:100%" disabled>
                                        <option value="">— Chọn quận / huyện —</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Phường / Xã -->
                            <div class="ui-field">
                                <label class="ui-label">Phường / Xã <span class="req">*</span></label>
                                <div class="select-wrap">
                                    <select id="addr-ward" name="ward" class="ui-input" style="width:100%" disabled>
                                        <option value="">— Chọn phường / xã —</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="ui-field">
                            <label class="ui-label">Ghi chú</label>
                            <textarea name="note" class="ui-input textarea"></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ĐƠN HÀNG + THANH TOÁN -->
            <div>
                <div class="ui-card">
                    <div class="ui-card-head">
                        <h5>Đơn hàng của bạn</h5>
                    </div>
                    <div class="ui-card-body">

                        <table class="ui-table">
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                        <span class="ui-table td muted">
                                            (<?= htmlspecialchars($item['size']) ?>)
                                        </span>
                                        × <?= $item['quantity'] ?>
                                    </td>
                                    <td class="right price">
                                        <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="ui-sum-row">
                            <span>Phí vận chuyển</span>
                            <span><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="ui-sum-row">
                            <span>Tổng cộng</span>
                            <span><?= number_format($grandTotal, 0, ',', '.') ?>đ</span>
                        </div>

                        <hr>

                        <h5 class="ui-title sm">Phương thức thanh toán</h5>

                        <div class="ui-sum-row">
                            <label>
                                <input type="radio" name="payment" value="cod" checked>
                                Thanh toán khi nhận hàng
                            </label>
                        </div>

                        <div class="ui-sum-row">
                            <label>
                                <input type="radio" name="payment" value="bank">
                                Chuyển khoản
                            </label>
                        </div>

                        <!-- Thông tin chuyển khoản -->
                        <div id="bank-info" style="display:none; background:#f8f9fa; border:1px solid #e0e0e0;
                            border-radius:8px; padding:14px 16px; margin:10px 0 14px; font-size:14px; line-height:2;">
                            <div>🏦 <strong>Ngân hàng:</strong> Vietcombank</div>
                            <div>💳 <strong>STK nhận:</strong> 123456789</div>
                            <div>👤 <strong>Tên Người nhận:</strong> Bonsai</div>
                            <div>💰 <strong>Số tiền cần chuyển:</strong>
                                <span style="color:#e53935; font-weight:600;">
                                    <?= number_format($grandTotal, 0, ',', '.') ?>đ
                                </span>
                            </div>
                            <div>📝 <strong>Nội dung:</strong>
                                <span id="bank-content" style="color:#e53935; font-weight:600;">
                                    <?php
                                    $parts = [];
                                    foreach ($items as $item) {
                                        $parts[] = $item['quantity'] . ' ' . $item['name'] . ' ' . $item['size'];
                                    }
                                    echo htmlspecialchars(implode(', ', $parts));
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="ui-sum-row">
                            <label>
                                <input type="radio" name="payment" value="momo">
                                Trực Tuyến
                            </label>
                        </div>

                        <button type="submit" class="ui-btn full" onclick="return validateForm()">
                            ĐẶT HÀNG
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.select-wrap {
    position: relative;
    width: 100%;
}
.ui-input, .ui-input select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
}
.ui-input:disabled, .ui-input select:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}
.ui-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.ui-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid #eee;
}
.ui-card-body {
    padding: 20px;
}
.ui-field {
    margin-bottom: 16px;
}
.ui-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 14px;
}
.req {
    color: #e53935;
}
.ui-sum-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
}
.ui-btn {
    background: #2e7d32;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
}
.ui-btn.full {
    width: 100%;
}
.ui-btn:hover {
    background: #1b5e20;
}
.ui-table {
    width: 100%;
    margin-bottom: 16px;
}
.ui-table td {
    padding: 8px 0;
}
.right {
    text-align: right;
}
.muted {
    color: #666;
    font-size: 12px;
}
hr {
    margin: 16px 0;
    border: none;
    border-top: 1px solid #eee;
}
.checkout-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}
@media (max-width: 768px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
const API = 'https://provinces.open-api.vn/api';

// Lấy text của option đang chọn
function getText(selectElement) {
    if (!selectElement || !selectElement.options || selectElement.selectedIndex === -1) return '';
    const txt = selectElement.options[selectElement.selectedIndex]?.text || '';
    return txt.startsWith('—') ? '' : txt;
}

// Load tỉnh/thành
async function loadProvinces() {
    const sel = document.getElementById('addr-province');
    if (!sel) return;
    
    try {
        const response = await fetch(`${API}/p/`);
        const data = await response.json();
        sel.innerHTML = '<option value="">— Chọn tỉnh / thành phố —</option>';
        data.forEach(p => {
            sel.insertAdjacentHTML('beforeend', `<option value="${p.code}">${p.name}</option>`);
        });
        sel.disabled = false;
    } catch (error) {
        console.error('Lỗi tải tỉnh/thành:', error);
        sel.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
    }
}

// Chọn tỉnh → load quận
async function loadDistricts() {
    const code = document.getElementById('addr-province')?.value;
    const districtSel = document.getElementById('addr-district');
    const wardSel = document.getElementById('addr-ward');
    
    if (!districtSel || !wardSel) return;
    
    districtSel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
    districtSel.disabled = true;
    wardSel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
    wardSel.disabled = true;
    
    if (!code) return;
    
    try {
        const response = await fetch(`${API}/p/${code}?depth=2`);
        const data = await response.json();
        districtSel.innerHTML = '<option value="">— Chọn quận / huyện —</option>';
        (data.districts || []).forEach(d => {
            districtSel.insertAdjacentHTML('beforeend', `<option value="${d.code}">${d.name}</option>`);
        });
        districtSel.disabled = false;
    } catch (error) {
        console.error('Lỗi tải quận/huyện:', error);
    }
}

// Chọn quận → load phường
async function loadWards() {
    const code = document.getElementById('addr-district')?.value;
    const wardSel = document.getElementById('addr-ward');
    
    if (!wardSel) return;
    
    wardSel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
    wardSel.disabled = true;
    
    if (!code) return;
    
    try {
        const response = await fetch(`${API}/d/${code}?depth=2`);
        const data = await response.json();
        wardSel.innerHTML = '<option value="">— Chọn phường / xã —</option>';
        (data.wards || []).forEach(w => {
            wardSel.insertAdjacentHTML('beforeend', `<option value="${w.code}">${w.name}</option>`);
        });
        wardSel.disabled = false;
    } catch (error) {
        console.error('Lỗi tải phường/xã:', error);
    }
}

// Xử lý khi chọn địa chỉ đã lưu hoặc nhập mới
function handleAddressOption() {
    const option = document.querySelector('input[name="address_option"]:checked').value;
    const savedAddressSelect = document.getElementById('saved_address');
    const newAddressForm = document.getElementById('new_address_form');
    const streetInput = document.getElementById('addr-street');
    const provinceSelect = document.getElementById('addr-province');
    const districtSelect = document.getElementById('addr-district');
    const wardSelect = document.getElementById('addr-ward');
    
    if (option === 'saved') {
        // Enable saved address, disable new address form
        savedAddressSelect.disabled = false;
        newAddressForm.style.display = 'none';
        
        // Disable và remove required của các input trong form mới
        streetInput.disabled = true;
        provinceSelect.disabled = true;
        districtSelect.disabled = true;
        wardSelect.disabled = true;
        
        streetInput.removeAttribute('required');
        provinceSelect.removeAttribute('required');
        districtSelect.removeAttribute('required');
        wardSelect.removeAttribute('required');
        
        // Add required cho saved address
        savedAddressSelect.setAttribute('required', 'required');
    } else {
        // Enable new address form, disable saved address
        savedAddressSelect.disabled = true;
        newAddressForm.style.display = 'block';
        
        // Enable và add required cho các input trong form mới
        streetInput.disabled = false;
        provinceSelect.disabled = false;
        districtSelect.disabled = false;
        wardSelect.disabled = false;
        
        streetInput.setAttribute('required', 'required');
        provinceSelect.setAttribute('required', 'required');
        districtSelect.setAttribute('required', 'required');
        wardSelect.setAttribute('required', 'required');
        
        // Remove required của saved address
        savedAddressSelect.removeAttribute('required');
        
        // Load provinces nếu chưa có
        if (provinceSelect.options.length <= 1) {
            loadProvinces();
        }
    }
}

// Validate form trước khi submit
function validateForm() {
    const option = document.querySelector('input[name="address_option"]:checked').value;
    
    if (option === 'saved') {
        const savedAddress = document.getElementById('saved_address').value;
        if (!savedAddress) {
            alert('Vui lòng chọn địa chỉ đã lưu');
            return false;
        }
    } else {
        const street = document.getElementById('addr-street').value.trim();
        const province = document.getElementById('addr-province').value;
        const district = document.getElementById('addr-district').value;
        const ward = document.getElementById('addr-ward').value;
        
        if (!street) {
            alert('Vui lòng nhập số nhà / tên đường');
            return false;
        }
        if (!province) {
            alert('Vui lòng chọn tỉnh / thành phố');
            return false;
        }
        if (!district) {
            alert('Vui lòng chọn quận / huyện');
            return false;
        }
        if (!ward) {
            alert('Vui lòng chọn phường / xã');
            return false;
        }
    }
    
    return true;
}

// Gán sự kiện khi trang load
document.addEventListener('DOMContentLoaded', function() {
    // Gán sự kiện cho radio address_option
    const addressRadios = document.querySelectorAll('input[name="address_option"]');
    addressRadios.forEach(function(radio) {
        radio.addEventListener('change', handleAddressOption);
    });
    
    // Gán sự kiện cho các select địa chỉ
    const provinceSel = document.getElementById('addr-province');
    const districtSel = document.getElementById('addr-district');
    const wardSel = document.getElementById('addr-ward');
    
    if (provinceSel) {
        provinceSel.addEventListener('change', loadDistricts);
    }
    if (districtSel) {
        districtSel.addEventListener('change', loadWards);
    }
    
    // Xử lý hiển thị thông tin chuyển khoản
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    paymentRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const bankInfo = document.getElementById('bank-info');
            if (bankInfo) {
                bankInfo.style.display = this.value === 'bank' ? 'block' : 'none';
            }
        });
    });
    
    // Khởi tạo trạng thái ban đầu (mặc định là địa chỉ đã lưu)
    handleAddressOption();
    
    // Load provinces nếu cần
    if (document.getElementById('addr-province')) {
        loadProvinces();
    }
});
</script>