<?php
ob_start();
session_start();
require "../config/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { header("Location: admin_order.php"); exit(); }

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_update'])) {
    $new_status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? ''));
    $new_note   = mysqli_real_escape_string($conn, trim($_POST['note']   ?? ''));
    $allowed    = ['processing','processed','shipping','shipped','cancelled'];
    if (!in_array($new_status, $allowed)) {
        $error_msg = 'Trạng thái không hợp lệ.';
    } else {
        $sql = "UPDATE orders SET status='$new_status', note='$new_note', updated_at=NOW() WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header("Location: http://localhost/Group7_HHVP/bonsai/admin_oder/admin_order.php?updated=1");
            exit();
        } else {
            $error_msg = 'Lỗi: ' . mysqli_error($conn);
        }
    }
}

$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id=$id");
if (!$order_res || mysqli_num_rows($order_res) === 0) { header("Location: admin_order.php"); exit(); }
$order = mysqli_fetch_assoc($order_res);

$items_res = mysqli_query($conn, "
    SELECT oi.quantity, oi.price, COALESCE(p.name, CONCAT('SP#',oi.product_id)) AS product_name
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = $id ORDER BY oi.id
");

// ---- MAP TRẠNG THÁI THỰC TẾ ----
$status_map = [
    'processing' => ['label'=>'Đang xử lý',    'badge'=>'bg-warning text-dark', 'icon'=>'fa-hourglass-half'],
    'processed'  => ['label'=>'Đã xử lý',      'badge'=>'bg-info text-dark',    'icon'=>'fa-check-circle'],
    'shipping'   => ['label'=>'Đang giao hàng','badge'=>'bg-primary',           'icon'=>'fa-truck'],
    'shipped'    => ['label'=>'Đã giao hàng',  'badge'=>'bg-success',           'icon'=>'fa-box-open'],
    'cancelled'  => ['label'=>'Đã hủy',        'badge'=>'bg-danger',            'icon'=>'fa-times-circle'],
];
$payment_map = [
    'cod'     => 'Tiền mặt khi nhận hàng (COD)',
    'momo'    => 'Ví MoMo',
    'banking' => 'Chuyển khoản ngân hàng',
    'vnpay'   => 'VNPay',
    'zalopay' => 'ZaloPay',
];

// Timeline steps (không gồm cancelled)
$timeline = [
    'processing' => ['icon'=>'fa-hourglass-half','label'=>'Đang xử lý'],
    'processed'  => ['icon'=>'fa-check-circle',  'label'=>'Đã xử lý'],
    'shipping'   => ['icon'=>'fa-truck',          'label'=>'Đang giao'],
    'shipped'    => ['icon'=>'fa-box-open',       'label'=>'Đã giao'],
];
$tl_order   = array_keys($timeline);
$cur_status = $order['status'];
$cur_idx    = array_search($cur_status, $tl_order);

function fmtMoney($n) { return number_format((float)$n,0,',','.') . 'đ'; }
function fmtDate($dt) { $ts=strtotime($dt); return $ts ? date('d/m/Y H:i',$ts) : $dt; }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include "../admin_includes/loader.php"; ?>
    <style>
        /* Timeline */
        .status-track{display:flex;border-radius:8px;overflow:hidden;margin-bottom:24px;box-shadow:0 1px 4px rgba(0,0,0,.07)}
        .status-step{flex:1;padding:11px 6px;text-align:center;font-size:.74rem;font-weight:600;background:#f1f3f5;color:#adb5bd;border-right:2px solid #fff;display:flex;flex-direction:column;align-items:center;gap:5px;transition:all .2s}
        .status-step:last-child{border-right:none}
        .status-step.done{background:#d4edda;color:#155724}
        .status-step.current{background:#28a745;color:#fff}
        .status-step i{font-size:.95rem}

        /* Cards */
        .info-card{background:#fff;border:1px solid #dee2e6;border-radius:10px;padding:20px 22px;margin-bottom:18px}
        .info-card h5{font-size:.82rem;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid #f1f3f5;display:flex;align-items:center;gap:7px}

        /* Info rows */
        .info-row{display:flex;gap:8px;margin-bottom:9px;font-size:.9rem;align-items:flex-start}
        .info-row .lbl{min-width:158px;color:#6c757d;font-weight:600;flex-shrink:0;font-size:.85rem}
        .info-row .val{color:#212529;word-break:break-word;font-size:.9rem}

        /* Inline status form */
        .status-inline-form{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
        .status-inline-form select{
            height:32px;border:1.5px solid #ced4da;border-radius:6px;
            padding:0 10px;font-size:.85rem;font-weight:600;
            color:#495057;background:#fff;cursor:pointer;
            transition:border-color .18s;flex:1;min-width:160px;
        }
        .status-inline-form select:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 2px rgba(40,167,69,.15)}

        /* Table */
        .order-table thead th{background:#f8f9fa;font-size:.78rem;font-weight:700;color:#6c757d;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid #dee2e6}
        .order-table tbody td,.order-table tfoot td{font-size:.9rem;vertical-align:middle}
        .order-table tfoot td{font-weight:700}

        /* Note textarea */
        .note-area{border-radius:8px;font-size:.88rem;resize:vertical;border:1.5px solid #ced4da;width:100%;padding:10px 12px;transition:border-color .2s}
        .note-area:focus{outline:none;border-color:#28a745;box-shadow:0 0 0 2px rgba(40,167,69,.12)}

        /* THÊM: sticky cột phải */
        .col-right-sticky { position: sticky; top: 20px; }

    </style>
</head>
<body>
<?php include "../admin_includes/header.php"; ?>



<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Chi tiết đơn hàng</h1>
        <span style="color:aliceblue">Mã đơn: <strong>#<?= str_pad($order['id'],4,'0',STR_PAD_LEFT) ?></strong></span>
    </div>
</div>

<div class="container py-5">

    <?php if ($error_msg): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
    <?php endif; ?>

    <!-- ===== TIMELINE ===== -->
    <?php if ($cur_status !== 'cancelled'): ?>
    <div class="status-track">
        <?php foreach ($timeline as $key => $s):
            $idx = array_search($key, $tl_order);
            if ($key === $cur_status)                               $cls = 'current';
            elseif ($cur_idx !== false && $idx < $cur_idx)         $cls = 'done';
            else                                                    $cls = '';
        ?>
        <div class="status-step <?= $cls ?>">
            <i class="fas <?= $s['icon'] ?>"></i>
            <?= $s['label'] ?>
            <?php if($cls==='done'):?><i class="fas fa-check" style="font-size:.6rem;margin-top:1px"></i><?php endif;?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="fas fa-times-circle fa-lg"></i>
        <strong>Đơn hàng này đã bị hủy.</strong>
    </div>
    <?php endif; ?>

    <!-- ===== FORM BAO NGOÀI (POST toàn bộ) ===== -->
    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?id=<?= $id ?>" id="updateForm">
    <input type="hidden" name="do_update" value="1">

    <div class="row g-4">

        <!-- ===== CỘT TRÁI: Thông tin giao hàng + Thông tin đơn hàng ===== -->
        <div class="col-lg-5 col-right-sticky">

            <!-- Thông tin giao hàng -->
            <div class="info-card">
                <h5><i class="fas fa-user text-success"></i>Thông tin giao hàng</h5>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-user me-1"></i>Họ tên:</span>
                    <span class="val"><?= htmlspecialchars($order['fullname']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-envelope me-1"></i>Email:</span>
                    <span class="val"><?= htmlspecialchars($order['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-phone me-1"></i>Số điện thoại:</span>
                    <span class="val"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-map-marker-alt me-1"></i>Địa chỉ:</span>
                    <span class="val"><?= htmlspecialchars($order['address']) ?></span>
                </div>
            </div>

            <!-- Thông tin đơn hàng + TRẠNG THÁI + GHI CHÚ + NÚT -->
            <div class="info-card">
                <h5><i class="fas fa-receipt text-success"></i>Thông tin đơn hàng</h5>

                <div class="info-row">
                    <span class="lbl"><i class="fas fa-hashtag me-1"></i>Mã đơn hàng:</span>
                    <span class="val"><strong>#<?= str_pad($order['id'],4,'0',STR_PAD_LEFT) ?></strong></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-calendar me-1"></i>Ngày đặt:</span>
                    <span class="val"><?= fmtDate($order['created_at']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-sync me-1"></i>Cập nhật lần cuối:</span>
                    <span class="val"><?= fmtDate($order['updated_at']) ?></span>
                </div>
                <div class="info-row">
                    <span class="lbl"><i class="fas fa-credit-card me-1"></i>Thanh toán:</span>
                    <span class="val"><?= htmlspecialchars($payment_map[$order['payment_method']] ?? strtoupper($order['payment_method'])) ?></span>
                </div>

                <!-- TRẠNG THÁI — DROPDOWN THAY ĐỔI TRỰC TIẾP -->
                <div class="info-row align-items-center">
                    <span class="lbl"><i class="fas fa-tag me-1"></i>Trạng thái:</span>
                    <span class="val w-100">
                        <div class="status-inline-form">
                            <select name="status" id="statusSelect">
                                <?php foreach ($status_map as $v => $info): ?>
                                <option value="<?= $v ?>" <?= $cur_status===$v ? 'selected':'' ?>>
                                    <?= $info['label'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Badge hiển thị trạng thái hiện tại -->
                        <span class="badge <?= $status_map[$cur_status]['badge'] ?? 'bg-secondary' ?> mt-2 d-inline-flex align-items-center gap-1" id="curBadge">
                            <i class="fas <?= $status_map[$cur_status]['icon'] ?? 'fa-tag' ?>"></i>
                            <?= htmlspecialchars($status_map[$cur_status]['label'] ?? ucfirst($cur_status)) ?>
                        </span>
                    </span>
                </div>

                <!-- GHI CHÚ — nằm dưới trạng thái, hiện nội dung cũ, click sửa được -->
                <div class="info-row" style="flex-direction:column;gap:6px;margin-top:4px">
                    <span class="lbl"><i class="fas fa-sticky-note me-1"></i>Ghi chú / Thông báo cho khách:</span>
                    <textarea name="note" class="note-area" rows="3"
                        placeholder="VD: Dự kiến giao đến vào 14/12/2025. Xin hãy chuẩn bị tiền thanh toán."
                    ><?= htmlspecialchars($order['note'] ?? '') ?></textarea>
                </div>

                <!-- NÚT HÀNH ĐỘNG — nằm trong card thông tin đơn hàng -->
                <div class="d-flex gap-2 mt-3">
                    <a href="admin_order.php" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="button" class="btn btn-success flex-fill" onclick="confirmUpdate()">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                </div>

            </div>

        </div>

        <!-- ===== CỘT PHẢI: Sản phẩm trong đơn ===== -->
        <div class="col-lg-7">

            <!-- Bảng sản phẩm (chỉ xem) -->
            <div class="info-card">
                <h5><i class="fas fa-shopping-basket text-success"></i>Sản phẩm trong đơn</h5>
                <div class="table-responsive">
                <table class="table order-table mb-0">
                    <thead><tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">SL</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr></thead>
                    <tbody>
                    <?php
                    $subtotal = 0;
                    if ($items_res && mysqli_num_rows($items_res) > 0):
                        while ($item = mysqli_fetch_assoc($items_res)):
                            $line = $item['quantity'] * $item['price'];
                            $subtotal += $line;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="text-center"><strong>x<?= (int)$item['quantity'] ?></strong></td>
                        <td class="text-end"><?= fmtMoney($item['price']) ?></td>
                        <td class="text-end"><?= fmtMoney($line) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">
                        <i class="fas fa-box-open me-1"></i>Không có dữ liệu sản phẩm
                    </td></tr>
                    <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <?php if($subtotal > 0): ?>
                        <tr>
                            <td colspan="3" class="text-end fw-normal text-muted">Tạm tính:</td>
                            <td class="text-end fw-normal"><?= fmtMoney($subtotal) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-end">Tổng thanh toán:</td>
                            <td class="text-end text-success fs-6">
                                <?= fmtMoney($order['total_price'] ?: $order['total_amount'] ?: $subtotal) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
            </div>

        </div>
    </div>
    </form>

</div>

<?php include '../admin_includes/footer.php'; ?>

<script>
function confirmUpdate() {
    const sel = document.getElementById('statusSelect');
    const txt = sel.options[sel.selectedIndex].text;
    if (confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng thành:\n"' + txt + '" không?')) {
        document.getElementById('updateForm').submit();
    }
}
</script>
</body>
</html>