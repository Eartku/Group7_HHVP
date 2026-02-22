<?php
require_once "../config/db.php";

/* ===============================
   1. LẤY DANH MỤC
================================ */
$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
}

/* ===============================
   2. GET PARAMS
================================ */
$searchQuery = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category'] ?? 0);
$minPrice = (float)($_GET['min_price'] ?? 0);
$maxPrice = (float)($_GET['max_price'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));

$limit = 12;
$offset = ($page - 1) * $limit;

/* ===============================
   3. BUILD WHERE
================================ */
$conditions = [];
$params = [];
$types = "";

if ($searchQuery !== '') {
    $conditions[] = "p.name LIKE CONCAT('%', ?, '%')";
    $params[] = $searchQuery;
    $types .= "s";
}

if ($categoryId > 0) {
    $conditions[] = "p.category_id = ?";
    $params[] = $categoryId;
    $types .= "i";
}

if ($minPrice > 0) {
    $conditions[] = "sale_price >= ?";
    $params[] = $minPrice;
    $types .= "d";
}

if ($maxPrice > 0) {
    $conditions[] = "sale_price <= ?";
    $params[] = $maxPrice;
    $types .= "d";
}

$whereClause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

/* ===============================
   4. COUNT TOTAL
================================ */
$countSql = "
SELECT COUNT(DISTINCT p.id) as total
FROM products p
$whereClause
";

$stmt = $conn->prepare($countSql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$totalProducts = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

$totalPages = ceil($totalProducts / $limit);

/* ===============================
   5. GET PRODUCTS
================================ */
$sql = "
SELECT 
    p.id, 
    p.name, 
    p.image,
    p.profit_rate,
    
    -- Tổng tồn kho
    IFNULL((
        SELECT SUM(quantity)
        FROM inventory
        WHERE product_id = p.id
    ), 0) as total_stock,

    -- Giá nhập bình quân
    IFNULL((
        SELECT SUM(import_price * quantity) / NULLIF(SUM(quantity), 0)
        FROM inventory_logs
        WHERE product_id = p.id
    ), 0) as avg_import_price,

    -- Giá bán (tính từ giá nhập x tỷ lệ lợi nhuận)
    ROUND(
        IFNULL((
            SELECT SUM(import_price * quantity) / NULLIF(SUM(quantity), 0)
            FROM inventory_logs
            WHERE product_id = p.id
        ), 0) * (1 + p.profit_rate / 100)
    , -3) as sale_price

FROM products p
$whereClause
GROUP BY p.id
ORDER BY p.id DESC
LIMIT ? OFFSET ?
";

$params2 = $params;
$types2 = $types . "ii";
$params2[] = $limit;
$params2[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();

$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'; ?>
    <title>BonSai | Tìm kiếm</title>
    <link rel="stylesheet" href="../css/search.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<!-- Search Form -->
<div class="search-container">
    <form id="searchFilterForm" class="search-form" autocomplete="off">
        
        <div class="filter-group">
            <!-- Search Input -->
            <div class="search-group">
                <input
                    id="searchInput"
                    name="q"
                    type="search"
                    class="form-control search-input"
                    placeholder="Tìm kiếm sản phẩm..."
                    aria-label="Tìm kiếm sản phẩm"
                    value="<?= htmlspecialchars($searchQuery) ?>"
                />
            </div>

            <!-- Category Filter -->
            <div class="filter-group">
                <span class="price-separator" style="color:aliceblue">Loại:</span>
                <select id="categoryFilter" name="category" class="form-select">
                    <option value="0">Tất cả danh mục</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Price Filter -->
            <div class="filter-group price-filter">
                <span class="price-separator" style="color:aliceblue">Giá từ:</span>
                <input
                    type="number"
                    id="minPrice"
                    name="min_price"
                    class="form-control"
                    placeholder="VND"
                    min="0"
                    value="<?= $minPrice > 0 ? $minPrice : '' ?>"
                />
                <span class="price-separator" style="color:aliceblue">- Giá đến:</span>
                <input
                    type="number"
                    id="maxPrice"
                    name="max_price"
                    class="form-control"
                    placeholder="VND"
                    min="0"
                    value="<?= $maxPrice > 0 ? $maxPrice : '' ?>"
                />
            </div>

            <!-- Search Button -->
            <div class="filter-group">
                <button type="submit" class="btn btn-dark">
                    <img src="../images/search.svg" width="18" alt="Tìm">
                </button>
            </div>
        </div>

        <!-- Live search results (for quick search) -->
        <div id="searchResults" class="search-results"></div>

    </form>
</div>

<!-- Search Results -->
<div class="product-section py-5">
    <div class="container">
        
        <?php if (!empty($searchQuery) || $categoryId > 0 || $minPrice > 0 || $maxPrice > 0): ?>
            
            <div class="mb-4">
                <p class="text-muted">
                    <?php if ($totalProducts > 0): ?>
                        Tìm thấy <strong><?= $totalProducts ?></strong> sản phẩm
                        <?php if (!empty($searchQuery)): ?>
                            với từ khóa "<strong><?= htmlspecialchars($searchQuery) ?></strong>"
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="row">
                <?php include '../includes/product_grid.php'; ?>
            </div>

            <?php include '../includes/pagination.php'; ?>

        <?php else: ?>
            <div class="text-center text-muted py-5">
                <h4>Nhập từ khóa hoặc chọn bộ lọc để tìm kiếm</h4>
                <p>Bạn có thể tìm kiếm theo tên, danh mục hoặc khoảng giá</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="../js/search.js"></script>
<script src="../js/ajax.js"></script>
<script src="../js/cartmsg.js"></script>
<div id="toast"></div>
</body>
</html>
