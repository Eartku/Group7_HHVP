<?php /* app/views/admin/categories/create/index.php */ ?>

<div class="container py-5" style="max-width:580px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-categories">Danh mục</a>
        <span class="sep">›</span>
        <span>Thêm mới</span>
    </div>

    <h2 class="ui-title">Thêm danh mục mới</h2>

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
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <h5>Thông tin danh mục</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-categories-create"
                  enctype="multipart/form-data">

                <div class="ui-field">
                    <label class="ui-label">Tên danh mục <span class="req">*</span></label>
                    <input type="text" name="name" class="ui-input"
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                           placeholder="VD: Cây để bàn, Cây nội thất..." required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Mô tả</label>
                    <input type="text" name="description" class="ui-input"
                           value="<?= htmlspecialchars($old['description'] ?? '') ?>"
                           placeholder="Mô tả ngắn về danh mục">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Ảnh đại diện</label>
                    <input type="file" name="image" class="ui-input"
                           accept="image/*" onchange="previewImg(this)">
                    <!-- Preview ảnh trước khi submit -->
                    <div id="previewWrap" style="display:none;margin-top:10px">
                        <img id="previewImg"
                             style="width:120px;height:120px;object-fit:cover;
                                    border-radius:10px;border:1px solid var(--border)"
                             alt="preview">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-categories"
                       class="ui-btn-outline sm">← Quay lại</a>
                    <button type="submit" class="ui-btn sm flex-grow-1"
                            onclick="return confirm('Thêm danh mục này?')">
                        + Thêm danh mục
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
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('previewWrap').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>