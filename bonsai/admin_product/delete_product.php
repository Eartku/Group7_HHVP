<?php
require_once "../config/db.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$confirm_hide = isset($_GET['confirm_hide']) ? (int)$_GET['confirm_hide'] : 0;

/* ========= LẤY THÔNG TIN SẢN PHẨM ========= */
$stmt = $conn->prepare("SELECT status FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại";
    exit;
}

$status = (int)$product['status'];

/* ========= KIỂM TRA TỒN KHO ========= */
$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM inventory WHERE product_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalStock = (int)($result['total'] ?? 0);

/* ========= XỬ LÝ ========= */
if ($totalStock > 0) {

    if ($status === 0) {
        // Sản phẩm đã ẩn nhưng vẫn còn hàng → không thể xóa
        echo "
        <script>
            alert('⚠️ Sản phẩm vẫn còn $totalStock trong kho và đang ẩn!\\nBạn không thể xóa.');
            window.history.back();
        </script>
        ";
        exit;
    }

    if (!$confirm_hide) {
        // Chưa xác nhận ẩn → hỏi người dùng
        echo "
        <script>
            if(confirm('⚠️ Sản phẩm vẫn còn $totalStock trong kho!\\nBạn không thể xóa.\\nBạn có muốn ẨN sản phẩm không?')) {
                window.location.href = 'delete_product.php?id=$id&confirm_hide=1';
            } else {
                window.history.back();
            }
        </script>
        ";
        exit;
    }

    // Người dùng xác nhận ẩn → cập nhật status = 0
    $stmt = $conn->prepare("UPDATE products SET status = 0 WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Quay lại sshop.php
    header("Location: sshop.php");
    exit;
}

/* ========= KHÔNG CÒN HÀNG → CHO XÓA ========= */
$stmt = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: sshop.php");
    exit;
} else {
    echo "Lỗi xóa sản phẩm";
}
?>