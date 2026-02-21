<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT id, total_price, status, created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Lỗi truy vấn đơn hàng");
}

$orders = $result->fetch_all(MYSQLI_ASSOC);

/* ===== Render Badge ===== */
function renderStatusBadge($status) {

    switch ($status) {

        case 'pending':
        case 'processing':
            return '<span class="badge-status status-processing">Đang xử lý</span>';

        case 'processed':
            return '<span class="badge-status status-processed">Đã xử lý</span>';

        case 'shipping':
            return '<span class="badge-status status-shipping">Đang vận chuyển</span>';

        case 'shipped':
            return '<span class="badge-status status-shipped">Đã giao</span>';

        case 'cancelled':
            return '<span class="badge-status status-cancelled">Đã hủy</span>';

        default:
            return '<span class="badge-status status-unknown">Không rõ</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>BonSai | Lịch sử đơn hàng</title>
<?php include '../includes/loader.php'?>
</head>

<body>

<?php include '../includes/header.php'?>

<div class="untree_co-section product-section before-footer-section" style="margin-top: -160px;">
    <h2 class="mb-4 container mt-5" style="text-align:center">Lịch sử các đơn hàng</h2>
    <!-- RADIO -->
    <input type="radio" name="order_page" id="page_all" checked>
    <input type="radio" name="order_page" id="page_processing">
    <input type="radio" name="order_page" id="page_processed">
    <input type="radio" name="order_page" id="page_shipping">
    <input type="radio" name="order_page" id="page_shipped">
    <input type="radio" name="order_page" id="page_cancelled">

    <!-- TAB MENU -->
    <div class="controls1">
        <label for="page_all">Tất cả</label>
        <label for="page_processing">Đang xử lý</label>
        <label for="page_processed">Đã xử lý</label>
        <label for="page_shipping">Đang vận chuyển</label>
        <label for="page_shipped">Đã giao</label>
        <label for="page_cancelled">Đã hủy</label>
    </div>

    <div class="container book" style="min-height:600px">

        <?php
        $pages = [
            'all'        => $orders,

            // GỘP pending + processing
            'processing' => array_filter($orders, fn($o) =>
                $o['status'] == 'processing' || $o['status'] == 'pending'
            ),

            'processed'  => array_filter($orders, fn($o)=>$o['status']=='processed'),
            'shipping'   => array_filter($orders, fn($o)=>$o['status']=='shipping'),
            'shipped'    => array_filter($orders, fn($o)=>$o['status']=='shipped'),
            'cancelled'  => array_filter($orders, fn($o)=>$o['status']=='cancelled'),
        ];

        foreach ($pages as $key => $list):
        ?>
        <div class="page page_<?= $key ?>">
            <div class="p-4 border bg-white rounded-3">

                <table class="table table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (!empty($list)): ?>
                        <?php foreach ($list as $order): ?>
                        <tr >
                            <td>#P<?= str_pad((int)$order['id'],3,"0",STR_PAD_LEFT) ?></td>
                            <td><?= date("d/m/Y", strtotime($order['created_at'])) ?></td>
                            <td><?= number_format($order['total_price'],0,",",".") ?>đ</td>
                            <td><?= renderStatusBadge($order['status']); ?></td>
                            <td>
                                <a href="orderdetail.php?id=<?= (int)$order['id'] ?>"
                                   class="btn btn-sm btn-outline-success"
                                   >
                                   Chi tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Không có đơn hàng</td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php include '../includes/footer.php'?>
<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>