<?php /* app/views/admin/categories/edit/index.php */ ?>

<div class="container py-5" style="max-width:580px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-categories">Danh mục</a>
        <span class="sep">›</span>
        <span>Chỉnh sửa</span>
    </div>

    <h2 class="ui-title">Chỉnh sửa danh mục</h2>

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

    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <h5>Thông tin danh mục</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-categories-edit&id=<?= (int)$category['id'] ?>"
                  enctype="multipart/form-data">

                <div class="ui-field">
                    <label class="ui-label">Mã danh mục</label>
                    <input type="text" class="ui-input"
                           value="#<?= (int)$category['id'] ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tên danh mục <span class="req">*</span></label>
                    <input type="text" name="name" class="ui-input"
                           value="<?= htmlspecialchars($category['name']) ?>"
                           placeholder="Tên danh mục" required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Mô tả</label>
                    <input type="text" name="description" class="ui-input"
                           value="<?= htmlspecialchars($category['description'] ?? '') ?>"
                           placeholder="Mô tả ngắn về danh mục">
                </div>
                <div class="ui-field">
                    <label class="ui-label">Trạng thái</label>
                    <select name="status" class="ui-input">
                        <option value="active"   <?= ($category['status'] ?? '') === 'active'   ? 'selected' : '' ?>>
                            Đang hoạt động
                        </option>
                        <option value="inactive" <?= ($category['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                            Đã ẩn
                        </option>
                    </select>
                </div>

                <!-- Ảnh hiện tại -->
                <div class="ui-field">
                    <label class="ui-label">Ảnh hiện tại</label>
                    <?php if (!empty($category['image'])): ?>
                    <div class="mb-2">
                        <img id="currentImg"
                             src="<?= BASE_URL ?>/images/<?= htmlspecialchars($category['image']) ?>"
                             style="width:120px;height:120px;object-fit:cover;
                                    border-radius:10px;border:1px solid var(--border)"
                             alt="current">
                    </div>
                    <?php else: ?>
                    <div style="width:120px;height:120px;border-radius:10px;
                                background:var(--bg-soft);border:1px solid var(--border);
                                display:flex;align-items:center;justify-content:center;
                                font-size:2rem;margin-bottom:8px">🌿</div>
                    <?php endif; ?>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Ảnh mới (để trống nếu không đổi)</label>
                    <input type="file" name="image" class="ui-input"
                           accept="image/*" onchange="previewImg(this)">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-categories"
                       class="ui-btn-outline sm">← Quay lại</a>
                    <button type="submit" class="ui-btn sm flex-grow-1"
                            onclick="return confirm('Lưu thay đổi danh mục này?')">
                        Lưu thay đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
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