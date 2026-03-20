<?php /* app/views/shop/index.php */ ?>

<!-- Search + Category tabs -->
<div class="container mt-4">

    <form method="GET" action="<?= BASE_URL ?>/index.php" class="d-flex gap-2 mb-4">
        <input type="hidden" name="url" value="shop">
        <input type="hidden" name="category" value="<?= $categoryId ?>">
        <input type="text" name="search" class="form-control"
               placeholder="Tìm kiếm sản phẩm..."
               value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-dark">Tìm</button>
        <?php if ($search !== ''): ?>
            <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $categoryId ?>"
               class="btn btn-outline-secondary">✕</a>
        <?php endif; ?>
    </form>

    <!-- Category tabs -->
    <div class="d-flex gap-2 flex-wrap mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=shop&category=0<?= $search ? '&search=' . urlencode($search) : '' ?>"
           class="btn btn-sm <?= $categoryId === 0 ? 'btn-dark' : 'btn-outline-dark' ?>">
            Tất cả
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= (int)$cat['id'] ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
               class="btn btn-sm <?= $categoryId === (int)$cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Active filter tags -->
    <?php if ($search !== '' || $categoryId > 0): ?>
    <div class="mb-3 text-muted small">
        Đang lọc:
        <?php if ($categoryId > 0): ?>
            <span class="badge bg-dark"><?= htmlspecialchars($catName) ?></span>
        <?php endif; ?>
        <?php if ($search !== ''): ?>
            <span class="badge bg-secondary">"<?= htmlspecialchars($search) ?>"</span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/index.php?url=shop" class="ms-1 text-danger">Xóa tất cả</a>
    </div>
    <?php endif; ?>

</div>

<!-- Products — dùng đúng class product-section / product-item từ style.css -->
<div class="product-section">
    <div class="container">
        <div class="row">

        <?php if (empty($products)): ?>
            <div class="col-12 text-center text-muted py-5">
                <h4>Không tìm thấy sản phẩm nào</h4>
                <p>Vui lòng thử lại với bộ lọc khác</p>
                <a href="<?= BASE_URL ?>/index.php?url=shop" class="btn btn-dark mt-2">Xem tất cả</a>
            </div>

        <?php else: ?>

            <?php foreach ($products as $p):
                $id           = (int)$p['id'];
                $name         = htmlspecialchars($p['name']);
                $stock        = (int)($p['total_stock'] ?? 0);
                $isOutOfStock = $stock <= 0;
                $avgImport    = (float)($p['avg_import_price'] ?? 0);
                $profitRate   = (float)($p['profit_rate']      ?? 0);
                $priceAdjust  = (float)($p['price_adjust']     ?? 0);
                $salePrice    = round(($avgImport + $priceAdjust) * (1 + $profitRate / 100), -3);
                $price        = number_format($salePrice, 0, ',', '.');
                $imagePath    = !empty($p['image'])
                    ? BASE_URL . '/images/' . htmlspecialchars($p['image'])
                    : BASE_URL . '/images/placeholder.png';
            ?>
            <div class="col-12 col-md-6 col-lg-3 mb-5">
                <div class="product-item text-center h-100">

                    <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= $id ?>">
                        <img src="<?= $imagePath ?>"
                             class="product-thumbnail img-fluid"
                             loading="lazy"
                             alt="<?= $name ?>">
                    </a>

                    <h3 class="product-title mt-3"><?= $name ?></h3>

                    <strong class="product-price d-block mb-1">
                        <?= $salePrice > 0 ? $price . 'đ' : 'Liên hệ' ?>
                    </strong>

                    <div class="<?= $isOutOfStock ? 'text-danger' : 'text-success' ?> mb-2 small">
                        <?= $isOutOfStock ? 'Hết hàng' : 'Còn hàng' ?>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm <?= $isOutOfStock ? 'btn-secondary' : 'btn-dark' ?>"
                                <?= $isOutOfStock ? 'disabled' : '' ?>
                                data-product-id="<?= $id ?>">
                            <img src="<?= BASE_URL ?>/images/cart.svg" width="18" alt="cart">
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= $id ?>"
                           class="btn btn-sm btn-outline-dark">
                            Chi tiết
                        </a>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>

        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-2 mb-5">
            <?php
            $qp        = $_GET;
            $qp['url'] = 'shop';
            unset($qp['page']);
            $qs = '?' . http_build_query($qp) . '&';
            ?>
            <?php if ($page > 1): ?>
                <a href="<?= $qs ?>page=<?= $page - 1 ?>"
                   class="btn btn-sm btn-outline-dark mx-1">&laquo; Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= $qs ?>page=<?= $i ?>"
                   class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= $qs ?>page=<?= $page + 1 ?>"
                   class="btn btn-sm btn-outline-dark mx-1">Sau &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<div id="toast"></div>

<script>
document.querySelectorAll('[data-product-id]').forEach(btn => {
    btn.addEventListener('click', function () {
        const productId = this.dataset.productId;
        fetch('<?= BASE_URL ?>/index.php?url=cart-add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&size_id=0&qty=1`
        })
        .then(r => r.json())
        .then(data => {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.innerText = data.message ?? (data.ok ? 'Đã thêm vào giỏ!' : 'Thêm thất bại');
            toast.className = 'show ' + (data.ok ? 'success' : 'error');
            setTimeout(() => toast.className = '', 2500);
        });
    });
});
</script>