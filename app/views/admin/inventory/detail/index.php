<?php /* app/views/admin/inventory/detail/index.php */ ?>

<div class="container-fluid py-4" style="max-width:900px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory">Nhập kho</a>
        <span class="sep">›</span>
        <span>Phiếu #<?= str_pad($receipt['id'], 3, '0', STR_PAD_LEFT) ?></span>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">
            Phiếu nhập #<?= str_pad($receipt['id'], 3, '0', STR_PAD_LEFT) ?>
        </h2>
        <?= InventoryModel::getStatusBadge($receipt['status']) ?>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['confirmed'])): ?>
    <div class="ui-alert success mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Xác nhận nhập kho thành công!
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
        Có lỗi xảy ra, vui lòng thử lại.
    </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Thông tin phiếu -->
        <div class="col-md-4">
            <div class="ui-card mb-0 h-100">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h5>Thông tin phiếu</h5>
                </div>
                <div class="ui-card-body">
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Ngày tạo</span>
                            <span class="ui-info-val">
                                <?= !empty($receipt['created_at'])
                                    ? date('d/m/Y H:i', strtotime($receipt['created_at']))
                                    : '—' ?>
                            </span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Người tạo</span>
                            <span class="ui-info-val">
                                <?= htmlspecialchars($receipt['created_by_name'] ?? '—') ?>
                            </span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Ghi chú</span>
                            <span class="ui-info-val">
                                <?= htmlspecialchars($receipt['note'] ?? '—') ?>
                            </span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Trạng thái</span>
                            <span class="ui-info-val">
                                <?= InventoryModel::getStatusBadge($receipt['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-8">
            <div class="ui-card mb-0">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                    <h5>Danh sách sản phẩm nhập</h5>
                </div>
                <div style="overflow-x:auto">
                    <table class="ui-table admin-head">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Size</th>
                                <th class="right">Giá nhập</th>
                                <th class="center">SL</th>
                                <th class="right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $grandTotal = 0;
                        foreach ($items as $item):
                            $subtotal    = $item['import_price'] * $item['quantity'];
                            $grandTotal += $subtotal;
                        ?>
                        <tr>
                            <td style="font-weight:600">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </td>
                            <td>
                                <span class="ui-badge neutral" style="font-size:11px">
                                    <?= htmlspecialchars($item['size']) ?>
                                </span>
                            </td>
                            <td class="right muted">
                                <?= number_format($item['import_price'], 0, ',', '.') ?>đ
                            </td>
                            <td class="center">
                                <span class="ui-badge info">×<?= (int)$item['quantity'] ?></span>
                            </td>
                            <td class="right price">
                                <?= number_format($subtotal, 0, ',', '.') ?>đ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="ui-card-body pt-2">
                    <div class="ui-sum-row total">
                        <span>Tổng giá trị phiếu</span>
                        <span><?= number_format($grandTotal, 0, ',', '.') ?>đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2 mt-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
           class="ui-btn-outline sm">← Quay lại</a>
        <?php if ($receipt['status'] === 'pending'): ?>
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-confirm&id=<?= $receipt['id'] ?>"
           class="ui-btn sm"
           style="background:linear-gradient(135deg,#38d9a9,#0ca678)"
           onclick="return confirm('Xác nhận nhập kho phiếu này?')">
            ✅ Xác nhận nhập kho
        </a>
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-cancel&id=<?= $receipt['id'] ?>"
           class="ui-btn sm"
           style="background:linear-gradient(135deg,#f76f8e,#db2777)"
           onclick="return confirm('Hủy phiếu này?')">
            ✕ Hủy phiếu
        </a>
        <?php endif; ?>
    </div>

</div>