<?php /* app/views/admin-products/index.php */ ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý sản phẩm</h2>
        <a href="<?= BASE_URL ?>/index.php?url=admin-products/create" class="ui-btn sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm sản phẩm
        </a>
    </div>

    <!-- Search card -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <h5>Tìm kiếm & lọc</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url" value="admin-products">
                <div class="d-flex gap-2 flex-wrap">
                    <div class="ui-input-group flex-grow-1">
                        <input type="text" name="search"
                               value="<?= htmlspecialchars($search ?? '') ?>"
                               placeholder="Tìm tên sản phẩm...">
                        <button type="submit" class="ui-input-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                    <select name="category" class="ui-input" style="width:auto;min-width:160px">
                        <option value="0">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                <?= ($categoryId ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status_filter" class="ui-input" style="width:auto;min-width:140px">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active"
                                <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>
                            Đang bán
                        </option>
                        <option value="inactive"
                                <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>>
                            Ngừng bán
                        </option>
                    </select>
                    <select name="stock_filter" class="ui-input" style="width:auto;min-width:140px">
                        <option value="">Tất cả tồn kho</option>
                        <option value="instock"
                                <?= ($stockFilter ?? '') === 'instock' ? 'selected' : '' ?>>
                            Còn hàng
                        </option>
                        <option value="outofstock"
                                <?= ($stockFilter ?? '') === 'outofstock' ? 'selected' : '' ?>>
                            Hết hàng
                        </option>
                    </select>
                    <button type="submit" class="ui-btn sm">Lọc</button>
                    <?php if (!empty($search) || ($categoryId ?? 0) > 0): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-products"
                       class="ui-btn-outline sm">✕ Xóa lọc</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Table card -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <h5>Danh sách sản phẩm</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th class="right">Giá bán</th>
                        <th class="right">Tồn kho</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8">
                        <div class="ui-empty py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                            </svg>
                            <h4>Không tìm thấy sản phẩm</h4>
                            <p>Thử thay đổi bộ lọc hoặc thêm sản phẩm mới</p>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-products/create"
                               class="ui-btn sm">+ Thêm sản phẩm</a>
                        </div>
                    </td>
                </tr>
                <?php else: foreach ($products as $p):
                    $statusCls   = ($p['status'] ?? 'active') === 'active' ? 'confirmed' : 'cancelled';
                    $statusLabel = ($p['status'] ?? 'active') === 'active' ? 'Đang bán' : 'Ngừng bán';
                    $stock       = (int)($p['total_stock'] ?? 0);
                ?>
                <tr>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                            SP<?= str_pad($p['id'], 3, '0', STR_PAD_LEFT) ?>
                        </span>
                    </td>
                    <td>
                        <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($p['image'] ?? $p['base_img'] ?? 'placeholder.png') ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--border)"
                             alt="">
                    </td>
                    <td style="font-weight:600;max-width:200px">
                        <?= htmlspecialchars($p['name']) ?>
                    </td>
                    <td class="muted"><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                    <td class="right price">
                        <?= $p['sale_price'] > 0 ? number_format($p['sale_price'], 0, ',', '.') . 'đ' : '/' ?>
                    </td>
                    <td class="right">
                        <span class="ui-badge <?= $stock > 0 ? 'confirmed' : 'cancelled' ?>">
                            <?= $stock ?>
                        </span>
                    </td>
                    <td class="center">
                        <span class="ui-badge <?= $statusCls ?>"><?= $statusLabel ?></span>
                    </td>
                   <td class="center">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= BASE_URL ?>/index.php?url=admin-products/edit&id=<?= $p['id'] ?>"
                        class="ui-btn-outline sm">Chỉnh sửa</a>

                        <?php if (($p['status'] ?? 'active') === 'active'): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-products/delete&id=<?= $p['id'] ?>"
                        class="ui-btn sm"
                        style="background:linear-gradient(135deg,#f76f8e,#db2777)"
                        onclick="return confirm('Hủy kích hoạt sản phẩm này?')">
                            Hủy kích hoạt
                        </a>
                        <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-products/restore&id=<?= $p['id'] ?>"
                        class="ui-btn sm"
                        style="background:linear-gradient(135deg,#38d9a9,#0ca678)"
                        onclick="return confirm('Kích hoạt lại sản phẩm này?')">
                            Kích hoạt
                        </a>
                        <?php endif; ?>

                        <?php if ($stock === 0): ?>
                        <a href="<?= BASE_URL ?>/index.php?url=admin-products/destroy&id=<?= $p['id'] ?>"
                        class="ui-btn sm"
                        style="background:linear-gradient(135deg,#6b7280,#374151)"
                        onclick="return confirm('Xóa vĩnh viễn sản phẩm này khỏi hệ thống? Hành động không thể hoàn tác!')">
                            Xóa
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($totalPages) && $totalPages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php
                $qs = '?url=admin-products&search=' . urlencode($search ?? '') . '&category=' . ($categoryId ?? 0) . '&page=';
                for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                   class="ui-page-btn <?= $i == ($page ?? 1) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>