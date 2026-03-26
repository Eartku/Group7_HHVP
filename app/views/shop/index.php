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
<!-- Category tabs + Price filter cùng hàng -->
<div class="d-flex gap-2 flex-wrap align-items-center mb-4">

    <!-- Category tabs (giữ nguyên) -->
    <a href="<?= BASE_URL ?>/index.php?url=shop&category=0<?= $search ? '&search='.urlencode($search) : '' ?>"
       class="btn btn-sm <?= $categoryId === 0 ? 'btn-dark' : 'btn-outline-dark' ?>">
        Tất cả
    </a>
    <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= (int)$cat['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?><?= $priceMin||$priceMax ? '&price_min='.$priceMin.'&price_max='.$priceMax : '' ?>"
           class="btn btn-sm <?= $categoryId === (int)$cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>

    <!-- Dropdown lọc giá -->
    <div class="ms-auto position-relative" id="priceDropdownWrap">
        <button class="btn btn-sm <?= $priceMin||$priceMax ? 'btn-dark' : 'btn-outline-dark' ?>"
                type="button"
                onclick="document.getElementById('priceDropdown').classList.toggle('show')">
            <?php if ($priceMin || $priceMax): ?>
                <?= $priceMin ? number_format($priceMin,0,',','.') : '0' ?>đ
                —
                <?= $priceMax ? number_format($priceMax,0,',','.') : '∞' ?>đ
                &nbsp;✕
            <?php else: ?>
                Lọc theo giá ▾
            <?php endif; ?>
        </button>

        <div id="priceDropdown"
             style="display:none;position:absolute;right:0;top:calc(100% + 6px);
                    background:#fff;border:1px solid var(--border);border-radius:10px;
                    padding:16px;min-width:240px;z-index:999;box-shadow:0 4px 16px rgba(0,0,0,.1)">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url"      value="shop">
                <input type="hidden" name="category" value="<?= $categoryId ?>">
                <?php if ($search): ?>
                <input type="hidden" name="search"   value="<?= htmlspecialchars($search) ?>">
                <?php endif; ?>

               <label class="form-label small fw-semibold mb-1">Giá từ</label>
<input type="text" name="price_min" id="price_min_display" class="form-control form-control-sm mb-2"
       placeholder="VD: 50.000"
       value="<?= $priceMin ? number_format($priceMin, 0, ',', '.') : '' ?>">
<input type="hidden" name="price_min" id="price_min_value" value="<?= $priceMin ?: '' ?>">

<label class="form-label small fw-semibold mb-1">Giá đến</label>
<input type="text" name="price_max" id="price_max_display" class="form-control form-control-sm mb-3"
       placeholder="VD: 500.000"
       value="<?= $priceMax ? number_format($priceMax, 0, ',', '.') : '' ?>">
<input type="hidden" name="price_max" id="price_max_value" value="<?= $priceMax ?: '' ?>">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-grow-1">Áp dụng</button>
                    <?php if ($priceMin || $priceMax): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $categoryId ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                       class="btn btn-sm btn-outline-secondary">Xóa</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
// Đóng dropdown khi click ra ngoài
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('priceDropdownWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('priceDropdown').classList.remove('show');
    }
});
// Toggle show/hide
const pd = document.getElementById('priceDropdown');
if (pd) {
    pd.style.display = '';
    pd.classList.remove('show');
    // Dùng CSS class thay style
    const style = document.createElement('style');
    style.textContent = '#priceDropdown { display:none } #priceDropdown.show { display:block }';
    document.head.appendChild(style);
}
</script>

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
<div class="product-section " style="height: 450px;">
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
function formatPriceInput(displayId, hiddenId) {
    const display = document.getElementById(displayId);
    const hidden  = document.getElementById(hiddenId);
    if (!display || !hidden) return;

    display.addEventListener('input', function () {
        // Chỉ giữ lại số
        const raw = this.value.replace(/\D/g, '');
        hidden.value = raw;
        // Format có dấu chấm ngàn
        this.value = raw ? Number(raw).toLocaleString('vi-VN') : '';
    });
}

formatPriceInput('price_min_display', 'price_min_value');
formatPriceInput('price_max_display', 'price_max_value');
</script>