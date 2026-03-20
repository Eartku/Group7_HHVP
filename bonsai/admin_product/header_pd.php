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

/* ===== AVATAR ADMIN ===== */
$avatar = "../images/avatar.svg";
if (!empty($_SESSION['admin']['avatar'])) {
    $avatar = "../uploads/" . $_SESSION['admin']['avatar'];
}
?>

<style>
/* ===== THANH DANH MỤC ===== */
.category-bar {
    background: linear-gradient(90deg,#f8f9fa,#ffffff);
    border-top:1px solid #e5e5e5;
    border-bottom:1px solid #e5e5e5;
    padding:12px 0;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

.category-title {
    font-weight: 700;
    font-size: 25px;
    color:#198754;
    margin-right:25px;
    white-space:nowrap;
    letter-spacing:0.5px;
}

.category-menu {
    display:flex;
    gap:15px;
    list-style:none;
    margin:0;
    padding:0;
    flex-wrap:wrap;
}

.category-menu li a {
    text-decoration:none;
    color:#444;
    font-size:14px;
    font-weight:500;
    padding:7px 14px;
    border-radius:20px;
    transition:all 0.25s ease;
    background:#f1f3f5;
}

.category-menu li a:hover {
    background:#198754;
    color:white;
    transform:translateY(-2px);
    box-shadow:0 3px 8px rgba(0,0,0,0.15);
}

/* ===== FORM TÌM KIẾM + LỌC ===== */
.search-filter-form {
    display: flex;
    flex-wrap: wrap;
    gap:12px;
    align-items: center;
    margin-top: 12px;
}

.search-filter-form .form-control,
.search-filter-form .form-select {
    border-radius: 25px;
    padding: 6px 10px;
    min-width: 150px;
    width: 20%;
}

.search-filter-form button,
.search-filter-form a.btn {
    border-radius: 25px;
    padding: 6px 14px;
    min-width: 80px;
    font-weight: 500;
}

@media(max-width:768px){
    .search-filter-form {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>

<!-- Start Header -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">
<div class="container">
    <!-- LOGO -->
    <a class="navbar-brand">BonSai<span>🌱</span></a>

    <!-- MOBILE BUTTON -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsFurni">
        <!-- MENU CHÍNH -->
        <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
            <li><a href="../admin_customer/customermanage.php" class="hover-text-glow-small" style="font-size:13px">KHÁCH HÀNG</a></li>
            <li><a href="admin/admin_type.html" class="hover-text-glow-small" style="font-size:13px">LOẠI SẢN PHẨM</a></li>
            <li><a href="../admin_product/sshop.php" class="hover-text-glow-small" style="font-size:13px">SẢN PHẨM</a></li>
            <li><a href="../admin_importproduct/adminipd.php" class="hover-text-glow-small" style="font-size:13px">NHẬP SẢN PHẨM</a></li>
            <li><a href="admin/admin_sellingprice.html" class="hover-text-glow-small" style="font-size:13px">GIÁ BÁN</a></li>
            <li><a href="../admin_oder/admin_order.php" class="hover-text-glow-small" style="font-size:13px">ĐƠN HÀNG</a></li>
            <li><a href="admin/admin_stock1.html" class="hover-text-glow-small" style="font-size:13px">KHO</a></li>
        </ul>

        <!-- ICON -->
        <ul class="custom-navbar-cta navbar-nav ms-4 align-items-center">
            <li class="nav-item"><a href="../admin/admin.php" class="nav-link"><img src="../images/home.svg" style="width:35px;height:35px;"></a></li>
            <li class="nav-item"><a href="admin_logout.php" class="nav-link"><img src="../images/exit.svg" style="width:35px;height:35px;"></a></li>
            <li class="nav-item"><a href="../adminprofile/profile.php" class="nav-link"><img src="<?= $avatar ?>" style="width:35px;height:35px;border-radius:50%;"></a></li>
        </ul>
    </div>
</div>
</nav>

<!-- DANH MỤC & TÌM KIẾM/LỌC -->
<nav class="category-bar">
<div class="container">

    <!-- DANH MỤC -->
    <div class="d-flex flex-wrap align-items-center mb-3">
        <span class="category-title me-3">CÁC LOẠI SẢN PHẨM</span>
        <ul class="category-menu">
            <?php foreach ($categories as $cat): ?>
            <li><a href="sshop.php?category=<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- FORM TÌM KIẾM & LỌC -->
    <form method="GET" action="sshop.php" class="search-filter-form">
        <input type="hidden" name="category" value="<?= (int)$category_id ?>">

        <!-- SEARCH -->
        <input type="text" name="keyword" placeholder="Tìm sản phẩm..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>" class="form-control">

        <!-- STATUS -->
        <select name="status" class="form-select">
            <option value="">Trạng thái</option>
            <option value="1" <?= (($_GET['status'] ?? '')==='1')?'selected':'' ?>>Đang bán</option>
            <option value="0" <?= (($_GET['status'] ?? '')==='0')?'selected':'' ?>>Ngừng bán</option>
        </select>

        <!-- STOCK -->
        <select name="stock" class="form-select">
            <option value="">Tồn kho</option>
            <option value="1" <?= (($_GET['stock'] ?? '')==='1')?'selected':'' ?>>Còn hàng</option>
            <option value="0" <?= (($_GET['stock'] ?? '')==='0')?'selected':'' ?>>Hết hàng</option>
        </select>

        <!-- BUTTONS -->
        <button class="btn btn-success">Lọc</button>
        <a href="sshop.php" class="btn btn-secondary">Reset</a>
    </form>

</div>
</nav>

<!-- BOOTSTRAP JS -->
<script src="../js/bootstrap.bundle.min.js"></script>