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
    $where = " WHERE p.category_id = $category_id";
}

/* ===============================
   4. ĐẾM TỔNG SẢN PHẨM
================================ */
$countSql = "SELECT COUNT(*) as total FROM products p $where";
$countResult = $conn->query($countSql);
$totalRow = $countResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

/* ===============================
   5. LẤY DANH SÁCH SẢN PHẨM
   TÍNH GIÁ TỪ INVENTORY_LOGS
================================ */
$sql = "
SELECT 
    p.id,
    p.name,
    p.image,
    p.status,
    p.profit_rate,
    COALESCE(AVG(il.import_price), 0) AS avg_import_price
FROM products p
LEFT JOIN inventory_logs il 
    ON il.product_id = p.id
$where
GROUP BY p.id
ORDER BY p.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    <title>
        BonSai | Shop
    </title>
</head>
<body>
    <?php include 'header_pd.php'; ?>
            <div class="hero">
                <div class="center-row text-center">
                    <h1 class="glow"><?= htmlspecialchars($cat_name) ?></h1>
                        <span style="color: aliceblue;">
                        </span>
                            </br>
                                <a href="add_product.php"
                                style="
                                background-color:#28a745;
                                color:white;
                                padding:8px 20px;
                                border-radius:50px;
                                text-decoration:none;
                                font-weight:bold;
                                display:inline-block;
                            
                                ">
                                + Thêm sản phẩm
                                </a>
                </div>
            </div>
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
    <!-- Search Section -->
    <?php include 'pproducts.php'; ?>
    <?php include '../admin_includes/footer.php'; ?>
         <script src="../js/bootstrap.bundle.min.js"></script>
         <script src="../js/custom.js"></script>
    <div id="toast"></div>
</body>
</html>
