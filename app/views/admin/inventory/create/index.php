<div class="container mt-4 pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0" style="color:#3b5d50;font-weight:700">Tạo phiếu nhập kho</h3>
            <small class="text-muted">Nhập thông tin hàng hoá cần nhập</small>
        </div>
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory" class="btn btn-secondary btn-sm">
            ← Quay lại
        </a>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/index.php?url=admin-inventory-create" id="importForm">

        <!-- Ghi chú -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <label class="form-label fw-bold">Ghi chú phiếu nhập</label>
                <input type="text" name="note" class="form-control"
                       placeholder="VD: Nhập hàng tháng 3, nhà cung cấp ABC...">
            </div>
        </div>

        <!-- Bảng sản phẩm -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-0 py-3 text-white"
                 style="background:#3b5d50;border-radius:12px 12px 0 0">
                <div class="row g-2 mb-0 align-items-center small fw-bold">
                    <div class="col-md-4">Sản phẩm</div>
                    <div class="col-md-2">Size</div>
                    <div class="col-md-2">Giá nhập (đ)</div>
                    <div class="col-md-1 text-center">Số lượng</div>
                    <div class="col-md-1 text-center">Thành tiền</div>
                    <div class="col-md-2"></div>
                </div>
            </div>

            <div class="card-body p-0">
                <div id="itemContainer">
                    <div class="item-row border-bottom px-3 py-3">
                        <div class="row g-2 align-items-center">

                            <div class="col-md-4">
                                <select name="product_id[]"
                                        class="form-select form-select-sm product-select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>">
                                            <?= htmlspecialchars($p['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="size_id[]"
                                        class="form-select form-select-sm size-select">
                                    <option value="">-- Size --</option>
                                    <?php foreach ($sizes as $s): ?>
                                        <option value="<?= $s['size_id'] ?>">
                                            <?= htmlspecialchars($s['size']) ?>
                                            <?php if ($s['price_adjust'] > 0): ?>
                                                (+<?= number_format($s['price_adjust'], 0, ',', '.') ?>đ)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <?php
                                /*
                                 * Dùng input type="text" + class "price-input"
                                 * KHÔNG dùng type="number" vì browser có thể
                                 * normalize hoặc làm mất số 0 khi format.
                                 * Giá trị thực (số nguyên) được lưu vào
                                 * hidden field price_raw[] trước khi submit.
                                 */
                                ?>
                                <div class="input-group input-group-sm">
                                    <input type="text"
                                           class="form-control price-input text-end"
                                           placeholder="0"
                                           autocomplete="off">
                                    <input type="hidden" name="price[]" class="price-raw">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <input type="number" name="quantity[]"
                                       class="form-control form-control-sm qty-input text-center"
                                       min="1" value="1">
                            </div>

                            <div class="col-md-1 text-center">
                                <span class="subtotal-display text-danger fw-bold"
                                      style="font-size:13px">0đ</span>
                            </div>

                            <div class="col-md-2 text-end">
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-remove">
                                    Xóa
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer border-0 bg-white px-3 pb-3">
                <button type="button" id="btnAddRow"
                        class="btn btn-sm btn-outline-success"
                        style="border-radius:20px">
                    + Thêm dòng
                </button>
            </div>
        </div>

        <!-- Tổng kết -->
        <div class="card border-0 shadow-sm mb-4 text-white"
             style="background:linear-gradient(135deg,#3b5d50,#4e7a66);border-radius:12px">
            <div class="card-body py-3">
                <div class="row text-center">
                    <div class="col-md-4 border-end border-white border-opacity-25">
                        <div class="small opacity-75">Tổng dòng</div>
                        <div id="totalProduct" class="fw-bold fs-4">0</div>
                    </div>
                    <div class="col-md-4 border-end border-white border-opacity-25">
                        <div class="small opacity-75">Tổng số lượng</div>
                        <div id="totalQty" class="fw-bold fs-4">0</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small opacity-75">Tổng giá trị</div>
                        <div id="totalValue" class="fw-bold fs-4" style="color:#f9bf29">0đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary px-4"
                    style="background:#3b5d50;border-color:#3b5d50;border-radius:20px;font-weight:600">
                Tạo phiếu nhập
            </button>
            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
               class="btn btn-secondary" style="border-radius:20px">
                Hủy
            </a>
        </div>

    </form>
</div>

<script>
const BASE_URL   = "<?= BASE_URL ?>";
const stockCache = {};

/* =========================================================
   PARSE / FORMAT
   - parsePrice: "100.000" hoặc "100000" → 100000 (số nguyên)
   - formatPrice: 100000 → "100.000"
   Không dùng parseFloat vì dấu chấm là phân cách nghìn (vi-VN),
   không phải dấu thập phân.
========================================================= */
function parsePrice(str) {
    // Xoá mọi ký tự không phải chữ số rồi parseInt
    return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
}

function formatPrice(num) {
    return num > 0 ? num.toLocaleString('vi-VN') : '';
}

/* =========================================================
   BIND sự kiện cho 1 ô giá trong row
========================================================= */
function bindPriceInput(row) {
    const display = row.querySelector('.price-input');
    const raw     = row.querySelector('.price-raw');
    if (!display || !raw) return;

    display.addEventListener('input', function () {
        const num  = parsePrice(this.value);
        raw.value  = num;                          // lưu số nguyên vào hidden
        this.value = formatPrice(num);             // hiển thị có dấu chấm
        calcTotal();
    });

    display.addEventListener('blur', function () {
        const num  = parsePrice(this.value);
        raw.value  = num;
        this.value = formatPrice(num);
    });

    display.addEventListener('focus', function () {
        // Khi focus: hiển thị số thuần để người dùng sửa dễ
        const num  = parsePrice(this.value);
        this.value = num > 0 ? String(num) : '';
    });
}

/* =========================================================
   LOAD SIZES khi đổi sản phẩm
========================================================= */
async function loadSizes(select) {
    const productId  = select.value;
    const row        = select.closest('.item-row');
    const sizeSelect = row.querySelector('.size-select');

    // Reset size về mặc định
    sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
    if (!productId) return;

    // Lấy từ cache nếu đã fetch
    if (!stockCache[productId]) {
        try {
            const res = await fetch(
                `${BASE_URL}/index.php?url=api-sizes&product_id=${productId}`
            );
            stockCache[productId] = await res.json();
        } catch (e) {
            console.error('Lỗi fetch sizes:', e);
            return;
        }
    }

    const sizes = stockCache[productId];
    sizes.forEach(s => {
        const opt   = document.createElement('option');
        opt.value   = s.size_id;
        opt.dataset.stock = s.stock ?? 0;
        let label   = s.size;
        if (s.price_adjust > 0) {
            label += ` (+${Number(s.price_adjust).toLocaleString('vi-VN')}đ)`;
        }
        label += ` [Tồn: ${s.stock ?? 0}]`;
        opt.textContent = label;
        if (!s.stock || s.stock <= 0) opt.style.color = '#aaa';
        sizeSelect.appendChild(opt);
    });
}

/* =========================================================
   TÍNH TỔNG
========================================================= */
function calcTotal() {
    const rows = document.querySelectorAll('.item-row');
    let totalQty   = 0;
    let totalValue = 0;

    rows.forEach(row => {
        const raw    = row.querySelector('.price-raw');
        const qty    = row.querySelector('.qty-input');
        const subEl  = row.querySelector('.subtotal-display');

        const price  = parseInt(raw?.value, 10)  || 0;
        const q      = parseInt(qty?.value,  10) || 0;
        const sub    = price * q;

        totalQty   += q;
        totalValue += sub;

        if (subEl) {
            subEl.textContent = sub > 0
                ? sub.toLocaleString('vi-VN') + 'đ'
                : '0đ';
        }
    });

    document.getElementById('totalProduct').textContent = rows.length;
    document.getElementById('totalQty').textContent     = totalQty;
    document.getElementById('totalValue').textContent   =
        totalValue > 0 ? totalValue.toLocaleString('vi-VN') + 'đ' : '0đ';
}

/* =========================================================
   THÊM DÒNG MỚI
========================================================= */
document.getElementById('btnAddRow').addEventListener('click', function () {
    const container = document.getElementById('itemContainer');
    const template  = container.querySelector('.item-row');
    const clone     = template.cloneNode(true);

    // Reset tất cả input/select
    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    clone.querySelectorAll('input').forEach(i => {
        i.value = i.classList.contains('qty-input') ? '1' : '';
    });
    clone.querySelector('.subtotal-display').textContent = '0đ';

    // Reset size select về mặc định (xoá option cũ do loadSizes inject)
    const sizeSelect = clone.querySelector('.size-select');
    sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
    <?php foreach ($sizes as $s): ?>
    (function(){
        const opt = document.createElement('option');
        opt.value = <?= $s['size_id'] ?>;
        opt.textContent = <?= json_encode($s['size'] . ($s['price_adjust'] > 0 ? ' (+' . number_format($s['price_adjust'], 0, ',', '.') . 'đ)' : '')) ?>;
        sizeSelect.appendChild(opt);
    })();
    <?php endforeach; ?>

    // Bind events cho dòng mới
    bindPriceInput(clone);
    clone.querySelector('.product-select').addEventListener('change', function () {
        loadSizes(this);
    });
    clone.querySelector('.qty-input').addEventListener('input', calcTotal);
    clone.querySelector('.btn-remove').addEventListener('click', function () {
        removeRow(this);
    });

    container.appendChild(clone);
    calcTotal();
});

/* =========================================================
   XÓA DÒNG
========================================================= */
function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) return;   // giữ ít nhất 1 dòng
    btn.closest('.item-row').remove();
    calcTotal();
}

/* =========================================================
   TRƯỚC KHI SUBMIT
   Đảm bảo price-raw[] luôn chứa số nguyên đúng,
   phòng hờ nếu user không blur trước khi submit.
========================================================= */
document.getElementById('importForm').addEventListener('submit', function (e) {
    let valid = true;

    document.querySelectorAll('.item-row').forEach(row => {
        const display = row.querySelector('.price-input');
        const raw     = row.querySelector('.price-raw');
        if (!display || !raw) return;

        const num = parsePrice(display.value);
        raw.value = num;                    // ghi lại chắc chắn trước POST

        // Hiển thị lại giá đã format (UX)
        display.value = formatPrice(num);
    });

    if (!valid) e.preventDefault();
});

/* =========================================================
   BIND events cho dòng đầu tiên (render từ PHP)
========================================================= */
document.querySelectorAll('.item-row').forEach(row => {
    bindPriceInput(row);

    row.querySelector('.product-select')
       ?.addEventListener('change', function () { loadSizes(this); });

    row.querySelector('.qty-input')
       ?.addEventListener('input', calcTotal);

    row.querySelector('.btn-remove')
       ?.addEventListener('click', function () { removeRow(this); });
});

calcTotal();
</script>