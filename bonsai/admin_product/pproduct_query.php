<?php
require_once "../config/db.php";

$category_id = isset($category_id) ? (int)$category_id : 0;
$page        = isset($page) ? (int)$page : 1;
if ($page < 1) $page = 1;

$limit  = 8;
$offset = ($page - 1) * $limit;


/* ================= ĐẾM TỔNG ================= */

$countSql = "SELECT COUNT(*) as total FROM products";

if ($category_id > 0) {
    $countSql .= " WHERE category_id = ?";
    $stmt = $conn->prepare($countSql);
    $stmt->bind_param("i", $category_id);
} else {
    $stmt = $conn->prepare($countSql);
}

$stmt->execute();
$totalProducts = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$totalPages = ceil($totalProducts / $limit);


/* ================= LẤY SẢN PHẨM ================= */

$sql = "
SELECT 
    p.id,
    p.name,
    p.image,
    p.profit_rate,

    IFNULL(SUM(i.quantity),0) AS total_stock,

    IFNULL(
        SUM(i.avg_import_price * i.quantity)
        / NULLIF(SUM(i.quantity),0)
    ,0) AS avg_import_price,

    IFNULL(
        SUM(i.price_adjust * i.quantity)
        / NULLIF(SUM(i.quantity),0)
    ,0) AS price_adjust

FROM products p
LEFT JOIN inventory i ON i.product_id = p.id
";

if ($category_id > 0) {
    $sql .= " WHERE p.category_id = ?";
}

$sql .= "
GROUP BY p.id
ORDER BY p.id DESC
LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

if ($category_id > 0) {
    $stmt->bind_param("iii", $category_id, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>