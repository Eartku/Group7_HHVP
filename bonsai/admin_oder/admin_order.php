<?php
// ============================================================
//  admin_order.php - Quản Lý Đơn Hàng (kết nối MySQL thật)
// ============================================================

// ---------- CẤU HÌNH DATABASE ----------
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'bonsai2'; // ← SỬA LẠI đúng tên database trong phpMyAdmin của bạn

$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die('<div class="alert alert-danger m-4">Kết nối thất bại: ' . htmlspecialchars($conn->connect_error) . '</div>');
}
$conn->set_charset('utf8mb4');

// Chọn database, báo lỗi rõ ràng nếu sai tên
if (!$conn->select_db($db_name)) {
    // Liệt kê các database đang có để dễ sửa
    $dbs = [];
    $r = $conn->query("SHOW DATABASES");
    while ($row = $r->fetch_row()) $dbs[] = $row[0];
    die('<div class="alert alert-danger m-4">'
      . '<strong>Không tìm thấy database "' . htmlspecialchars($db_name) . '".</strong><br>'
      . 'Các database hiện có: <code>' . implode(', ', $dbs) . '</code><br>'
      . 'Hãy sửa biến <code>$db_name</code> trong file PHP cho đúng.'
      . '</div>');
}

// ---------- ĐỌC THAM SỐ LỌC ----------
$search     = isset($_GET['search'])     ? trim($_GET['search'])     : '';
$trang_thai = isset($_GET['trang_thai']) ? trim($_GET['trang_thai']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : '';
$current_page = isset($_GET['page'])     ? max(1, (int)$_GET['page']) : 1;
$per_page   = 10;

// ---------- MAP TRẠNG THÁI ----------
$status_map = [
    'pending'    => 'Mới đặt',
    'processing' => 'Đã xử lý',
    'completed'  => 'Đã giao',
    'delivered'  => 'Đã giao',
    'cancelled'  => 'Hủy',
];
$status_map_reverse = [
    'Mới đặt'  => ['pending'],
    'Đã xử lý' => ['processing'],
    'Đã giao'  => ['completed', 'delivered'],
    'Hủy'      => ['cancelled'],
];

// ---------- XÂY DỰNG WHERE ----------
$where  = [];
$params = [];
$types  = '';

if ($search !== '') {
    $like    = '%' . $search . '%';
    $where[] = '(o.id LIKE ? OR o.fullname LIKE ? OR o.email LIKE ?)';
    array_push($params, $like, $like, $like);
    $types  .= 'sss';
}

if ($trang_thai !== '' && isset($status_map_reverse[$trang_thai])) {
    $en_list      = $status_map_reverse[$trang_thai];
    $placeholders = implode(',', array_fill(0, count($en_list), '?'));
    $where[]      = "o.status IN ($placeholders)";
    $params       = array_merge($params, $en_list);
    $types       .= str_repeat('s', count($en_list));
}

if ($start_date !== '') {
    $where[]  = 'DATE(o.created_at) >= ?';
    $params[] = $start_date;
    $types   .= 's';
}
if ($end_date !== '') {
    $where[]  = 'DATE(o.created_at) <= ?';
    $params[] = $end_date;
    $types   .= 's';
}

$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// ---------- ĐẾM TỔNG ĐỂ PHÂN TRANG ----------
$count_sql  = "SELECT COUNT(DISTINCT o.id) AS total FROM orders o $where_sql";
$stmt_count = $conn->prepare($count_sql);
if ($types) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total = (int)$stmt_count->get_result()->fetch_assoc()['total'];
$stmt_count->close();

$total_pages  = max(1, (int)ceil($total / $per_page));
$current_page = min($current_page, $total_pages);
$offset       = ($current_page - 1) * $per_page;

// ---------- QUERY CHÍNH ----------
$data_sql = "
    SELECT
        o.id,
        o.fullname,
        o.email,
        o.status,
        o.created_at,
        o.total_price,
        o.payment_method,
        COALESCE(
            GROUP_CONCAT(DISTINCT
                COALESCE(p.name, CONCAT('SP#', oi.product_id))
                ORDER BY oi.id SEPARATOR ', '
            ),
            '—'
        ) AS san_pham
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN products    p  ON p.id        = oi.product_id
    $where_sql
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT ? OFFSET ?
";

$data_params = array_merge($params, [$per_page, $offset]);
$data_types  = $types . 'ii';

$stmt = $conn->prepare($data_sql);
$stmt->bind_param($data_types, ...$data_params);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

// ---------- HELPERS ----------
function statusVi(string $status, array $map): string {
    return $map[$status] ?? ucfirst($status);
}
function badgeClass(string $status): string {
    return match($status) {
        'pending'               => 'bg-warning text-dark',
        'processing'            => 'bg-info text-dark',
        'completed','delivered' => 'bg-success',
        'cancelled'             => 'bg-danger',
        default                 => 'bg-secondary',
    };
}
function pageLink(int $p): string {
    $params = $_GET;
    $params['page'] = $p;
    return '?' . http_build_query($params);
}
function fmtDate(string $dt): string {
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y', $ts) : $dt;
}
?>
<!--
* Bootstrap 5
* Template Name: Plantaris (Admin - QLKH)
* Author: Kosmoz
-->
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="shortcut icon" href="../images/logo.png" />
  <title>BonSai | Quản Lý Đơn Hàng</title>

  <link href="../css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <link href="../css/hover.css" rel="stylesheet" />
  <!-- KHÔNG load page2.css nữa vì nó ẩn .page bằng CSS radio-button trick -->
  <!-- <link href="../css/page2.css" rel="stylesheet" /> -->
  <link href="../css/tiny-slider.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

  <style>
    /* ===== Override: đảm bảo bảng luôn hiện dù có class .page ===== */
    .table { display: table !important; }
    /* Ẩn radio input ảo nếu còn sót trong HTML cũ */
    input[type="radio"][name="page"] { display: none !important; }
    /* Ẩn .book / .controls nếu còn sót */
    .book  { display: block !important; }
    .controls label { display: none !important; }
  </style>
</head>

<body>
  <!-- ===== NAVBAR ===== -->
  <?php include "../admin_includes/header.php"; ?>
  <!-- ===== BỘ LỌC ===== -->
  <div>
    <div class="center-row" style="scale:1; text-align:center">
      <h2 class="fw-bold text-success" style="line-height:2.2;">Quản lý đơn hàng</h2>

      <div class="page-header1 fade-in-up">
        <form method="GET" action="admin_order.php">
          <div class="search-filter-container">

            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" name="search" id="searchInput"
                placeholder="Tìm kiếm mã ĐH, tên KH, email..."
                value="<?= htmlspecialchars($search) ?>" />
            </div>

            <div class="brand-filter">
              <div class="mb-2" style="margin-bottom:0rem !important">
                <select name="trang_thai" class="form-select form-select-sm"
                  style="padding:14px 20px; font-size:1.123rem">
                  <option value="">Trạng thái</option>
                  <?php foreach (['Mới đặt','Đã xử lý','Đã giao','Hủy'] as $tt): ?>
                    <option value="<?= $tt ?>" <?= $trang_thai === $tt ? 'selected' : '' ?>><?= $tt ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="date-filter">
              <div class="date-filter d-flex align-items-center gap-2">
                <span>Từ</span>
                <input type="date" name="start_date" id="startDate"
                  class="form-control" style="min-width:180px"
                  value="<?= htmlspecialchars($start_date) ?>" />
                <span>đến</span>
                <input type="date" name="end_date" id="endDate"
                  class="form-control" style="min-width:180px"
                  value="<?= htmlspecialchars($end_date) ?>" />
              </div>
              <div>
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-filter me-1"></i> Lọc
                </button>
                <a href="admin_order.php" class="btn btn-outline-secondary ms-1">
                  <i class="fas fa-redo me-1"></i> Reset
                </a>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ===== BẢNG ĐƠN HÀNG ===== -->
  <div class="container py-5" style="min-height:50px">
    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <small class="text-muted">
          Tổng: <strong><?= $total ?></strong> đơn hàng
          <?= ($search || $trang_thai || $start_date || $end_date)
              ? '<span class="badge bg-secondary ms-1">đã lọc</span>' : '' ?>
        </small>
        <small class="text-muted">Trang <?= $current_page ?> / <?= $total_pages ?></small>
      </div>

      <?php if (empty($orders)): ?>
        <p class="text-center text-muted py-4">
          <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
          Không tìm thấy đơn hàng nào.
        </p>
      <?php else: ?>
        <div class="table-responsive">
          <!-- KHÔNG dùng class "page page1" để tránh bị CSS cũ ẩn đi -->
          <table class="table table-bordered text-center align-middle" style="display:table !important">
            <thead class="table-light">
              <tr>
                <th>Mã ĐH</th>
                <th>Tên sản phẩm</th>
                <th>Tên Khách hàng</th>
                <th>Ngày Đặt</th>
                <th>Trạng thái</th>
                <th>Chi tiết</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <tr>
                <td><strong>#<?= htmlspecialchars($o['id']) ?></strong></td>
                <td class="text-start"><?= htmlspecialchars($o['san_pham']) ?></td>
                <td><?= htmlspecialchars($o['fullname']) ?></td>
                <td><?= fmtDate($o['created_at']) ?></td>
                <td>
                  <span class="badge <?= badgeClass($o['status']) ?>">
                    <?= statusVi($o['status'], $status_map) ?>
                  </span>
                </td>
                <td>
                  <a href="http://localhost/Group7_HHVP/bonsai/admin_oder/orderdetails.html?id=<?= urlencode($o['id']) ?>"
                     class="badge bg-primary text-decoration-none px-2 py-1">
                    <i class="fas fa-eye me-1"></i>Xem
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <!-- ===== PHÂN TRANG PHP (thay thế CSS radio trick cũ) ===== -->
      <?php if ($total_pages > 1): ?>
      <nav class="mt-3">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= pageLink($current_page - 1) ?>">&laquo;</a>
          </li>
          <?php
          $start_p = max(1, $current_page - 2);
          $end_p   = min($total_pages, $current_page + 2);
          if ($start_p > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= pageLink(1) ?>">1</a></li>
            <?php if ($start_p > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
          <?php endif; ?>
          <?php for ($p = $start_p; $p <= $end_p; $p++): ?>
          <li class="page-item <?= $p === $current_page ? 'active' : '' ?>">
            <a class="page-link" href="<?= pageLink($p) ?>"><?= $p ?></a>
          </li>
          <?php endfor; ?>
          <?php if ($end_p < $total_pages): ?>
            <?php if ($end_p < $total_pages - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
            <li class="page-item"><a class="page-link" href="<?= pageLink($total_pages) ?>"><?= $total_pages ?></a></li>
          <?php endif; ?>
          <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= pageLink($current_page + 1) ?>">&raquo;</a>
          </li>
        </ul>
      </nav>
      <?php endif; ?>

    </div>
  </div>

  <!-- ===== FOOTER ===== -->
  <footer class="footer-section bg-dark text-light mt-5">
    <div class="container py-4">
      <div class="row">
        <div class="col-lg-8"><h5>Admin - BonSai 🌱</h5></div>
      </div>
      <div class="border-top pt-3 mt-3 text-center">
        <p class="mb-0">
          Copyright &copy; <?= date('Y') ?>. All Rights Reserved. — Designed by
          <a href="group7.html" class="text-light text-decoration-underline">Group 7</a>
        </p>
      </div>
    </div>
  </footer>

</body>
</html>