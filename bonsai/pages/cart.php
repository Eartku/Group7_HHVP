<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

session_start();
require_once "../config/db.php";

/* ========================
   CHECK LOGIN
======================== */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

/* ========================
   LẤY HOẶC TẠO CART
======================== */
$sql = "SELECT * FROM carts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc();

if (!$cart) {
    $sql = "INSERT INTO carts (user_id, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
} else {
    $cart_id = $cart['id'];
}

/* ========================
   XÓA SẢN PHẨM
======================== */
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $conn->query("DELETE FROM cart_items 
                  WHERE id = $remove_id 
                  AND cart_id = $cart_id");

    $_SESSION['updated_time'] = date("H:i:s d/m/Y");
    header("Location: cart.php");
    exit();
}

/* ========================
   CẬP NHẬT SỐ LƯỢNG
======================== */
if (isset($_POST['update']) && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $item_id => $qty) {
        $qty = max(1, intval($qty));
        $conn->query("UPDATE cart_items 
                      SET quantity = $qty 
                      WHERE id = $item_id 
                      AND cart_id = $cart_id");
    }

    $_SESSION['updated_time'] = date("H:i:s d/m/Y");
    header("Location: cart.php");
    exit();
}

/* ========================
   LẤY SẢN PHẨM TRONG CART
======================== */
$sql = "
SELECT cart_items.id as item_id, 
       products.name, 
       products.price, 
       products.image,
       cart_items.quantity
FROM cart_items
JOIN products ON cart_items.product_id = products.id
WHERE cart_items.cart_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$items = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<?php include '../includes/loader.php'; ?>
<title>
  BonSai | Cart
</title>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">🛒 Giỏ hàng của bạn</h2>

    <?php if ($items->num_rows > 0): ?>

    <form method="post">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $items->fetch_assoc()): 
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td style="width:100px">
                        <img src="../images/<?= htmlspecialchars($row['image']) ?>" 
                             style="width:80px;">
                    </td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price'],0,",",".") ?>đ</td>
                    <td style="width:120px">
                        <input type="number"
                               name="qty[<?= $row['item_id'] ?>]"
                               value="<?= $row['quantity'] ?>"
                               min="1"
                               class="form-control text-center">
                    </td>
                    <td><?= number_format($subtotal,0,",",".") ?>đ</td>
                    <td>
                        <a href="cart.php?remove=<?= $row['item_id'] ?>" 
                           class="btn btn-danger btn-sm">
                           X
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">

            <a href="shop.php" class="btn btn-secondary">
                Tiếp tục mua sắm
            </a>

            <div class="d-flex align-items-center gap-3">

                <strong>
                    Tổng cộng: <?= number_format($total,0,",",".") ?>đ
                </strong>

                <?php if (isset($_SESSION['updated_time'])): ?>
                    <span class="text-success">
                        ✔ Đã cập nhật lúc <?= $_SESSION['updated_time'] ?>
                    </span>
                    <?php unset($_SESSION['updated_time']); ?>
                <?php endif; ?>

                <button type="submit" 
                        name="update" 
                        class="btn btn-warning">
                    Cập nhật giỏ hàng
                </button>

                <a href="checkout.php" 
                   class="btn btn-success">
                   Thanh toán
                </a>

            </div>
        </div>
    </form>

    <?php else: ?>

        <div class="text-center mt-5">
            <h4>🛒 Giỏ hàng đang trống</h4>
            <a href="shop.php" class="btn btn-primary mt-3">
                Mua sắm ngay
            </a>
        </div>

    <?php endif; ?>

</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
