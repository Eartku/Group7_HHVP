<?php /* app/views/admin/categories/index.php */ ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý danh mục</h2>
    </div>

    <!-- Alert -->
    <?php if (!empty($alert)): ?>
    <div class="ui-alert <?= $alert['type'] ?> mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <?php if ($alert['type'] === 'success'): ?>
                <polyline points="20 6 9 17 4 12"/>
            <?php else: ?>
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            <?php endif; ?>
        </svg>
        <?= htmlspecialchars($alert['msg']) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm mới -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <h5>Thêm danh mục mới</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-categories-create"
                  enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Tên danh mục <span class="req">*</span></label>
                            <input type="text" name="name" class="ui-input"
                                   placeholder="VD: Cây để bàn, Cây nội thất..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Mô tả</label>
                            <input type="text" name="description" class="ui-input"
                                   placeholder="Mô tả ngắn...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Ảnh đại diện</label>
                            <input type="file" name="image" class="ui-input"
                                   accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="ui-btn sm w-100">+ Thêm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                <line x1="7" y1="7" x2="7.01" y2="7"/>
            </svg>
            <h5>Danh sách danh mục — <?= count($categories) ?> danh mục</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th class="center">Số sản phẩm</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="6">
                        <div class="ui-empty py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                            </svg>
                            <h4>Chưa có danh mục nào</h4>
                            <p>Thêm danh mục đầu tiên phía trên</p>
                        </div>
                    </td>
                </tr>
                <?php else: foreach ($categories as $cat): ?>
                <tr>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                            #<?= $cat['id'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($cat['image'])): ?>
                        <img src="<?= BASE_URL ?>/images/<?= htmlspecialchars($cat['image']) ?>"
                             style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--border)"
                             alt="">
                        <?php else: ?>
                        <div style="width:48px;height:48px;border-radius:8px;background:var(--bg-soft);
                                    display:flex;align-items:center;justify-content:center;font-size:1.3rem;
                                    border:1px solid var(--border)">🌿</div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="muted" style="max-width:200px">
                        <?= htmlspecialchars($cat['description'] ?? '—') ?>
                    </td>
                    <td class="center">
                        <span class="ui-badge <?= (int)$cat['product_count'] > 0 ? 'info' : 'neutral' ?>">
                            <?= (int)$cat['product_count'] ?> sản phẩm
                        </span>
                    </td>
                    <td class="center">
                        <span class="ui-badge <?= $cat['status'] === 'active' ? 'success' : 'neutral' ?>">
                            <?= $cat['status'] === 'active' ? 'Đang hoạt động' : 'Đã ẩn' ?>
                        </span>
                    </td>
                    <td class="center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= BASE_URL ?>/index.php?url=admin-categories-edit&id=<?= $cat['id'] ?>"
                               class="ui-btn-outline sm">Chỉnh sửa</a>
                            <?php if ($cat['status'] === 'inactive'): ?>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-categories-restore&id=<?= $cat['id'] ?>"
                                   class="ui-btn sm"
                                   style="background:linear-gradient(135deg,#4CAF50,#2E7D32)"
                                   onclick="return confirm('Khôi phục danh mục <?= htmlspecialchars($cat['name']) ?>?')">
                                    Khôi phục
                                </a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/index.php?url=admin-categories-delete&id=<?= $cat['id'] ?>"
                                   class="ui-btn sm"
                                   style="background:linear-gradient(135deg,#f76f8e,#db2777)"
                                   onclick="return confirm('Ẩn danh mục <?= htmlspecialchars($cat['name']) ?>?')">
                                    Ẩn
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>