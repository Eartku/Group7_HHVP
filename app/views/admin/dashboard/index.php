<?php $pageTitle = 'Dashboard'; ?>

<div class="container-fluid py-4">

    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Dashboard</h2>
        <span class="ui-badge neutral" style="font-size:13px;padding:6px 14px">
            📅 <?= date('d/m/Y — H:i') ?>
        </span>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
               class="ui-card mb-0 text-decoration-none d-block"
               style="transition:transform .2s"
               onmouseover="this.style.transform='translateY(-3px)'"
               onmouseout="this.style.transform=''">
                <div class="ui-card-head" style="background:linear-gradient(135deg,#4f8ef7,#2563eb)">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h5>Khách hàng</h5>
                </div>
                <div class="ui-card-body text-center py-3">
                    <div style="font-size:2.2rem;font-weight:700;color:#2563eb;line-height:1">
                        <?= number_format($totalUsers) ?>
                    </div>
                    <div class="ui-badge info mt-2">Xem tất cả →</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="<?= BASE_URL ?>/index.php?url=admin-products"
               class="ui-card mb-0 text-decoration-none d-block"
               style="transition:transform .2s"
               onmouseover="this.style.transform='translateY(-3px)'"
               onmouseout="this.style.transform=''">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <h5>Sản phẩm</h5>
                </div>
                <div class="ui-card-body text-center py-3">
                    <div style="font-size:2.2rem;font-weight:700;color:var(--brand);line-height:1">
                        <?= number_format($totalProducts) ?>
                    </div>
                    <div class="ui-badge success mt-2">Xem tất cả →</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
               class="ui-card mb-0 text-decoration-none d-block"
               style="transition:transform .2s"
               onmouseover="this.style.transform='translateY(-3px)'"
               onmouseout="this.style.transform=''">
                <div class="ui-card-head" style="background:linear-gradient(135deg,#f7c948,#d97706)">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <h5>Đơn hàng</h5>
                </div>
                <div class="ui-card-body text-center py-3">
                    <div style="font-size:2.2rem;font-weight:700;color:#d97706;line-height:1">
                        <?= number_format($totalOrders) ?>
                    </div>
                    <div class="ui-badge warning mt-2">Xem tất cả →</div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="<?= BASE_URL ?>/index.php?url=admin-categories"
               class="ui-card mb-0 text-decoration-none d-block"
               style="transition:transform .2s"
               onmouseover="this.style.transform='translateY(-3px)'"
               onmouseout="this.style.transform=''">
                <div class="ui-card-head" style="background:linear-gradient(135deg,#f76f8e,#db2777)">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    <h5>Danh mục</h5>
                </div>
                <div class="ui-card-body text-center py-3">
                    <div style="font-size:2.2rem;font-weight:700;color:#db2777;line-height:1">
                        <?= number_format($totalCategories) ?>
                    </div>
                    <div class="ui-badge danger mt-2">Xem tất cả →</div>
                </div>
            </a>
        </div>

    </div>

    <!-- ── Bottom Row ── -->
    <div class="row g-3">

        <!-- Sản phẩm theo danh mục -->
        <div class="col-md-5">
            <div class="ui-card mb-0 h-100">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <h5>Sản phẩm theo danh mục</h5>
                </div>
                <div class="ui-card-body">
                    <?php if (!empty($categoryStats)):
                        $maxCount = max(array_column($categoryStats, 'total'));
                        foreach ($categoryStats as $cat): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:13px;font-weight:600;color:var(--text-dark)">
                                <?= htmlspecialchars($cat['name']) ?>
                            </span>
                            <span class="ui-badge neutral" style="font-size:11px;padding:2px 8px">
                                <?= $cat['total'] ?> sp
                            </span>
                        </div>
                        <div style="height:8px;background:var(--border);border-radius:99px;overflow:hidden">
                            <div style="
                                height:100%;
                                width:<?= $maxCount > 0 ? round($cat['total'] / $maxCount * 100) : 0 ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                        <p class="ui-subtitle mb-0">Chưa có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Đơn hàng theo trạng thái -->
        <div class="col-md-4">
            <div class="ui-card mb-0 h-100">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    <h5>Đơn hàng theo trạng thái</h5>
                </div>
                <div class="ui-card-body">
                    <?php
                    $statusConfig = [
                        'processing' => ['label' => 'Đang xử lý',     'cls' => 'processing'],
                        'processed'  => ['label' => 'Đã xử lý',        'cls' => 'processed'],
                        'shipping'   => ['label' => 'Đang vận chuyển', 'cls' => 'shipping'],
                        'shipped'    => ['label' => 'Đã giao',          'cls' => 'shipped'],
                        'cancelled'  => ['label' => 'Đã hủy',           'cls' => 'cancelled'],
                    ];
                    if (!empty($orderStats)):
                        foreach ($statusConfig as $key => $cfg):
                            $count = $orderStats[$key] ?? 0;
                            if ($count <= 0) continue;
                    ?>
                    <div class="ui-info-row">
                        <div class="ui-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <span class="ui-badge <?= $cfg['cls'] ?> lg"><?= $cfg['label'] ?></span>
                        </div>
                        <div style="font-size:1.3rem;font-weight:700;color:var(--text-dark)">
                            <?= $count ?>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                        <p class="ui-subtitle mb-0">Chưa có đơn hàng</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Truy cập nhanh -->
        <div class="col-md-3">
            <div class="ui-card mb-0 h-100">
                <div class="ui-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    <h5>Truy cập nhanh</h5>
                </div>
                <div class="ui-card-body d-flex flex-column gap-2">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-products"
                       class="ui-btn full sm">
                        + Thêm sản phẩm
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
                       class="ui-btn full sm"
                       style="background:linear-gradient(135deg,#4f8ef7,#2563eb)">
                        + Tạo phiếu nhập
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
                       class="ui-btn full sm"
                       style="background:linear-gradient(135deg,#f7c948,#d97706)">
                        + Xem đơn hàng mới
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="ui-btn-outline full sm">
                        + Quản lý khách hàng
                    </a>
                    <hr style="border-color:var(--border);margin:4px 0">
                    <a href="<?= BASE_URL ?>/index.php?url=logout"
                       class="ui-btn-danger"
                       onclick="return confirm('Đăng xuất?')">
                        ⏻ Đăng xuất
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>