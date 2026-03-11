<?php

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page-1)*$limit;

$sql_total = "SELECT COUNT(*) as total FROM import_receipts";
$res_total = $conn->query($sql_total);
$total = $res_total->fetch_assoc()['total'];

$totalPages = ceil($total/$limit);

$sql = "
SELECT ir.*, p.name as product_name
FROM import_receipts ir
LEFT JOIN products p ON ir.product_id = p.id
ORDER BY ir.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

$imports = [];
while($row = $result->fetch_assoc()){
    $imports[] = $row;
}
?>