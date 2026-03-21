<?php /* app/views/admin-products/edit/index.php */ ?>

<div class="container py-5" style="max-width:680px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-products">Sản phẩm</a>
        <span class="sep">›</span>
        <span>Sửa SP<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?></span>
    </div>

    <h2 class="ui-title">Chỉnh sửa sản phẩm</h2>

    <?php if (!empty($success)): ?>
    <div class="ui-alert success mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="ui-alert danger mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= BASE_URL ?>/index.php?url=admin-products/edit&id=<?= (int)$product['id'] ?>"
          enctype="multipart/form-data"
          onsubmit="return confirm('Cập nhật sản phẩm này?')">

        <!-- Info card -->
        <div class="ui-card mb-4">
            <div class="ui-card-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <h5>Thông tin sản phẩm</h5>
            </div>
            <div class="ui-card-body">

                <div class="ui-field">
                    <label class="ui-label">Mã sản phẩm</label>
                    <input type="text" class="ui-input"
                           value="SP<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tên sản phẩm <span class="req">*</span></label>
                    <input type="text" name="name" class="ui-input"
                           value="<?= htmlspecialchars($product['name']) ?>"
                           placeholder="Nhập tên sản phẩm" required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Danh mục</label>
                    <select name="category" class="ui-input">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Tỉ lệ lợi nhuận (%)</label>
                            <input type="number" name="profit_rate" step="0.1"
                                   class="ui-input"
                                   value="<?= $product['profit_rate'] ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Trạng thái</label>
                            <select name="status" class="ui-input">
                                <option value="active"   <?= ($product['status'] ?? '') === 'active'   ? 'selected' : '' ?>>✅ Đang bán</option>
                                <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>🔒 Ngừng bán</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="ui-field mt-3">
                    <label class="ui-label">Mô tả</label>
                    <textarea name="description" class="ui-input textarea"
                              style="height:100px"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

            </div>
        </div>

        <!-- Image card -->
        <div class="ui-card mb-4">
            <div class="ui-card-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <h5>Hình ảnh</h5>
            </div>
            <div class="ui-card-body">
                <!-- Ảnh hiện tại -->
                <?php
                $imgFile = $product['base_img'] ?? $product['image'] ?? '';
                if (!empty($imgFile)): ?>
                <div class="mb-3">
                    <label class="ui-label">Ảnh hiện tại</label>
                    <div>
                        <img id="currentImg"
                             src="<?= BASE_URL ?>/images/<?= htmlspecialchars($imgFile) ?>"
                             style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--border)"
                             alt="current">
                    </div>
                </div>
                <?php endif; ?>

                <div class="ui-field mb-0">
                    <label class="ui-label">Ảnh mới (để trống nếu không đổi)</label>
                    <input type="file" name="new_image" class="ui-input"
                           accept="image/*" onchange="previewImg(this)">
                </div>
            </div>
        </div>

        <!-- Stock card — chỉ hiển thị, không cho sửa trực tiếp -->
        <div class="ui-card mb-4">
            <div class="ui-card-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <h5>Tồn kho</h5>
            </div>
            <div class="ui-card-body">
                <div class="ui-alert info mb-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Tồn kho hiện tại: <strong><?= (int)($stock ?? 0) ?> sản phẩm</strong>.
                    Để nhập thêm hàng, dùng
                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-create"
                       style="color:var(--brand);font-weight:700">phiếu nhập kho</a>.
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?url=admin-products"
               class="ui-btn-outline sm">← Quay lại</a>
            <button type="submit" class="ui-btn sm flex-grow-1">
                💾 Lưu thay đổi
            </button>
        </div>

    </form>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const cur = document.getElementById('currentImg');
            if (cur) cur.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>