<?php /* app/views/checkout/thankyou.php */ ?>

<div class="thankyou-wrap">

    <div class="thankyou-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h2 class="thankyou-title">Đặt hàng thành công!</h2>

    <p class="thankyou-sub">
        Cảm ơn bạn đã tin tưởng <strong>BonSai</strong>.<br>
        Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.
    </p>

    <?php if (!empty($orderId)): ?>
    <div class="thankyou-order-id">
        Mã đơn hàng: #P<?= str_pad((int)$orderId, 3, '0', STR_PAD_LEFT) ?>
    </div>
    <?php endif; ?>

    <div class="thankyou-actions">
        <a href="<?= BASE_URL ?>/index.php?url=orders-detail&id=<?= $orderId ?>" class="ui-btn pill">
            Xem đơn hàng
        </a>
        <a href="<?= BASE_URL ?>/index.php?url=shop" class="ui-btn-outline pill">
            Tiếp tục mua sắm
        </a>
    </div>

</div>