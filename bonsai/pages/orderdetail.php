<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}


$order_id = (int)$_GET['id'];
$user_id  = (int)$_SESSION['user']['id'];

/* ========================= */
/* ===== HỦY ĐƠN HÀNG ===== */
/* ========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {

    $conn->begin_transaction();

    try {

        // 1️⃣ Lock order trước
        $stmt = $conn->prepare("
            SELECT status 
            FROM orders 
            WHERE id = ? AND user_id = ? 
            FOR UPDATE
        ");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $orderResult = $stmt->get_result();

        if ($orderResult->num_rows === 0) {
            throw new Exception("Đơn hàng không tồn tại.");
        }

        $orderRow = $orderResult->fetch_assoc();

        if ($orderRow['status'] !== 'processing') {
            throw new Exception("Chỉ có thể hủy đơn đang xử lý.");
        }

        // 2️⃣ Update status trước
        $stmt = $conn->prepare("
            UPDATE orders 
            SET status = 'cancelled', updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Không thể cập nhật trạng thái.");
        }

        // 3️⃣ Lấy order_items
        $stmt = $conn->prepare("
            SELECT product_id, size, quantity 
            FROM order_items 
            WHERE order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items = $stmt->get_result();
        
// 4️⃣ Trả kho từng sản phẩm
while ($item = $items->fetch_assoc()) {

    // Trả kho
    $stmt2 = $conn->prepare("
        UPDATE inventory
        SET quantity = quantity + ?
        WHERE product_id = ?
        AND UPPER(TRIM(size)) = UPPER(TRIM(?))
    ");

    $stmt2->bind_param(
        "iis",
        $item['quantity'],
        $item['product_id'],
        $item['size']
    );

    $stmt2->execute();

    if ($stmt2->affected_rows === 0) {
        throw new Exception(
            "Không match inventory khi hoàn kho - product_id="
            . $item['product_id']
            . " size=" . $item['size']
        );
    }

    // Ghi log hoàn kho
    $stmtLog = $conn->prepare("
        INSERT INTO inventory_logs
        (product_id, type, quantity, import_price, note, created_at)
        VALUES (?, 'import', ?, 0, ?, NOW())
    ");

    $note = "Hoàn kho do hủy đơn #" . $order_id;

    $stmtLog->bind_param(
        "iis",
        $item['product_id'],
        $item['quantity'],
        $note
    );

    $stmtLog->execute();
}

        $conn->commit();

    } catch (Exception $e) {

        $conn->rollback();
        die($e->getMessage());
    }

    header("Location: orderdetail.php?id=" . $order_id);
    exit();
}

/* ========================= */
/* ===== LẤY ORDER ========= */
/* ========================= */

$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Không tìm thấy đơn hàng hoặc đơn không thuộc user hiện tại");
}

/* ========================= */
/* ===== LẤY ORDER ITEMS === */
/* ========================= */

$stmt = $conn->prepare("
    SELECT oi.*, p.name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$subtotal = 0;

while ($row = $result->fetch_assoc()) {
    $row_total = $row['price'] * $row['quantity'];
    $subtotal += $row_total;
    $row['row_total'] = $row_total;
    $items[] = $row;
}

$shipping_fee = 20000;
$grand_total  = $order['total_price'];

/* ========================= */
/* ===== STATUS BADGE ====== */
/* ========================= */

$status = $order['status'];
$badge = "bg-secondary";
$text  = "Không xác định";

switch ($status) {
    case 'processing':
        $badge = "bg-warning text-dark";
        $text  = "Đang xử lý";
        break;

    case 'processed':
        $badge = "bg-primary";
        $text  = "Đã xác nhận";
        break;

    case 'shipping':
        $badge = "bg-info text-dark";
        $text  = "Đang giao";
        break;

    case 'shipped':
        $badge = "bg-success";
        $text  = "Đã giao";
        break;

    case 'cancelled':
        $badge = "bg-danger";
        $text  = "Đã huỷ";
        break;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'; ?>
    <title>BonSai | Chi tiết đơn hàng</title>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container py-5" style="margin-top:60px;">

    <h2 class="mb-4">Chi tiết đơn hàng #<?= $order_id ?></h2>

    <div class="row">

        <!-- PRODUCT INFO -->
        <div class="mb-4">
            <div class="card p-4 shadow-sm">

                <h4>Sản phẩm trong đơn</h4>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($item['name']) ?>
                                    x <?= $item['quantity'] ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($item['row_total'],0,",",".") ?>đ
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr>
                            <td>Phí vận chuyển</td>
                            <td class="text-end">
                                <?= number_format($shipping_fee,0,",",".") ?>đ
                            </td>
                        </tr>

                        <tr class="fw-bold">
                            <td>Tổng cộng</td>
                            <td class="text-end">
                                <?= number_format($grand_total,0,",",".") ?>đ
                            </td>
                        </tr>

                    </tbody>
                </table>

                <h5 class="mt-4">Trạng thái đơn hàng</h5>
                <span class="badge <?= $badge ?>"><?= $text ?></span>

                <?php if ($order['status'] === 'processing'): ?>
                    <form method="POST" class="mt-3"
                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                        <button type="submit"
                                name="cancel_order"
                                class="btn btn-danger btn-sm">
                            Hủy đơn hàng
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>

        <!-- ORDER INFO -->
        <div >
            <div class="card p-4 shadow-sm">

                <h4>Thông tin đơn hàng</h4>

                <p><strong>Mã đơn:</strong> #<?= $order_id ?></p>

                <p><strong>Ngày đặt:</strong>
                    <?= date("d/m/Y H:i", strtotime($order['created_at'])) ?>
                </p>
                <p><strong>Địa chỉ giao hàng:</strong>
                    <?= htmlspecialchars($order['address']) ?>
                </p>

                <p><strong>Phương thức thanh toán:</strong>
                    <?= strtoupper($order['payment_method']) ?>
                </p>

                <hr>

                <h5>Ghi chú</h5>
                <p><?= htmlspecialchars($order['note'] ?: 'Không có ghi chú') ?></p>

            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <a href="orders.php" class="btn btn-dark">
            ← Quay lại lịch sử đơn
        </a>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>