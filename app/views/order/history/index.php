<?php
function renderStatusBadge(string $status): string {
    return match($status) {
        'pending', 'processing' => '<span class="badge-status status-processing">Đang xử lý</span>',
        'processed'             => '<span class="badge-status status-processed">Đã xử lý</span>',
        'shipping'              => '<span class="badge-status status-shipping">Đang vận chuyển</span>',
        'shipped'               => '<span class="badge-status status-shipped">Đã giao</span>',
        'cancelled'             => '<span class="badge-status status-cancelled">Đã hủy</span>',
        default                 => '<span class="badge-status status-unknown">Không rõ</span>',
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

<div class="untree_co-section product-section before-footer-section" style="margin-top:-160px;">
    <h2 class="mb-4 container mt-5" style="text-align:center">Lịch sử các đơn hàng</h2>

    <!-- RADIO tabs -->
    <?php foreach (array_keys($tabs) as $key): ?>
        <input type="radio" name="order_page" id="page_<?= $key ?>"
               <?= $key === 'all' ? 'checked' : '' ?>>
    <?php endforeach; ?>

    <!-- TAB MENU -->
    <div class="controls1">
        <?php foreach ($tabs as $key => $label): ?>
            <label for="page_<?= $key ?>"><?= $label ?></label>
        <?php endforeach; ?>
    </div>

    <div class="container book" style="min-height:600px">
        <?php foreach ($tabs as $key => $label):
            $list = $pages[$key] ?? [];
        ?>
        <div class="page page_<?= $key ?>">
            <div class="p-4 border bg-white rounded-3">
                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $order): ?>
                        <tr>
                            <td>#P<?= str_pad((int)$order['id'], 3, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                            <td><?= number_format($order['total_price'], 0, ',', '.') ?>đ</td>
                            <td><?= renderStatusBadge($order['status']) ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/index.php?url=orders-detail&id=<?= (int)$order['id'] ?>"
                                   class="btn btn-sm btn-outline-success">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Không có đơn hàng</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>