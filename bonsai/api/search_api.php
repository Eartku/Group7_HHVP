<?php
require_once "../config/db.php";

header('Content-Type: application/json');

// Get parameters
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Build WHERE clause
$conditions = [];
$params = [];
$types = "";

if (!empty($q)) {
    $conditions[] = "p.name LIKE CONCAT('%', ?, '%')";
    $params[] = $q;
    $types .= "s";
}

if ($category > 0) {
    $conditions[] = "p.category_id = ?";
    $params[] = $category;
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

// If no search criteria, return empty
if (empty($q) && $category == 0 && $minPrice == 0 && $maxPrice == 0) {
    echo json_encode([]);
    exit;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Query with filters - use same price calculation as product_query.php
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
LIMIT 15";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    // Format price
    $row['price_formatted'] = number_format((float)$row['sale_price'], 0, ',', '.') . 'đ';
    $data[] = $row;
}

$stmt->close();

echo json_encode($data);
exit;
