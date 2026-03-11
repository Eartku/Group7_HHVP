<?php
session_start();
require "../config/db.php";
/* xử lý khóa / mở */
if (isset($_GET['action']) && isset($_GET['id'])) {

    $id = intval($_GET['id']);

    if ($_GET['action'] == "lock") {

        $sql = "UPDATE users SET status='inactive' WHERE id=$id";

    }
    elseif ($_GET['action'] == "unlock") {

        $sql = "UPDATE users SET status='active' WHERE id=$id";

    }

    mysqli_query($conn, $sql);

    header("Location: customermanage.php");
    exit();
}

/* kiểm tra admin */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../adminlogin/admin_login.php");
    exit();
}

/* lấy danh sách khách hàng */
$sql = "SELECT * FROM users WHERE role='customer'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="../images/logo.png">
    <title>BonSai | Quản Lý Khách Hàng</title>

    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
          rel="stylesheet">

    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/hover.css" rel="stylesheet">
    <link href="../css/page2.css" rel="stylesheet">
    <link href="../css/tiny-slider.css" rel="stylesheet">

</head>

<body>

<!-- Navbar -->

<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand" href="../admin.php">
            ADMIN<span>🌱</span>
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarsFurni">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">

            <ul class="custom-navbar-nav navbar-nav ms-auto">

                <li>
                    <a href="customermanage.php"
                       class="nav-link active-import"
                       style="font-size:19px">
                        KHÁCH HÀNG
                    </a>
                </li>

                <li>
                    <a href="admin_type.php"
                       class="nav-link hover-text-glow"
                       style="font-size:14px">
                        LOẠI SẢN PHẨM
                    </a>
                </li>

                <li>
                    <a href="admin_product.php"
                      class="nav-link hover-text-glow"
                       style="font-size:14px">
                        SẢN PHẨM
                    </a>
                </li>

                <li>
                    <a href="admin_importproduct.php"
                      class="nav-link hover-text-glow"
                       style="font-size:14px">
                        NHẬP SẢN PHẨM
                    </a>
                </li>

                <li>
                    <a href="admin_sellingprice.php"
                      class="nav-link hover-text-glow"
                       style="font-size:14px">
                        GIÁ BÁN
                    </a>
                </li>

                <li>
                    <a href="admin_order.php"
                      class="nav-link hover-text-glow"
                       style="font-size:14px">
                        ĐƠN HÀNG
                    </a>
                </li>

                <li>
                    <a href="admin_stock1.php"
                      class="nav-link hover-text-glow"
                       style="font-size:14px">
                        KHO
                    </a>
                </li>

            </ul>

            <ul class="custom-navbar-cta navbar-nav ms-4">

                <li>
                    <a href="../admin.php" class="nav-link hover-box">

                        <div class="front">
                            <img src="../images/home.svg" style="scale:1.3">
                        </div>

                        <div class="back">
                            <span>Trang chủ</span>
                        </div>

                    </a>
                </li>

                <li style="margin-left:10px">

                    <a class="nav-link hover-box" href="admin_logout.php">

                        <div class="front">
                            <img src="../images/exit.svg">
                        </div>

                        <div class="back">
                            <span>Đăng xuất</span>
                        </div>

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>

<!-- Customer Management -->

<div class="container py-5">

    <div class="text-center">
        <h2 class="fw-bold text-success" style="padding:30px">
            Quản lý Khách Hàng
        </h2>
    </div>

    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

        <table class="table table-bordered text-center align-middle">

            <thead class="table-light">

                <tr>
                    <th>Mã KH</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>

            </thead>

            <tbody>

                <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <tr>

                    <td>
                        #C<?php echo str_pad($row['id'], 4, "0", STR_PAD_LEFT); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['fullname']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['email']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['phone']); ?>
                    </td>

                    <td>

                        <?php
                            if ($row['status'] == "active") {
                                echo '<span class="badge bg-success">Hoạt động</span>';
                            }
                            elseif ($row['status'] == "warning") {
                                echo '<span class="badge bg-warning">Cảnh báo</span>';
                            }
                            elseif ($row['status'] == "inactive") {
                                echo '<span class="badge bg-danger">Bị khóa</span>';
                            }
                        ?>

                    </td>

                    <td>

                        <a href="editcustomer.php?id=<?php echo $row['id']; ?>"
                           class="btn btn-sm btn-outline-success me-1">

                            <i class="fas fa-edit"></i>
                            Chỉnh sửa

                        </a>

                        <?php if ($row['status'] == "active") { ?>

                            <a href="?action=lock&id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-outline-secondary">

                                <i class="fas fa-lock"></i>
                                Khóa

                            </a>

                        <?php } else { ?>

                            <a href="?action=unlock&id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-outline-primary">

                                <i class="fas fa-unlock"></i>
                                Mở

                            </a>

                        <?php } ?>

                    </td>

                </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>

<!-- Footer -->

<footer class="footer-section bg-dark text-light mt-5">

    <div class="container py-4">

        <div class="border-top pt-3 mt-3 text-center">

            <p class="mb-0">

                Copyright ©

                <?php echo date("Y"); ?>

                All Rights Reserved — Designed by
                <a href="group7.php" class="text-light">Group 7</a>

            </p>

        </div>

    </div>

</footer>

</body>
</html>