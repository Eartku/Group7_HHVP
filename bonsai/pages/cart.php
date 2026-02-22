<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

/* ========================
   LẤY / TẠO CART
======================== */
$stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc();

if (!$cart) {
    $stmt = $conn->prepare("INSERT INTO carts (user_id, created_at) VALUES (?, NOW())");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
} else {
    $cart_id = $cart['id'];
}

/* ========================
   XÓA ITEM
======================== */
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
    $stmt->bind_param("ii", $remove_id, $cart_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

/* ========================
   UPDATE CART (SERVER TÍNH GIÁ)
======================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $conn->begin_transaction();

    try {

        foreach ($_POST['qty'] as $item_id => $qty) {

            $item_id = intval($item_id);
            $qty = max(1, intval($qty));
            $newSize = strtoupper(trim($_POST['size'][$item_id] ?? ''));

            // Lấy product_id
            $stmtItem = $conn->prepare("
                SELECT product_id 
                FROM cart_items 
                WHERE id = ? AND cart_id = ?
            ");
            $stmtItem->bind_param("ii", $item_id, $cart_id);
            $stmtItem->execute();
            $item = $stmtItem->get_result()->fetch_assoc();
            if (!$item) continue;

            $product_id = $item['product_id'];

            // Lấy giá từ inventory giống details
            $stmtProduct = $conn->prepare("
                SELECT 
                    p.profit_rate,
                    COALESCE(i.avg_import_price,0) AS avg_import_price
                FROM products p
                LEFT JOIN inventory i 
                    ON i.product_id = p.id
                WHERE p.id = ?
                LIMIT 1
            ");
            $stmtProduct->bind_param("i", $product_id);
            $stmtProduct->execute();
            $product = $stmtProduct->get_result()->fetch_assoc();
            if (!$product) continue;

            $avg = floatval($product['avg_import_price']);
            $profit = floatval($product['profit_rate']);

            $base_price = round($avg * (1 + $profit/100), -3);

            // Lấy size adjust
            $stmtStock = $conn->prepare("
                SELECT quantity, price_adjust
                FROM inventory
                WHERE product_id = ?
                AND UPPER(TRIM(size)) = ?
            ");
            $stmtStock->bind_param("is", $product_id, $newSize);
            $stmtStock->execute();
            $stockRow = $stmtStock->get_result()->fetch_assoc();
            if (!$stockRow) continue;

            $available = intval($stockRow['quantity']);
            $adjust = floatval($stockRow['price_adjust']);

            if ($available <= 0) continue;
            if ($qty > $available) $qty = $available;

            $final_price = $base_price + $adjust;

            $stmtUpdate = $conn->prepare("
                UPDATE cart_items
                SET quantity = ?, size = ?, price = ?
                WHERE id = ? AND cart_id = ?
            ");
            $stmtUpdate->bind_param(
                "isdii",
                $qty,
                $newSize,
                $final_price,
                $item_id,
                $cart_id
            );
            $stmtUpdate->execute();
        }

        $conn->commit();
        header("Location: cart.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Update cart failed");
    }
}

/* ========================
   LOAD CART
======================== */
$stmt = $conn->prepare("
    SELECT
        ci.id,
        p.name,
        p.image,
        ci.size,
        ci.quantity,
        ci.price,
        IFNULL(i.quantity,0) AS stock
    FROM cart_items ci
    JOIN products p ON p.id = ci.product_id
    LEFT JOIN inventory i
        ON i.product_id = ci.product_id
        AND UPPER(TRIM(i.size)) = UPPER(TRIM(ci.size))
    WHERE ci.cart_id = ?
");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$items = $stmt->get_result();

$total = 0;
$canCheckout = true;

$stmtCheck = $conn->prepare("
    SELECT ci.quantity, IFNULL(i.quantity,0) AS stock
    FROM cart_items ci
    LEFT JOIN inventory i
        ON i.product_id = ci.product_id
        AND UPPER(TRIM(i.size)) = UPPER(TRIM(ci.size))
    WHERE ci.cart_id = ?
");
$stmtCheck->bind_param("i", $cart_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows == 0) {
    $canCheckout = false;
}

while ($r = $resultCheck->fetch_assoc()) {
    if ($r['stock'] <= 0 || $r['quantity'] > $r['stock']) {
        $canCheckout = false;
        break;
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'; ?>
    <title>BonSai | Cart</title>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4"style="text-align:center" >Giỏ hàng của bạn</h2>

        <?php if ($items->num_rows > 0): ?>

            <form method="post">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php while ($row = $items->fetch_assoc()):
                        $stock = intval($row['stock']);
                        $qty = min(intval($row['quantity']), $stock);

                        $price = floatval($row['price']);
                        $subtotal = $price * $qty;
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td style="width:100px">
                            <img src="../images/<?= htmlspecialchars($row['image']) ?>"
                                style="width:80px;">
                        </td>

                        <td>
                            <?= htmlspecialchars($row['name']) ?>

                            <?php if ($stock <= 0): ?>
                                <div class="text-danger fw-bold">Hết hàng</div>
                            <?php elseif ($stock < $row['quantity']): ?>
                                <div class="text-warning">Chỉ còn <?= $stock ?> sản phẩm</div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <select name="size[<?= $row['id'] ?>]"
                                    class="form-select">

                                <?php
                                $stmtSize = $conn->prepare("
                                    SELECT size, quantity
                                    FROM inventory
                                    WHERE product_id = (
                                        SELECT product_id FROM cart_items WHERE id = ?
                                    )
                                ");
                                $stmtSize->bind_param("i", $row['id']);
                                $stmtSize->execute();
                                $sizes = $stmtSize->get_result();

                                while($s = $sizes->fetch_assoc()):
                                    $sizeValue = strtoupper(trim($s['size']));
                                    $currentSize = strtoupper(trim($row['size']));
                                    $stockQty = intval($s['quantity']);
                                    $disabled = $stockQty <= 0 ? 'disabled' : '';
                                ?>
                                    <option value="<?= $sizeValue ?>"
                                            <?= $sizeValue == $currentSize ? 'selected' : '' ?>
                                            <?= $disabled ?>>
                                        <?= $sizeValue ?>
                                        <?= $stockQty <= 0 ? '(Hết hàng)' : '' ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>

                        <td>
                            <div class="fw-bold text-danger">
                                <?= number_format($price,0,",",".") ?>đ
                            </div>
                        </td>

                        <td style="width:120px">
                            <input type="number"
                                name="qty[<?= $row['id'] ?>]"
                                value="<?= $qty ?>"
                                min="1"
                                max="<?= $stock ?>"
                                data-price="<?= $price ?>"
                                class="form-control text-center qty-input">
                        </td>

                        <td class="subtotal">
                            <?= number_format($subtotal,0,",",".") ?>đ
                        </td>

                        <td>
                            <a href="cart.php?remove=<?= $row['id'] ?>"
                            class="btn btn-danger btn-sm">X</a>
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

                        <strong id="cart-total">
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
                            Cập nhật
                        </button>

                        <a href="checkout.php"
                           class="btn btn-success <?= !$canCheckout ? 'disabled' : '' ?>">
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
    <script>
document.addEventListener("DOMContentLoaded", function(){

    function formatMoney(number){
        return number.toLocaleString('vi-VN') + "đ";
    }

    function updateCartTotal(){

        let total = 0;

        document.querySelectorAll("tbody tr").forEach(row => {

            const qtyInput = row.querySelector(".qty-input");
            if(!qtyInput) return;

            // 🔥 Lấy giá đã được backend tính sẵn
            let price = parseFloat(qtyInput.dataset.price);
            if(isNaN(price)) price = 0;

            let qty = parseInt(qtyInput.value);
            if(isNaN(qty) || qty < 0) qty = 0;

            let subtotal = price * qty;

            const subtotalCell = row.querySelector(".subtotal");
            if(subtotalCell){
                subtotalCell.innerText = formatMoney(subtotal);
            }

            total += subtotal;
        });

        const totalEl = document.getElementById("cart-total");
        if(totalEl){
            totalEl.innerText = "Tổng cộng: " + formatMoney(total);
        }
    }

    // Khi đổi số lượng
    document.querySelectorAll(".qty-input")
        .forEach(input => input.addEventListener("input", updateCartTotal));

    // Chạy khi load trang
    updateCartTotal();
});
    </script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
