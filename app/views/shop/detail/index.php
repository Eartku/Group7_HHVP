<?php /* app/views/shop/detail/index.php */ ?>

<div class="detail-page">
<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb-wrap">
        <a href="<?= BASE_URL ?>/index.php?url=shop">Cửa hàng</a>
        <span class="breadcrumb-sep">›</span>
        <?php if (!empty($product['category_name'])): ?>
        <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $product['category_id'] ?>">
            <?= htmlspecialchars($product['category_name']) ?>
        </a>
        <span class="breadcrumb-sep">›</span>
        <?php endif; ?>
        <span style="color:#555"><?= htmlspecialchars($product['name']) ?></span>
    </div>

    <!-- Main grid -->
    <div class="detail-grid">

        <!-- Image panel -->
        <div class="img-panel">
            <div class="main-img-wrap">
                <img id="mainImage"
                     src="<?= BASE_URL ?>/images/<?= htmlspecialchars($mainImage) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <?php if (count($images) > 1): ?>
            <div class="thumbs-row" id="thumbs">
                <?php foreach ($images as $i => $img): ?>
                <div class="thumb-item <?= $i === 0 ? 'active' : '' ?>"
                     data-src="<?= BASE_URL ?>/images/<?= htmlspecialchars($img) ?>">
                    <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($img) ?>"
                         alt="thumb">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info panel -->
        <div class="info-panel">
            <div class="product-badge">
                <?= htmlspecialchars($product['category_name'] ?? 'Sản phẩm') ?>
            </div>

            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="price-display" id="priceDisplay"
                 data-base="<?= (float)$product['sale_price'] ?>">
                <?= number_format($product['sale_price'], 0, ',', '.') ?>đ
            </div>

            <?php if ($totalStock <= 0): ?>
            <div class="stock-info stock-oos">
                <span class="stock-dot"></span> Hết hàng
            </div>
            <?php else: ?>
            <div class="stock-info stock-ok">
                <span class="stock-dot"></span>
                Còn <?= $totalStock ?> sản phẩm
            </div>
            <?php endif; ?>

            <?php if (!empty($product['description'])): ?>
            <div class="desc-text">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            <?php endif; ?>

            <!-- Size -->
            <?php if (!empty($sizes)): ?>
            <div class="mb-4">
                <label class="ui-label">Chọn size</label>
                <div class="ui-size-grid" id="sizeGrid">
                    <?php foreach ($sizes as $s): ?>
                    <button type="button"
                            class="ui-size-btn"
                            <?= $s['stock'] <= 0 ? 'disabled' : '' ?>
                            data-size-id="<?= $s['size_id'] ?>"
                            data-adjust="<?= (float)$s['price_adjust'] ?>"
                            data-stock="<?= (int)$s['stock'] ?>">
                        <?= htmlspecialchars($s['size']) ?>
                        <?php if ($s['price_adjust'] > 0): ?>
                            <small>(+<?= number_format($s['price_adjust'],0,',','.') ?>đ)</small>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quantity -->
            <div class="mb-0">
                <label class="ui-label">Số lượng</label>
                <div class="qty-wrap">
                    <button type="button" class="qty-btn" id="qtyMinus">−</button>
                    <input type="number" id="qtyInput" value="1" min="1"
                           max="<?= $totalStock ?>">
                    <button type="button" class="qty-btn" id="qtyPlus">+</button>
                </div>
            </div>

            <!-- Actions -->
            <div class="action-row">
                <button id="addCartBtn"
                        class="btn-add-cart"
                        <?php if (!isset($_SESSION['user'])): ?>
                            onclick="showLoginAlert(event); return false;"
                        <?php endif; ?>
                        <?= $totalStock <= 0 ? 'disabled' : '' ?>
                        data-product-id="<?= (int)$product['id'] ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <?= $totalStock <= 0 ? 'Hết hàng' : 'Thêm vào giỏ' ?>
                </button><!-- ✅ thêm đóng button -->
                <button class="btn-wishlist" title="Yêu thích">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke-width="2" stroke-linecap="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                 a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Related -->
    <?php if (!empty($related)): ?>
    <div class="related-section">
        <h2 class="section-title">Sản phẩm liên quan</h2>
        <div class="related-grid">
            <?php foreach ($related as $r): ?>
            <div class="related-card">
                <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= (int)$r['id'] ?>">
                    <div class="related-card-img">
                        <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($r['image']) ?>"
                             alt="<?= htmlspecialchars($r['name']) ?>" loading="lazy">
                    </div>
                </a>
                <div class="related-card-body">
                    <div class="related-card-name"><?= htmlspecialchars($r['name']) ?></div>
                    <div class="related-card-price">
                        <?= number_format($r['sale_price'], 0, ',', '.') ?>đ
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= (int)$r['id'] ?>"
                       class="related-card-link">Xem chi tiết</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<div id="toast"></div>

<script>
function showLoginAlert(e) {
    if (e) e.preventDefault();
    document.getElementById('loginModal').style.display = 'flex';
}
function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}
document.getElementById('loginModal').addEventListener('click', function(e) {
    if (e.target === this) closeLoginModal();
});

<?php if (!isset($_SESSION['user'])): ?>
// Chặn tất cả nút giỏ hàng nếu chưa login
document.querySelectorAll('[data-product-id]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        showLoginAlert(e);
    });
});
<?php endif; ?>
</script>
<script>
(function () {
    /* ── Thumbnails ── */
    const mainImg = document.getElementById('mainImage');
    document.querySelectorAll('.thumb-item').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            mainImg.src = this.dataset.src;
        });
    });

    /* ── Size selector ── */
    let selectedSizeId = null;
    let selectedAdjust = 0;
    let selectedStock  = <?= (int)$totalStock ?>;

    const basePrice  = parseFloat(document.getElementById('priceDisplay').dataset.base) || 0;
    const priceEl    = document.getElementById('priceDisplay');
    const qtyInput   = document.getElementById('qtyInput');
    const addCartBtn = document.getElementById('addCartBtn');

    // ✅ Auto-select first available size
    const firstAvail = document.querySelector('.ui-size-btn:not(:disabled)');
    if (firstAvail) firstAvail.click();

    // ✅ Đổi .size-btn → .ui-size-btn tất cả chỗ
    document.querySelectorAll('.ui-size-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.ui-size-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');

            selectedSizeId = this.dataset.sizeId;
            selectedAdjust = parseFloat(this.dataset.adjust) || 0;
            selectedStock  = parseInt(this.dataset.stock)  || 0;

            priceEl.innerText = (basePrice + selectedAdjust).toLocaleString('vi-VN') + 'đ';
            qtyInput.max = selectedStock;
            if (parseInt(qtyInput.value) > selectedStock) qtyInput.value = selectedStock;

            if (addCartBtn) {
                addCartBtn.disabled = selectedStock <= 0;
                addCartBtn.innerText = selectedStock <= 0 ? 'Hết hàng' : 'Thêm vào giỏ';
            }
        });
    });

    /* ── Qty +/- ── */
    document.getElementById('qtyMinus')?.addEventListener('click', () => {
        const v = parseInt(qtyInput.value) || 1;
        if (v > 1) qtyInput.value = v - 1;
    });
    document.getElementById('qtyPlus')?.addEventListener('click', () => {
        const v   = parseInt(qtyInput.value) || 1;
        const max = parseInt(qtyInput.max)   || selectedStock;
        if (v < max) qtyInput.value = v + 1;
    });

    /* ── Add to cart ── */
    addCartBtn?.addEventListener('click', function () {
        const productId = this.dataset.productId;
        const sizeId    = selectedSizeId || 0;
        const qty       = parseInt(qtyInput.value) || 1;

        fetch('<?= BASE_URL ?>/index.php?url=cart-add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&size_id=${sizeId}&qty=${qty}`
        })
        .then(r => r.json())
        .then(data => {
            const toast = document.getElementById('toast');
            toast.innerText = data.message ?? (data.ok ? 'Đã thêm vào giỏ!' : 'Thêm thất bại');
            toast.className = 'show ' + (data.ok ? 'success' : 'error');
            setTimeout(() => toast.className = '', 2500);
        });
    });
})();
</script>