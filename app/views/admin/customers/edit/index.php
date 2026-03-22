<?php /* app/views/admin/customers/edit/index.php */ ?>

<div class="container py-5" style="max-width:580px">

    <!-- Breadcrumb -->
    <div class="ui-breadcrumb mb-4">
        <a href="<?= BASE_URL ?>/index.php?url=admin">Dashboard</a>
        <span class="sep">›</span>
        <a href="<?= BASE_URL ?>/index.php?url=admin-customers">Khách hàng</a>
        <span class="sep">›</span>
        <span>Chỉnh sửa #C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?></span>
    </div>

    <h2 class="ui-title">Chỉnh sửa khách hàng</h2>

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

    <!-- Avatar card -->
    <div class="ui-avatar-card mb-4">
        <div class="ui-avatar-wrap is-default">
            <img src="<?= htmlspecialchars($avatarPath ?? BASE_URL . '/images/user.png') ?>"
                 class="ui-avatar-img" alt="avatar">
        </div>
        <div class="ui-avatar-name"><?= htmlspecialchars($user['fullname']) ?></div>
        <div class="ui-avatar-role">
            #C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>
        </div>

        <!-- Status toggle -->
        <div class="d-flex justify-content-center gap-2 mt-3">
            <?php
            $statuses = [
                'active'   => ['label' => 'Hoạt động', 'cls' => 'ui-btn sm',
                               'style' => ''],
                'warning'  => ['label' => 'Cảnh báo',  'cls' => 'ui-btn sm',
                               'style' => 'background:linear-gradient(135deg,#f7c948,#d97706)'],
                'inactive' => ['label' => 'Bị khóa',   'cls' => 'ui-btn sm',
                               'style' => 'background:linear-gradient(135deg,#f76f8e,#db2777)'],
            ];
            foreach ($statuses as $val => $cfg):
                $isActive = ($user['status'] === $val);
            ?>
            <a href="<?= BASE_URL ?>/index.php?url=admin-customers-edit&id=<?= $user['id'] ?>&set_status=<?= $val ?>"
               class="<?= $cfg['cls'] ?>"
               style="<?= $cfg['style'] ?>;<?= $isActive ? '' : 'opacity:.4;filter:grayscale(.5)' ?>"
               onclick="return confirm('Đổi trạng thái sang <?= $cfg['label'] ?>?')">
                <?= $cfg['label'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Form card -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <h5>Thông tin chi tiết</h5>
        </div>
        <div class="ui-card-body">
            <form method="POST"
                  action="<?= BASE_URL ?>/index.php?url=admin-customers-edit&id=<?= $user['id'] ?>">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <div class="ui-field">
                    <label class="ui-label">Mã khách hàng</label>
                    <input type="text" class="ui-input"
                           value="#C<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Họ và tên</label>
                    <input type="text" name="fullname" class="ui-input"
                           value="<?= htmlspecialchars($user['fullname']) ?>"
                           placeholder="Nhập họ tên">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Tên đăng nhập</label>
                    <input type="text" name="username" class="ui-input"
                           value="<?= htmlspecialchars($user['username']) ?>"
                           placeholder="Nhập username">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Mật khẩu mới</label>
                    <div class="ui-pwd-wrap">
                        <input type="password" name="password" id="pwdInput"
                               class="ui-input" placeholder="Để trống nếu không đổi">
                        <button type="button" class="ui-pwd-toggle" onclick="togglePwd()">
                            <img id="eyeIcon"
                                 src="<?= BASE_URL ?>/images/show.svg"
                                 alt="toggle"
                                 style ='background-color:green;width:25px;height:25px'>
                        </button>
                    </div>
                </div>

                <div class="ui-field">
                    <label class="ui-label">Email</label>
                    <input type="email" name="email" class="ui-input"
                           value="<?= htmlspecialchars($user['email']) ?>"
                           placeholder="Nhập email">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Số điện thoại</label>
                    <input type="text" name="phone" class="ui-input"
                           value="<?= htmlspecialchars($user['phone']) ?>"
                           placeholder="Nhập số điện thoại">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Địa chỉ</label>
                    <input type="text" name="address" class="ui-input"
                           value="<?= htmlspecialchars($user['address'] ?? '') ?>"
                           placeholder="Nhập địa chỉ">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Ngày tham gia</label>
                    <input type="date" class="ui-input"
                           value="<?= date('Y-m-d', strtotime($user['created_at'])) ?>"
                           readonly style="background:#f5f5f5;color:#888">
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="ui-btn-outline sm">
                        ← Quay lại
                    </a>
                    <button type="submit" class="ui-btn sm flex-grow-1">
                        Lưu thay đổi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function togglePwd() {
    const input = document.getElementById('pwdInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>