<?php
session_start();
require "../config/db.php";
/* kiểm tra admin */
if (!isset($_SESSION['admin'])) {
    header("Location: ../admin_login/admin_login.php");
    exit();
}

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

if (isset($_GET['do_search']) && $search_value !== '') {
    $search_done = true;
    $escaped = mysqli_real_escape_string($conn, $search_value);

    if ($search_type === 'id') {
        /* ID phải là số */
        if (!ctype_digit($search_value)) {
            $search_error = 'ID không hợp lệ. Vui lòng nhập số nguyên.';
        } else {
            $sql_search = "SELECT * FROM users WHERE role='customer' AND id = $escaped";
            $customers_result = mysqli_query($conn, $sql_search);
            if (mysqli_num_rows($customers_result) === 0) {
                $search_error = "ID #C" . str_pad($escaped, 4, "0", STR_PAD_LEFT) . " không tồn tại trong hệ thống.";
            }
        }
    } elseif ($search_type === 'email') {
        $sql_search = "SELECT * FROM users WHERE role='customer' AND email = '$escaped'";
        $customers_result = mysqli_query($conn, $sql_search);
        if (mysqli_num_rows($customers_result) === 0) {
            $search_error = "Email \"$search_value\" không tồn tại trong hệ thống.";
        }
    } elseif ($search_type === 'phone') {
        $sql_search = "SELECT * FROM users WHERE role='customer' AND phone = '$escaped'";
        $customers_result = mysqli_query($conn, $sql_search);
        if (mysqli_num_rows($customers_result) === 0) {
            $search_error = "Số điện thoại \"$search_value\" không tồn tại trong hệ thống.";
        }
    } else {
        /* Tìm theo tên — LIKE */
        $sql_search = "SELECT * FROM users WHERE role='customer' AND fullname LIKE '%$escaped%'";
        $customers_result = mysqli_query($conn, $sql_search);
        if (mysqli_num_rows($customers_result) === 0) {
            $search_error = "Không tìm thấy khách hàng nào có tên chứa \"$search_value\".";
        }
    }
}

/* lấy toàn bộ khi không tìm kiếm */
if (!$search_done) {
    $sql_customers = "SELECT * FROM users WHERE role='customer'";
    $customers_result = mysqli_query($conn, $sql_customers);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/logo.png">
    <title>BonSai | Quản Lý Khách Hàng</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/hover.css" rel="stylesheet">
    <link href="../css/page2.css" rel="stylesheet">
    <link href="../css/tiny-slider.css" rel="stylesheet">

    <style>
        /* ===== SEARCH BAR ===== */
        .search-wrapper {
            background: linear-gradient(135deg, #f0faf4 0%, #f8fdf8 100%);
            border: 1px solid #c3e6cb;
            border-radius: 16px;
            padding: 24px 270px 18px;
            margin-bottom: 28px;
            box-shadow: 0 2px 12px rgba(25,135,84,.06);
        }
        .search-wrapper .search-title {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #141615;
            margin-bottom: 34px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── PILL TABS ── */
        .search-type-tabs {
            display: flex;
            gap: 0;
            background: #e8f5ee;
            border-radius: 10px;
            padding: 4px;
            border: 1px solid #c3e6cb;
            flex-shrink: 0;
        }
        .stt-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 7px 16px;
            border: none;
            background: transparent;
            border-radius: 7px;
            font-size: 0.83rem;
            font-weight: 500;
            color: #5a7a65;
            cursor: pointer;
            transition: background .18s, color .18s, box-shadow .18s, transform .12s;
            white-space: nowrap;
            line-height: 1;
        }
        .stt-btn i {
            font-size: 0.78rem;
            opacity: .75;
        }
        .stt-btn:hover {
            background: rgba(25,135,84,.1);
            color: #146c43;
        }
        .stt-btn.active {
            background: #198754;
            color: #fff;
            box-shadow: 0 2px 8px rgba(25,135,84,.35);
            font-weight: 700;
            transform: translateY(-1px);
        }
        .stt-btn.active i {
            opacity: 1;
        }
        /* hidden radio */
        .stt-radio { display: none; }

        /* ── SEARCH ROW ── */
        .search-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .search-input-wrap {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        .search-input-wrap .form-control {
            border-radius: 10px;
            border: 1.5px solid #ced4da;
            padding-left: 42px;
            height: 44px;
            font-size: 0.93rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .search-input-wrap .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25,135,84,.12);
        }
        .search-input-wrap .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
            transition: color .2s;
        }
        .search-input-wrap .form-control:focus ~ .search-icon,
        .search-input-wrap:focus-within .search-icon {
            color: #198754;
        }
        .btn-search {
            height: 44px;
            padding: 0 22px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            box-shadow: 0 2px 8px rgba(25,135,84,.25);
            transition: transform .12s, box-shadow .12s;
        }
        .btn-search:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(25,135,84,.35);
        }
        .btn-reset {
            height: 44px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        /* divider between tabs and input */
        .search-divider {
            width: 1px;
            height: 32px;
            background: #c3e6cb;
            flex-shrink: 0;
            align-self: center;
        }

        /* placeholder hint */
        #search_hint {
            font-size: 0.76rem;
            color: #6c757d;
            margin-top: 8px;
            min-height: 16px;
            padding-left: 2px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        #search_hint i { color: #198754; font-size: 0.7rem; }

        /* ===== ALERT / RESULT BADGE ===== */
        .search-alert {
            border-radius: 10px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
        }
        .result-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #d1e7dd;
            color: #0f5132;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 14px;
        }

        /* ===== TABLE ===== */
        .table thead th {
            background: #f0faf4;
            color: #157347;
            font-weight: 700;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .table tbody tr:hover {
            background: #f8fdf8;
        }
        .table td {
            font-size: 0.91rem;
        }

        /* highlight matched text */
        mark.hl {
            background: #fff3cd;
            padding: 0 2px;
            border-radius: 3px;
        }
    </style>
</head>

<body>

<!-- Navbar -->
<?php include "../admin_includes/header.php"; ?>

<div class="container py-5">

    <div class="text-center">
        <h2 class="fw-bold text-success" style="padding:30px">
            Quản lý Khách Hàng
        </h2>
    </div>

    <div class="p-4 p-lg-5 border bg-white rounded-3 shadow-sm">

        <!-- ===== THANH TÌM KIẾM ===== -->
        <div class="search-wrapper">
            <div class="search-title"><i class="fas fa-filter me-1"></i> </div>

            <form method="GET" action="customermanage.php" id="searchForm" autocomplete="off">

                <div class="search-row">

                    <!-- ── PILL TABS ── -->
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

                    <!-- divider -->
                    <div class="search-divider d-none d-sm-block"></div>

                    <!-- ── Ô nhập ── -->
                    <div class="search-input-wrap">
                        <i class="fas fa-search search-icon" id="inputIcon"></i>
                        <input type="text"
                               name="search_value"
                               id="search_value"
                               class="form-control"
                               placeholder="Nhập tên khách hàng..."
                               value="<?= htmlspecialchars($search_value) ?>">
                    </div>

                    <!-- ── Nút Lọc ── -->
                    <button type="submit" name="do_search" value="1" class="btn btn-success btn-search">
                        <i class="fas fa-filter"></i> Lọc
                    </button>

                    <!-- ── Nút Xóa lọc ── -->
                    <?php if ($search_done): ?>
                    <a href="customermanage.php" class="btn btn-outline-secondary btn-reset">
                        <i class="fas fa-rotate-left"></i> Xóa
                    </a>
                    <?php endif; ?>

                </div>

                <!-- Gợi ý nhập -->
                <div id="search_hint"></div>

            </form>
        </div>
        <!-- /THANH TÌM KIẾM -->

        <!-- ===== THÔNG BÁO LỖI ===== -->
        <?php if ($search_done && $search_error): ?>
        <div class="alert search-alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
            <span><?= htmlspecialchars($search_error) ?></span>
        </div>
        <?php endif; ?>

        <!-- ===== BADGE KẾT QUẢ ===== -->
        <?php
        $row_count = ($customers_result && !$search_error) ? mysqli_num_rows($customers_result) : 0;
        if ($search_done && !$search_error):
        ?>
        <div class="result-badge">
            <i class="fas fa-check-circle"></i>
            Tìm thấy <?= $row_count ?> kết quả
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

                    /* highlight khi tìm theo tên */
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
                    if ($row['status'] == "active") {
                        echo '<span class="badge bg-success">Hoạt động</span>';
                    } elseif ($row['status'] == "warning") {
                        echo '<span class="badge bg-warning text-dark">Cảnh báo</span>';
                    } elseif ($row['status'] == "inactive") {
                        echo '<span class="badge bg-danger">Bị khóa</span>';
                    }
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
            if (!$has_rows && $search_done) {
                /* đã hiển thị thông báo lỗi ở trên, không cần row riêng */
            } elseif (!$has_rows) {
            ?>
            <tr>
                <td colspan="6" class="text-muted py-4">
                    <i class="fas fa-inbox fa-lg me-2"></i>Chưa có khách hàng nào.
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php endif; ?>

    </div>
</div>

<!-- Footer -->
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
    /* active class */
    document.querySelectorAll('.stt-btn').forEach(b => b.classList.remove('active'));
    const lbl = document.querySelector(`label[for="stt_${type}"]`);
    if (lbl) lbl.classList.add('active');

    /* check the radio */
    const radio = document.getElementById(`stt_${type}`);
    if (radio) radio.checked = true;

    /* update placeholder & hint */
    const inp = document.getElementById('search_value');
    inp.placeholder = placeholders[type] || 'Nhập từ khóa...';

    const hint = document.getElementById('search_hint');
    hint.innerHTML = `<i class="fas fa-circle-info"></i> ${hints[type] || ''}`;

    /* update icon */
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