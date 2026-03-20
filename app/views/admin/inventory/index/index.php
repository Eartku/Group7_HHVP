<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quản lý nhập kho</h3>
        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
           class="btn btn-success">+ Tạo phiếu nhập</a>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>Mã phiếu</th>
                <th>Ngày tạo</th>
                <th>Ghi chú</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($imports as $row): ?>
            <tr>
                <td>#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                <td><?= htmlspecialchars($row['note'] ?? '—') ?></td>
                <td><?= InventoryModel::getStatusBadge($row['status']) ?></td>
                <td class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-primary">Chi tiết</a>
                    <?php if ($row['status'] === 'pending'): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-confirm&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-success"
                           onclick="return confirm('Xác nhận nhập kho?')">Xác nhận</a>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-cancel&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Hủy phiếu này?')">Hủy</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-center mt-3">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&page=<?= $i ?>"
               class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>