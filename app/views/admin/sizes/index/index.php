<?php /* app/views/admin/sizes/index.php */ ?>

<div class="container-fluid py-4" style="max-width:700px">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý Size</h2>
    </div>

    <!-- Alerts -->
    <?php
    $alertMap = [
        'created'   => ['success', 'Thêm size thành công.'],
        'deleted'   => ['success', 'Đã xóa size thành công.'],
        'failed'    => ['danger',  'Thao tác thất bại. Vui lòng thử lại.'],
        'duplicate' => ['warning', 'Tên size đã tồn tại.'],
        'inuse'     => ['warning', 'Không thể xóa — size đang được dùng trong kho hàng.'],
        'empty'     => ['warning', 'Tên size không được để trống.'],
        'invalid'   => ['danger',  'ID không hợp lệ.'],
    ];
    $alertKey = $_GET['success'] ?? $_GET['error'] ?? '';
    if ($alertKey && isset($alertMap[$alertKey])):
        [$alertType, $alertMsg] = $alertMap[$alertKey];
    ?>
    <div class="ui-alert <?= $alertType ?> mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <?php if ($alertType === 'success'): ?>
                <polyline points="20 6 9 17 4 12"/>
            <?php else: ?>
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            <?php endif; ?>
        </svg>
        <?= $alertMsg ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm size mới -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <h5>Thêm size mới</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-sizes"
                  class="d-flex gap-3 align-items-end flex-wrap">
                <div class="ui-field mb-0 flex-grow-1">
                    <label class="ui-label">Tên size <span class="req">*</span></label>
                    <input type="text" name="size_name" class="ui-input"
                           placeholder="VD: S, M, L, XL, Nhỏ, Vừa..."
                           required>
                </div>
                <div class="ui-field mb-0" style="min-width:160px">
                    <label class="ui-label">Điều chỉnh giá (đ)</label>
                    <input type="number" name="price_adjust" class="ui-input"
                           value="0" step="1000" min="0"
                           placeholder="0">
                </div>
                <button type="submit" class="ui-btn sm" style="margin-bottom:1px">
                    + Thêm
                </button>
            </form>
        </div>
    </div>

    <!-- Danh sách sizes -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <line x1="8" y1="6" x2="21" y2="6"/>
                <line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/>
                <line x1="3" y1="6"  x2="3.01" y2="6"/>
                <line x1="3" y1="12" x2="3.01" y2="12"/>
                <line x1="3" y1="18" x2="3.01" y2="18"/>
            </svg>
            <h5>Danh sách size — <?= count($sizes) ?> size</h5>
        </div>
        <div class="ui-card-body p-0">
            <?php if (empty($sizes)): ?>
            <div class="ui-empty py-4">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                </svg>
                <h4>Chưa có size nào</h4>
                <p>Thêm size đầu tiên phía trên</p>
            </div>
            <?php else: ?>
            <?php foreach ($sizes as $s): ?>
            <div class="d-flex align-items-center gap-3 px-4 py-3"
                 style="border-bottom:1px solid var(--border)"
                 id="row-<?= $s['id'] ?>">

                <!-- Tên size -->
                <div style="min-width:80px;font-weight:700;font-size:15px;color:var(--brand)">
                    <?= htmlspecialchars($s['size_name']) ?>
                </div>

                <!-- Inline edit price_adjust -->
                <form method="POST"
                      action="<?= BASE_URL ?>/index.php?url=admin-sizes-edit&id=<?= $s['id'] ?>"
                      class="d-flex align-items-center gap-2 flex-grow-1">
                    <input type="hidden" name="size_name" value="<?= htmlspecialchars($s['size_name']) ?>">
                    <label class="ui-label mb-0" style="white-space:nowrap;font-size:11px">
                        Điều chỉnh giá:
                    </label>
                    <input type="number" name="price_adjust"
                           class="ui-input" step="1000" min="0"
                           value="<?= (float)$s['price_adjust'] ?>"
                           style="width:120px;text-align:center">
                    <span style="font-size:13px;color:var(--text-muted)">đ</span>
                    <button type="submit" class="ui-btn sm"
                            onclick="return confirm('Lưu thay đổi price_adjust?')">
                        Lưu
                    </button>
                </form>

                <!-- Badge giá điều chỉnh -->
                <?php if ((float)$s['price_adjust'] > 0): ?>
                <span class="ui-badge info" style="white-space:nowrap">
                    +<?= number_format($s['price_adjust'], 0, ',', '.') ?>đ
                </span>
                <?php else: ?>
                <span class="ui-badge neutral" style="white-space:nowrap">
                    Không điều chỉnh
                </span>
                <?php endif; ?>

                <!-- Xóa -->
                <a href="<?= BASE_URL ?>/index.php?url=admin-sizes-delete&id=<?= $s['id'] ?>"
                   class="ui-btn sm"
                   style="background:linear-gradient(135deg,#f76f8e,#db2777);flex-shrink:0"
                   onclick="return confirm('Xóa size <?= htmlspecialchars($s['size_name']) ?>?')">
                    Xóa
                </a>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>