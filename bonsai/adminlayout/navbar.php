<?php
$avatar = "../images/avatar.svg";
if (!empty($_SESSION['admin']['avatar'])) {
    $avatar = "../uploads/" . $_SESSION['admin']['avatar'];
}
?>

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

            <ul class="custom-navbar-cta navbar-nav ms-4 align-items-center">

                <li class="nav-item">
                    <a href="../admin.php" class="nav-link">
                        <img src="../images/home.svg"
                        style="width:35px;height:35px;">
                    </a>
                </li>

                <li class="nav-item">
                    <a href="admin_logout.php" class="nav-link">
                        <img src="../images/exit.svg"
                        style="width:35px;height:35px;">
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../adminprofile/profile.php" class="nav-link">
                        <img src="<?php echo $avatar; ?>"
                        style="width:35px;height:35px;border-radius:50%;">
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>