<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    die("Vui lòng đăng nhập");
}

$user_id = $_SESSION['user']['id'];
$product_id = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
$size = strtoupper(trim($_POST['size'] ?? ''));

if ($product_id <= 0 || $qty <= 0 || empty($size)) {
    die("Dữ liệu không hợp lệ");
}

/* LẤY HOẶC TẠO CART */
$stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cart_id = $result->fetch_assoc()['id'];
} else {
    $stmt = $conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
}

/* LẤY GIÁ + TỒN KHO THEO SIZE */
$stmt = $conn->prepare("
    SELECT p.price, i.quantity, i.price_adjust
    FROM products p
    LEFT JOIN inventory i 
        ON p.id = i.product_id 
        AND UPPER(TRIM(i.size)) = ?
    WHERE p.id = ?
");
$stmt->bind_param("si", $size, $product_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Sản phẩm không tồn tại");
}

$basePrice = $data['price'];
$adjust = floatval($data['price_adjust'] ?? 0);
$price = $basePrice + $adjust;
$stock = intval($data['quantity'] ?? 0);

if ($stock < $qty) {
    die("Không đủ hàng trong kho");
}

/* CHECK TRÙNG PRODUCT + SIZE */
$stmt = $conn->prepare("
    SELECT id, quantity 
    FROM cart_items 
    WHERE cart_id = ? 
      AND product_id = ?
      AND UPPER(TRIM(size)) = ?
");
$stmt->bind_param("iis", $cart_id, $product_id, $size);
$stmt->execute();
$exist = $stmt->get_result();

if ($exist->num_rows > 0) {

    $row = $exist->fetch_assoc();
    $newQty = $row['quantity'] + $qty;

    if ($newQty > $stock) {
        die("Số lượng vượt quá tồn kho");
    }

    $stmt = $conn->prepare("
        UPDATE cart_items 
        SET quantity = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $newQty, $row['id']);
    $stmt->execute();

} else {

    $stmt = $conn->prepare("
        INSERT INTO cart_items 
        (cart_id, product_id, size, quantity, price) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisid", $cart_id, $product_id, $size, $qty, $price);
    $stmt->execute();
}

echo "success";
