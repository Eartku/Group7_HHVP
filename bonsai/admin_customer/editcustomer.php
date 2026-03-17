<?php
session_start();
require "../config/db.php";

    // /* kiểm tra admin */
    // if (!isset($_SESSION['admin'])) {
    //     header("Location: ../adminlogin/admin_login.php");
    //     exit();
    // }

/* lấy id khách hàng */
if (!isset($_GET['id'])) {
    header("Location: customermanage.php");
    exit();
}

$id = intval($_GET['id']);

/* lấy dữ liệu khách hàng */
$sql = "SELECT * FROM users WHERE id=$id AND role='customer'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: customermanage.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

/* đường dẫn avatar */
$avatarPath = !empty($user['avatar'])
    ? "../uploads/avatars/" . htmlspecialchars($user['avatar'])
    : "../images/avatar.svg";
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BonSai | Chỉnh Sửa Khách Hàng</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/hover.css" rel="stylesheet">
    <link href="../css/animation.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>

<body>

    <!-- Navbar -->
   <?php include "../admin_includes/header.php"; ?>


    <!-- Hero -->
    <div class="text-center">
        <h2 class="fw-bold text-success" style="padding:30px">
            Chỉnh sửa thông tin khách hàng
        </h2>
    </div>


    <!-- Form -->
    <div class="untree_co-section">

        <div class="container">

            <div class="row justify-content-center">

                <div class="col-md-8">

                    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

                        <form method="POST" action="updatecustomer.php">

                            <!-- Avatar + name -->
                            <div class="text-center mb-4">

                                <div class="text-center mb-4">

                                     <img src="<?= $avatarPath ?>"
                                     class="rounded-circle shadow mb-3"
                                    width="120" height="120">

                                </div>

                                <h4 class="mt-3 text-black fw-bold">
                                    <?php echo htmlspecialchars($user['fullname']); ?>
                                </h4>


                                <!-- STATUS -->
                                <div class="btn-group w-100" role="group" style="margin-bottom:10px">

                                    <input type="radio"
                                           class="btn-check"
                                           name="status"
                                           id="active"
                                           value="active"
                                           <?php if($user['status']=="active") echo "checked"; ?>>

                                    <label class="btn btn-outline-success" for="active">
                                        <i class="fas fa-user-check me-1"></i>
                                        Hoạt động
                                    </label>


                                    <input type="radio"
                                           class="btn-check"
                                           name="status"
                                           id="locked"
                                           value="inactive"
                                           <?php if($user['status']=="inactive") echo "checked"; ?>>

                                    <label class="btn btn-outline-danger" for="locked">
                                        <i class="fas fa-lock me-1"></i>
                                        Bị khóa
                                    </label>


                                    <input type="radio"
                                           class="btn-check"
                                           name="status"
                                           id="warning"
                                           value="warning"
                                           <?php if($user['status']=="warning") echo "checked"; ?>>

                                    <label class="btn btn-outline-warning" for="warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Cảnh báo
                                    </label>

                                </div>

                            </div>


                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">


                            <!-- ID -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Mã khách hàng
                                </label>

                                <input type="text"
                                       class="form-control"
                                       value="#C<?php echo str_pad($user['id'],4,'0',STR_PAD_LEFT); ?>"
                                       readonly>

                            </div>


                            <!-- FULLNAME -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Họ tên
                                </label>

                                <input type="text"
                                       name="fullname"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($user['fullname']); ?>">

                            </div>


                            <!-- USERNAME -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Username
                                </label>

                                <input type="text"
                                       name="username"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($user['username']); ?>">

                            </div>


                            <!-- PASSWORD -->
                            <div class="mb-3 position-relative">

                                <label class="form-label fw-bold">
                                    Mật khẩu
                                </label>

                                <div class="input-group">

                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="form-control"
                                           value="">

                                    <button type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="togglePassword()">

                                        <i id="eyeIcon" class="fas fa-eye"></i>

                                    </button>

                                    <button type="button"
                                            class="btn btn-success"
                                            onclick="changePassword()">

                                        <i class="fas fa-pen me-1"></i>
                                        Đổi

                                    </button>

                                </div>

                            </div>


                            <!-- EMAIL -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Email
                                </label>

                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($user['email']); ?>">

                            </div>


                            <!-- PHONE -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Số điện thoại
                                </label>

                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($user['phone']); ?>">

                            </div>


                            <!-- ADDRESS -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Địa chỉ
                                </label>

                                <input type="text"
                                       name="address"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($user['address']); ?>">

                            </div>


                            <!-- DATE -->
                            <div class="mb-3">

                                <label class="form-label fw-bold">
                                    Ngày tham gia
                                </label>

                                <input type="date"
                                       class="form-control"
                                       value="<?php echo date('Y-m-d', strtotime($user['created_at'])); ?>"
                                       readonly>

                            </div>


                            <!-- BUTTON -->
                            <div class="text-center mt-4">

                                <a href="customermanage.php"
                                   class="btn btn-dark me-2">

                                    <i class="fas fa-arrow-left me-1"></i>
                                    Quay lại

                                </a>


                                <button type="submit"
                                        class="btn btn-success">

                                    <i class="fas fa-save me-1"></i>
                                    Lưu thay đổi

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

  <?php include '../admin_includes/footer.php'; ?>

<script>

function togglePassword(){

    const input=document.getElementById("password");
    const icon=document.getElementById("eyeIcon");

    if(input.type==="password"){
        input.type="text";
        icon.classList.replace("fa-eye","fa-eye-slash");
    }
    else{
        input.type="password";
        icon.classList.replace("fa-eye-slash","fa-eye");
    }
}

function changePassword(){

    let newPass = prompt("Nhập mật khẩu mới:");

    if(newPass != null && newPass != ""){

        document.getElementById("password").value = newPass;

        alert("Đã đặt mật khẩu mới, bấm Lưu để cập nhật");

    }

}

</script>

<script src="../js/bootstrap.bundle.min.js"></script>

</body>
</html>