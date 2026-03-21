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
    COALESCE(ROUND(AVG(il.import_price) * 1.1), 0) AS sale_price
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
    <?php include '../admin_includes/header.php'; ?>
        <div class="hero">
            <div class="center-row text-center">
                <h1 class="glow">Quản Lý Nhập sản phẩm</h1>
                </br>
                    <a href="add_import.php"                
                    style="
                    background-color:#28a745;
                    color:white;
                    padding:8px 20px;
                    border-radius:50px;
                    text-decoration:none;
                    font-weight:bold;
                    display:inline-block;
                
                    ">
                    + Tạo phiếu nhập
                    </a>
            </div>
        </div>
        
            <style>
                .filter-box {
                    margin-bottom: 20px;
                    padding: 15px;
                    border: 1px solid #ddd;
                    border-radius: 0;
                    background-color: #f8f9fa;

                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;

                    justify-content: center;
                    align-items: center;
                }

                /* chỉ ảnh hưởng nút trong form */
                .filter-box .btn {
                    border-radius: 35px;
                    padding: 6px 15px;
                    min-width: 80px;
                    font-weight: 500;

                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                /* chỉ ảnh hưởng input trong form */
                .filter-box .form-control,
                .filter-box .form-select {
                    height: 37px;
                    border-radius: 20px;
                    font-family: "Inter", sans-serif;
                }
            </style>
      
            <form method="GET" action="adminipd.php" 
                class="filter-box d-flex flex-wrap gap-2">

                <!-- TÌM KIẾM -->
                <input type="text" name="keyword" placeholder="Tìm theo tên sản phẩm..."
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                    class="form-control" style="max-width:200px;">

                <!-- TỪ NGÀY -->
                <input type="date" name="from_date"
                    value="<?= $_GET['from_date'] ?? '' ?>"
                    class="form-control" style="max-width:150px;">

                <!-- ĐẾN NGÀY -->
                <input type="date" name="to_date"
                    value="<?= $_GET['to_date'] ?? '' ?>"
                    class="form-control" style="max-width:150px;">

                <!-- TRẠNG THÁI -->
                <select name="status" class="form-select" style="max-width:150px;">
                    <option value="">Trạng thái</option>
                    <option value="pending" <?= (($_GET['status'] ?? '')=='pending')?'selected':'' ?>>Đang xử lý</option>
                    <option value="completed" <?= (($_GET['status'] ?? '')=='completed')?'selected':'' ?>>Đã hoàn thành</option>
                    <option value="cancelled" <?= (($_GET['status'] ?? '')=='cancelled')?'selected':'' ?>>Đã hủy</option>
                </select>

                <!-- BUTTON -->
                <button class="btn btn-success">Lọc</button>
                <a href="adminipd.php" class="btn btn-secondary"> Reset</a>

            </form>
      
    <?php include "pimports.php"; ?>
    <?php include '../admin_includes/footer.php'; ?>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/custom.js"></script>
    <div id="toast"></div>
</body>
</html>
