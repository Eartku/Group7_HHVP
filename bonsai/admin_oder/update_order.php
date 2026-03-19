<?php
// ============================================================
//  orderdetails.php - Chi tiết đơn hàng (kết nối MySQL thật)
// ============================================================

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'bonsai2';

$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die('<div class="alert alert-danger m-4">Kết nối thất bại: ' . htmlspecialchars($conn->connect_error) . '</div>');
}
$conn->set_charset('utf8mb4');

if (!$conn->select_db($db_name)) {
    $dbs = [];
    $r = $conn->query("SHOW DATABASES");
    while ($row = $r->fetch_row()) $dbs[] = $row[0];
    die('<div class="alert alert-danger m-4">Không tìm thấy database <strong>' . htmlspecialchars($db_name) . '</strong>.<br>Database hiện có: <code>' . implode(', ', $dbs) . '</code></div>');
}

// ---------- LẤY ID TỪ URL ----------
// Nhận ?id= hoặc ?order_id=
$order_id = 0;
if (!empty($_GET['id'])) {
    $order_id = (int)$_GET['id'];
} elseif (!empty($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
}

if ($order_id <= 0) {
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8">
    <link href="../css/bootstrap.min.css" rel="stylesheet"></head><body>
    <div class="container mt-5">
      <div class="alert alert-warning">
        <strong>Chưa có mã đơn hàng!</strong><br>
        Truy cập đúng: <code>orderdetails.php?id=5</code><br><br>
        Nhập thử mã đơn hàng:
        <form class="d-flex gap-2 mt-2" method="GET">
          <input type="number" name="id" class="form-control" placeholder="ID đơn hàng" style="max-width:200px">
          <button class="btn btn-success">Xem</button>
        </form>
      </div>
      <a href="admin_order.php" class="btn btn-dark">&laquo; Quay lại danh sách</a>
    </div></body></html>';
    exit;
}

// ---------- LẤY ĐƠN HÀNG ----------
$stmt = $conn->prepare("
    SELECT o.id, o.fullname, o.email, o.status, o.created_at,
           o.total_price, o.payment_method, o.note,
           u.phone, u.address
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
    LIMIT 1
");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $total_count = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
    die('<!DOCTYPE html><html lang="vi"><head><meta charset="utf-8">
    <link href="../css/bootstrap.min.css" rel="stylesheet"></head><body>
    <div class="container mt-5">
      <div class="alert alert-warning">
        Không tìm thấy đơn hàng <strong>#' . $order_id . '</strong> trong database.<br>
        Tổng số đơn đang có: <strong>' . $total_count . '</strong><br><br>
        Nhập thử mã đơn hàng khác:
        <form class="d-flex gap-2 mt-2" method="GET">
          <input type="number" name="id" class="form-control" placeholder="ID đơn hàng" style="max-width:200px">
          <button class="btn btn-success">Xem</button>
        </form>
      </div>
      <a href="admin_order.php" class="btn btn-dark">&laquo; Quay lại danh sách</a>
    </div></body></html>');
}

// ---------- LẤY SẢN PHẨM ----------
$stmt2 = $conn->prepare("
    SELECT oi.quantity, oi.size, oi.price,
           p.name AS product_name, p.image
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
    ORDER BY oi.id
");
$stmt2->bind_param('i', $order_id);
$stmt2->execute();
$items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();

// ---------- HELPERS ----------
$status_map = [
    'pending'    => ['label' => 'Mới đặt',  'badge' => 'bg-warning text-dark'],
    'processing' => ['label' => 'Đã xử lý', 'badge' => 'bg-info text-dark'],
    'completed'  => ['label' => 'Đã giao',  'badge' => 'bg-success'],
    'delivered'  => ['label' => 'Đã giao',  'badge' => 'bg-success'],
    'cancelled'  => ['label' => 'Hủy',      'badge' => 'bg-danger'],
];
$payment_map = [
    'cod'   => 'Tiền mặt khi nhận hàng',
    'momo'  => 'Ví MoMo',
    'vnpay' => 'VNPay',
    'bank'  => 'Chuyển khoản ngân hàng',
];

$status_info   = $status_map[$order['status']] ?? ['label' => ucfirst($order['status']), 'badge' => 'bg-secondary'];
$payment_label = $payment_map[$order['payment_method']] ?? htmlspecialchars($order['payment_method']);

function fmtMoney(float $n): string {
    return number_format($n, 0, ',', '.') . 'đ';
}
function fmtDate(string $dt): string {
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y', $ts) : $dt;
}

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 20000;
$total = $order['total_price'] > 0 ? (float)$order['total_price'] : ($subtotal + $shipping);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="shortcut icon" href="../images/logo.png" />
  <title>BonSai | Chi tiết đơn hàng #<?= $order_id ?></title>
  <link href="../css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <link href="../css/hover.css" rel="stylesheet" />
  <link href="../css/tiny-slider.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>
  <!-- ===== NAVBAR ===== -->
  <?php include = "../"
  <!-- HERO -->
  <div>
    <div class="center-row" style="text-align:center">
      <h2 class="fw-bold text-success" style="padding:30px">
        Chi tiết đơn hàng <span class="text-dark">#<?= $order_id ?></span>
      </h2>
    </div>
  </div>

  <!-- NỘI DUNG -->
  <div class="untree_co-section">
    <div class="container">
      <div class="row">

        <!-- CỘT TRÁI: Sản phẩm -->
        <div class="col-md-7 mb-5 mb-md-0">
          <h2 class="h3 mb-3 text-black">Sản phẩm trong đơn</h2>
          <div class="p-3 p-lg-5 border bg-white">
            <table class="table site-block-order-table mb-5">
              <thead>
                <tr><th>Sản Phẩm</th><th class="text-end">Tổng</th></tr>
              </thead>
              <tbody>
                <?php if (empty($items)): ?>
                  <tr><td colspan="2" class="text-muted text-center">Không có sản phẩm</td></tr>
                <?php else: ?>
                  <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <?= htmlspecialchars($item['product_name'] ?? 'Sản phẩm') ?>
                      <?php if (!empty($item['size'])): ?>
                        <span class="text-muted small"> - size <?= htmlspecialchars($item['size']) ?></span>
                      <?php endif; ?>
                      <strong class="mx-2">x</strong><?= (int)$item['quantity'] ?>
                    </td>
                    <td class="text-end"><?= fmtMoney($item['price'] * $item['quantity']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                <tr>
                  <td><strong>Ship</strong></td>
                  <td class="text-end"><?= fmtMoney($shipping) ?></td>
                </tr>
                <tr>
                  <td><strong>Tổng</strong></td>
                  <td class="text-end"><strong><?= fmtMoney($total) ?></strong></td>
                </tr>
              </tbody>
            </table>

            <h4 class="text-black mb-3">Trạng thái đơn hàng</h4>
            <p><span class="badge <?= $status_info['badge'] ?>"><?= $status_info['label'] ?></span></p>
            <p style="margin-bottom:2.1rem">
              <img src="../images/truck.svg" style="width:40px" />
            </p>
          </div>
        </div>

        <!-- CỘT PHẢI: Thông tin khách -->
        <div class="col-md-5">
          <h2 class="h3 mb-3 text-black">Thông tin giao hàng</h2>
          <div class="p-3 p-lg-5 border bg-white">
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
            <p><strong>SĐT:</strong> <?= $order['phone'] ? htmlspecialchars($order['phone']) : '<span class="text-muted">—</span>' ?></p>
            <p><strong>Địa chỉ:</strong> <?= $order['address'] ? htmlspecialchars($order['address']) : '<span class="text-muted">—</span>' ?></p>
            <p><strong>Phương thức thanh toán:</strong> <?= $payment_label ?></p>
            <p><strong>Mã đơn hàng:</strong> #<?= $order_id ?></p>
            <p><strong>Ngày đặt:</strong> <?= fmtDate($order['created_at']) ?></p>
            <hr />
            <h4 class="text-black mb-3">Ghi chú</h4>
            <p><?= $order['note'] ? nl2br(htmlspecialchars($order['note'])) : '<span class="text-muted">Không có ghi chú</span>' ?></p>
          </div>
        </div>

      </div>

      <!-- NÚT -->
      <div class="text-center mt-5 mb-5">
        <a href="admin_order.php" class="btn btn-dark btn-lg px-5">&laquo; Quay lại</a>
        <a href="suahoadonn.php?id=<?= $order_id ?>" class="btn btn-success btn-lg px-5 ms-3">
          <i class="fas fa-edit me-1"></i> Cập nhật trạng thái
        </a>
      </div>

    </div>
  </div>

  <!-- FOOTER -->
  <footer class="footer-section">
    <div class="container relative">
      <div class="sofa-img">
        <img src="../images/senda.png" alt="Image" class="img-fluid" />
      </div>
      <div class="row g-5 mb-5">
        <div class="col-lg-4">
          <div class="mb-4 footer-logo-wrap">
            <a href="#" class="footer-logo">BonSai<span>.</span></a>
          </div>
          <p class="mb-4">Mang đến cho bạn trải nghiệm xanh tốt nhất!</p>
          <ul class="list-unstyled custom-social">
            <li><a href="#"><span class="fa fa-brands fa-facebook-f"></span></a></li>
            <li><a href="#"><span class="fa fa-brands fa-twitter"></span></a></li>
            <li><a href="#"><span class="fa fa-brands fa-instagram"></span></a></li>
            <li><a href="#"><span class="fa fa-brands fa-linkedin"></span></a></li>
          </ul>
        </div>
      </div>
    </div>
  </footer>

  <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>