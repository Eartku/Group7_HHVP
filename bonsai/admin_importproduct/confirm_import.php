<?php
require_once "../config/db.php";

$id = $_GET['id'];

$res = $conn->query("SELECT * FROM import_receipts WHERE id = $id");
$row = $res->fetch_assoc();

$product = $row['product_id'];
$size = $row['size'];
$quantity = $row['quantity'];
$price = $row['import_price'];

$conn->begin_transaction();

try {
    // 1. Cập nhật hoặc Chèn vào bảng inventory (Kho tổng)
    $check = $conn->prepare("SELECT quantity, avg_import_price FROM inventory WHERE product_id = ? AND size = ? FOR UPDATE");
    $check->bind_param("is", $product, $size);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $oldQty = $data['quantity'];
        $oldAvg = $data['avg_import_price'];
        $newQty = $oldQty + $quantity;
        $newAvg = ($oldQty * $oldAvg + $quantity * $price) / $newQty;

        $update = $conn->prepare("UPDATE inventory SET quantity = ?, avg_import_price = ? WHERE product_id = ? AND size = ?");
        $update->bind_param("idis", $newQty, $newAvg, $product, $size);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO inventory (product_id, size, quantity, avg_import_price) VALUES (?, ?, ?, ?)");
        $insert->bind_param("isid", $product, $size, $quantity, $price);
        $insert->execute();
    }

    // ========================================================
    // BƯỚC B: GHI NHẬT KÝ VÀO inventory_logs
    // ========================================================
    $log_sql = "INSERT INTO inventory_logs (product_id, size, type, quantity, import_price, created_at) 
                VALUES (?, ?, 'import', ?, ?, NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("isid", $product, $size, $quantity, $price);
    $log_stmt->execute();
    // ========================================================

    // 2. Cập nhật trạng thái phiếu nhập trong bảng chờ thành 'completed'
    $conn->query("UPDATE import_receipts SET status = 'completed' WHERE id = $id");

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
}

header("Location:adminipd.php");
exit;