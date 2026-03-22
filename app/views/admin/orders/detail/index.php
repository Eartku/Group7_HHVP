<?php /* app/views/admin/orders/detail/index.php */ ?>

<div class="container-fluid py-4" style="max-width:1000px">
    

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-orders">Đơn hàng</a>
        <span class="sep">›</span>
        <span>#P<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></span>
    </div>
          <?php if (!empty($updated)): ?>
                <div class="ui-alert success mb-4">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Cập nhật trạng thái đơn hàng thành công.
                </div>
                <?php endif; ?>
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="ui-title mb-1">
                Đơn hàng #P<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?>
            </h2>
            <p class="ui-subtitle mb-0">
                Đặt lúc <?= date('H:i — d/m/Y', strtotime($order['created_at'])) ?>
            </p>
        </div>
        <span class="ui-badge <?= $badge['class'] ?> lg"><?= $badge['label'] ?></span>
    </div>

    <div class="row g-4">
        

        <!-- LEFT: sản phẩm -->
        <div class="col-lg-7">
            <div class="ui-card mb-0">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <h5>Sản phẩm trong đơn</h5>
                </div>
                <div class="ui-card-body p-0">
                    <?php foreach ($items as $item): ?>
                    <div class="d-flex align-items-center gap-3 p-3"
                         style="border-bottom:1px solid var(--border)">
                        <div style="width:56px;height:56px;border-radius:8px;overflow:hidden;flex-shrink:0;background:var(--bg-soft)">
                            <?php if (!empty($item['base_img'])): ?>
                            <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($item['base_img']) ?>"
                                 style="width:100%;height:100%;object-fit:cover" alt="">
                            <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.4rem">🌿</div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <div style="font-weight:600;font-size:13px">
                                <?= htmlspecialchars($item['name']) ?>
                            </div>
                            <?php if (!empty($item['size_name'])): ?>
                            <span class="ui-badge neutral mt-1" style="font-size:11px;padding:2px 8px">
                                Size: <?= htmlspecialchars($item['size_name']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div style="font-size:12px;color:#777">×<?= (int)$item['quantity'] ?></div>
                            <div style="font-size:12px;color:#555"><?= number_format($item['price'], 0, ',', '.') ?>đ</div>
                            <div style="font-weight:700;font-size:13px;color:var(--text-dark)">
                                <?= number_format($item['row_total'], 0, ',', '.') ?>đ
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="p-3">
                        <div class="ui-sum-row">
                            <span>Tạm tính</span>
                            <span><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="ui-sum-row">
                            <span>Phí vận chuyển</span>
                            <span><?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="ui-sum-row total">
                            <span>Tổng cộng</span>
                            <span><?= number_format($order['total_price'], 0, ',', '.') ?>đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: thông tin + cập nhật trạng thái -->
        <div class="col-lg-5 d-flex flex-column gap-4">

            <!-- Cập nhật trạng thái -->
            <div class="ui-card mb-0">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <h5>Cập nhật trạng thái</h5>
                </div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="ui-label mb-0">Hiện tại:</span>
                        <span class="ui-badge <?= $badge['class'] ?> lg"><?= $badge['label'] ?></span>
                    </div>
                    <form method="POST"
                          action="<?= BASE_URL ?>/index.php?url=admin-orders-update&id=<?= (int)$order['id'] ?>">
                        <div class="ui-field">
                            <label class="ui-label">Đổi sang</label>
                            <select name="status" class="ui-input">
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
                                        <?= $order['status'] === $val ? 'selected' : '' ?>>
                                    <?= $lbl ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="ui-btn sm full"
                                onclick="return confirm('Cập nhật trạng thái đơn hàng?')">
                            Lưu trạng thái
                        </button>
                    </form>
                </div>
            </div>

            <!-- Thông tin giao hàng -->
            <div class="ui-card mb-0">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <h5>Thông tin giao hàng</h5>
                </div>
                <div class="ui-card-body">
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Người nhận</span>
                            <span class="ui-info-val"><?= htmlspecialchars($order['fullname']) ?></span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Điện thoại</span>
                            <span class="ui-info-val"><?= htmlspecialchars($order['phone'] ?? '—') ?></span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Địa chỉ</span>
                            <span class="ui-info-val"><?= htmlspecialchars($order['address']) ?></span>
                        </div>
                    </div>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Thanh toán</span>
                            <span class="ui-info-val">
                                <?= htmlspecialchars(strtoupper($order['payment_method'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($order['note'])): ?>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div>
                            <span class="ui-info-lbl">Ghi chú</span>
                            <span class="ui-info-val"><?= htmlspecialchars($order['note']) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
           class="ui-btn-outline pill">← Quay lại danh sách</a>
    </div>

</div>