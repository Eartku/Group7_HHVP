<?php
// header.php
require_once "../config/db.php";

/* ===== LẤY DANH MỤC ===== */
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);
$categories = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

#avatar
$avatar = "../images/avatar.svg";
if (!empty($_SESSION['admin']['avatar'])) {
    $avatar = "../uploads/" . $_SESSION['admin']['avatar'];
}

?>

<!-- Start Header -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">
      
<div class="container">
    <!-- LOGO -->
    <a class="navbar-brand">Admin BonSai<span>🌱</span></a>

    <!-- MOBILE BUTTON -->
    <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarsFurni">
        <span class="navbar-toggler-icon"></span>
    </button>



      <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
            <li>
              <a href="../admin_customer/customermanage.php" class="hover-text-glow-small"
                  style="font-size: 13px"
                >KHÁCH HÀNG</a
              >
            </li>
            <li>
              <a href="admin/admin_type.html" class="hover-text-glow-small"
                 style="font-size: 13px"
                >LOẠI SẢN PHẨM</a
              >
            </li>
            <li>
              <a href="../admin_product/sshop.php" class="hover-text-glow-small"
                 style="font-size: 13px"
                >SẢN PHẨM</a
              >
            </li>
             <li>
              <a href="../admin_importproduct/adminipd.php" class="hover-text-glow-small"
                 style="font-size: 13px"
                >NHẬP SẢN PHẨM</ax
              >
            </li>
             <li>
              <a href="admin/admin_sellingprice.html" class="hover-text-glow-small"
                 style="font-size: 13px"
                >GIÁ BÁN</a
              >
            </li>
            <li>
              <a  href="admin/admin_order.html" class="hover-text-glow-small"
                 style="font-size: 13px"
                >ĐƠN HÀNG</a
              >
            </li>
            <li>
              <a href="admin/admin_stock1.html" class="hover-text-glow-small"
                 style="font-size: 13px"
                >KHO</a
              >
            </li>
          </ul>


        <!-- ICON -->
            <ul class="custom-navbar-cta navbar-nav ms-4 align-items-center">

                <li class="nav-item">
                    <a href="../admin/admin.php" class="nav-link">
                        <img src="../images/home.svg"
                        style="width:35px;height:35px;">
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../admin_login/admin_login.php" class="nav-link">
                        <img src="../images/exit.svg"
                        style="width:35px;height:35px;">
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../adminprofile/admin_profile.php" class="nav-link">
                        <img src="<?php echo $avatar; ?>"
                        style="width:35px;height:35px;border-radius:50%;">
                    </a>
                </li>

            </ul>
    

</div>
   

</nav>
<!-- End Header -->

<!-- BOOTSTRAP JS (BẮT BUỘC ĐỂ DROPDOWN CHẠY) -->
<script src="../js/bootstrap.bundle.min.js"></script>