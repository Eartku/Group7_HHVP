<?php /* app/views/admin-products/create/index.php */ ?>

<div class="container py-5" style="max-width:680px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-products">Sản phẩm</a>
        <span class="sep">›</span>
        <span>Thêm mới</span>
    </div>

    <h2 class="ui-title">Thêm sản phẩm mới</h2>

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
          action="<?= BASE_URL ?>/index.php?url=admin-products/create"
          enctype="multipart/form-data"
          onsubmit="return confirm('Thêm sản phẩm này?')">

        <div class="ui-card mb-4">
            <div class="ui-card-head">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <h5>Thông tin sản phẩm</h5>
            </div>
            <div class="ui-card-body">

                <div class="ui-field">
                    <label class="ui-label">Tên sản phẩm <span class="req">*</span></label>
                    <input type="text" name="name" class="ui-input"
                           value="<?= htmlspecialchars($data['name'] ?? '') ?>"
                           placeholder="Nhập tên sản phẩm" required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Danh mục <span class="req">*</span></label>
                    <select name="category" class="ui-input">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                                <?= ($data['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
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
                                   value="<?= $data['profit_rate'] ?? 0 ?>"
                                   placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Trạng thái</label>
                            <select name="status" class="ui-input">
                                <option value="active"  <?= ($data['status'] ?? '') === 'active'  ? 'selected' : '' ?>>Đang bán</option>
                                <option value="inactive" <?= ($data['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="ui-field mt-3">
                    <label class="ui-label">Mô tả</label>
                    <textarea name="description" class="ui-input textarea"
                              style="height:100px"
                              placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
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
                <div class="ui-field mb-0">
                    <label class="ui-label">Ảnh sản phẩm</label>
                    <input type="file" name="image" class="ui-input"
                           accept="image/*" onchange="previewImg(this)">
                </div>
                <div id="imgPreview" style="display:none;margin-top:12px">
                    <img id="previewEl"
                         style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--border)"
                         alt="preview">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?url=admin-products"
               class="ui-btn-outline sm">← Quay lại</a>
            <button type="submit" class="ui-btn sm flex-grow-1">
                🌿 Thêm sản phẩm
            </button>
        </div>

    </form>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewEl').src = e.target.result;
            document.getElementById('imgPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>