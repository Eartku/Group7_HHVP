<?php
session_start();
require "../config/db.php";

/* xử lý khóa / mở */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == "lock") {
        $sql = "UPDATE users SET status='inactive' WHERE id=$id";
    } elseif ($_GET['action'] == "unlock") {
        $sql = "UPDATE users SET status='active' WHERE id=$id";
    }
    mysqli_query($conn, $sql);
    header("Location: customermanage.php");
    exit();
}

/* ---------- XỬ LÝ TÌM KIẾM ---------- */
$search_value  = isset($_GET['search_value'])  ? trim($_GET['search_value'])  : '';
$search_type   = isset($_GET['search_type'])   ? trim($_GET['search_type'])   : 'name';
$search_error  = '';
$search_done   = false;
$customers_result = null;

/* ---------- PHÂN TRANG ---------- */
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ---------- BUILD WHERE ---------- */
$where_base = "role='customer'";

if (isset($_GET['do_search']) && $search_value !== '') {
    $search_done = true;
    $escaped = mysqli_real_escape_string($conn, $search_value);

    if ($search_type === 'id') {
        if (!ctype_digit($search_value)) {
            $search_error = 'ID không hợp lệ. Vui lòng nhập số nguyên.';
        } else {
            $where_search = "$where_base AND id = $escaped";
        }
    } elseif ($search_type === 'email') {
        $where_search = "$where_base AND email = '$escaped'";
    } elseif ($search_type === 'phone') {
        $where_search = "$where_base AND phone = '$escaped'";
    } else {
        $where_search = "$where_base AND fullname LIKE '%$escaped%'";
    }

    if (!$search_error && isset($where_search)) {
        /* đếm tổng */
        $count_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE $where_search");
        $total_rows = mysqli_fetch_assoc($count_res)['total'];

        if ($total_rows === 0) {
            if ($search_type === 'id')
                $search_error = "ID #C" . str_pad($escaped, 4, "0", STR_PAD_LEFT) . " không tồn tại trong hệ thống.";
            elseif ($search_type === 'email')
                $search_error = "Email \"$search_value\" không tồn tại trong hệ thống.";
            elseif ($search_type === 'phone')
                $search_error = "Số điện thoại \"$search_value\" không tồn tại trong hệ thống.";
            else
                $search_error = "Không tìm thấy khách hàng nào có tên chứa \"$search_value\".";
        } else {
            $total_pages   = ceil($total_rows / $limit);
            $customers_result = mysqli_query($conn,
                "SELECT * FROM users WHERE $where_search LIMIT $limit OFFSET $offset");
        }
    }
}

/* lấy toàn bộ khi không tìm kiếm */
if (!$search_done) {
    $count_res   = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE $where_base");
    $total_rows  = mysqli_fetch_assoc($count_res)['total'];
    $total_pages = ceil($total_rows / $limit);

    $customers_result = mysqli_query($conn,
        "SELECT * FROM users WHERE $where_base LIMIT $limit OFFSET $offset");
}

/* helper: giữ query params khi chuyển trang */
function page_url($p) {
    $params = $_GET;
    $params['page'] = $p;
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include "../admin_includes/loader.php"; ?>
    <!-- <style>
        /* ===== SEARCH BAR ===== */
        
    </style> -->
</head>

<body>

<?php include "../admin_includes/header.php"; ?>

<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Quản lý khách hàng</h1>
        <span style="color: aliceblue;"></span>
        <br>
        <a href="add_customer.php"
           style="background-color:#28a745;color:white;padding:8px 20px;
                  border-radius:25px;text-decoration:none;font-weight:bold;
                  display:inline-block;">
            + Thêm tài khoản
        </a>
    </div>
</div>

<div class="container py-5">
    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

        <!-- ===== THANH TÌM KIẾM ===== -->
        <div class="search-wrapper">
            <form method="GET" action="customermanage.php" id="searchForm" autocomplete="off">

                <div class="search-row">
                    <!-- PILL TABS -->
                    <div class="search-type-tabs" id="searchTypeTabs">

                        <input type="radio" class="stt-radio" name="search_type" id="stt_name"
                               value="name" <?= ($search_type==='name'  ? 'checked' : '') ?>>
                        <label class="stt-btn <?= ($search_type==='name'  ? 'active' : '') ?>"
                               for="stt_name" onclick="selectTab('name')">
                            <i class="fas fa-user"></i> Họ tên
                        </label>

                        <input type="radio" class="stt-radio" name="search_type" id="stt_id"
                               value="id" <?= ($search_type==='id'    ? 'checked' : '') ?>>
                        <label class="stt-btn <?= ($search_type==='id'    ? 'active' : '') ?>"
                               for="stt_id" onclick="selectTab('id')">
                            <i class="fas fa-hashtag"></i> Mã KH
                        </label>

                        <input type="radio" class="stt-radio" name="search_type" id="stt_email"
                               value="email" <?= ($search_type==='email' ? 'checked' : '') ?>>
                        <label class="stt-btn <?= ($search_type==='email' ? 'active' : '') ?>"
                               for="stt_email" onclick="selectTab('email')">
                            <i class="fas fa-envelope"></i> Email
                        </label>

                        <input type="radio" class="stt-radio" name="search_type" id="stt_phone"
                               value="phone" <?= ($search_type==='phone' ? 'checked' : '') ?>>
                        <label class="stt-btn <?= ($search_type==='phone' ? 'active' : '') ?>"
                               for="stt_phone" onclick="selectTab('phone')">
                            <i class="fas fa-phone"></i> Số ĐT
                        </label>

                    </div>

                    <div class="search-divider d-none d-sm-block"></div>

                    <!-- Ô nhập -->
                    <div class="search-input-wrap">
                        <i class="fas fa-search search-icon" id="inputIcon"></i>
                        <input type="text"
                               name="search_value"
                               id="search_value"
                               class="form-control"
                               placeholder="Nhập tên khách hàng..."
                               value="<?= htmlspecialchars($search_value) ?>">
                    </div>

                    <!-- Nút Lọc -->
                    <button type="submit" name="do_search" value="1" class="btn btn-success btn-search">
                        <i class="fas fa-filter"></i> Lọc
                    </button>

                    <!-- Nút Xóa lọc -->
                    <?php if ($search_done): ?>
                    <a href="customermanage.php" class="btn btn-outline-secondary btn-reset">
                        <i class="fas fa-rotate-left"></i> Xóa
                    </a>
                    <?php endif; ?>
                </div>

                <div id="search_hint"></div>
            </form>
        </div>

        <!-- ===== THÔNG BÁO LỖI ===== -->
        <?php if ($search_done && $search_error): ?>
        <div class="alert search-alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span><?= htmlspecialchars($search_error) ?></span>
        </div>
        <?php endif; ?>

        <!-- ===== BADGE KẾT QUẢ ===== -->
        <?php if ($search_done && !$search_error): ?>
        <div class="result-badge">
            <i class="fas fa-check-circle"></i>
            Tìm thấy <?= $total_rows ?> kết quả
            <?php if ($search_value): ?>
                cho "<strong><?= htmlspecialchars($search_value) ?></strong>"
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ===== BẢNG DANH SÁCH ===== -->
        <?php if (!$search_error): ?>
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mã KH</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $has_rows = false;
            if ($customers_result) {
                while ($row = mysqli_fetch_assoc($customers_result)) {
                    $has_rows = true;
                    $fullname = htmlspecialchars($row['fullname']);
                    $email    = htmlspecialchars($row['email']);
                    $phone    = htmlspecialchars($row['phone']);

                    if ($search_done && $search_type === 'name' && $search_value !== '') {
                        $kw = htmlspecialchars($search_value);
                        $fullname = preg_replace('/(' . preg_quote($kw, '/') . ')/iu',
                                    '<mark class="hl">$1</mark>', $fullname);
                    }
            ?>
            <tr>
                <td>#C<?= str_pad($row['id'], 4, "0", STR_PAD_LEFT) ?></td>
                <td><?= $fullname ?></td>
                <td><?= $email ?></td>
                <td><?= $phone ?></td>
                <td>
                    <?php
                    if ($row['status'] == "active")
                        echo '<span class="badge bg-success">Hoạt động</span>';
                    elseif ($row['status'] == "warning")
                        echo '<span class="badge bg-warning text-dark">Cảnh báo</span>';
                    elseif ($row['status'] == "inactive")
                        echo '<span class="badge bg-danger">Bị khóa</span>';
                    ?>
                </td>
                <td>
                    <a href="editcustomer.php?id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-outline-success me-1">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </a>
                    <?php if ($row['status'] == "active"): ?>
                        <a href="?action=lock&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-lock"></i> Khóa
                        </a>
                    <?php else: ?>
                        <a href="?action=unlock&id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-unlock"></i> Mở
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
                }
            }
            if (!$has_rows): ?>
            <tr>
                <td colspan="6" class="text-muted py-4">
                    <i class="fas fa-inbox fa-lg me-2"></i>Chưa có khách hàng nào.
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- ===== PHÂN TRANG ===== -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Phân trang khách hàng">
            <ul class="pagination justify-content-center flex-wrap">

                <!-- Trang đầu -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url(1) ?>" title="Trang đầu">
                        <i class="fas fa-angles-left"></i>
                    </a>
                </li>

                <!-- Trang trước -->
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url($page - 1) ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                </li>

                <!-- Các số trang -->
                <?php
                $range = 2; // hiện tối đa 5 nút (page-2 → page+2)
                $start = max(1, $page - $range);
                $end   = min($total_pages, $page + $range);

                if ($start > 1): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif;

                for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= page_url($i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($end < $total_pages): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>

                <!-- Trang sau -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url($page + 1) ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                </li>

                <!-- Trang cuối -->
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= page_url($total_pages) ?>" title="Trang cuối">
                        <i class="fas fa-angles-right"></i>
                    </a>
                </li>

            </ul>
        </nav>

        <!-- Thông tin trang -->
        <p class="page-info">
            Trang <strong><?= $page ?></strong> / <strong><?= $total_pages ?></strong>
            &nbsp;|&nbsp; Tổng <strong><?= $total_rows ?></strong> khách hàng
        </p>
        <?php endif; ?>

        <?php endif; // end !$search_error ?>

    </div>
</div>

<?php include '../admin_includes/footer.php'; ?>

<script>
const hints = {
    name:  'Nhập tên hoặc một phần họ tên — VD: nguyen → hiện tất cả họ Nguyễn',
    id:    'Nhập số ID — VD: 1 hoặc 12',
    email: 'Nhập đúng địa chỉ email — VD: example@gmail.com',
    phone: 'Nhập đúng số điện thoại — VD: 0901234567',
};
const placeholders = {
    name:  'Nhập tên khách hàng...',
    id:    'Nhập mã số ID...',
    email: 'Nhập địa chỉ email...',
    phone: 'Nhập số điện thoại...',
};
const icons = {
    name:  'fa-user',
    id:    'fa-hashtag',
    email: 'fa-envelope',
    phone: 'fa-phone',
};

function selectTab(type) {
    document.querySelectorAll('.stt-btn').forEach(b => b.classList.remove('active'));
    const lbl = document.querySelector(`label[for="stt_${type}"]`);
    if (lbl) lbl.classList.add('active');

    const radio = document.getElementById(`stt_${type}`);
    if (radio) radio.checked = true;

    const inp = document.getElementById('search_value');
    inp.placeholder = placeholders[type] || 'Nhập từ khóa...';

    document.getElementById('search_hint').innerHTML =
        `<i class="fas fa-circle-info"></i> ${hints[type] || ''}`;

    const ic = document.getElementById('inputIcon');
    ic.className = `fas ${icons[type] || 'fa-search'} search-icon`;

    inp.focus();
}

document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('.stt-radio:checked');
    const type = checked ? checked.value : 'name';
    const inp = document.getElementById('search_value');
    inp.placeholder = placeholders[type] || 'Nhập từ khóa...';
    document.getElementById('search_hint').innerHTML =
        `<i class="fas fa-circle-info"></i> ${hints[type] || ''}`;
    const ic = document.getElementById('inputIcon');
    ic.className = `fas ${icons[type] || 'fa-search'} search-icon`;
});
</script>

</body>
</html>