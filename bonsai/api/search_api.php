<?php
require_once "../config/db.php";

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT id, name, price 
    FROM products 
    WHERE name LIKE CONCAT('%', ?, '%')
    LIMIT 10
");

$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
exit;
