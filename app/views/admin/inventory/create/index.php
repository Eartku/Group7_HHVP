<div class="container mt-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý nhập kho</h2>
        <button type="button" id="btnShowForm" class="ui-btn sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tạo phiếu nhập
        </button>
    </div>

    <!-- Modal tạo phiếu nhập -->
    <div id="createFormModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; overflow-y:auto;">
        <div style="position:relative; width:90%; max-width:1200px; margin:50px auto; background:white; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="padding:20px; border-bottom:1px solid #e0e0e0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; color:#3b5d50;">Tạo phiếu nhập kho</h3>
                <button type="button" id="btnCloseModal" style="background:none; border:none; font-size:28px; cursor:pointer; color:#999;">&times;</button>
            </div>
            
            <form method="POST" action="<?= BASE_URL ?>/index.php?url=admin-inventory-create" id="importForm">
                <div style="padding:20px;">
                    
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
                                        <!-- PRODUCT với search -->
                                        <div class="col-md-4">
                                            <div class="product-search-wrapper" style="position: relative;">
                                                <input type="text"
                                                    class="form-control form-control-sm product-search-input"
                                                    placeholder="-- Nhập tên sản phẩm --"
                                                    autocomplete="off"
                                                    style="width: 100%;">
                                                <input type="hidden" name="product_id[]" class="product-id-hidden" value="">
                                                <div class="product-search-dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; max-height: 250px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                    <div class="search-dropdown-header" style="padding: 8px; border-bottom: 1px solid #eee;">
                                                        <input type="text" class="form-control form-control-sm search-filter-input" placeholder="Lọc sản phẩm...">
                                                    </div>
                                                    <div class="search-dropdown-list">
                                                        <?php foreach ($products as $p): ?>
                                                        <div class="product-item" data-id="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f0f0f0;">
                                                            <span style="font-weight: 500;"><?= htmlspecialchars($p['name']) ?></span>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SIZE -->
                                        <div class="col-md-2">
                                            <select name="size_id[]" class="form-select form-select-sm size-select">
                                                <option value="">-- Chọn size --</option>
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

                                        <!-- PRICE -->
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                    class="form-control price-input text-end"
                                                    placeholder="0"
                                                    autocomplete="off">
                                                <input type="hidden" name="price[]" class="price-raw">
                                                <span class="input-group-text">đ</span>
                                            </div>
                                        </div>

                                        <!-- QTY -->
                                        <div class="col-md-1">
                                            <input type="number" name="quantity[]"
                                                class="form-control form-control-sm qty-input text-center"
                                                min="1" value="1">
                                        </div>

                                        <!-- SUBTOTAL -->
                                        <div class="col-md-1 text-center">
                                            <span class="subtotal-display text-danger fw-bold"
                                                style="font-size:13px">0đ</span>
                                        </div>

                                        <!-- REMOVE BUTTON -->
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

                        <!-- Ngày nhập và Ghi chú đặt trong card-footer, trước nút thêm dòng -->
                        <div class="card-footer border-0 bg-white px-3 py-3">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <label class="form-label fw-bold">Ngày nhập phiếu</label>
                                    <input type="date"
                                        name="import_date"
                                        id="import_date"
                                        class="form-control"
                                        value="<?= date('Y-m-d') ?>">
                                    <small class="text-muted">
                                        Có thể thay đổi ngày nhập
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ghi chú phiếu nhập</label>
                                    <input type="text" name="note" id="note_input" class="form-control"
                                           placeholder="VD: Nhập hàng tháng 3, nhà cung cấp ABC...">
                                </div>
                            </div>
                            
                            <button type="button" id="btnAddRow" class=""
                                    style="border-radius:25px">
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
                        <button type="button"
                                id="btnCancelForm"
                                class="btn btn-secondary"
                                style="border-radius:20px">
                            Hủy
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bộ lọc phiếu nhập -->
    <div class="ui-card mb-3">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <h5>Lọc phiếu nhập</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url" value="admin-inventory-create">
                <div class="d-flex gap-2 flex-wrap align-items-end">
                    <div class="ui-field mb-0">
                        <label class="ui-label">Từ ngày</label>
                        <input type="date" name="filter_from" class="ui-input"
                            value="<?= htmlspecialchars($filterFrom ?? '') ?>">
                    </div>
                    <div class="ui-field mb-0">
                        <label class="ui-label">Đến ngày</label>
                        <input type="date" name="filter_to" class="ui-input"
                            value="<?= htmlspecialchars($filterTo ?? '') ?>">
                    </div>
                    <div class="ui-field mb-0">
                        <label class="ui-label">Trạng thái</label>
                        <select name="filter_status" class="ui-input" style="min-width:160px">
                            <option value="">Tất cả</option>
                            <option value="pending"
                                <?= ($filterStatus ?? '') === 'pending'   ? 'selected' : '' ?>>
                                Đang xử lý
                            </option>
                            <option value="confirmed"
                                <?= ($filterStatus ?? '') === 'confirmed' ? 'selected' : '' ?>>
                                Đã xác nhận
                            </option>
                            <option value="cancelled"
                                <?= ($filterStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>
                                Đã hủy
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="ui-btn sm">Lọc</button>
                    <?php if (!empty($filterFrom) || !empty($filterTo) || !empty($filterStatus)): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
                    class="ui-btn-outline sm">✕ Xóa lọc</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách phiếu nhập -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            <h5>Danh sách phiếu nhập</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Ngày tạo</th>
                        <th>Người tạo</th>
                        <th>Ghi chú</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($imports)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="ui-empty py-4">
                                <h4>Chưa có phiếu nhập nào</h4>
                            </div>
                        </td>
                    </tr>
                <?php else: foreach ($imports as $row): ?>
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                                #<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td class="muted">
                            <?= !empty($row['created_at'])
                                ? date('d/m/Y', strtotime($row['created_at']))
                                : '—' ?>
                        </td>
                        <td class="muted">
                            <?= htmlspecialchars($row['created_by_name'] ?? '—') ?>
                        </td>
                        <td style="max-width:200px;color:#555">
                            <?= htmlspecialchars($row['note'] ?? '—') ?>
                        </td>
                        <td class="center">
                            <?= InventoryModel::getStatusBadge($row['status']) ?>
                        </td>
                        <td class="center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if ($row['status'] === 'pending'): ?>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-edit&id=<?= $row['id'] ?>"
                                   class="ui-btn-outline sm">Chỉnh sửa</a>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-confirm&id=<?= $row['id'] ?>"
                                   class="ui-btn sm"
                                   style="background:linear-gradient(135deg,#38d9a9,#0ca678)"
                                   onclick="return confirm('Xác nhận nhập kho phiếu này?')">Xác nhận</a>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-cancel&id=<?= $row['id'] ?>"
                                   class="ui-btn sm"
                                   style="background:linear-gradient(135deg,#f76f8e,#db2777)"
                                   onclick="return confirm('Hủy phiếu này?')">✕ Hủy</a>
                                <?php else: ?>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= $row['id'] ?>"
                                   class="ui-btn-outline sm">Chi tiết</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php
                $qs = '?url=admin-inventory-create'
                    . '&filter_status=' . urlencode($filterStatus ?? '')
                    . '&filter_from='   . urlencode($filterFrom   ?? '')
                    . '&filter_to='     . urlencode($filterTo     ?? '')
                    . '&page=';
                for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                   class="ui-page-btn <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Animation cho modal */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #createFormModal > div {
        animation: modalFadeIn 0.3s ease-out;
    }
    /* Style cho product search */
    .product-search-wrapper {
        position: relative;
        width: 100%;
    }

    .product-search-input {
        cursor: pointer;
    }

    .product-search-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 300px;
        overflow: hidden;
        z-index: 1000;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .search-dropdown-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .product-item:hover {
        background-color: #f0f9f0;
    }

    .product-item.selected {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
</style>

<script>
const BASE_URL   = "<?= BASE_URL ?>";
const stockCache = {};

/* =========================================================
   PARSE / FORMAT
========================================================= */
function parsePrice(str) {
    return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
}

function formatPrice(num) {
    return num > 0 ? num.toLocaleString('vi-VN') : '';
}

/* =========================================================
   LOAD SIZES khi đổi sản phẩm
========================================================= */
async function loadSizes(productId, row) {
    const sizeSelect = row.querySelector('.size-select');
    if(!sizeSelect) return;

    sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
    if (!productId) return;

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
   PRODUCT SEARCH - KHỞI TẠO CHO MỖI DÒNG
========================================================= */
function initProductSearch(row) {
    const searchInput = row.querySelector('.product-search-input');
    const hiddenId = row.querySelector('.product-id-hidden');
    const dropdown = row.querySelector('.product-search-dropdown');
    const filterInput = row.querySelector('.search-filter-input');
    const itemsContainer = row.querySelector('.search-dropdown-list');
    
    if (!searchInput || !dropdown) return;
    
    // Lưu lại tất cả các item gốc
    const allItems = Array.from(itemsContainer.querySelectorAll('.product-item'));
    
    // Lọc sản phẩm
    function filterProducts(searchText) {
        const searchLower = searchText.toLowerCase().trim();
        allItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const id = item.getAttribute('data-id');
            if (searchLower === '' || name.includes(searchLower) || id.includes(searchLower)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Chọn sản phẩm
    function selectProduct(id, name) {
        searchInput.value = name;
        hiddenId.value = id;
        dropdown.style.display = 'none';
        
        // Kích hoạt load sizes sau khi chọn sản phẩm
        loadSizes(id, row);
    }
    
    // Hiển thị dropdown
    function showDropdown() {
        dropdown.style.display = 'block';
        // Reset filter
        if (filterInput) {
            filterInput.value = '';
            filterProducts('');
            setTimeout(() => filterInput.focus(), 100);
        }
    }
    
    // Ẩn dropdown
    function hideDropdown() {
        dropdown.style.display = 'none';
    }
    
    // Sự kiện click vào ô input
    searchInput.addEventListener('click', (e) => {
        e.stopPropagation();
        showDropdown();
    });
    
    // Sự kiện nhập text để tìm kiếm trực tiếp
    searchInput.addEventListener('input', (e) => {
        const searchText = e.target.value;
        if (searchText.trim() !== '') {
            showDropdown();
            if (filterInput) {
                filterInput.value = searchText;
                filterProducts(searchText);
            } else {
                filterProducts(searchText);
            }
        }
    });
    
    // Lọc khi gõ vào ô filter trong dropdown
    if (filterInput) {
        filterInput.addEventListener('input', (e) => {
            e.stopPropagation();
            filterProducts(e.target.value);
        });
        
        filterInput.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
    
    // Click chọn sản phẩm
    itemsContainer.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            const id = item.getAttribute('data-id');
            const name = item.getAttribute('data-name');
            selectProduct(id, name);
        });
    });
    
    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!row.contains(e.target)) {
            hideDropdown();
        }
    });
    
    // Ngăn click trong dropdown đóng
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });
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
        raw.value  = num;
        this.value = formatPrice(num);
        calcTotal();
    });

    display.addEventListener('blur', function () {
        const num  = parsePrice(this.value);
        raw.value  = num;
        this.value = formatPrice(num);
    });

    display.addEventListener('focus', function () {
        const num  = parsePrice(this.value);
        this.value = num > 0 ? String(num) : '';
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

    const totalProductElem = document.getElementById('totalProduct');
    const totalQtyElem = document.getElementById('totalQty');
    const totalValueElem = document.getElementById('totalValue');
    
    if(totalProductElem) totalProductElem.textContent = rows.length;
    if(totalQtyElem) totalQtyElem.textContent = totalQty;
    if(totalValueElem) totalValueElem.textContent = totalValue > 0 ? totalValue.toLocaleString('vi-VN') + 'đ' : '0đ';
}

/* =========================================================
   THÊM DÒNG MỚI
========================================================= */
function addNewRow() {
    const container = document.getElementById('itemContainer');
    const template  = container.querySelector('.item-row');
    const clone     = template.cloneNode(true);

    // Reset các input trong dòng mới
    clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    clone.querySelectorAll('input').forEach(i => {
        if (i.classList.contains('qty-input')) {
            i.value = '1';
        } else if (i.classList.contains('product-search-input')) {
            i.value = '';
        } else if (i.classList.contains('product-id-hidden')) {
            i.value = '';
        } else if (i.classList.contains('price-input')) {
            i.value = '';
        }
    });
    clone.querySelector('.subtotal-display').textContent = '0đ';
    
    // Reset price raw
    const priceRaw = clone.querySelector('.price-raw');
    if(priceRaw) priceRaw.value = '';

    // Reset size select
    const sizeSelect = clone.querySelector('.size-select');
    if(sizeSelect) {
        sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
        <?php foreach ($sizes as $s): ?>
        (function(){
            const opt = document.createElement('option');
            opt.value = <?= $s['size_id'] ?>;
            opt.textContent = <?= json_encode($s['size'] . ($s['price_adjust'] > 0 ? ' (+' . number_format($s['price_adjust'], 0, ',', '.') . 'đ)' : '')) ?>;
            sizeSelect.appendChild(opt);
        })();
        <?php endforeach; ?>
    }

    // Khởi tạo product search cho dòng mới
    initProductSearch(clone);
    
    bindPriceInput(clone);
    clone.querySelector('.qty-input')?.addEventListener('input', calcTotal);
    clone.querySelector('.btn-remove')?.addEventListener('click', function () {
        removeRow(this);
    });

    container.appendChild(clone);
    calcTotal();
}

/* =========================================================
   XÓA DÒNG
========================================================= */
function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) return;
    btn.closest('.item-row').remove();
    calcTotal();
}

/* =========================================================
   TRƯỚC KHI SUBMIT
========================================================= */
const importForm = document.getElementById('importForm');
if(importForm) {
    importForm.addEventListener('submit', function (e) {
        document.querySelectorAll('.item-row').forEach(row => {
            const display = row.querySelector('.price-input');
            const raw     = row.querySelector('.price-raw');
            if (!display || !raw) return;

            const num = parsePrice(display.value);
            raw.value = num;
            display.value = formatPrice(num);
        });
    });
}

/* =========================================================
   BIND events cho dòng đầu tiên
========================================================= */
document.querySelectorAll('.item-row').forEach(row => {
    // Khởi tạo product search cho dòng
    initProductSearch(row);
    
    bindPriceInput(row);
    row.querySelector('.qty-input')
       ?.addEventListener('input', calcTotal);
    row.querySelector('.btn-remove')
       ?.addEventListener('click', function () { removeRow(this); });
});

/* =========================================================
   HIỆN/ẨN MODAL
========================================================= */
const modal = document.getElementById('createFormModal');
const btnShow = document.getElementById('btnShowForm');
const btnClose = document.getElementById('btnCloseModal');
const btnCancel = document.getElementById('btnCancelForm');

function openModal() {
    if(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Reset form về trạng thái mới
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const dateOnly = `${year}-${month}-${day}`;
        const importDateInput = document.getElementById('import_date');
        if(importDateInput) importDateInput.value = dateOnly;
        
        const noteInput = document.getElementById('note_input');
        if(noteInput) noteInput.value = '';
        
        // Reset danh sách sản phẩm về 1 dòng
        const container = document.getElementById('itemContainer');
        if(container) {
            const rows = container.querySelectorAll('.item-row');
            for(let i = rows.length - 1; i > 0; i--) {
                rows[i].remove();
            }
            
            // Reset dòng đầu tiên
            const firstRow = container.querySelector('.item-row');
            if(firstRow) {
                // Reset product search
                const searchInput = firstRow.querySelector('.product-search-input');
                const hiddenId = firstRow.querySelector('.product-id-hidden');
                if(searchInput) searchInput.value = '';
                if(hiddenId) hiddenId.value = '';
                
                firstRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                firstRow.querySelectorAll('input').forEach(i => {
                    if(i.classList.contains('qty-input')) i.value = '1';
                    if(i.classList.contains('price-input')) i.value = '';
                });
                const subDisplay = firstRow.querySelector('.subtotal-display');
                if(subDisplay) subDisplay.textContent = '0đ';
                
                // Reset price raw
                const priceRaw = firstRow.querySelector('.price-raw');
                if(priceRaw) priceRaw.value = '';
                
                // Reset size select
                const sizeSelect = firstRow.querySelector('.size-select');
                if(sizeSelect) {
                    sizeSelect.innerHTML = '<option value="">-- Chọn size --</option>';
                    <?php foreach ($sizes as $s): ?>
                    (function(){
                        const opt = document.createElement('option');
                        opt.value = <?= $s['size_id'] ?>;
                        opt.textContent = <?= json_encode($s['size'] . ($s['price_adjust'] > 0 ? ' (+' . number_format($s['price_adjust'], 0, ',', '.') . 'đ)' : '')) ?>;
                        sizeSelect.appendChild(opt);
                    })();
                    <?php endforeach; ?>
                }
            }
        }
        
        calcTotal();
    }
}

function closeModal() {
    if(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

if(btnShow) {
    btnShow.addEventListener('click', openModal);
}

if(btnClose) {
    btnClose.addEventListener('click', closeModal);
}

if(btnCancel) {
    btnCancel.addEventListener('click', closeModal);
}

// Đóng modal khi click ra ngoài
if(modal) {
    modal.addEventListener('click', function(e) {
        if(e.target === modal) {
            closeModal();
        }
    });
}

// Thêm dòng
const btnAddRow = document.getElementById('btnAddRow');
if(btnAddRow) {
    btnAddRow.addEventListener('click', addNewRow);
}

// Tính tổng ban đầu
calcTotal();
</script>