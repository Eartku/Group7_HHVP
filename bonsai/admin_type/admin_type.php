<?php
ob_start();
session_start();
require "../config/db.php";

$success_msg = '';
$error_msg   = '';

// ===== XỬ LÝ THÊM LOẠI MỚI =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_add'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    if ($name === '') {
        $error_msg = 'Tên loại không được để trống.';
    } else {
        // Kiểm tra trùng tên
        $check = mysqli_query($conn, "SELECT id FROM categories WHERE name='$name' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error_msg = 'Tên loại "' . htmlspecialchars($name) . '" đã tồn tại.';
        } else {
            if (mysqli_query($conn, "INSERT INTO categories (name, `status`) VALUES ('$name', 'active')")) {
                $success_msg = 'Đã thêm loại "' . htmlspecialchars($name) . '" thành công.';
            } else {
                $error_msg = 'Lỗi: ' . mysqli_error($conn);
            }
        }
    }
}

// ===== XỬ LÝ ĐỔI TRẠNG THÁI (ẩn/hiện) =====
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $tid = intval($_GET['toggle']);
    $cur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `status` FROM categories WHERE id=$tid"));
    if ($cur) {
        $cur_st     = (isset($cur['status']) && $cur['status'] !== '') ? $cur['status'] : 'active';
        $new_status = ($cur_st === 'active') ? 'inactive' : 'active';
        mysqli_query($conn, "UPDATE categories SET `status`='$new_status' WHERE id=$tid");
        header("Location: " . $_SERVER['PHP_SELF'] . "?toggled=1");
        exit();
    }
}

// ===== XỬ LÝ SỬA TÊN LOẠI =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_edit'])) {
    $eid  = intval($_POST['edit_id'] ?? 0);
    $ename = mysqli_real_escape_string($conn, trim($_POST['edit_name'] ?? ''));
    if ($eid <= 0 || $ename === '') {
        $error_msg = 'Dữ liệu không hợp lệ.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM categories WHERE name='$ename' AND id != $eid LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error_msg = 'Tên loại "' . htmlspecialchars($ename) . '" đã tồn tại.';
        } else {
            if (mysqli_query($conn, "UPDATE categories SET name='$ename' WHERE id=$eid")) {
                $success_msg = 'Đã cập nhật tên loại thành công.';
            } else {
                $error_msg = 'Lỗi: ' . mysqli_error($conn);
            }
        }
    }
}

if (isset($_GET['updated'])) $success_msg = 'Cập nhật thành công.';
if (isset($_GET['toggled'])) $success_msg = 'Đã thay đổi trạng thái.';

// ===== THÊM CỘT status NẾU CHƯA CÓ =====
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM `categories` LIKE 'status'");
if (!$col_check || mysqli_num_rows($col_check) === 0) {
    mysqli_query($conn, "ALTER TABLE `categories` ADD COLUMN `status` ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    mysqli_query($conn, "UPDATE `categories` SET `status` = 'active'");
}

// ===== ĐẾM SẢN PHẨM THEO LOẠI =====
$prod_counts = [];
$pc_res = mysqli_query($conn, "SELECT `category_id`, COUNT(`id`) AS cnt FROM `products` GROUP BY `category_id`");
if ($pc_res) {
    while ($pc = mysqli_fetch_assoc($pc_res)) {
        if ($pc['category_id'] !== null) {
            $prod_counts[(int)$pc['category_id']] = (int)$pc['cnt'];
        }
    }
}

// ===== LẤY DANH SÁCH LOẠI =====
$categories  = [];
$cats_res    = mysqli_query($conn, "SELECT `id`, `name`, `status` FROM `categories` ORDER BY `id` ASC");
if ($cats_res && mysqli_num_rows($cats_res) > 0) {
    while ($r = mysqli_fetch_assoc($cats_res)) {
        $categories[] = [
            'id'            => (int)$r['id'],
            'name'          => (string)$r['name'],
            'status'        => (!empty($r['status'])) ? (string)$r['status'] : 'active',
            'product_count' => $prod_counts[(int)$r['id']] ?? 0,
        ];
    }
}

$total_active   = 0;
$total_inactive = 0;
$total_products = 0;
foreach ($categories as $c) {
    if ($c['status'] === 'active') $total_active++;
    else                           $total_inactive++;
    $total_products += $c['product_count'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include "../admin_includes/loader.php"; ?>
    <style>
        /* ===== ROOT ===== */
        :root {
            --green-50:  #f0fdf4;
            --green-100: #dcfce7;
            --green-200: #bbf7d0;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --green-800: #166534;
            --gray-50:  #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }

        /* ===== STAT CARDS ===== */
        .stat-row { display: flex; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
        .stat-card {
            flex: 1; min-width: 160px;
            background: #fff; border: 1px solid var(--gray-200);
            border-radius: 12px; padding: 18px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            display: flex; align-items: center; gap: 14px;
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .stat-icon.green  { background: var(--green-100); color: var(--green-700); }
        .stat-icon.orange { background: #fff7ed; color: #c2410c; }
        .stat-icon.blue   { background: #eff6ff; color: #1d4ed8; }
        .stat-label { font-size: .75rem; color: var(--gray-400); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--gray-800); line-height: 1.2; }

        /* ===== LAYOUT ===== */
        .ty-layout { display: grid; grid-template-columns: 320px 1fr; gap: 20px; align-items: start; }
        @media(max-width:900px){ .ty-layout { grid-template-columns: 1fr; } }

        /* ===== CARD ===== */
        .ty-card {
            background: #fff; border: 1px solid var(--gray-200);
            border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .ty-card-head {
            padding: 16px 20px; border-bottom: 1px solid var(--gray-100);
            display: flex; align-items: center; gap: 10px;
        }
        .ty-card-icon {
            width: 30px; height: 30px; border-radius: 7px;
            background: var(--green-100); color: var(--green-700);
            display: flex; align-items: center; justify-content: center;
            font-size: .82rem; flex-shrink: 0;
        }
        .ty-card-head h5 {
            font-size: .8rem; font-weight: 800; color: var(--gray-700);
            text-transform: uppercase; letter-spacing: .07em; margin: 0;
        }
        .ty-card-body { padding: 20px; }

        /* ===== ADD FORM ===== */
        .add-input-wrap { position: relative; margin-bottom: 12px; }
        .add-input {
            width: 100%; height: 42px;
            border: 1.5px solid var(--gray-200); border-radius: 8px;
            padding: 0 14px; font-size: .9rem; color: var(--gray-700);
            outline: none; transition: border-color .2s, box-shadow .2s;
            background: var(--gray-50);
        }
        .add-input:focus {
            border-color: var(--green-500); background: #fff;
            box-shadow: 0 0 0 3px rgba(34,197,94,.12);
        }
        .add-input::placeholder { color: var(--gray-400); }
        .btn-add {
            width: 100%; height: 42px; border: none; border-radius: 8px;
            background: linear-gradient(135deg, var(--green-600), var(--green-500));
            color: #fff; font-size: .9rem; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            gap: 8px; transition: all .2s; box-shadow: 0 3px 10px rgba(22,163,74,.25);
        }
        .btn-add:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(22,163,74,.35); }

        /* ===== FILTER TABS ===== */
        .filter-tabs { display: flex; gap: 6px; padding: 16px 20px 0; }
        .ftab {
            padding: 5px 14px; border-radius: 50px; font-size: .78rem;
            font-weight: 700; cursor: pointer; border: 1.5px solid var(--gray-200);
            background: var(--gray-50); color: var(--gray-500);
            transition: all .18s; text-decoration: none;
        }
        .ftab.active, .ftab:hover { background: var(--green-600); color: #fff; border-color: var(--green-600); }
        .ftab .cnt {
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.25); border-radius: 50px;
            width: 18px; height: 18px; font-size: .68rem; margin-left: 4px;
        }
        .ftab:not(.active) .cnt { background: var(--gray-200); color: var(--gray-600); }

        /* ===== SEARCH BAR ===== */
        .search-wrap { padding: 12px 20px; border-bottom: 1px solid var(--gray-100); }
        .search-input {
            width: 100%; height: 36px; border: 1.5px solid var(--gray-200);
            border-radius: 8px; padding: 0 14px 0 36px;
            font-size: .85rem; color: var(--gray-700); outline: none;
            background: var(--gray-50); transition: border-color .2s;
        }
        .search-input:focus { border-color: var(--green-500); background: #fff; }
        .search-wrap { position: relative; }
        .search-icon {
            position: absolute; left: 32px; top: 50%;
            transform: translateY(-50%); color: var(--gray-400); font-size: .8rem;
            pointer-events: none;
        }

        /* ===== TABLE ===== */
        .ty-table { width: 100%; border-collapse: collapse; }
        .ty-table thead tr { background: var(--gray-50); }
        .ty-table thead th {
            padding: 10px 16px; text-align: left;
            font-size: .72rem; font-weight: 800; color: var(--gray-500);
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 2px solid var(--gray-200);
        }
        .ty-table thead th.ta-c { text-align: center; }
        .ty-table tbody tr {
            border-bottom: 1px solid var(--gray-100);
            transition: background .15s;
        }
        .ty-table tbody tr:last-child { border-bottom: none; }
        .ty-table tbody tr:hover { background: var(--gray-50); }
        .ty-table tbody td { padding: 12px 16px; font-size: .88rem; vertical-align: middle; }
        .ty-table tbody td.ta-c { text-align: center; }

        /* row ẩn */
        .ty-table tbody tr.row-inactive { opacity: .55; }

        /* ===== INLINE EDIT ===== */
        .name-display { display: flex; align-items: center; gap: 8px; }
        .name-text { font-weight: 600; color: var(--gray-800); }
        .btn-edit-name {
            background: none; border: none; cursor: pointer;
            color: var(--gray-400); font-size: .8rem; padding: 2px 5px;
            border-radius: 4px; transition: color .15s, background .15s;
        }
        .btn-edit-name:hover { color: var(--green-600); background: var(--green-50); }
        .edit-name-form { display: none; gap: 6px; align-items: center; }
        .edit-name-form.show { display: flex; }
        .edit-input {
            flex: 1; height: 32px; border: 1.5px solid var(--green-400);
            border-radius: 6px; padding: 0 10px; font-size: .85rem;
            outline: none; background: #fff;
            box-shadow: 0 0 0 3px rgba(34,197,94,.1);
        }
        .btn-save-inline {
            height: 32px; padding: 0 12px; border: none; border-radius: 6px;
            background: var(--green-600); color: #fff; font-size: .78rem;
            font-weight: 700; cursor: pointer; white-space: nowrap;
        }
        .btn-cancel-inline {
            height: 32px; padding: 0 10px; border: 1px solid var(--gray-300);
            border-radius: 6px; background: #fff; font-size: .78rem;
            color: var(--gray-500); cursor: pointer;
        }

        /* ===== PRODUCT COUNT BADGE ===== */
        .prod-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 50px; font-size: .78rem; font-weight: 700;
        }
        .prod-badge.has-prod { background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-200); }
        .prod-badge.no-prod  { background: var(--gray-100); color: var(--gray-500); border: 1px solid var(--gray-200); }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 50px; font-size: .75rem; font-weight: 700;
        }
        .status-badge.active   { background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-200); }
        .status-badge.inactive { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-dot.active   { background: var(--green-600); }
        .status-dot.inactive { background: #f59e0b; }

        /* ===== TOGGLE BUTTON ===== */
        .btn-toggle {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 6px; font-size: .78rem; font-weight: 700;
            border: none; cursor: pointer; transition: all .18s; text-decoration: none;
        }
        .btn-toggle.hide  { background: #fef3c7; color: #92400e; }
        .btn-toggle.hide:hover  { background: #fde68a; }
        .btn-toggle.show  { background: var(--green-100); color: var(--green-800); }
        .btn-toggle.show:hover  { background: var(--green-200); }

        /* ===== EMPTY STATE ===== */
        .ty-empty { text-align: center; padding: 40px 20px; color: var(--gray-400); font-size: .88rem; }
        .ty-empty i { font-size: 2.2rem; display: block; margin-bottom: 10px; }

        /* ===== ALERT ===== */
        .ty-alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: 8px; font-size: .88rem;
            font-weight: 600; margin-bottom: 20px;
        }
        .ty-alert.success { background: var(--green-50); color: var(--green-800); border: 1px solid var(--green-200); }
        .ty-alert.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* ===== CONFIRM MODAL ===== */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.45); z-index: 9999;
            align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: #fff; border-radius: 14px; padding: 28px 28px 24px;
            width: 360px; max-width: 90vw; box-shadow: 0 20px 60px rgba(0,0,0,.2);
            text-align: center;
        }
        .modal-icon { font-size: 2.2rem; margin-bottom: 12px; }
        .modal-title { font-size: 1rem; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }
        .modal-desc  { font-size: .85rem; color: var(--gray-500); margin-bottom: 20px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 10px; justify-content: center; }
        .modal-btn {
            padding: 9px 22px; border-radius: 8px; font-size: .88rem;
            font-weight: 700; cursor: pointer; border: none; transition: all .18s;
        }
        .modal-btn.confirm-hide { background: #fef3c7; color: #92400e; }
        .modal-btn.confirm-hide:hover { background: #fde68a; }
        .modal-btn.confirm-show { background: var(--green-100); color: var(--green-800); }
        .modal-btn.confirm-show:hover { background: var(--green-200); }
        .modal-btn.cancel { background: var(--gray-100); color: var(--gray-600); }
        .modal-btn.cancel:hover { background: var(--gray-200); }
    </style>
</head>
<body>
<?php include "../admin_includes/header.php"; ?>

<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Quản lý loại sản phẩm</h1>
        <span style="color:aliceblue">Thêm, chỉnh sửa và ẩn/hiện các loại sản phẩm</span>
    </div>
</div>

<div class="container py-5">

    <?php if ($success_msg): ?>
    <div class="ty-alert success"><i class="fas fa-check-circle"></i><?= $success_msg ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
    <div class="ty-alert error"><i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <!-- ===== STAT CARDS ===== -->
    <div class="stat-row">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-tags"></i></div>
            <div>
                <div class="stat-label">Tổng loại</div>
                <div class="stat-value"><?= count($categories) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-eye"></i></div>
            <div>
                <div class="stat-label">Đang bán</div>
                <div class="stat-value"><?= $total_active ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-eye-slash"></i></div>
            <div>
                <div class="stat-label">Đã ẩn</div>
                <div class="stat-value"><?= $total_inactive ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-box"></i></div>
            <div>
                <div class="stat-label">Tổng sản phẩm</div>
                <div class="stat-value"><?= $total_products ?></div>
            </div>
        </div>
    </div>

    <!-- ===== MAIN LAYOUT ===== -->
    <div class="ty-layout">

        <!-- CỘT TRÁI: Form thêm mới -->
        <div>
            <div class="ty-card">
                <div class="ty-card-head">
                    <div class="ty-card-icon"><i class="fas fa-plus"></i></div>
                    <h5>Thêm loại mới</h5>
                </div>
                <div class="ty-card-body">
                    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                        <input type="hidden" name="do_add" value="1">
                        <div class="add-input-wrap">
                            <input type="text" name="name" class="add-input"
                                placeholder="Nhập tên loại sản phẩm..."
                                autocomplete="off" required>
                        </div>
                        <p style="font-size:.8rem;color:var(--gray-400);margin-bottom:14px;line-height:1.5">
                            <i class="fas fa-info-circle me-1"></i>
                            Loại mới sẽ được thêm với trạng thái <strong>Đang bán</strong>. Bạn có thể ẩn đi bất cứ lúc nào.
                        </p>
                        <button type="submit" class="btn-add">
                            <i class="fas fa-plus-circle"></i> Thêm loại sản phẩm
                        </button>
                    </form>
                </div>
            </div>

            <!-- Ghi chú -->
            <div style="margin-top:16px;padding:16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;font-size:.82rem;color:#92400e;line-height:1.6">
                <strong><i class="fas fa-lightbulb me-1"></i>Lưu ý:</strong><br>
                • Ẩn loại sản phẩm sẽ <strong>không xóa</strong> sản phẩm trong loại đó.<br>
                • Loại bị ẩn sẽ không hiển thị cho khách hàng.<br>
                • Bấm vào tên để đổi tên loại trực tiếp.
            </div>
        </div>

        <!-- CỘT PHẢI: Bảng danh sách -->
        <div class="ty-card">
            <!-- Filter tabs -->
            <div class="filter-tabs">
                <a href="?filter=all"      class="ftab <?= (!isset($_GET['filter']) || $_GET['filter']==='all')      ? 'active' : '' ?>">Tất cả <span class="cnt"><?= count($categories) ?></span></a>
                <a href="?filter=active"   class="ftab <?= (isset($_GET['filter']) && $_GET['filter']==='active')   ? 'active' : '' ?>">Đang bán <span class="cnt"><?= $total_active ?></span></a>
                <a href="?filter=inactive" class="ftab <?= (isset($_GET['filter']) && $_GET['filter']==='inactive') ? 'active' : '' ?>">Đã ẩn <span class="cnt"><?= $total_inactive ?></span></a>
            </div>

            <!-- Search -->
            <div class="search-wrap" style="padding:12px 20px;border-bottom:1px solid var(--gray-100);position:relative">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input"
                    placeholder="Tìm kiếm theo tên loại...">
            </div>

            <!-- Table -->
            <div style="overflow-x:auto">
            <table class="ty-table" id="typeTable">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Tên loại</th>
                        <th class="ta-c">Số sản phẩm</th>
                        <th class="ta-c">Trạng thái</th>
                        <th class="ta-c">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $filter = $_GET['filter'] ?? 'all';
                $shown  = 0;
                foreach ($categories as $i => $cat):
                    $cat_status = isset($cat['status']) ? $cat['status'] : 'active';
                    $cat_count  = isset($cat['product_count']) ? (int)$cat['product_count'] : 0;
                    if ($filter === 'active'   && $cat_status !== 'active') continue;
                    if ($filter === 'inactive' && $cat_status === 'active') continue;
                    $shown++;
                    $is_active  = ($cat_status === 'active');
                    $prod_count = $cat_count;
                ?>
                <tr class="<?= $is_active ? '' : 'row-inactive' ?>"
                    data-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>">
                    <td style="color:var(--gray-400);font-size:.8rem;font-weight:700"><?= $cat['id'] ?></td>
                    <td>
                        <!-- Hiển thị tên + nút sửa -->
                        <div class="name-display" id="display-<?= $cat['id'] ?>">
                            <span class="name-text"><?= htmlspecialchars($cat['name']) ?></span>
                            <button type="button" class="btn-edit-name"
                                onclick="showEditForm(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')"
                                title="Đổi tên">
                                <i class="fas fa-pen"></i>
                            </button>
                        </div>
                        <!-- Form sửa inline -->
                        <form method="POST" class="edit-name-form" id="editform-<?= $cat['id'] ?>">
                            <input type="hidden" name="do_edit" value="1">
                            <input type="hidden" name="edit_id" value="<?= $cat['id'] ?>">
                            <input type="text" name="edit_name" class="edit-input"
                                id="editinput-<?= $cat['id'] ?>"
                                value="<?= htmlspecialchars($cat['name']) ?>">
                            <button type="submit" class="btn-save-inline">
                                <i class="fas fa-check"></i> Lưu
                            </button>
                            <button type="button" class="btn-cancel-inline"
                                onclick="hideEditForm(<?= $cat['id'] ?>)">Hủy</button>
                        </form>
                    </td>
                    <td class="ta-c">
                        <?php if ($prod_count > 0): ?>
                        <span class="prod-badge has-prod">
                            <i class="fas fa-box" style="font-size:.7rem"></i>
                            <?= $prod_count ?> sản phẩm
                        </span>
                        <?php else: ?>
                        <span class="prod-badge no-prod">
                            <i class="fas fa-inbox" style="font-size:.7rem"></i>
                            Chưa có
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="ta-c">
                        <span class="status-badge <?= $is_active ? 'active' : 'inactive' ?>">
                            <span class="status-dot <?= $is_active ? 'active' : 'inactive' ?>"></span>
                            <?= $is_active ? 'Đang bán' : 'Đã ẩn' ?>
                        </span>
                    </td>
                    <td class="ta-c">
                        <button type="button"
                            class="btn-toggle <?= $is_active ? 'hide' : 'show' ?>"
                            onclick="confirmToggle(<?= $cat['id'] ?>, <?= $is_active ? 'true' : 'false' ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>')">
                            <?php if ($is_active): ?>
                                <i class="fas fa-eye-slash"></i> Ẩn
                            <?php else: ?>
                                <i class="fas fa-eye"></i> Hiện
                            <?php endif; ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php if ($shown === 0): ?>
                <tr>
                    <td colspan="5">
                        <div class="ty-empty">
                            <i class="fas fa-folder-open"></i>
                            Không có loại sản phẩm nào<?= $filter !== 'all' ? ' phù hợp' : '' ?>.
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div><!-- /ty-layout -->
</div>

<!-- ===== CONFIRM MODAL ===== -->
<div class="modal-overlay" id="toggleModal">
    <div class="modal-box">
        <div class="modal-icon" id="modalIcon"></div>
        <div class="modal-title" id="modalTitle"></div>
        <div class="modal-desc"  id="modalDesc"></div>
        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeModal()">Hủy bỏ</button>
            <a href="#" class="modal-btn" id="modalConfirmBtn"></a>
        </div>
    </div>
</div>

<?php include '../admin_includes/footer.php'; ?>

<script>
// ===== INLINE EDIT =====
function showEditForm(id, currentName) {
    document.getElementById('display-' + id).style.display = 'none';
    const form = document.getElementById('editform-' + id);
    form.classList.add('show');
    const input = document.getElementById('editinput-' + id);
    input.focus();
    input.select();
}
function hideEditForm(id) {
    document.getElementById('display-' + id).style.display = 'flex';
    document.getElementById('editform-' + id).classList.remove('show');
}

// ===== CONFIRM MODAL =====
function confirmToggle(id, isActive, name) {
    const modal   = document.getElementById('toggleModal');
    const icon    = document.getElementById('modalIcon');
    const title   = document.getElementById('modalTitle');
    const desc    = document.getElementById('modalDesc');
    const btn     = document.getElementById('modalConfirmBtn');

    if (isActive) {
        icon.textContent  = '🙈';
        title.textContent = 'Ẩn loại "' + name + '"?';
        desc.textContent  = 'Loại này sẽ không còn hiển thị cho khách hàng. Sản phẩm bên trong vẫn được giữ nguyên.';
        btn.textContent   = 'Xác nhận ẩn';
        btn.className     = 'modal-btn confirm-hide';
    } else {
        icon.textContent  = '👁️';
        title.textContent = 'Hiện lại loại "' + name + '"?';
        desc.textContent  = 'Loại này sẽ được hiển thị trở lại cho khách hàng.';
        btn.textContent   = 'Xác nhận hiện';
        btn.className     = 'modal-btn confirm-show';
    }
    btn.href = 'admin_type.php?toggle=' + id;
    modal.classList.add('show');
}
function closeModal() {
    document.getElementById('toggleModal').classList.remove('show');
}
document.getElementById('toggleModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// ===== SEARCH =====
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('#typeTable tbody tr[data-name]').forEach(function(row) {
        row.style.display = row.dataset.name.includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>