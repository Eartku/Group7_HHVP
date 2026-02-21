<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once "../config/db.php";

/* ========================= */
/* ===== CHECK LOGIN ======= */
/* ========================= */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];

/* ========================= */
/* ===== DEFAULT VALUES ==== */
/* ========================= */
$fullname = '';
$email = '';
$phone = '';
$address = '';
$note = '';
$error = '';

/* ========================= */
/* ===== LẤY THÔNG TIN USER */
/* ========================= */
$stmt = $conn->prepare("SELECT fullname, email, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($user = $userResult->fetch_assoc()) {
    $fullname = $user['fullname'] ?? '';
    $email     = $user['email'] ?? '';
    $phone     = $user['phone'] ?? '';
    $address   = $user['address'] ?? '';
}

/* ========================= */
/* ===== LẤY CART ========= */
/* ========================= */
$stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartResult = $stmt->get_result();

if ($cartResult->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

$cart_id = $cartResult->fetch_assoc()['id'];

$stmt = $conn->prepare("
    SELECT ci.product_id, ci.size, ci.quantity, ci.price, p.name
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.cart_id = ?
");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;

    $products[] = [
        'id' => $row['product_id'],
        'size' => $row['size'],   // thêm dòng này
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'subtotal' => $subtotal
    ];
}

if (empty($products)) {
    header("Location: cart.php");
    exit();
}

$shipping_fee = 20000;
$grand_total = $total + $shipping_fee;

/* ========================= */
/* ===== XỬ LÝ ORDER ====== */
/* ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $note      = trim($_POST['note'] ?? '');
    $payment   = $_POST['payment'] ?? 'cod';
    $option    = $_POST['address_option'] ?? 'saved';

    $address = ($option === 'saved')
        ? trim($_POST['saved_address'] ?? '')
        : trim($_POST['new_address'] ?? '');

    if (!$fullname || !$email || !$phone || !$address) {
        $error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
    } else {

        $conn->begin_transaction();

        try {

            /* ========================= */
            /* ===== CHECK & LOCK KHO == */
            /* ========================= */
            foreach ($products as $p) {

                // Lock dòng tồn kho
                $stmt = $conn->prepare("
                    SELECT quantity 
                    FROM inventory
                    WHERE product_id = ? AND size = ?
                    FOR UPDATE
                ");
                $stmt->bind_param("is", $p['id'], $p['size']);
                $stmt->execute();
                $stockResult = $stmt->get_result();

                if ($stockResult->num_rows === 0) {
                    throw new Exception("Sản phẩm không tồn tại trong kho.");
                }

                $stock = $stockResult->fetch_assoc()['quantity'];

                if ($stock < $p['quantity']) {
                    throw new Exception("Sản phẩm '{$p['name']}' không đủ số lượng trong kho.");
                }
            }

            /* ========================= */
            /* ===== TẠO ORDER ========= */
            /* ========================= */
            $stmt = $conn->prepare("
                INSERT INTO orders
                (user_id, note, payment_method, total_price, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'processing', NOW(), NOW())
            ");
            $stmt->bind_param("issd", $user_id, $note, $payment, $grand_total);
            $stmt->execute();
            $order_id = $stmt->insert_id;

            /* ========================= */
            /* ===== INSERT ITEMS + TRỪ KHO */
            /* ========================= */
            foreach ($products as $p) {

                // Insert order item
              $stmt = $conn->prepare("
                    INSERT INTO order_items
                    (order_id, product_id, size, quantity, price)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "iisid",
                    $order_id,
                    $p['id'],
                    $p['size'],
                    $p['quantity'],
                    $p['price']
                );
                $stmt->execute();

                // Trừ kho
                $stmt = $conn->prepare("
                    UPDATE inventory
                    SET quantity = quantity - ?
                    WHERE product_id = ? AND size = ?
                ");
                $stmt->bind_param("iis", $p['quantity'], $p['id'], $p['size']);
                $stmt->execute();
            }

            /* ========================= */
            /* ===== XOÁ CART ========= */
            /* ========================= */
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();

            $conn->commit();

            header("Location: thankyou.php?id=" . $order_id);
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php' ?>
    <title>BonSai | Thanh toán</title>
</head>
<body>

    <?php include '../includes/header.php' ?>

    <div class="container py-5">

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">

                <div>
                    <div class="card p-4 shadow-sm">

                        <h4>Thông tin khách hàng</h4>

                        <div class="mb-3">
                            <label>Họ và Tên *</label>
                            <input type="text" name="fullname" class="form-control"
                                   value="<?= htmlspecialchars($fullname) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($email) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label>Số điện thoại *</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($phone) ?>" required>
                        </div>

                        <hr>

                        <h5>Địa chỉ giao hàng</h5>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="address_option"
                                   value="saved" checked>
                            <label class="form-check-label">Dùng địa chỉ đã lưu</label>
                        </div>

                        <select name="saved_address" class="form-select mb-3">
                            <option value="<?= htmlspecialchars($address) ?>">
                                <?= htmlspecialchars($address) ?>
                            </option>
                        </select>

                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="address_option" value="new">
                            <label class="form-check-label">Nhập địa chỉ mới</label>
                        </div>

                        <input type="text" name="new_address"
                               class="form-control mb-3"
                               placeholder="Nhập địa chỉ mới">

                        <div class="mb-3">
                            <label>Ghi chú</label>
                            <textarea name="note" class="form-control"><?= htmlspecialchars($note) ?></textarea>
                        </div>

                    </div>
                </div>

                <div>
                    <div class="card p-4 shadow-sm">

                        <h4>Đơn hàng của bạn</h4>

                        <table class="table">
                            <tbody>

                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['name']) ?> x <?= $p['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($p['subtotal'],0,",",".") ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>

                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td class="text-end"><?= number_format($shipping_fee,0,",",".") ?>đ</td>
                                </tr>

                                <tr class="fw-bold">
                                    <td>Tổng cộng</td>
                                    <td class="text-end"><?= number_format($grand_total,0,",",".") ?>đ</td>
                                </tr>

                            </tbody>
                        </table>

                        <hr>

                        <h5>Phương thức thanh toán</h5>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" value="cod" checked>
                            <label class="form-check-label">Thanh toán khi nhận hàng</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" value="bank">
                            <label class="form-check-label">Chuyển khoản ngân hàng</label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment" value="momo">
                            <label class="form-check-label">Thanh toán MoMo</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            ĐẶT HÀNG
                        </button>

                    </div>
                </div>

            </div>
        </form>
    </div>

    <?php include '../includes/footer.php' ?>
</body>
</html>
