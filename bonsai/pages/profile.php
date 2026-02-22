<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$errors = [];

/* ===== LẤY USER ===== */
$stmt = $conn->prepare("
    SELECT fullname, email, phone, address, password, avatar 
    FROM users 
    WHERE id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Không tìm thấy người dùng.");
}

/* ===== XỬ LÝ FORM ===== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ================= UPDATE PROFILE ================= */
    if (isset($_POST['update_profile'])) {

        $fullname = trim($_POST['fullname']);
        $email    = trim($_POST['email']);
        $phone    = trim($_POST['phone']);
        $address  = trim($_POST['address']);

        if ($fullname == "") $errors[] = "Họ tên không được để trống.";
        if ($email == "")    $errors[] = "Email không được để trống.";

        /* ===== UPLOAD AVATAR ===== */
        $avatarFileName = $user['avatar']; // giữ avatar cũ

        if (!empty($_FILES['avatar']['name'])) {

            $uploadDir = "../uploads/avatars/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExt = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];

            if (!in_array($fileExt, $allowed)) {
                $errors[] = "Chỉ cho phép JPG, JPEG, PNG, GIF.";
            } else {
                $avatarFileName = time() . "_" . basename($_FILES["avatar"]["name"]);
                $targetFile = $uploadDir . $avatarFileName;

                move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile);
            }
        }

        if (empty($errors)) {

            $stmt = $conn->prepare("
                UPDATE users 
                SET fullname=?, email=?, phone=?, address=?, avatar=? 
                WHERE id=?
            ");
            $stmt->bind_param(
                "sssssi",
                $fullname,
                $email,
                $phone,
                $address,
                $avatarFileName,
                $userId
            );
            $stmt->execute();

            header("Location: profile.php?success=1");
            exit;
        }
    }

    /* ================= ĐỔI MẬT KHẨU ================= */
    if (isset($_POST['change_password'])) {

        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];

        if ($current == "" || $new == "") {
            $errors[] = "Vui lòng nhập đầy đủ mật khẩu.";
        } elseif (!password_verify($current, $user['password'])) {
            $errors[] = "Mật khẩu hiện tại không đúng.";
        } else {

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $hash, $userId);
            $stmt->execute();

            header("Location: profile.php?success=2");
            exit;
        }
    }
}

/* ===== ĐƯỜNG DẪN AVATAR ===== */
$avatarPath = !empty($user['avatar'])
    ? "../uploads/avatars/" . htmlspecialchars($user['avatar'])
    : "../images/avatar.svg";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'?>
</head>
<body>

<?php include '../includes/header.php'?>


<div class="untree_co-section" style="margin-top: -20px;">
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
              <div class="alert alert-success">
                  Cập nhật thành công!
              </div>
          <?php endif; ?>

          <!-- ================= FORM PROFILE ================= -->
          <form method="POST" enctype="multipart/form-data">

            <div class="text-center mb-4">

                <img src="<?= $avatarPath ?>"
                     class="rounded-circle shadow avatar-img mb-3"
                     width="120" height="120">

                <!-- 🔥 NÚT UPLOAD -->
                <div class="mb-3">
                    <input type="file" name="avatar" class="form-control">
                </div>

                <h4 class="text-black">
                    <?= htmlspecialchars($user['fullname']) ?>
                </h4>
            </div>

            <div class="mb-3">
              <label class="text-black">Họ và Tên</label>
              <input type="text" name="fullname" class="form-control"
                     value="<?= htmlspecialchars($user['fullname']) ?>">
            </div>

            <div class="mb-3">
              <label class="text-black">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($user['email']) ?>">
            </div>

            <div class="mb-3">
              <label class="text-black">Số điện thoại</label>
              <input type="text" name="phone" class="form-control"
                     value="<?= htmlspecialchars($user['phone']) ?>">
            </div>

            <div class="mb-3">
              <label class="text-black">Địa chỉ giao hàng</label>
              <textarea name="address"
                        class="form-control"
                        rows="3"
                        style="height:100px !important"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>

            <button type="submit" name="update_profile"
                    class="btn btn-dark w-100">
                Lưu thay đổi
            </button>
          </form>

          <hr>

          <!-- ================= FORM ĐỔI MẬT KHẨU ================= -->
          <form method="POST">

            <h5 class="text-black mb-3">Đổi mật khẩu</h5>

            <div class="mb-3 position-relative">
            <label class="text-black">Mật khẩu hiện tại</label>
            <div class="input-group">
                <input type="password" name="current_password" 
                    class="form-control" id="currentPassword">
                <button type="button" class="btn btn-outline-secondary toggle-password"
                        data-target="currentPassword">
                    <img src="../images/hide.svg" width="16" height="16">
                </button>
            </div>
            </div>

            <div class="mb-3 position-relative">
            <label class="text-black">Mật khẩu mới</label>
            <div class="input-group">
                <input type="password" name="new_password" 
                    class="form-control" id="newPassword">
                <button type="button" class="btn btn-outline-secondary toggle-password"
                        data-target="newPassword">
                    <img src="../images/hide.svg" width="16" height="16">
                </button>
            </div>
            </div>

            <button type="submit" name="change_password"
                    class="btn btn-dark w-100">
                Đổi mật khẩu
            </button>

          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'?>
<script>
    // TOGGLE PASSWORD
    document.querySelectorAll(".toggle-password").forEach(button => {
        button.addEventListener("click", () => {
            const targetId = button.getAttribute("data-target");
            const input = document.getElementById(targetId);
            if (input.type === "password") {
                input.type = "text";
                button.innerHTML = '<img src="../images/show.svg" width="16" height="16">';
            } else {
                input.type = "password";
                button.innerHTML = '<img src="../images/hide.svg" width="16" height="16">';
            }
        });
    });
</script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>