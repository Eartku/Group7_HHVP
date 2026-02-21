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
    $conditions[] = "name LIKE CONCAT('%', ?, '%')";
    $params[] = $q;
    $types .= "s";
}

if ($category > 0) {
    $conditions[] = "category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($minPrice > 0) {
    $conditions[] = "price >= ?";
    $params[] = $minPrice;
    $types .= "d";
}

if ($maxPrice > 0) {
    $conditions[] = "price <= ?";
    $params[] = $maxPrice;
    $types .= "d";
}

// If no search criteria, return empty
if (empty($q) && $category == 0 && $minPrice == 0 && $maxPrice == 0) {
    echo json_encode([]);
    exit;
}

$whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Query with filters
$sql = "SELECT id, name, price 
        FROM products 
        $whereClause
        ORDER BY id DESC
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
    $row['price_formatted'] = number_format((float)$row['price'], 0, ',', '.') . 'đ';
    $data[] = $row;
}

$stmt->close();

echo json_encode($data);
exit;
