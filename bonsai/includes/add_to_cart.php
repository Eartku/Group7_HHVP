<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    die("Vui lòng đăng nhập");
}

$user_id = $_SESSION['user']['id'];
$product_id = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);

if ($product_id <= 0 || $qty <= 0) {
    die("Dữ liệu không hợp lệ");
}

/* =========================
   1. LẤY HOẶC TẠO CART
========================= */
$stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cart = $result->fetch_assoc();
    $cart_id = $cart['id'];
} else {
    $stmt = $conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
}

/* =========================
   2. LẤY GIÁ SẢN PHẨM
========================= */
$stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$productResult = $stmt->get_result();

if ($productResult->num_rows === 0) {
    die("Sản phẩm không tồn tại");
}

$product = $productResult->fetch_assoc();
$price = $product['price'];

/* =========================
   3. CHECK SẢN PHẨM ĐÃ CÓ TRONG GIỎ CHƯA
========================= */
$stmt = $conn->prepare("SELECT id FROM cart_items 
                        WHERE cart_id = ? AND product_id = ?");
$stmt->bind_param("ii", $cart_id, $product_id);
$stmt->execute();
$itemResult = $stmt->get_result();

if ($itemResult->num_rows > 0) {

    // Nếu đã có → tăng số lượng
    $stmt = $conn->prepare("UPDATE cart_items 
                            SET quantity = quantity + ? 
                            WHERE cart_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $qty, $cart_id, $product_id);
    $stmt->execute();

} else {

    // Nếu chưa có → insert mới
    $stmt = $conn->prepare("INSERT INTO cart_items 
                            (cart_id, product_id, quantity, price) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $cart_id, $product_id, $qty, $price);
    $stmt->execute();
}

echo "success";
