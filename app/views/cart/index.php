<?php /* app/views/cart/index.php */ ?>
<div class="cart-page">
<div class="container">

    <h2 class="title" style='text-align:center'>Giỏ hàng của bạn</h2>

    <?php if (!empty($items)): ?>

    <form method="post" action="<?= BASE_URL ?>/index.php?url=cart" id="cartForm">
    <div class="cart-layout">

        <!-- Items -->
        <div class="ui-card">
            <div class="ui-card-head">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke-width="2" stroke-linecap="round">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <h5><?= count($items) ?> sản phẩm</h5>
            </div>

            <?php foreach ($items as $row):
                $stock    = (int)$row['stock'];
                $qty      = min((int)$row['quantity'], max(1, $stock));
                $price    = (float)$row['price'];
                $subtotal = $price * $qty;
            ?>
            <div class="cart-item">

                <div class="item-img">
                    <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($row['image']) ?>"
                         alt="<?= htmlspecialchars($row['name']) ?>">
                </div>

                <div class="item-info">
                    <input type="hidden"
                           name="size_id[<?= $row['id'] ?>]"
                           value="<?= $row['size_id'] ?>">
                    <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                    <span class="item-size"><?= htmlspecialchars($row['size']) ?></span>
                    <?php if ($stock <= 0): ?>
                        <div class="item-warn oos">⚠ Hết hàng</div>
                    <?php elseif ($row['quantity'] > $stock): ?>
                        <div class="item-warn low">Chỉ còn <?= $stock ?> sản phẩm</div>
                    <?php endif; ?>
                </div>

                <div class="item-price">
                    <?= number_format($price, 0, ',', '.') ?>đ
                </div>

                <div class="ui-stepper">
                    <button type="button" class="ui-stepper-btn" data-action="minus"
                            data-target="qty-<?= $row['id'] ?>">−</button>
                    <input type="number"
                           class="qty-num qty-input"
                           id="qty-<?= $row['id'] ?>"
                           name="qty[<?= $row['id'] ?>]"
                           value="<?= $qty ?>"
                           min="1"
                           max="<?= $stock ?>"
                           data-price="<?= $price ?>"
                           data-row="<?= $row['id'] ?>">
                    <button type="button" class="ui-stepper-btn" data-action="plus"
                            data-target="qty-<?= $row['id'] ?>">+</button>
                </div>

                <div class="item-subtotal" id="sub-<?= $row['id'] ?>">
                    <?= number_format($subtotal, 0, ',', '.') ?>đ
                </div>

                <a href="<?= BASE_URL ?>/index.php?url=cart&remove=<?= $row['id'] ?>"
                   class="btn-remove" title="Xóa">✕</a>
            </div>
            <?php endforeach; ?>

            <div class="cart-update-bar">
                <a href="<?= BASE_URL ?>/index.php?url=shop" class="ui-btn-outline">
                    ← Tiếp tục mua sắm
                </a>
                <button type="submit" name="update" class="btn-update">
                    Cập nhật giỏ hàng
                </button>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-card">
            <div class="summary-head">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke-width="2" stroke-linecap="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <h5>Tóm tắt đơn hàng</h5>
            </div>
            <div class="summary-body">
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span id="subtotal-display"><?= number_format($total, 0, ',', '.') ?>đ</span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển</span>
                    <span>20.000đ</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span id="grand-total-display">
                        <?= number_format($total + 20000, 0, ',', '.') ?>đ
                    </span>
                </div>

                <a href="<?= BASE_URL ?>/index.php?url=checkout"
                   class="ui-btn <?= !$canCheckout ? 'disabled' : '' ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <?= !$canCheckout ? 'Không thể thanh toán' : 'Tiến hành thanh toán' ?>
                </a>

                <a href="<?= BASE_URL ?>/index.php?url=shop"
                   style="display:block;text-align:center;margin-top:12px;
                          font-size:13px;color:#888;text-decoration:none">
                    ← Tiếp tục mua sắm
                </a>
            </div>
        </div>

    </div>
    </form>

    <?php else: ?>

    <div class="empty-cart">
        <div class="empty-cart-icon">🛒</div>
        <h4>Giỏ hàng đang trống</h4>
        <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục</p>
        <a href="<?= BASE_URL ?>/index.php?url=shop" class="btn-shop">
            Mua sắm ngay →
        </a>
    </div>

    <?php endif; ?>
</div>
</div>

<script>
(function () {
    const SHIP = 20000;

    function parseVN(str) {
        return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
    }
    function fmtVN(n) { return n.toLocaleString('vi-VN') + 'đ'; }

    function recalc() {
        let subtotal = 0;
        document.querySelectorAll('.qty-input').forEach(inp => {
            const price = parseFloat(inp.dataset.price) || 0;
            const qty   = parseInt(inp.value) || 0;
            const sub   = price * qty;
            const subEl = document.getElementById('sub-' + inp.dataset.row);
            if (subEl) subEl.innerText = fmtVN(sub);
            subtotal += sub;
        });
        const subDisp = document.getElementById('subtotal-display');
        const totDisp = document.getElementById('grand-total-display');
        if (subDisp) subDisp.innerText = fmtVN(subtotal);
        if (totDisp) totDisp.innerText = fmtVN(subtotal + SHIP);
    }

    /* qty +/- buttons */
    document.querySelectorAll('.qty-step-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;
            const max = parseInt(input.max) || 9999;
            let v = parseInt(input.value) || 1;
            if (this.dataset.action === 'plus')  v = Math.min(v + 1, max);
            if (this.dataset.action === 'minus') v = Math.max(v - 1, 1);
            input.value = v;
            recalc();
        });
    });

    document.querySelectorAll('.qty-input').forEach(inp => {
        inp.addEventListener('input', recalc);
    });

    recalc();
})();
</script>