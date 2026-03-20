<?php
$appendix = false;
$pageTitle = 'Hồ sơ cá nhân';
?>
<div class="profile-page">
    <div style="max-width:900px;margin:0 auto;padding:0 20px">
        <h2 class="profile-title" style="text-align:center">Hồ sơ cá nhân</h2>
    </div>

    <?php
    $hasAvatar  = !empty($user['avatar']);
    $avatarSrc  = $hasAvatar
        ? BASE_URL . '/uploads/avatars/' . htmlspecialchars($user['avatar'])
        : BASE_URL . '/images/avatar.svg';
    $defaultClass = $hasAvatar ? '' : 'is-default';
    ?>

    <div class="profile-grid">

        <!-- ── LEFT PANEL ── -->
        <div class="profile-left">

            <!-- Avatar card -->
            <div class="avatar-card">
                <div class="avatar-wrap <?= $defaultClass ?>" id="avatarWrap">

                    <img src="<?= $avatarSrc ?>"
                         class="avatar-img" id="avatarPreview" alt="Avatar">

                    <!-- Nút xóa ảnh -->
                    <button type="button"
                            class="btn-del-avatar"
                            id="btnDelAvatar"
                            title="Xóa ảnh">✕</button>

                    <!-- Click overlay để chọn ảnh mới -->
                    <label for="avatarFileInput" class="avatar-overlay">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             stroke="#fff" stroke-width="2" stroke-linecap="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8
                                     a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Đổi ảnh</span>
                    </label>

                </div>

                <div class="avatar-name"><?= htmlspecialchars($user['fullname'] ?? 'Người dùng') ?></div>
                <div class="avatar-role">Khách hàng</div>
            </div>

            <!-- Info summary -->
            <div class="info-card">
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Email</div>
                        <div class="val"><?= htmlspecialchars($user['email'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                                     19.79 19.79 0 0 1-8.63-3.07
                                     19.5 19.5 0 0 1-6-6
                                     19.79 19.79 0 0 1-3.07-8.67
                                     A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72
                                     c.127.96.361 1.903.7 2.81
                                     a2 2 0 0 1-.45 2.11L8.09 9.91
                                     a16 16 0 0 0 6 6l1.27-1.27
                                     a2 2 0 0 1 2.11-.45
                                     c.907.339 1.85.573 2.81.7
                                     A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Điện thoại</div>
                        <div class="val"><?= htmlspecialchars($user['phone'] ?? '—') ?></div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13
                                     a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="info-text">
                        <div class="lbl">Địa chỉ</div>
                        <div class="val"><?= htmlspecialchars($user['address'] ?? '—') ?></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ── RIGHT PANEL ── -->
        <div class="profile-right">

            <!-- Alerts -->
            <?php if (!empty($errors)): ?>
            <div class="alert-custom alert-danger-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . ' '; ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert-custom alert-success-custom">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <?= $_GET['success'] == 2 ? 'Đổi mật khẩu thành công!' : 'Cập nhật thông tin thành công!' ?>
            </div>
            <?php endif; ?>

            <!-- Form cập nhật thông tin -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <h5>Thông tin cá nhân</h5>
                </div>
                <div class="form-card-body">
                    <form method="POST"
                          enctype="multipart/form-data"
                          action="<?= BASE_URL ?>/index.php?url=profile"
                          id="profileForm">

                        <!-- Avatar file input ẩn -->
                        <input type="file" name="avatar" id="avatarFileInput"
                               accept="image/jpeg,image/png,image/gif,image/webp">

                        <!-- Hidden field xóa avatar -->
                        <input type="hidden" name="delete_avatar" id="deleteAvatarField" value="0">

                        <div class="field-group">
                            <label class="field-label">Họ và tên</label>
                            <input type="text" name="fullname" class="field-input"
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                   placeholder="Nhập họ và tên">
                        </div>

                        <div class="field-group">
                            <label class="field-label">Email</label>
                            <input type="email" name="email" class="field-input"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                   placeholder="Nhập email">
                        </div>

                        <div class="field-group">
                            <label class="field-label">Số điện thoại</label>
                            <input type="text" name="phone" class="field-input"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   placeholder="Nhập số điện thoại">
                        </div>

                        <div class="field-group">
                            <label class="field-label">Địa chỉ</label>
                            <textarea name="address" class="field-input textarea-field"
                                      placeholder="Nhập địa chỉ"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="btn-submit">
                            Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form đổi mật khẩu -->
            <div class="form-card">
                <div class="form-card-head">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <h5>Đổi mật khẩu</h5>
                </div>
                <div class="form-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/index.php?url=profile">

                        <div class="field-group">
                            <label class="field-label">Mật khẩu hiện tại</label>
                            <div class="input-group-custom">
                                <input type="password" name="current_password" id="currentPassword"
                                       placeholder="Nhập mật khẩu hiện tại">
                                <button type="button" class="toggle-pwd" data-target="currentPassword">
                                    <img src="<?= BASE_URL ?>/images/hide.svg" id="ico-currentPassword">
                                </button>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Mật khẩu mới</label>
                            <div class="input-group-custom">
                                <input type="password" name="new_password" id="newPassword"
                                       placeholder="Nhập mật khẩu mới">
                                <button type="button" class="toggle-pwd" data-target="newPassword">
                                    <img src="<?= BASE_URL ?>/images/hide.svg" id="ico-newPassword">
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="change_password" class="btn-submit">
                            Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ── Modal xác nhận xóa ảnh ── -->
<div class="modal-overlay" id="delModal">
    <div class="modal-box">
        <h6>Xóa ảnh đại diện?</h6>
        <p>Ảnh sẽ được đặt lại về mặc định. Hành động này không thể hoàn tác.</p>
        <div class="modal-actions">
            <button class="modal-btn modal-btn-cancel" id="modalCancel">Hủy</button>
            <button class="modal-btn modal-btn-confirm" id="modalConfirm">Xóa ảnh</button>
        </div>
    </div>
</div>

<script>
/* ── Toggle password visibility ── */
document.querySelectorAll('.toggle-pwd').forEach(btn => {
    btn.addEventListener('click', () => {
        const id    = btn.dataset.target;
        const input = document.getElementById(id);
        const ico   = document.getElementById('ico-' + id);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        ico.src = isHidden
            ? '<?= BASE_URL ?>/images/show.svg'
            : '<?= BASE_URL ?>/images/hide.svg';
    });
});

/* ── Preview ảnh khi chọn file mới ── */
document.getElementById('avatarFileInput').addEventListener('change', function () {
    if (!this.files || !this.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').src = e.target.result;
        // Có ảnh mới → bật nút xóa, tắt cờ delete
        document.getElementById('avatarWrap').classList.remove('is-default');
        document.getElementById('deleteAvatarField').value = '0';
    };
    reader.readAsDataURL(this.files[0]);
});

/* ── Nút xóa ảnh → mở modal ── */
document.getElementById('btnDelAvatar').addEventListener('click', () => {
    document.getElementById('delModal').classList.add('active');
});

document.getElementById('modalCancel').addEventListener('click', () => {
    document.getElementById('delModal').classList.remove('active');
});

document.getElementById('modalConfirm').addEventListener('click', () => {
    // Đặt flag xóa
    document.getElementById('deleteAvatarField').value = '1';

    // Xóa file đã chọn (nếu có)
    const fileInput = document.getElementById('avatarFileInput');
    fileInput.value = '';

    // Đổi preview về avatar mặc định
    document.getElementById('avatarPreview').src = '<?= BASE_URL ?>/images/avatar.svg';

    // Ẩn nút xóa
    document.getElementById('avatarWrap').classList.add('is-default');

    // Đóng modal
    document.getElementById('delModal').classList.remove('active');

    // Submit form luôn
    document.getElementById('profileForm').submit();
});

/* ── Click overlay ngoài modal để đóng ── */
document.getElementById('delModal').addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('active');
});
</script>