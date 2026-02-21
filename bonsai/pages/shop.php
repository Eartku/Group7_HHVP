<?php
require_once "../config/db.php";

/* ===============================
   1. LẤY CATEGORY (NẾU CÓ)
================================ */
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$cat_name = "Tất cả sản phẩm";

if ($category_id > 0) {
    $cat_result = $conn->query("SELECT * FROM categories WHERE id = $category_id");

    if ($cat_result && $cat_result->num_rows > 0) {
        $cat = $cat_result->fetch_assoc();
        $cat_name = $cat['name'];
    }
}

/* ===============================
   2. PHÂN TRANG
================================ */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 8;
$offset = ($page - 1) * $limit;

/* ===============================
   3. WHERE CONDITION
================================ */
$where = "";
if ($category_id > 0) {
    $where = " WHERE category_id = $category_id";
}

/* ===============================
   4. ĐẾM TỔNG SẢN PHẨM
================================ */
$countSql = "SELECT COUNT(*) as total FROM products $where";
$countResult = $conn->query($countSql);
$totalRow = $countResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

/* ===============================
   5. LẤY DANH SÁCH SẢN PHẨM
================================ */
$sql = "SELECT id, name, price, image
        FROM products
        $where
        ORDER BY id DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'; ?>
    <title>
        BonSai | Shop
    </title>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="hero">
        <div class="center-row text-center">
            <h1 class="glow"><?= htmlspecialchars($cat_name) ?></h1>
            <span style="color: aliceblue;">
                Tạo nên một không gian nhỏ xinh!
            </span>
        </div>
    </div>
    <!-- Search Section -->
    <?php include '../pages/products.php'; ?>

    <?php include '../includes/footer.php'; ?>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/custom.js"></script>
    <div id="toast"></div>
</body>
</html>
