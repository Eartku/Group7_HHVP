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

/* ===== LẤY AVATAR USER ===== */
$userAvatar = "../images/user.png";

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("SELECT avatar FROM users WHERE id=?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $u = $res->fetch_assoc();

    if (!empty($u['avatar'])) {
        $userAvatar = "../uploads/avatars/" . htmlspecialchars($u['avatar']);
    }
}
?>

<!-- Start Header -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">
<div class="container">
    <!-- LOGO -->
    <a class="navbar-brand">BonSai<span>🌱</span></a>

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
              <a href="admin/customermanage.html" class="hover-text-glow-small"
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
              <a  href="admin_product/sshop.php" class="hover-text-glow-small"
                 style="font-size: 13px"
                >SẢN PHẨM</a
              >
            </li>
             <li>
              <a href="admin_importproduct/adminipd.php" class="hover-text-glow-small"
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
        <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">

            <li>
                <a href="../admin.html" class="nav-link hover-box">
                    <div class="front">
                        <img src="../images/home.svg" style="scale:1.3">
                    </div>
                    <div class="back">
                        <span>TRANG CHỦ</span>
                    </div>
                </a>
            </li>

            <li style="margin-left:10px">
                <a class="nav-link hover-box" href="admin_login.html">
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
</nav>
<!-- End Header -->

<!-- BOOTSTRAP JS (BẮT BUỘC ĐỂ DROPDOWN CHẠY) -->
<script src="../js/bootstrap.bundle.min.js"></script>