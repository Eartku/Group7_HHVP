<?php $pageTitle = 'Dashboard'; ?>

<div class="container-fluid py-4">

    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard</h3>
        <span class="text-muted small">
            <?= date('d/m/Y H:i') ?>
        </span>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <img src="<?= BASE_URL ?>/images/user.png"
                             width="28" style="filter:invert(0)">
                    </div>
                    <div>
                        <div class="text-muted small">Khách hàng</div>
                        <div class="fw-bold fs-4"><?= number_format($totalUsers) ?></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="small text-primary">Xem tất cả →</a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <img src="<?= BASE_URL ?>/images/bag.svg"
                             width="28">
                    </div>
                    <div>
                        <div class="text-muted small">Sản phẩm</div>
                        <div class="fw-bold fs-4"><?= number_format($totalProducts) ?></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?= BASE_URL ?>/index.php?url=admin/products"
                       class="small text-success">Xem tất cả →</a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <img src="<?= BASE_URL ?>/images/cart.svg"
                             width="28">
                    </div>
                    <div>
                        <div class="text-muted small">Đơn hàng</div>
                        <div class="fw-bold fs-4"><?= number_format($totalOrders) ?></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
                       class="small text-warning">Xem tất cả →</a>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <img src="<?= BASE_URL ?>/images/truck.svg"
                             width="28">
                    </div>
                    <div>
                        <div class="text-muted small">Danh mục</div>
                        <div class="fw-bold fs-4"><?= number_format($totalCategories) ?></div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-categories"
                       class="small text-info">Xem tất cả →</a>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">

        <!-- Thống kê sản phẩm theo danh mục -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-500">
                    Sản phẩm theo danh mục
                </div>
                <div class="card-body">
                    <?php if (!empty($categoryStats)): ?>
                        <?php
                        $maxCount = max(array_column($categoryStats, 'total'));
                        $colors   = ['bg-success', 'bg-primary', 'bg-warning',
                                     'bg-info', 'bg-danger', 'bg-secondary'];
                        ?>
                        <?php foreach ($categoryStats as $i => $cat): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-500">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </span>
                                <span class="small text-muted">
                                    <?= $cat['total'] ?> sản phẩm
                                </span>
                            </div>
                            <div class="progress" style="height:8px">
                                <div class="progress-bar <?= $colors[$i % count($colors)] ?>"
                                     style="width:<?= $maxCount > 0 ? round($cat['total'] / $maxCount * 100) : 0 ?>%">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">Chưa có dữ liệu</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thống kê đơn hàng theo trạng thái -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-500">
                    Đơn hàng theo trạng thái
                </div>
                <div class="card-body">
                    <?php
                    $statusConfig = [
                        'processing' => ['label' => 'Đang xử lý',      'class' => 'bg-warning'],
                        'processed'  => ['label' => 'Đã xử lý',         'class' => 'bg-primary'],
                        'shipping'   => ['label' => 'Đang vận chuyển',  'class' => 'bg-info'],
                        'shipped'    => ['label' => 'Đã giao',           'class' => 'bg-success'],
                        'cancelled'  => ['label' => 'Đã hủy',            'class' => 'bg-danger'],
                    ];
                    ?>
                    <?php if (!empty($orderStats)): ?>
                        <?php foreach ($statusConfig as $key => $cfg):
                            $count = $orderStats[$key] ?? 0;
                            if ($count <= 0) continue;
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge <?= $cfg['class'] ?> me-2">
                                <?= $cfg['label'] ?>
                            </span>
                            <span class="fw-bold"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">Chưa có đơn hàng</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Truy cập nhanh -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 fw-500">
                    Truy cập nhanh
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="<?= BASE_URL ?>/index.php?url=admin/products"
                       class="btn btn-outline-success btn-sm text-start">
                        + Thêm sản phẩm
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
                       class="btn btn-outline-primary btn-sm text-start">
                        + Tạo phiếu nhập
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders"
                       class="btn btn-outline-warning btn-sm text-start">
                        Xem đơn hàng mới
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="btn btn-outline-info btn-sm text-start">
                        Quản lý khách hàng
                    </a>
                    <hr class="my-1">
                    <a href="<?= BASE_URL ?>/index.php?url=logout"
                       class="btn btn-outline-danger btn-sm text-start"
                       onclick="return confirm('Đăng xuất?')">
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>