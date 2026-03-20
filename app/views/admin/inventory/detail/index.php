<div class="container mt-4">
    <h3>Chi tiết phiếu nhập #<?= str_pad($receipt['id'], 3, '0', STR_PAD_LEFT) ?></h3>

    <?php if (isset($_GET['confirmed'])): ?>
        <div class="alert alert-success">Xác nhận nhập kho thành công!</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">Có lỗi xảy ra, vui lòng thử lại.</div>
    <?php endif; ?>

    <div class="card p-4 mb-4">
        <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($receipt['created_at'])) ?></p>
        <p><strong>Ghi chú:</strong> <?= htmlspecialchars($receipt['note'] ?? '—') ?></p>
        <p><strong>Trạng thái:</strong> <?= InventoryModel::getStatusBadge($receipt['status']) ?></p>
    </div>

    <h5>Danh sách sản phẩm nhập</h5>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Sản phẩm</th>
                <th>Size</th>
                <th>Giá nhập</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $grandTotal = 0;
        foreach ($items as $item):
            $subtotal    = $item['import_price'] * $item['quantity'];
            $grandTotal += $subtotal;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= htmlspecialchars($item['size']) ?></td>
                <td><?= number_format($item['import_price'], 0, ',', '.') ?>đ</td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="4" class="text-end">Tổng giá trị:</td>
                <td><?= number_format($grandTotal, 0, ',', '.') ?>đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="d-flex gap-2 mt-3">
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
           class="btn btn-secondary">← Quay lại</a>
        <?php if ($receipt['status'] === 'pending'): ?>
            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-confirm&id=<?= $receipt['id'] ?>"
               class="btn btn-success"
               onclick="return confirm('Xác nhận nhập kho?')">Xác nhận nhập kho</a>
            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-cancel&id=<?= $receipt['id'] ?>"
               class="btn btn-danger"
               onclick="return confirm('Hủy phiếu này?')">Hủy phiếu</a>
        <?php endif; ?>
    </div>
</div>