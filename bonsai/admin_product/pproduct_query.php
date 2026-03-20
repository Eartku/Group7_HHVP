<?php
require_once "../config/db.php";

/* ================= NHẬN PARAM ================= */
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page        = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$keyword = $_GET['keyword'] ?? '';
$status  = $_GET['status'] ?? '';
$stock   = $_GET['stock'] ?? '';

$limit  = 8;
$offset = ($page - 1) * $limit;


/* ================= WHERE ĐỘNG ================= */
$where = [];
$params = [];
$types = "";

/* CATEGORY */
if ($category_id > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

/* SEARCH */
if (!empty($keyword)) {
    $where[] = "p.name LIKE ?";
    $params[] = "%" . $keyword . "%";
    $types .= "s";
}

/* STATUS */
if ($status !== '') {
    $where[] = "p.status = ?";
    $params[] = (int)$status;
    $types .= "i";
}


/* ================= ĐẾM TỔNG ================= */
$countSql = "SELECT COUNT(DISTINCT p.id) as total FROM products p";

if (!empty($where)) {
    $countSql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $conn->prepare($countSql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
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
    p.status,
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

/* GẮN WHERE */
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

/* GROUP + FILTER STOCK */
$sql .= " GROUP BY p.id";

if ($stock !== '') {
    if ($stock == 1) {
        $sql .= " HAVING total_stock > 0";
    } else {
        $sql .= " HAVING total_stock <= 0";
    }
}

/* ORDER + LIMIT */
$sql .= " ORDER BY p.id DESC LIMIT ? OFFSET ?";

/* PREPARE */
$params2 = $params;
$types2 = $types;

$params2[] = $limit;
$params2[] = $offset;
$types2 .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types2, ...$params2);

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>