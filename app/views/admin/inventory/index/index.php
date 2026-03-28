    <?php /* app/views/admin/inventory/index.php */ ?>

    <div class="container-fluid py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ui-title mb-0">Quản lý nhập kho</h2>
            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create" class="ui-btn sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tạo phiếu nhập
            </a>
        </div>

        <!-- Alerts (giữ nguyên) -->
        <?php if (isset($_GET['confirmed'])): ?>
        <div class="ui-alert success mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Đã xác nhận phiếu nhập kho thành công.
        </div>
        <?php elseif (isset($_GET['cancelled'])): ?>
        <div class="ui-alert warning mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Đã hủy phiếu nhập.
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div class="ui-alert danger mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            Thao tác thất bại. Vui lòng thử lại.
        </div>
        <?php endif; ?>

        <!-- Bảng phiếu nhập (giữ nguyên) -->

            <div class="ui-tabs mb-3">
                <button class="tab-btn active" data-tab="inventory">
                    <i class="fa fa-box"></i> Tồn kho
                </button>

                <button class="tab-btn" data-tab="logs">
                    <i class="fa fa-exchange-alt"></i> Xuất / Nhập
                </button>

                <button class="tab-btn" data-tab="out">
                    <i class="fa fa-exclamation-triangle"></i> Hết hàng
                </button>
            </div>

        <!-- ═══════════════════════════════════════
            BẢNG TỒN KHO
        ═══════════════════════════════════════ -->
        <div id="tab-inventory" class="tab-content active">
            <div class="ui-card mb-4">
                <div class="ui-card-head">
                    <h5>Danh sách tồn kho</h5>
                </div>

                <div style="overflow-x:auto">
                    <!-- Filter tồn kho -->
                <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                    <form method="GET" action="<?= BASE_URL ?>/index.php">
                        <input type="hidden" name="url" value="admin-inventory">
                        <input type="hidden" name="tab" value="inventory">

                        <div class="row g-3 align-items-end">

                            <div class="col-md-3">
                                <label>Danh mục</label>
                                <select name="category_id" class="ui-input">
                                    <option value="">Tất cả</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>"
                                            <?= ($categoryId ?? '') == $c['id'] ? 'selected' : '' ?>>
                                            <?= $c['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Trạng thái</label>
                                <select name="status" class="ui-input">
                                    <option value="">Tất cả</option>
                                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang bán</option>
                                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Sắp xếp</label>
                                <select name="sort" class="ui-input">
                                    <option value="">Mặc định</option>
                                        <option value="desc">Số lượng cao → thấp</option>
                                        <option value="asc">Số lượng thấp → cao</option>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button class="ui-btn sm">Lọc</button>

                                <?php if (!empty($categoryId) || !empty($status) || !empty($sort)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
                                    class="ui-btn-outline sm">✕ Xóa</a>
                                <?php endif; ?>
                            </div>

                        </div>
                    </form>
                </div>
                    <table class="ui-table admin-head">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Size</th>
                        <th>Trạng thái</th>
                        <th>Số lượng</th>
                        <th class="right">Giá Bán</th>
                    </tr>
                    </thead>
                        <tbody>
                            <?php if (empty($inventory)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                            <?php else: foreach ($inventory as $inv): ?>
                            <tr>
                                <td>#<?= $inv['id'] ?></td>
                                <td><?= htmlspecialchars($inv['product_name']) ?></td>

                                <td>
                                    <span class="ui-badge neutral">
                                        <?= htmlspecialchars($inv['category_name'] ?? '—') ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($inv['size_name']) ?></td>

                                <td>
                                    <?php if (($inv['status'] ?? 'inactive') === 'active'): ?>
                                        <span class="ui-badge success">Đang bán</span>
                                    <?php else: ?>
                                        <span class="ui-badge danger">Ngừng bán</span>
                                    <?php endif; ?>
                                </td>
                                <td style="<?= $inv['quantity'] == 0 ? 'color:red;font-weight:700' : '' ?>">
                                    <?= number_format($inv['quantity']) ?>
                                </td>
                                <td class="right"><?= number_format($inv['avg_import_price']) ?>đ</td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($invTotalPages) && $invTotalPages > 1): ?>
                <div class="ui-card-body">
                    <div class="ui-pagination">

                    <?php if ($invPage > 1): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&inv_page=<?= $invPage - 1 ?>">‹</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $invTotalPages; $i++): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&inv_page=<?= $i ?>"
                        class="<?= $i == $invPage ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($invPage < $invTotalPages): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&inv_page=<?= $invPage + 1 ?>">›</a>
                    <?php endif; ?>

                     </div>
                </div>
            <?php endif; ?>
            </div>
        </div> <!-- END tab inventory -->
        <!-- ═══════════════════════════════════════
            BẢNG HẾT HÀNG
        ═══════════════════════════════════════ -->
        <div id="tab-out" class="tab-content">
            <div class="ui-card mb-4">

                <div class="ui-card-head">
                    <h5>Sản phẩm hết hàng</h5>
                </div>

                <!-- Filter (KHÔNG có sort) -->
                <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                    <form method="GET" action="<?= BASE_URL ?>/index.php">
                        <input type="hidden" name="url" value="admin-inventory">
                         <input type="hidden" name="tab" value="out"> <!-- ✅ THÊM DÒNG NÀY -->

                        <div class="row g-3 align-items-end">

                            <div class="col-md-3">
                                <label>Danh mục</label>
                                <select name="category_id" class="ui-input">
                                    <option value="">Tất cả</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>"
                                            <?= ($categoryId ?? '') == $c['id'] ? 'selected' : '' ?>>
                                            <?= $c['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Trạng thái</label>
                                <select name="status" class="ui-input">
                                    <option value="">Tất cả</option>
                                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang bán</option>
                                    <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex gap-2">
                                <button class="ui-btn sm">Lọc</button>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
                                class="ui-btn-outline sm">✕ Xóa</a>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div style="overflow-x:auto">
                    <table class="ui-table admin-head">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Size</th>
                                <th>Trạng thái</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($outStock)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có sản phẩm hết hàng</td>
                            </tr>
                        <?php else: foreach ($outStock as $inv): ?>
                            <tr>
                                <td>#<?= $inv['id'] ?></td>
                                <td><?= htmlspecialchars($inv['product_name']) ?></td>
                                <td><?= htmlspecialchars($inv['category_name']) ?></td>
                                <td><?= htmlspecialchars($inv['size_name']) ?></td>
                                <td>
                                    <?= $inv['status'] === 'active'
                                        ? '<span class="ui-badge success">Đang bán</span>'
                                        : '<span class="ui-badge danger">Ngừng bán</span>' ?>
                                </td>
                                <td style="color:red;font-weight:700">0</td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
             </div>

        <?php if (!empty($outTotalPages) && $outTotalPages > 1): ?>
        <div class="ui-card-body">
            <div class="ui-pagination">

                <?php
                $qs = '?url=admin-inventory&tab=out'
                    . '&category_id=' . urlencode($categoryId ?? '')
                    . '&status=' . urlencode($status ?? '')
                    . '&out_page=';
                ?>

                <?php if ($outPage > 1): ?>
                    <a href="<?= BASE_URL ?>/index.php<?= $qs . ($outPage - 1) ?>"
                    class="ui-page-btn">‹</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $outTotalPages; $i++): ?>
                    <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                    class="ui-page-btn <?= $i == $outPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($outPage < $outTotalPages): ?>
                    <a href="<?= BASE_URL ?>/index.php<?= $qs . ($outPage + 1) ?>"
                    class="ui-page-btn">›</a>
                <?php endif; ?>

            </div>
        </div>
        <?php endif; ?>

        </div>
        </div>

        <!-- ═══════════════════════════════════════
            BẢNG LOG XUẤT/NHẬP KHO
        ═══════════════════════════════════════ -->
    <div id="tab-logs" class="tab-content">
        <div class="ui-card mb-0">
            <div class="ui-card-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <h5>Lịch sử xuất / nhập kho</h5>
            </div>

            <!-- Filter ngày -->
            <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                <form method="GET" action="<?= BASE_URL ?>/index.php">
                    <input type="hidden" name="url" value="admin-inventory">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <div class="ui-field mb-0">
                                <label class="ui-label">Loại</label>
                                <select name="log_type" class="ui-input">
                                    <option value="">Tất cả</option>
                                    <option value="import" <?= ($logType ?? '') === 'import' ? 'selected' : '' ?>>Nhập</option>
                                    <option value="export" <?= ($logType ?? '') === 'export' ? 'selected' : '' ?>>Xuất</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="ui-field mb-0">
                                <label class="ui-label">Từ ngày</label>
                                <input type="date" name="log_from" class="ui-input"
                                    value="<?= htmlspecialchars($logFrom ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="ui-field mb-0">
                                <label class="ui-label">Đến ngày</label>
                                <input type="date" name="log_to" class="ui-input"
                                    value="<?= htmlspecialchars($logTo ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="ui-btn sm flex-grow-1">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                    style="width:14px;height:14px;stroke:#fff">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                                Lọc
                            </button>
                            <?php if (!empty($logFrom) || !empty($logTo) || !empty($logType)): ?>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
                            class="ui-btn-outline sm">✕ Xóa</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2 text-end">
                            <span style="font-size:13px;color:var(--text-muted)">
                                Tổng <strong><?= count($logs ?? []) ?></strong> bản ghi
                                <?php if (!empty($logFrom) || !empty($logTo) || !empty($logType)): ?>
                                <span class="ui-badge info" style="margin-left:6px">Đang lọc</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <div style="overflow-x:auto">
                <table class="ui-table admin-head">
                    <thead>
                        <tr>
                            <th style="width:60px">Mã</th>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th class="center" style="width:110px">Loại</th>
                            <th class="right" style="width:100px">Số lượng</th>
                            <th class="right" style="width:140px">Giá nhập</th>
                            <th style="width:180px">Ghi chú</th>
                            <th style="width:140px">Thời gian</th>
                            <th style="width:160px" class="center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="8">
                            <div class="ui-empty py-4">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                </svg>
                                <h4>Không có bản ghi nào</h4>
                                <p>Thử thay đổi khoảng ngày lọc</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                                #<?= (int)$log['id'] ?>
                            </span>
                        </td>
                        <td style="font-weight:600">
                            <?= htmlspecialchars($log['product_name'] ?? '—') ?>
                        </td>
                        <td>
                            <span class="ui-badge neutral">
                                <?= htmlspecialchars($log['size_name'] ?? '—') ?>
                            </span>
                        </td>
                        <td class="center">
                            <?php if ($log['type'] === 'import'): ?>
                            <span class="ui-badge success">↓ Nhập</span>
                            <?php else: ?>
                            <span class="ui-badge warning">↑ Xuất</span>
                            <?php endif; ?>
                        </td>
                        <td class="right" style="font-weight:700">
                            <?= $log['type'] === 'import' ? '+' : '-' ?>
                            <?= number_format((int)$log['quantity'], 0, ',', '.') ?>
                        </td>
                        <td class="right price">
                            <?= $log['import_price'] > 0
                                ? number_format((float)$log['import_price'], 0, ',', '.') . 'đ'
                                : '<span class="muted">—</span>' ?>
                        </td>
                        <td class="muted" style="font-size:13px;max-width:180px">
                            <?= htmlspecialchars($log['note'] ?? '—') ?>
                        </td>
                        <td class="muted" style="font-size:13px">
                            <?= !empty($log['created_at'])
                                ? date('d/m/Y H:i', strtotime($log['created_at']))
                                : '—' ?>
                        </td>
                        <td class="center">
                            <?php if ($log['type'] === 'export' && !empty($log['order_id'])): ?>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-orders-detail&id=<?= (int)$log['order_id'] ?>"
                                    class="ui-btn-outline sm">
                                    📦 Đơn hàng
                                </a>

                                <?php elseif ($log['type'] === 'import' && !empty($log['receipt_id'])): ?>

                                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= (int)$log['receipt_id'] ?>"
                                class="ui-btn sm">
                                📥 Phiếu nhập
                                </a>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                            </td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Log pagination -->
            <?php if (!empty($logTotalPages) && $logTotalPages > 1): ?>
            <div class="ui-card-body pt-0">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span style="font-size:13px;color:var(--text-muted)">
                        Trang <?= $logPage ?> / <?= $logTotalPages ?>
                    </span>
                    <div class="ui-pagination mb-0" style="margin:0">
                        <?php
                    $logQp = array_filter([
                        'url'      => 'admin-inventory',
                        'log_from' => $logFrom ?? '',
                        'log_to'   => $logTo   ?? '',
                        'log_type' => $logType ?? '',
                    ]);
                        $logQs = '?' . http_build_query($logQp) . '&';
                        $logStart = max(1, $logPage - 2);
                        $logEnd   = min($logTotalPages, $logStart + 4);
                        $logStart = max(1, $logEnd - 4);
                        ?>
                        <?php if ($logPage > 1): ?>
                        <a href="<?= BASE_URL ?>/index.php<?= $logQs ?>log_page=<?= $logPage - 1 ?>"
                        class="ui-page-btn">‹</a>
                        <?php endif; ?>

                        <?php for ($i = $logStart; $i <= $logEnd; $i++): ?>
                        <a href="<?= BASE_URL ?>/index.php<?= $logQs ?>log_page=<?= $i ?>"
                        class="ui-page-btn <?= $i === $logPage ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>

                        <?php if ($logPage < $logTotalPages): ?>
                        <a href="<?= BASE_URL ?>/index.php<?= $logQs ?>log_page=<?= $logPage + 1 ?>"
                        class="ui-page-btn">›</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- /log card -->
    </div> <!-- END tab logs -->

</div>
<script>
// Lấy tab từ URL
const params = new URLSearchParams(window.location.search);
const currentTab = params.get('tab') || 'inventory';

// Active tab theo URL
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.classList.remove('active');
    if (btn.dataset.tab === currentTab) {
        btn.classList.add('active');
    }
});

document.querySelectorAll('.tab-content').forEach(c => {
    c.classList.remove('active');
});

const activeTab = document.getElementById('tab-' + currentTab);
if (activeTab) activeTab.classList.add('active');

// Click tab (giữ nguyên nhưng thêm push URL)
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');

        // ✅ cập nhật URL (quan trọng)
        const url = new URL(window.location);
        url.searchParams.set('tab', btn.dataset.tab);
        window.history.replaceState({}, '', url);
    });
});
</script>

<style>
/* Ẩn / hiện tab */
.tab-content { display: none; }
.tab-content.active { display: block; }
/* wrapper */
.ui-tabs {
    display: flex;
    gap: 10px;
}

/* nút tab */
.tab-btn {
    border: none;
    padding: 8px 16px;
    border-radius: 999px; /* 🔥 làm tròn full */
    background: #f1f1f1;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

/* hover */
.tab-btn:hover {
    background: #e2e2e2;
    transform: translateY(-1px);
}

/* active */
.tab-btn.active {
    background: linear-gradient(135deg, #4CAF50, #2e7d32);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* icon */
.tab-btn i {
    font-size: 13px;
}
.ui-pagination {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 10px;
}

.ui-page-btn {
    padding: 6px 12px;
    border-radius: 8px;
    background: #f1f1f1;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.ui-page-btn:hover {
    background: #e0e0e0;
}

.ui-page-btn.active {
    background: linear-gradient(135deg, #4CAF50, #2e7d32);
    color: #fff;
    font-weight: 600;
}
</style>