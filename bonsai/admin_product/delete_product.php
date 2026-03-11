<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? 0;

$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    $back = $_SERVER['HTTP_REFERER'] ?? 'sshop.php';
    header("Location: " . $back);
    exit;

} else {

    echo "Lỗi xóa sản phẩm";

}
?>