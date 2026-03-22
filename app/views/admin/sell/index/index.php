<?php /* app/views/admin/sell/index.php */ ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý Giá Bán</h2>
    </div>

    <?php if (!empty($_GET['updated'])): ?>
    <div class="ui-alert success mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        Cập nhật % lợi nhuận thành công.
    </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
    <div class="ui-alert danger mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Thao tác thất bại. Vui lòng thử lại.
    </div>
    <?php endif; ?>

    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            <h5>Bộ lọc sản phẩm</h5>
        </div>
        <div class="ui-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <div class="ui-field mb-0">
                        <label class="ui-label">Tìm kiếm sản phẩm</label>
                        <input type="text" id="filterSearch" class="ui-input"
                               placeholder="Nhập tên sản phẩm..."
                               value="<?= htmlspecialchars($search) ?>"
                               oninput="applyFilter()">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="ui-field mb-0">
                        <label class="ui-label">Phân loại</label>
                        <select id="filterCat" class="ui-input" onchange="applyFilter()">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="ui-field mb-0">
                        <label class="ui-label">Giá vốn (VNĐ)</label>
                        <select id="filterGiaVon" class="ui-input" onchange="applyFilter()">
                            <option value="">Tất cả</option>
                            <option value="0-30000">0 – 30.000</option>
                            <option value="30000-80000">30.000 – 80.000</option>
                            <option value="80000-150000">80.000 – 150.000</option>
                            <option value="150000-999999999">Trên 150.000</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="ui-field mb-0">
                        <label class="ui-label">% Lợi nhuận</label>
                        <select id="filterProfit" class="ui-input" onchange="applyFilter()">
                            <option value="">Tất cả</option>
                            <option value="0-10">Dưới 10%</option>
                            <option value="10-30">10% – 30%</option>
                            <option value="30-50">30% – 50%</option>
                            <option value="50-999">Trên 50%</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="ui-btn-outline sm w-100" onclick="resetFilter()">✕ Xóa bộ lọc</button>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <h5>Danh sách sản phẩm — <?= count($products) ?> sản phẩm</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head" id="tableSanPham">
                <thead>
                    <tr>
                        <th style="width:60px">Mã</th>
                        <th>Tên sản phẩm</th>
                        <th>Phân loại</th>
                        <th class="right" style="width:140px">Giá vốn TB</th>
                        <th class="center" style="width:160px">% Lợi nhuận</th>
                        <th class="right" style="width:140px">Giá bán</th>
                        <th class="center" style="width:140px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($products)): ?>
                <tr><td colspan="7"><div class="ui-empty py-4"><h4>Không tìm thấy sản phẩm</h4><p>Thử thay đổi bộ lọc</p></div></td></tr>
                <?php else: foreach ($products as $p):
                    $avgImport = (float)($p['avg_import_price'] ?? 0);
                    $profit    = (float)($p['profit_rate']      ?? 0);
                    $salePrice = (float)($p['sale_price']       ?? 0);
                    $catName   = htmlspecialchars($p['category_name'] ?? '—');
                ?>
                <tr data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                    data-cat="<?= $catName ?>"
                    data-cost="<?= $avgImport ?>"
                    data-profit="<?= $profit ?>">
                    <td><span style="font-family:monospace;font-weight:700;color:var(--brand)">#<?= (int)$p['id'] ?></span></td>
                    <td style="font-weight:600"><?= htmlspecialchars($p['name']) ?></td>
                    <td><span class="ui-badge info"><?= $catName ?></span></td>
                    <td class="right price">
                        <?= $avgImport > 0 ? number_format($avgImport,0,',','.') . 'đ' : '<span class="muted">—</span>' ?>
                    </td>
                    <td class="center">
                        <form method="POST" action="<?= BASE_URL ?>/index.php?url=admin-sell"
                              class="d-inline-flex align-items-center gap-1">
                            <input type="hidden" name="type" value="product">
                            <input type="hidden" name="id"   value="<?= (int)$p['id'] ?>">
                            <input type="number" name="profit_rate"
                                   class="ui-input profit-input"
                                   value="<?= $profit ?>" min="0" max="999" step="0.1"
                                   readonly
                                   style="text-align:center;width:80px">
                            <span class="muted" style="font-size:13px">%</span>
                        </form>
                    </td>
                    <td class="right price sale-price"
                        data-cost="<?= $avgImport ?>" data-profit="<?= $profit ?>">
                        <?= $salePrice > 0 ? number_format($salePrice,0,',','.') . 'đ' : '<span class="muted">—</span>' ?>
                    </td>
                    <td class="center">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="ui-btn-outline sm btn-edit" onclick="editRow(this)">Chỉnh sửa</button>
                            <button type="button" class="ui-btn sm btn-save" style="display:none" onclick="saveRow(this)">Lưu</button>
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
                $qs = '?url=admin-sell&search=' . urlencode($search ?? '') . '&category=' . ($categoryId ?? 0) . '&page=';
                for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                   class="ui-page-btn <?= $i == ($page ?? 1) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <!-- Pagination -->

</div>

<script>
function editRow(btn) {
    const row   = btn.closest('tr');
    const input = row.querySelector('.profit-input');
    input.removeAttribute('readonly');
    input.style.background = '#fff';
    input.focus();
    btn.style.display = 'none';
    row.querySelector('.btn-save').style.display = '';
}

function saveRow(btn) {
    const row   = btn.closest('tr');
    const input = row.querySelector('.profit-input');
    if (!confirm('Lưu thay đổi % lợi nhuận này?')) return;
    const cost      = parseFloat(row.querySelector('.sale-price').dataset.cost) || 0;
    const newProfit = parseFloat(input.value) || 0;
    const newPrice  = Math.round(cost * (1 + newProfit / 100) / 1000) * 1000;
    row.querySelector('.sale-price').textContent = newPrice > 0 ? newPrice.toLocaleString('vi-VN') + 'đ' : '—';
    row.querySelector('form').submit();
}

function applyFilter() {
    const search    = document.getElementById('filterSearch').value.toLowerCase();
    const cat       = document.getElementById('filterCat').value;
    const vonRange  = document.getElementById('filterGiaVon').value;
    const profRange = document.getElementById('filterProfit').value;
    document.querySelectorAll('#tableSanPham tbody tr[data-name]').forEach(row => {
        const ok = row.dataset.name.includes(search)
                && (!cat    || row.dataset.cat === cat)
                && inRange(parseFloat(row.dataset.cost)   || 0, vonRange)
                && inRange(parseFloat(row.dataset.profit) || 0, profRange);
        row.style.display = ok ? '' : 'none';
    });
}

function inRange(val, range) {
    if (!range) return true;
    const [min, max] = range.split('-').map(Number);
    return val >= min && val <= max;
}

function resetFilter() {
    ['filterSearch','filterCat','filterGiaVon','filterProfit'].forEach(id => {
        const el = document.getElementById(id);
        el.value = el.tagName === 'SELECT' ? '' : '';
    });
    applyFilter();
}
</script>