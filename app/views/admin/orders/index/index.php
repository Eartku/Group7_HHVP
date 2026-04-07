<?php /* app/views/admin/orders/index.php */ ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý đơn hàng</h2>
        <span class="ui-badge neutral" style="font-size:13px;padding:6px 14px">
            Tổng: <strong><?= $total ?? 0 ?></strong> đơn
        </span>
    </div>

    <!-- Search card -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <h5>Tìm kiếm & lọc</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url" value="admin-orders">
                <disv class="d-flex gap-2 flex-wrap">
                    <div class="ui-input-group flex-grow-1">
                        <input type="text" name="search"
                               value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Tìm mã ĐH, tên KH, email...">
                        <button type="submit" class="ui-input-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Lọc theo phường / xã -->
                <select name="ward" class="ui-input" style="width:auto;min-width:160px">
                    <option value="">Tất cả phường/xã</option>
                    <?php foreach ($address_options['wards'] as $w): ?>
                    <option value="<?= htmlspecialchars($w) ?>"
                            <?= ($ward_filter ?? '') === $w ? 'selected' : '' ?>>
                        <?= htmlspecialchars($w) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                    <select name="status" class="ui-input" style="width:auto;min-width:150px">
                        <option value="">Tất cả trạng thái</option>
                        <?php
                        $statusOpts = [
                            'processing' => 'Đang xử lý',
                            'processed'  => 'Đã xử lý',
                            'shipping'   => 'Đang giao',
                            'shipped'    => 'Đã giao',
                            'cancelled'  => 'Đã hủy',
                        ];
                        foreach ($statusOpts as $val => $lbl): ?>
                        <option value="<?= $val ?>"
                                <?= ($status_filter ?? '') === $val ? 'selected' : '' ?>>
                            <?= $lbl ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="start_date"
                           class="ui-input" style="width:auto"
                           value="<?= htmlspecialchars($start_date ?? '') ?>">
                    <input type="date" name="end_date"
                           class="ui-input" style="width:auto"
                           value="<?= htmlspecialchars($end_date ?? '') ?>">
                    <button type="submit" class="ui-btn sm">Lọc</button>
                    <?php if (!empty($search) || !empty($status_filter) || !empty($start_date) || !empty($end_date)): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
                       class="ui-btn-outline sm">✕ Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Table card -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <h5>Danh sách đơn hàng</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Ngày đặt</th>
                        <th class="right">Tổng tiền</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="7">
                        <div class="ui-empty py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <h4>Không tìm thấy đơn hàng</h4>
                            <p>Thử thay đổi bộ lọc</p>
                        </div>
                    </td>
                </tr>
                <?php else: foreach ($orders as $o):
                    $badge = OrderModel::getStatusBadge($o['status']);
                ?>
                <tr>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                            #P<?= str_pad($o['id'], 3, '0', STR_PAD_LEFT) ?>
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px">
                            <?= htmlspecialchars($o['fullname']) ?>
                        </div>
                        <div class="muted" style="font-size:12px">
                            <?= htmlspecialchars($o['email']) ?>
                        </div>
                    </td>
                    <td class="muted" style="font-size:12px;max-width:180px">
                        <?= htmlspecialchars($o['san_pham'] ?? '—') ?>
                    </td>
                    <td class="muted">
                        <?= !empty($o['created_at'])
                            ? date('d/m/Y', strtotime($o['created_at']))
                            : '—' ?>
                    </td>
                    <td class="right price">
                        <?= number_format($o['total_price'], 0, ',', '.') ?>đ
                    </td>
                    <td class="center">
                        <span class="ui-badge <?= $badge['class'] ?>">
                            <?= $badge['label'] ?>
                        </span>
                    </td>
                    <td class="center">
                        <a href="<?= BASE_URL ?>/index.php?url=admin-orders-detail&id=<?= $o['id'] ?>"
                           class="ui-btn-outline sm">Chi tiết</a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($total_pages) && $total_pages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php
                $qp = $_GET;
                $qp['url'] = 'admin-orders';
                unset($qp['page']);
                $qs = '?' . http_build_query($qp) . '&page=';
                $start_p = max(1, ($current_page ?? 1) - 2);
                $end_p   = min($total_pages, ($current_page ?? 1) + 2);
                if ($start_p > 1) echo '<a href="' . BASE_URL . '/index.php' . $qs . '1" class="ui-page-btn">«</a>';
                for ($i = $start_p; $i <= $end_p; $i++): ?>
                <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                   class="ui-page-btn <?= $i == ($current_page ?? 1) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor;
                if ($end_p < $total_pages) echo '<a href="' . BASE_URL . '/index.php' . $qs . $total_pages . '" class="ui-page-btn">»</a>';
                ?>
            </div>
            <p class="text-center ui-subtitle mb-0">
                Trang <strong><?= $current_page ?? 1 ?></strong> /
                <strong><?= $total_pages ?></strong>
            </p>
        </div>
        <?php endif; ?>
    </div>

</div>