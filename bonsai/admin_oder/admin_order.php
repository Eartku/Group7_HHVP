<?php
session_start();
// ============================================================
//  admin_order.php - Quản Lý Đơn Hàng
//  Giao diện đồng bộ với customermanage.php
// ============================================================

require "../config/db.php";

// ---------- ĐỌC THAM SỐ LỌC ----------
$search_value = isset($_GET['search_value']) ? trim($_GET['search_value']) : '';
$search_type  = isset($_GET['search_type'])  ? trim($_GET['search_type'])  : 'id';
$trang_thai   = isset($_GET['trang_thai'])   ? trim($_GET['trang_thai'])   : '';
$start_date   = isset($_GET['start_date'])   ? trim($_GET['start_date'])   : '';
$end_date     = isset($_GET['end_date'])     ? trim($_GET['end_date'])     : '';
$sort_address = isset($_GET['sort_address']) ? trim($_GET['sort_address']) : ''; // 'asc' | 'desc' | ''
$search_done  = isset($_GET['do_search']);
$search_error = '';

// Trạng thái mở rộng: pending = Mới đặt/Xử lý, confirmed = Đã xác nhận, delivered = Đã giao, cancelled = Đã huỷ


// ---------- PHÂN TRANG ----------
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ---------- MAP TRẠNG THÁI (theo đúng enum DB) ----------
$status_map = [
    'processing' => 'Đang xử lý',
    'processed'  => 'Đã xử lý',
    'shipping'   => 'Đang giao hàng',
    'shipped'    => 'Đã giao hàng',
    'cancelled'  => 'Đã hủy',
];
// Mỗi key = giá trị lọc → mảng giá trị DB khớp
$status_filter_map = [
    'processing' => ['processing'],
    'processed'  => ['processed'],
    'shipping'   => ['shipping'],
    'shipped'    => ['shipped'],
    'cancelled'  => ['cancelled'],
];
$status_label_map = [
    'processing' => 'Đang xử lý',
    'processed'  => 'Đã xử lý',
    'shipping'   => 'Đang giao hàng',
    'shipped'    => 'Đã giao hàng',
    'cancelled'  => 'Đã hủy',
];

// ---------- BUILD WHERE ----------
$where_parts = [];

// -- Bộ lọc tìm kiếm theo tab (chỉ khi nhấn nút Lọc)
if ($search_done && $search_value !== '') {
    $escaped = mysqli_real_escape_string($conn, $search_value);

    if ($search_type === 'id') {
        if (!ctype_digit($search_value)) {
            $search_error = 'Mã đơn hàng không hợp lệ. Vui lòng nhập số nguyên.';
        } else {
            $where_parts[] = "o.id = '$escaped'";
        }
    } elseif ($search_type === 'email') {
        $where_parts[] = "o.email = '$escaped'";
    } elseif ($search_type === 'phone') {
        $where_parts[] = "o.phone = '$escaped'";
    } elseif ($search_type === 'address') {
        $where_parts[] = "o.address LIKE '%$escaped%'";
    } else {
        $where_parts[] = "o.fullname LIKE '%$escaped%'";
    }
}

// -- Lọc trạng thái (luôn áp dụng khi có giá trị)
if ($trang_thai !== '' && isset($status_filter_map[$trang_thai])) {
    $en_list = $status_filter_map[$trang_thai];
    $placeholders_tt = implode(',', array_map(fn($v) => "'".mysqli_real_escape_string($conn, $v)."'", $en_list));
    $where_parts[] = "o.status IN ($placeholders_tt)";
}

// -- Lọc khoảng thời gian đặt hàng (luôn áp dụng khi có giá trị)
if ($start_date !== '') {
    $escaped_sd    = mysqli_real_escape_string($conn, $start_date);
    $where_parts[] = "DATE(o.created_at) >= '$escaped_sd'";
}
if ($end_date !== '') {
    $escaped_ed    = mysqli_real_escape_string($conn, $end_date);
    $where_parts[] = "DATE(o.created_at) <= '$escaped_ed'";
}

$where_sql = $where_parts ? ('WHERE ' . implode(' AND ', $where_parts)) : '';

// -- Xác định ORDER BY
$order_sql = 'ORDER BY o.created_at DESC'; // mặc định: mới nhất trước
if ($sort_address === 'asc') {
    $order_sql = 'ORDER BY o.address ASC, o.created_at DESC';
} elseif ($sort_address === 'desc') {
    $order_sql = 'ORDER BY o.address DESC, o.created_at DESC';
}

// ---------- ĐẾM TỔNG ----------
$count_sql = "
    SELECT COUNT(DISTINCT o.id) AS total
    FROM orders o
    $where_sql
";
$count_res  = mysqli_query($conn, $count_sql);
$total_rows = (int)mysqli_fetch_assoc($count_res)['total'];

// Kiểm tra không có kết quả khi tìm kiếm
if ($search_done && $search_value !== '' && !$search_error && $total_rows === 0) {
    if ($search_type === 'id')
        $search_error = "Mã đơn hàng #" . htmlspecialchars($search_value) . " không tồn tại.";
    elseif ($search_type === 'email')
        $search_error = "Email \"" . htmlspecialchars($search_value) . "\" không tồn tại trong hệ thống.";
    elseif ($search_type === 'phone')
        $search_error = "Số điện thoại \"" . htmlspecialchars($search_value) . "\" không tồn tại.";
    elseif ($search_type === 'address')
        $search_error = "Không tìm thấy đơn hàng nào có địa chỉ chứa \"" . htmlspecialchars($search_value) . "\".";
    else
        $search_error = "Không tìm thấy đơn hàng nào với tên chứa \"" . htmlspecialchars($search_value) . "\".";
}

$total_pages  = max(1, (int)ceil($total_rows / $limit));
$page         = min($page, $total_pages);
$offset       = ($page - 1) * $limit;

// ---------- QUERY CHÍNH ----------
$orders_result = null;
if (!$search_error) {
    $data_sql = "
        SELECT
            o.id,
            o.fullname,
            o.email,
            o.phone,
            o.address,
            o.status,
            o.created_at,
            o.total_price,
            o.payment_method,
            COALESCE(
                GROUP_CONCAT(DISTINCT
                    COALESCE(p.name, CONCAT('SP#', oi.product_id))
                    ORDER BY oi.id SEPARATOR ', '
                ), '—'
            ) AS san_pham
        FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN products    p  ON p.id        = oi.product_id
        $where_sql
        GROUP BY o.id
        $order_sql
        LIMIT $limit OFFSET $offset
    ";
    $orders_result = mysqli_query($conn, $data_sql);
}

// ---------- HELPERS ----------
function statusVi($status, $map) {
    return $map[$status] ?? ucfirst($status);
}
function badgeClass($status) {
    return match($status) {
        'processing'            => 'bg-warning text-dark',
        'processed'             => 'bg-info text-dark',
        'shipping'              => 'bg-primary',
        'shipped'               => 'bg-success',
        'cancelled'             => 'bg-danger',
        default                 => 'bg-secondary',
    };
}
function page_url_order($p) {
    $params = $_GET;
    $params['page'] = $p;
    return '?' . http_build_query($params);
}
function fmtDate($dt) {
    $ts = strtotime($dt);
    return $ts ? date('d/m/Y', $ts) : $dt;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include "../admin_includes/loader.php"; ?>
    <link rel="stylesheet" href="../css/admin_order.css">
</head>

<body>

<?php include "../admin_includes/header.php"; ?>

<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Quản lý đơn hàng</h1>
        <span style="color: aliceblue;"></span>
    </div>
</div>

<div class="container py-5">
    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

        <!-- ===== THANH LỌC 1 HÀNG ===== -->
        <form method="GET" action="admin_order.php" id="searchForm" autocomplete="off">
        <div class="order-filter-bar">

            <!-- GROUP 1: Tìm kiếm + Ngày đặt + Nút Lọc chung -->
            <div class="filter-group fg-search">

                <!-- PILL TABS (giống customermanage.php) -->
                <div class="search-type-tabs" id="searchTypeTabs">

                    <input type="radio" class="stt-radio" name="search_type" id="stt_fullname"
                           value="fullname" <?= $search_type==='fullname' ? 'checked':'' ?>>
                    <label class="stt-btn <?= $search_type==='fullname' ? 'active':'' ?>"
                           for="stt_fullname" onclick="selectTab('fullname')">
                        <i class="fas fa-user"></i> Họ tên
                    </label>

                    <input type="radio" class="stt-radio" name="search_type" id="stt_id"
                           value="id" <?= $search_type==='id' ? 'checked':'' ?>>
                    <label class="stt-btn <?= $search_type==='id' ? 'active':'' ?>"
                           for="stt_id" onclick="selectTab('id')">
                        <i class="fas fa-hashtag"></i> Mã ĐH
                    </label>

                    <input type="radio" class="stt-radio" name="search_type" id="stt_email"
                           value="email" <?= $search_type==='email' ? 'checked':'' ?>>
                    <label class="stt-btn <?= $search_type==='email' ? 'active':'' ?>"
                           for="stt_email" onclick="selectTab('email')">
                        <i class="fas fa-envelope"></i> Email
                    </label>

                    <input type="radio" class="stt-radio" name="search_type" id="stt_phone"
                           value="phone" <?= $search_type==='phone' ? 'checked':'' ?>>
                    <label class="stt-btn <?= $search_type==='phone' ? 'active':'' ?>"
                           for="stt_phone" onclick="selectTab('phone')">
                        <i class="fas fa-phone"></i> Số ĐT
                    </label>

                    <input type="radio" class="stt-radio" name="search_type" id="stt_address"
                           value="address" <?= $search_type==='address' ? 'checked':'' ?>>
                    <label class="stt-btn <?= $search_type==='address' ? 'active':'' ?>"
                           for="stt_address" onclick="selectTab('address')">
                        <i class="fas fa-map-marker-alt"></i> Địa chỉ
                    </label>

                </div>

                <div class="search-vdivider"></div>

                <div class="search-input-bar">
                    <i class="fas fa-search si-icon" id="inputIcon"></i>
                    <input type="text"
                           name="search_value"
                           id="search_value"
                           placeholder="Nhập tên khách hàng..."
                           value="<?= htmlspecialchars($search_value) ?>">
                </div>
                <div id="search_hint" style="display:none"></div>

                <span class="fg-inner-sep"></span>
                <i class="fas fa-calendar-alt fg-date-icon"></i>

                <input type="date" name="start_date" class="date-input-inline"
                       value="<?= htmlspecialchars($start_date) ?>" title="Từ ngày">
                <span class="date-sep">→</span>
                <input type="date" name="end_date" class="date-input-inline"
                       value="<?= htmlspecialchars($end_date) ?>" title="Đến ngày">

                <button type="submit" name="do_search" value="1" class="btn-bar-search">
                    <i class="fas fa-filter"></i> Lọc
                </button>

                <?php if ($start_date || $end_date): ?>
                <a href="<?= '?' . http_build_query(array_merge($_GET, ['start_date'=>'','end_date'=>'','page'=>1])) ?>"
                   class="btn-date-clear-x" title="Xoá lọc ngày"><i class="fas fa-times"></i></a>
                <span class="date-chip">
                    <?= $start_date ? date('d/m/Y', strtotime($start_date)) : '…' ?>
                    → <?= $end_date ? date('d/m/Y', strtotime($end_date)) : '…' ?>
                </span>
                <?php endif; ?>
            </div>

            <!-- HÀNG 2: Trạng thái + Địa chỉ + Reset — căn giữa -->
            <div class="filter-row2">

            <!-- GROUP 2: Trạng thái — onclick dropdown -->
            <div class="filter-group fg-status">
                <span class="fg-label">Trạng thái</span>
                <div class="status-dropdown-wrap" id="statusWrap">
                    <?php
                    // Xác định nhãn + dot đang chọn
                    $cur_tt_info = [
                        ''           => ['dot'=>'dot-all',        'text'=>'Tất cả'],
                        'processing' => ['dot'=>'dot-processing',  'text'=>'Đang xử lý'],
                        'processed'  => ['dot'=>'dot-confirmed',   'text'=>'Đã xử lý'],
                        'shipping'   => ['dot'=>'dot-delivered',   'text'=>'Đang giao hàng'],
                        'shipped'    => ['dot'=>'dot-delivered',   'text'=>'Đã giao hàng'],
                        'cancelled'  => ['dot'=>'dot-cancelled',   'text'=>'Đã hủy'],
                    ];
                    $cur = $cur_tt_info[$trang_thai] ?? $cur_tt_info[''];
                    ?>
                    <button type="button" class="status-dropdown-btn" onclick="toggleStatus(event)">
                        <span class="status-dot <?= $cur['dot'] ?>"></span>
                        <span id="statusBtnText"><?= $cur['text'] ?></span>
                        <i class="fas fa-chevron-down caret" id="statusCaret"></i>
                    </button>

                    <div class="status-dropdown-menu" id="statusMenu">
                        <?php
                        $tt_menu = [
                            ''           => ['dot'=>'dot-all',        'text'=>'Tất cả',          'icon'=>'fa-list'],
                            'processing' => ['dot'=>'dot-processing',  'text'=>'Đang xử lý',      'icon'=>'fa-hourglass-half'],
                            'processed'  => ['dot'=>'dot-confirmed',   'text'=>'Đã xử lý',        'icon'=>'fa-check-circle'],
                            'shipping'   => ['dot'=>'dot-delivered',   'text'=>'Đang giao hàng',  'icon'=>'fa-truck'],
                            'shipped'    => ['dot'=>'dot-delivered',   'text'=>'Đã giao hàng',    'icon'=>'fa-box-open'],
                            'cancelled'  => ['dot'=>'dot-cancelled',   'text'=>'Đã hủy',          'icon'=>'fa-times-circle'],
                        ];
                        foreach ($tt_menu as $val => $m):
                            $lp = $_GET; $lp['trang_thai'] = $val; $lp['page'] = 1;
                            unset($lp['do_search']);
                            $link = '?' . http_build_query($lp);
                            $active_cls = ($trang_thai === $val) ? 'is-active' : '';
                        ?>
                        <a href="<?= $link ?>" class="status-menu-item <?= $active_cls ?>">
                            <span class="sm-dot <?= $m['dot'] ?>"></span>
                            <i class="fas <?= $m['icon'] ?>" style="font-size:.78rem;opacity:.7"></i>
                            <?= $m['text'] ?>
                            <?php if ($trang_thai === $val): ?><i class="fas fa-check ms-auto" style="color:#28a745;font-size:.75rem"></i><?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>


            <!-- GROUP 3: Sắp xếp địa chỉ -->
            <div class="filter-group fg-sort">
                <span class="fg-label">Địa chỉ</span>
                <?php
                $sort_opts = [
                    ''     => ['icon'=>'fa-clock',          'label'=>'Mặc định'],
                    'asc'  => ['icon'=>'fa-sort-alpha-down', 'label'=>'A → Z'],
                    'desc' => ['icon'=>'fa-sort-alpha-up',   'label'=>'Z → A'],
                ];
                foreach ($sort_opts as $val => $opt):
                    $lp = $_GET; $lp['sort_address'] = $val; $lp['page'] = 1;
                    $active_cls = ($sort_address === $val) ? 'is-active' : '';
                ?>
                <a href="<?= '?' . http_build_query($lp) ?>"
                   class="btn-sort-bar <?= $active_cls ?>">
                    <i class="fas <?= $opt['icon'] ?>"></i> <?= $opt['label'] ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- GROUP 5: Reset toàn bộ -->
            <?php if ($search_done || $trang_thai || $start_date || $end_date || $sort_address): ?>
            <div class="filter-group fg-reset">
                <a href="admin_order.php" class="btn-bar-reset">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            </div>
            <?php endif; ?>

            </div><!-- end .filter-row2 -->

        </div>
        </form>

        <!-- ===== THÔNG BÁO LỖI ===== -->
        <?php if ($search_done && $search_error): ?>
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span><?= htmlspecialchars($search_error) ?></span>
        </div>
        <?php endif; ?>

        <!-- ===== BADGE TỔNG KẾT QUẢ ===== -->
        <?php
        $has_any_filter = ($search_done && !$search_error && $search_value !== '')
                        || $trang_thai !== ''
                        || $start_date !== ''
                        || $end_date   !== ''
                        || $sort_address !== '';
        if ($has_any_filter): ?>
        <div class="result-summary-badge">
            <i class="fas fa-check-circle"></i>
            Hiển thị <strong><?= $total_rows ?></strong> đơn hàng
            <?php if ($search_value): ?>
                — tìm: <span class="tag-chip"><i class="fas fa-search"></i> <?= htmlspecialchars($search_value) ?></span>
            <?php endif; ?>
            <?php if ($trang_thai): ?>
                — <span class="tag-chip"><i class="fas fa-tag"></i> <?= htmlspecialchars($status_label_map[$trang_thai] ?? $trang_thai) ?></span>
            <?php endif; ?>
            <?php if ($start_date || $end_date): ?>
                — <span class="tag-chip"><i class="fas fa-calendar"></i>
                    <?= $start_date ? date('d/m/Y', strtotime($start_date)) : '…' ?>
                    → <?= $end_date ? date('d/m/Y', strtotime($end_date)) : '…' ?></span>
            <?php endif; ?>
            <?php if ($sort_address): ?>
                — <span class="tag-chip"><i class="fas fa-map-marker-alt"></i> <?= $sort_address === 'asc' ? 'A→Z' : 'Z→A' ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ===== BẢNG ĐƠN HÀNG ===== -->
        <?php if (!$search_error): ?>
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã ĐH</th>
                    <th>Tên sản phẩm</th>
                    <th>Tên khách hàng</th>
                    <th>Địa chỉ
                        <?php if ($sort_address === 'asc'): ?>
                            <i class="fas fa-sort-alpha-down text-success ms-1" title="Sắp xếp A→Z"></i>
                        <?php elseif ($sort_address === 'desc'): ?>
                            <i class="fas fa-sort-alpha-up text-success ms-1" title="Sắp xếp Z→A"></i>
                        <?php else: ?>
                            <i class="fas fa-sort text-muted ms-1" style="opacity:.4"></i>
                        <?php endif; ?>
                    </th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $has_rows = false;
            if ($orders_result) {
                while ($row = mysqli_fetch_assoc($orders_result)) {
                    $has_rows   = true;
                    $fullname   = htmlspecialchars($row['fullname']);
                    $address    = htmlspecialchars($row['address'] ?? '—');
                    $san_pham   = htmlspecialchars($row['san_pham']);

                    // Highlight tên nếu tìm kiếm theo tên
                    if ($search_done && $search_type === 'fullname' && $search_value !== '') {
                        $kw = htmlspecialchars($search_value);
                        $fullname = preg_replace('/(' . preg_quote($kw, '/') . ')/iu',
                                    '<mark class="hl">$1</mark>', $fullname);
                    }
                    // Highlight địa chỉ nếu tìm kiếm theo địa chỉ
                    if ($search_done && $search_type === 'address' && $search_value !== '') {
                        $kw = htmlspecialchars($search_value);
                        $address = preg_replace('/(' . preg_quote($kw, '/') . ')/iu',
                                   '<mark class="hl">$1</mark>', $address);
                    }
            ?>
            <tr>
                <td><strong>#<?= str_pad($row['id'], 4, "0", STR_PAD_LEFT) ?></strong></td>
                <td class="text-start"><?= $san_pham ?></td>
                <td><?= $fullname ?></td>
                <td class="text-start"><?= $address ?></td>
                <td><?= fmtDate($row['created_at']) ?></td>
                <td>
                    <span class="badge <?= badgeClass($row['status']) ?>">
                        <?= statusVi($row['status'], $status_map) ?>
                    </span>
                </td>
                <td>
                    <a href="orderdetails.php?id=<?= urlencode($row['id']) ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                </td>
            </tr>
            <?php
                }
            }
            if (!$has_rows): ?>
            <tr>
                <td colspan="7" class="text-muted py-4">
                    <i class="fas fa-inbox fa-lg me-2"></i>Chưa có đơn hàng nào.
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- ===== PHÂN TRANG ===== -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Phân trang đơn hàng">
            <ul class="pagination justify-content-center flex-wrap">

                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url_order(1) ?>" title="Trang đầu">
                        <i class="fas fa-angles-left"></i>
                    </a>
                </li>
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url_order($page - 1) ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>

                <?php
                $range = 2;
                $start = max(1, $page - $range);
                $end   = min($total_pages, $page + $range);

                if ($start > 1): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif;

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= page_url_order($i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($end < $total_pages): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url_order($page + 1) ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url_order($total_pages) ?>" title="Trang cuối">
                        <i class="fas fa-angles-right"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <p class="page-info">
            Trang <strong><?= $page ?></strong> / <strong><?= $total_pages ?></strong>
            &nbsp;|&nbsp; Tổng <strong><?= $total_rows ?></strong> đơn hàng
        </p>
        <?php endif; ?>

        <?php endif; // end !$search_error ?>

    </div>
</div>

<?php include '../admin_includes/footer.php'; ?>

<script>
// ---- Pill tabs — giống customermanage.php ----
const hints = {
    fullname: 'Nhập tên hoặc một phần họ tên — VD: nguyen → hiện tất cả họ Nguyễn',
    id:       'Nhập số mã đơn hàng — VD: 1 hoặc 12',
    email:    'Nhập đúng địa chỉ email — VD: example@gmail.com',
    phone:    'Nhập đúng số điện thoại — VD: 0901234567',
    address:  'Nhập một phần địa chỉ — VD: Quận 5 hoặc TPHCM',
};
const placeholders = {
    fullname: 'Nhập tên khách hàng...',
    id:       'Nhập mã đơn hàng...',
    email:    'Nhập địa chỉ email...',
    phone:    'Nhập số điện thoại...',
    address:  'Nhập địa chỉ giao hàng...',
};
const icons = {
    fullname: 'fa-user',
    id:       'fa-hashtag',
    email:    'fa-envelope',
    phone:    'fa-phone',
    address:  'fa-map-marker-alt',
};

function selectTab(type) {
    document.querySelectorAll('.stt-btn').forEach(b => b.classList.remove('active'));
    const lbl = document.querySelector(`label[for="stt_${type}"]`);
    if (lbl) lbl.classList.add('active');

    const radio = document.getElementById(`stt_${type}`);
    if (radio) radio.checked = true;

    const inp = document.getElementById('search_value');
    inp.placeholder = placeholders[type] || 'Nhập từ khóa...';

    const hint = document.getElementById('search_hint');
    if (hint) hint.innerHTML = `<i class="fas fa-circle-info"></i> ${hints[type] || ''}`;

    const ic = document.getElementById('inputIcon');
    ic.className = `fas ${icons[type] || 'fa-search'} si-icon`;

    inp.focus();
}

document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('.stt-radio:checked');
    const type = checked ? checked.value : 'id';
    const inp = document.getElementById('search_value');
    inp.placeholder = placeholders[type] || 'Nhập từ khóa...';
    const hint = document.getElementById('search_hint');
    if (hint) hint.innerHTML = `<i class="fas fa-circle-info"></i> ${hints[type] || ''}`;
    const ic = document.getElementById('inputIcon');
    ic.className = `fas ${icons[type] || 'fa-search'} si-icon`;
});

// ---- Dropdown trạng thái ----
function toggleStatus(e) {
    e.stopPropagation();
    const menu  = document.getElementById('statusMenu');
    const caret = document.getElementById('statusCaret');
    const open  = menu.classList.toggle('open');
    caret.style.transform = open ? 'rotate(180deg)' : '';
}
document.addEventListener('click', () => {
    const menu  = document.getElementById('statusMenu');
    const caret = document.getElementById('statusCaret');
    if (menu)  menu.classList.remove('open');
    if (caret) caret.style.transform = '';
});
</script>

</body>
</html>