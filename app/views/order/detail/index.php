<div class="container py-5" style="margin-top:60px;">

    <h2 class="mb-4">Chi tiết đơn hàng #<?= (int)$order['id'] ?></h2>

    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-success">Đã hủy đơn hàng thành công.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Không thể hủy đơn hàng. Vui lòng thử lại.</div>
    <?php endif; ?>

    <div class="row">

        <!-- SẢN PHẨM -->
        <div class="mb-4">
            <div class="card p-4 shadow-sm">
                <h4>Sản phẩm trong đơn</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($item['name']) ?>
                                x<?= (int)$item['quantity'] ?>
                            </td>
                            <td class="text-end">
                                <?= number_format($item['row_total'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>Phí vận chuyển</td>
                            <td class="text-end">
                                <?= number_format($order['shipping_fee'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Tổng cộng</td>
                            <td class="text-end">
                                <?= number_format($order['total_price'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4">Trạng thái</h5>
                <span class="badge <?= $badge['class'] ?>"><?= $badge['text'] ?></span>

                <?php if ($order['status'] === 'processing'): ?>
                    <form method="POST"
                          action="<?= BASE_URL ?>/index.php?url=orders-detail&id=<?= (int)$order['id'] ?>"
                          class="mt-3"
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                        <button type="submit" name="cancel_order" class="btn btn-danger btn-sm">
                            Hủy đơn hàng
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- THÔNG TIN ĐƠN -->
        <div>
            <div class="card p-4 shadow-sm">
                <h4>Thông tin đơn hàng</h4>
                <p><strong>Mã đơn:</strong> #<?= str_pad((int)$order['id'], 3, '0', STR_PAD_LEFT) ?></p>
                <p><strong>Ngày đặt:</strong>
                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                </p>
                <p><strong>Địa chỉ giao hàng:</strong>
                    <?= htmlspecialchars($order['address']) ?>
                </p>
                <p><strong>Phương thức thanh toán:</strong>
                    <?= htmlspecialchars(strtoupper($order['payment_method'])) ?>
                </p>
                <hr>
                <h5>Ghi chú</h5>
                <p><?= htmlspecialchars($order['note'] ?: 'Không có ghi chú') ?></p>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <a href="<?= BASE_URL ?>/index.php?url=orders-history" class="btn btn-dark">
            ← Quay lại lịch sử đơn
        </a>
    </div>

</div>