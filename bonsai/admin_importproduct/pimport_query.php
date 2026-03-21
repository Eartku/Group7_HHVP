<?php

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page-1)*$limit;

/* ======================
   WHERE FILTER
====================== */
$where = " WHERE 1=1 ";

// tìm kiếm tên sản phẩm
if(!empty($_GET['keyword'])){
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $where .= " AND p.name LIKE '%$keyword%'";
}

// lọc theo ngày
if(!empty($_GET['from_date'])){
    $from = $_GET['from_date'];
    $where .= " AND ir.import_date >= '$from'";
}

if(!empty($_GET['to_date'])){
    $to = $_GET['to_date'];
    $where .= " AND ir.import_date <= '$to 23:59:59'";
}

// lọc trạng thái
if(!empty($_GET['status'])){
    $status = $conn->real_escape_string($_GET['status']);
    $where .= " AND ir.status = '$status'";
}

/* ======================
   COUNT
====================== */
$sql_total = "
SELECT COUNT(*) as total
FROM import_receipts ir
LEFT JOIN products p ON ir.product_id = p.id
$where
";

$res_total = $conn->query($sql_total);
$total = $res_total->fetch_assoc()['total'];

$totalPages = ceil($total/$limit);

/* ======================
   DATA
====================== */
$sql = "
SELECT ir.*, p.name as product_name
FROM import_receipts ir
LEFT JOIN products p ON ir.product_id = p.id
$where
ORDER BY ir.id DESC
LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

$imports = [];
while($row = $result->fetch_assoc()){
    $imports[] = $row;
}
?>