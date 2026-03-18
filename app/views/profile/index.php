<?php 
$appendix = false;
?>
<div class="untree_co-section" style="margin-top:-20px;">
  <h2 class="mb-4 container mt-5" style="text-align:center;">Hồ sơ cá nhân</h2>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="p-4 p-lg-5 border bg-white rounded-3">

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
            </div>
          <?php endif; ?>

          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Cập nhật thành công!</div>
          <?php endif; ?>

          <!-- FORM PROFILE -->
          <form method="POST" enctype="multipart/form-data" action="/app/index.php?url=profile">
            <div class="text-center mb-4">
              <img src="<?= !empty($user['avatar'])
                ? '/app/uploads/avatars/' . htmlspecialchars($user['avatar'])
                : '/app/images/avatar.svg' ?>"
                class="rounded-circle shadow mb-3"
                width="120" height="120" style="object-fit:cover;">
              <div class="mb-3">
                <input type="file" name="avatar" class="form-control">
              </div>
              <h4 class="text-black"><?= htmlspecialchars($user['fullname'] ?? '') ?></h4>
            </div>

            <div class="mb-3">
              <label class="text-black">Họ và Tên</label>
              <input type="text" name="fullname" class="form-control"
                     value="<?= htmlspecialchars($user['fullname'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="text-black">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="text-black">Số điện thoại</label>
              <input type="text" name="phone" class="form-control"
                     value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="text-black">Địa chỉ</label>
              <textarea name="address" class="form-control" rows="3"
                style="height:100px"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
            <button type="submit" name="update_profile" class="btn btn-dark w-100">
              Lưu thay đổi
            </button>
          </form>

          <hr>

          <!-- FORM ĐỔI MẬT KHẨU -->
          <form method="POST" action="/app/index.php?url=profile">
            <h5 class="text-black mb-3">Đổi mật khẩu</h5>
            <div class="mb-3">
              <label class="text-black">Mật khẩu hiện tại</label>
              <div class="input-group">
                <input type="password" name="current_password"
                       class="form-control" id="currentPassword">
                <button type="button" class="btn btn-outline-secondary toggle-password"
                        data-target="currentPassword">
                  <img src="/app/images/hide.svg" width="16">
                </button>
              </div>
            </div>
            <div class="mb-3">
              <label class="text-black">Mật khẩu mới</label>
              <div class="input-group">
                <input type="password" name="new_password"
                       class="form-control" id="newPassword">
                <button type="button" class="btn btn-outline-secondary toggle-password"
                        data-target="newPassword">
                  <img src="/app/images/hide.svg" width="16">
                </button>
              </div>
            </div>
            <button type="submit" name="change_password" class="btn btn-dark w-100">
              Đổi mật khẩu
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", () => {
        const input = document.getElementById(button.getAttribute("data-target"));
        const img   = button.querySelector("img");
        if (input.type === "password") {
            input.type = "text";
            img.src = "/app/images/show.svg";
        } else {
            input.type = "password";
            img.src = "/app/images/hide.svg";
        }
    });
});
</script>