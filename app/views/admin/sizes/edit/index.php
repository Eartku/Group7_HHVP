<?php /* app/views/admin/sizes/edit/index.php */ ?>

<div class="container py-5" style="max-width:500px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-sizes">Size</a>
        <span class="sep">›</span>
        <span>Chỉnh sửa</span>
    </div>

    <h2 class="ui-title">Chỉnh sửa size</h2>

    <!-- Alerts -->
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
            <h5>Thông tin size</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-sizes-edit&id=<?= (int)$size['size_id'] ?>">

                <div class="ui-field">
                    <label class="ui-label">Mã size</label>
                    <input type="text" class="ui-input"
                           value="#<?= (int)$size['size_id'] ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tên size <span class="req">*</span></label>
                    <input type="text" name="size_name" class="ui-input"
                           value="<?= htmlspecialchars($size['size']) ?>"
                           placeholder="VD: S, M, L, XL, Nhỏ, Vừa..."
                           required>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Điều chỉnh giá (đ)</label>
                    <input type="number" name="price_adjust" class="ui-input"
                           value="<?= (float)$size['price_adjust'] ?>"
                           step="1000" min="0"
                           placeholder="0">
                    <span style="font-size:12px;color:var(--text-muted);margin-top:4px;display:block">
                        Giá bán = Giá vốn TB × (1 + % lợi nhuận) + Điều chỉnh giá
                    </span>
                </div>

                <!-- Preview giá điều chỉnh -->
                <div class="ui-alert info mb-4" id="adjustPreview">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Điều chỉnh hiện tại:
                    <strong id="adjustDisplay">
                        <?= (float)$size['price_adjust'] > 0
                            ? '+' . number_format($size['price_adjust'], 0, ',', '.') . 'đ'
                            : 'Không điều chỉnh' ?>
                    </strong>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-sizes"
                       class="ui-btn-outline sm">← Quay lại</a>
                    <button type="submit" class="ui-btn sm flex-grow-1"
                            onclick="return confirm('Lưu thay đổi size này?')">
                        Lưu thay đổi
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
// Preview price_adjust realtime
document.querySelector('input[name="price_adjust"]')
    .addEventListener('input', function () {
        const val = parseFloat(this.value) || 0;
        const display = document.getElementById('adjustDisplay');
        display.textContent = val > 0
            ? '+' + val.toLocaleString('vi-VN') + 'đ'
            : 'Không điều chỉnh';
    });
</script>