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
   XÓA SẢN PHẨM
======================== */
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);

    $stmt = $conn->prepare("
        DELETE FROM cart_items
        WHERE id = ? AND cart_id = ?
    ");
    $stmt->bind_param("ii", $remove_id, $cart_id);
    $stmt->execute();

    header("Location: cart.php");
    exit();
}

/* ========================
   CẬP NHẬT CART
======================== */
if (isset($_POST['update'])) {

    $conn->begin_transaction();

    try {

        foreach ($_POST['qty'] as $item_id => $qty) {

            $item_id = intval($item_id);
            $qty     = max(1, intval($qty));
            $newSize = strtoupper(trim($_POST['size'][$item_id] ?? ''));

            /* ========================
               1. LẤY PRODUCT_ID
            ========================= */
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

            /* ========================
               2. LẤY STOCK + ADJUST
            ========================= */
            $stmtStock = $conn->prepare("
                SELECT quantity, price_adjust
                FROM inventory
                WHERE product_id = ?
                AND UPPER(TRIM(size)) = ?
            ");
            $stmtStock->bind_param("is", $product_id, $newSize);
            $stmtStock->execute();
            $stockRow = $stmtStock->get_result()->fetch_assoc();

            if (!$stockRow || $stockRow['quantity'] <= 0) continue;

            $available = intval($stockRow['quantity']);
            $adjust    = floatval($stockRow['price_adjust']);

            if ($qty > $available) {
                $qty = $available;
            }

            /* ========================
               3. LẤY GIÁ GỐC
            ========================= */
            $stmtBase = $conn->prepare("
                SELECT price FROM products WHERE id = ?
            ");
            $stmtBase->bind_param("i", $product_id);
            $stmtBase->execute();
            $base_price = $stmtBase->get_result()->fetch_assoc()['price'];

            $final_price = $base_price + $adjust;

            /* ========================
               4. CHECK DUPLICATE
            ========================= */
            $stmtCheck = $conn->prepare("
                SELECT id, quantity
                FROM cart_items
                WHERE cart_id = ?
                AND product_id = ?
                AND size = ?
                AND id != ?
            ");
            $stmtCheck->bind_param(
                "iisi",
                $cart_id,
                $product_id,
                $newSize,
                $item_id
            );
            $stmtCheck->execute();
            $existing = $stmtCheck->get_result()->fetch_assoc();

            if ($existing) {

                // Merge quantity
                $mergedQty = $existing['quantity'] + $qty;

                if ($mergedQty > $available) {
                    $mergedQty = $available;
                }

                $stmtMerge = $conn->prepare("
                    UPDATE cart_items
                    SET quantity = ?, price = ?
                    WHERE id = ?
                ");
                $stmtMerge->bind_param(
                    "idi",
                    $mergedQty,
                    $final_price,
                    $existing['id']
                );
                $stmtMerge->execute();

                // Delete old row
                $stmtDelete = $conn->prepare("
                    DELETE FROM cart_items
                    WHERE id = ?
                ");
                $stmtDelete->bind_param("i", $item_id);
                $stmtDelete->execute();

            } else {

                // Update normally
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
        }

        $conn->commit();
        $_SESSION['updated_time'] = date("H:i:s d/m/Y");

    } catch (Exception $e) {

        $conn->rollback();
        die("Update cart failed: " . $e->getMessage());
    }

    header("Location: cart.php");
    exit();
}

/* ========================
   LẤY DANH SÁCH CART
======================== */
$stmt = $conn->prepare("
    SELECT
        ci.id,
        p.name,
        p.price AS base_price,
        ci.price AS final_price,
        p.image,
        ci.size,
        ci.quantity,
        IFNULL(i.quantity,0) AS stock,
        IFNULL(i.price_adjust,0) AS adjust
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
                            $qty = min($row['quantity'], $stock);
                            $subtotal = $row['final_price'] * $qty;
                            $total += $subtotal;

                            if ($stock <= 0) {
                                $canCheckout = false;
                            }
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
                                            class="form-select size-select">

                                        <?php
                                        $stmtSize = $conn->prepare("
                                            SELECT size, price_adjust, quantity
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
                                                    data-adjust="<?= $s['price_adjust'] ?>"
                                                    <?= $sizeValue == $currentSize ? 'selected' : '' ?>
                                                    <?= $disabled ?>>

                                                <?= $sizeValue ?>

                                                <?php if($stockQty <= 0): ?>
                                                    (Hết hàng)
                                                <?php elseif($s['price_adjust'] > 0): ?>
                                                    (+<?= number_format($s['price_adjust'],0,",",".") ?>đ)
                                                <?php endif; ?>

                                            </option>

                                        <?php endwhile; ?>

                                    </select>
                                </td>

                                <td>
                                    <div class="fw-bold text-danger">
                                        <?= number_format($row['final_price'],0,",",".") ?>đ
                                    </div>
                                </td>


                                <td style="width:120px">
                                    <input type="number"
                                           name="qty[<?= $row['id'] ?>]"
                                           value="<?= $qty ?>"
                                           min="1"
                                           max="<?= $stock ?>"
                                           data-price="<?= $row['base_price'] ?>"
                                           class="form-control text-center qty-input"
                                           <?= $stock <= 0 ? 'disabled' : '' ?>>

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
                    const sizeSelect = row.querySelector(".size-select");

                    if(!qtyInput) return;

                    let basePrice = parseFloat(qtyInput.dataset.price);
                    let qty = parseInt(qtyInput.value) || 0;

                    let adjust = 0;

                    if(sizeSelect){
                        let selected = sizeSelect.options[sizeSelect.selectedIndex];
                        adjust = parseFloat(selected.dataset.adjust) || 0;
                    }

                    let finalPrice = basePrice + adjust;
                    let subtotal = finalPrice * qty;

                    row.querySelector(".subtotal").innerText = formatMoney(subtotal);

                    total += subtotal;
                });

                document.getElementById("cart-total").innerText =
                    "Tổng cộng: " + formatMoney(total);
            }

            document.querySelectorAll(".qty-input")
                .forEach(input => input.addEventListener("input", updateCartTotal));

            document.querySelectorAll(".size-select")
                .forEach(select => select.addEventListener("change", updateCartTotal));
        });

    </script>
<?php include '../includes/footer.php'; ?>
</body>
</html>
