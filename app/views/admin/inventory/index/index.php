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

    <!-- Alerts -->
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

    <!-- Table card -->
    <div class="ui-card mb-0">
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
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                            <h4>Chưa có phiếu nhập nào</h4>
                            <p>Tạo phiếu nhập đầu tiên để bắt đầu</p>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
                               class="ui-btn sm">+ Tạo phiếu nhập</a>
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
                            ? date('d/m/Y H:i', strtotime($row['created_at']))
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
                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= $row['id'] ?>"
                               class="ui-btn-outline sm">
                                🔍 Chi tiết
                            </a>
                            <?php if ($row['status'] === 'pending'): ?>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-confirm&id=<?= $row['id'] ?>"
                               class="ui-btn sm"
                               style="background:linear-gradient(135deg,#38d9a9,#0ca678)"
                               onclick="return confirm('Xác nhận nhập kho phiếu này?')">
                                ✅ Xác nhận
                            </a>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-cancel&id=<?= $row['id'] ?>"
                               class="ui-btn sm"
                               style="background:linear-gradient(135deg,#f76f8e,#db2777)"
                               onclick="return confirm('Hủy phiếu này?')">
                                ✕ Hủy
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&page=<?= $i ?>"
                   class="ui-page-btn <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>