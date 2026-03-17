<?php
session_start();
require "../config/db.php";

// /* ===== KIỂM TRA ADMIN ===== */
// if (!isset($_SESSION['admin'])) {
//     header("Location: ../admin_login/admin_login.php");
//     exit;
// }

$adminId = $_SESSION['admin']['id'];
$errors = [];

/* ===== LẤY THÔNG TIN ADMIN ===== */
$stmt = $conn->prepare("
    SELECT fullname, email, phone, address, password, avatar 
    FROM users 
    WHERE id = ? AND role = 'admin'
");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    die("Không tìm thấy tài khoản admin.");
}

/* ===== XỬ LÝ FORM ===== */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ================= CẬP NHẬT PROFILE ================= */
    if (isset($_POST['update_profile'])) {

        $fullname = trim($_POST['fullname']);
        $email    = trim($_POST['email']);
        $phone    = trim($_POST['phone']);
        $address  = trim($_POST['address']);

        if ($fullname == "") $errors[] = "Họ tên không được để trống.";
        if ($email == "")    $errors[] = "Email không được để trống.";

        /* ===== UPLOAD AVATAR ===== */
        $avatarFileName = $admin['avatar'];

        if (!empty($_FILES['avatar']['name'])) {

            $uploadDir = "../uploads/avatars/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExt = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

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
            $stmt->bind_param("sssssi", $fullname, $email, $phone, $address, $avatarFileName, $adminId);
            $stmt->execute();

            /* Cập nhật lại session admin */
            $_SESSION['admin']['fullname'] = $fullname;
            $_SESSION['admin']['email']    = $email;

            header("Location: admin_profile.php?success=1");
            exit;
        }
    }

    /* ================= ĐỔI MẬT KHẨU ================= */
    if (isset($_POST['change_password'])) {

        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        if ($current == "" || $new == "" || $confirm == "") {
            $errors[] = "Vui lòng nhập đầy đủ các trường mật khẩu.";
        } elseif (!password_verify($current, $admin['password'])) {
            $errors[] = "Mật khẩu hiện tại không đúng.";
        } elseif ($new !== $confirm) {
            $errors[] = "Mật khẩu mới và xác nhận không khớp.";
        } elseif (strlen($new) < 6) {
            $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
        } else {

            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $hash, $adminId);
            $stmt->execute();

            header("Location: admin_profile.php?success=2");
            exit;
        }
    }
}

/* ===== ĐƯỜNG DẪN AVATAR ===== */
$avatarPath = !empty($admin['avatar'])
    ? "../uploads/avatars/" . htmlspecialchars($admin['avatar'])
    : "../images/user.png";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    <title>BonSai | Hồ Sơ Admin</title>
    <style>
        .avatar-img {
            object-fit: cover;
            border: 3px solid #198754;
        }
        .profile-card {
            max-width: 620px;
            margin: 0 auto;
        }
        .section-divider {
            border-top: 2px dashed #dee2e6;
            margin: 30px 0;
        }
        .admin-badge {
            background: #198754;
            color: white;
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<?php include '../admin_includes/header.php'; ?>

<div class="container py-5">

    <h2 class="text-center fw-bold text-success mb-4">Hồ Sơ Admin</h2>

    <div class="profile-card">
        <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php
                        if ($_GET['success'] == 1) echo "✅ Cập nhật hồ sơ thành công!";
                        if ($_GET['success'] == 2) echo "✅ Đổi mật khẩu thành công!";
                    ?>
                </div>
            <?php endif; ?>

            <!-- ================= FORM PROFILE ================= -->
            <form method="POST" enctype="multipart/form-data">

                <div class="text-center mb-4">
                    <img src="<?= $avatarPath ?>"
                         class="rounded-circle shadow avatar-img mb-3"
                         width="120" height="120"
                         alt="Avatar Admin">

                    <div class="admin-badge">🛡️ Quản trị viên</div>

                    <h4 class="text-black mb-1">
                        <?= htmlspecialchars($admin['fullname']) ?>
                    </h4>
                    <p class="text-muted small"><?= htmlspecialchars($admin['email']) ?></p>

                    <div class="mb-3">
                        <label class="form-label text-muted small">Ảnh đại diện mới</label>
                        <input type="file" name="avatar" class="form-control form-control-sm"
                               accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Họ và Tên</label>
                    <input type="text" name="fullname" class="form-control"
                           value="<?= htmlspecialchars($admin['fullname']) ?>">
                </div>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($admin['email']) ?>">
                </div>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= htmlspecialchars($admin['phone']) ?>">
                </div>


                <button type="submit" name="update_profile" class="btn btn-success w-100">
                    💾 Lưu thay đổi
                </button>
            </form>

            <div class="section-divider"></div>

            <!-- ================= FORM ĐỔI MẬT KHẨU ================= -->
            <form method="POST">

                <h5 class="text-black mb-3">🔒 Đổi mật khẩu</h5>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Mật khẩu hiện tại</label>
                    <div class="input-group">
                        <input type="password" name="current_password"
                               class="form-control" id="currentPassword">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="currentPassword">
                            <img src="../images/hide.svg" width="16" height="16">
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Mật khẩu mới</label>
                    <div class="input-group">
                        <input type="password" name="new_password"
                               class="form-control" id="newPassword">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="newPassword">
                            <img src="../images/hide.svg" width="16" height="16">
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-black fw-semibold">Xác nhận mật khẩu mới</label>
                    <div class="input-group">
                        <input type="password" name="confirm_password"
                               class="form-control" id="confirmPassword">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                                data-target="confirmPassword">
                            <img src="../images/hide.svg" width="16" height="16">
                        </button>
                    </div>
                </div>

                <button type="submit" name="change_password" class="btn btn-dark w-100">
                    🔑 Đổi mật khẩu
                </button>

            </form>

        </div>
    </div>
</div>

<?php include '../admin_includes/footer.php'; ?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
    // TOGGLE HIỆN/ẨN MẬT KHẨU
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

</body>
</html>