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
<style>
    /* ===== THANH DANH MỤC ===== */

/* ===== THANH DANH MỤC ===== */

.category-bar{
background: linear-gradient(90deg,#f8f9fa,#ffffff);
border-top:1px solid #e5e5e5;
border-bottom:1px solid #e5e5e5;
padding:12px 0;
box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

.category-title{
font-weight:700;
font-size:16px;
color:#198754;
margin-right:25px;
white-space:nowrap;
letter-spacing:0.5px;
}

.category-menu{
display:flex;
gap:15px;
list-style:none;
margin:0;
padding:0;
flex-wrap:wrap;
}

.category-menu li a{
text-decoration:none;
color:#444;
font-size:14px;
font-weight:500;
padding:7px 14px;
border-radius:20px;
transition:all 0.25s ease;
background:#f1f3f5;
}

/* hover */
.category-menu li a:hover{
background:#198754;
color:white;
transform:translateY(-2px);
box-shadow:0 3px 8px rgba(0,0,0,0.15);
}
</style>


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

    <div class="collapse navbar-collapse" id="navbarsFurni">
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
<nav class="category-bar">
<div class="container d-flex align-items-center">

<span class="category-title">CÁC LOẠI SẢN PHẨM</span>

<ul class="category-menu">

<?php foreach ($categories as $cat): ?>
<li>
<a href="sshop.php?category=<?= (int)$cat['id'] ?>">
<?= htmlspecialchars($cat['name']) ?>
</a>
</li>
<?php endforeach; ?>

</ul>

</div>
</nav>
<!-- End Header -->

<!-- BOOTSTRAP JS (BẮT BUỘC ĐỂ DROPDOWN CHẠY) -->
<script src="../js/bootstrap.bundle.min.js"></script>