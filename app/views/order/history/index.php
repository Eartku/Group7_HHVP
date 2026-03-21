<?php /* app/views/order/history/index.php */

function renderStatusBadge(string $status): string {
    return match($status) {
        'pending', 'processing' => '<span class="ui-badge badge-processing">Đang xử lý</span>',
        'processed'             => '<span class="ui-badge badge-processed">Đã xử lý</span>',
        'shipping'              => '<span class="ui-badge badge-shipping">Đang vận chuyển</span>',
        'shipped'               => '<span class="ui-badge badge-shipped">Đã giao</span>',
        'cancelled'             => '<span class="ui-badge badge-cancelled">Đã hủy</span>',
        default                 => '<span class="ui-badge badge-unknown">Không rõ</span>',
    };
}

$tabs = [
    'all'        => 'Tất cả',
    'processing' => 'Đang xử lý',
    'processed'  => 'Đã xử lý',
    'shipping'   => 'Đang vận chuyển',
    'shipped'    => 'Đã giao',
    'cancelled'  => 'Đã hủy',
];
?>


<div class="orders-page">
<div class="container">

    <h2 class="orders-title" style="text-align:center; margin-top: 45px">Lịch sử đơn hàng</h2>

    <!-- Hidden radio inputs (phải đặt TRƯỚC tab-bar và book để CSS sibling selector hoạt động) -->
    <?php foreach (array_keys($tabs) as $key): ?>
        <input type="radio" name="order_page" id="page_<?= $key ?>"
               <?= $key === 'all' ? 'checked' : '' ?>>
    <?php endforeach; ?>

    <!-- Tab bar -->
    <div class="ui-tab-bar">
        <?php foreach ($tabs as $key => $label): ?>
            <label for="page_<?= $key ?>"><?= $label ?></label>
        <?php endforeach; ?>
    </div>

    <!-- Pages -->
    <div class="ui-tab-book">
        <?php foreach ($tabs as $key => $label):
            $list = $pages[$key] ?? [];
        ?>
        <div class="ui-tab-page page_<?= $key ?>">
<div class="ui-card">

                <div class="ui-card-head">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke-width="2" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <h5><?= $label ?> — <?= count($list) ?> đơn</h5>
                </div>

                <?php if (!empty($list)): ?>
                <div style="overflow-x:auto">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $order): ?>
                            <tr>
                                <td>
                                    <span class="order-id">
                                        #P<?= str_pad((int)$order['id'], 3, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="order-date">
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="order-price">
                                    <?= number_format($order['total_price'], 0, ',', '.') ?>đ
                                </td>
                                <td><?= renderStatusBadge($order['status']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/index.php?url=orders-detail&id=<?= (int)$order['id'] ?>"
                                       class="ui-btn-outline">
                                        Xem →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php else: ?>
                <div class="empty-orders ui-card-body text-center">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                         stroke="#6b7c2e" stroke-width="1.5" stroke-linecap="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <p>Không có đơn hàng nào</p>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>
</div>
