<?php
require_once "../config/db.php";

/* ======================
   0. KIỂM TRA ID
====================== */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Thiếu ID phiếu nhập");

/* ======================
   1. LẤY DỮ LIỆU (KHÔNG DÙNG get_result)
====================== */
$stmt = $conn->prepare("
    SELECT ir.id, ir.import_date, ir.product_id, ir.size, 
           ir.quantity, ir.import_price, ir.total_value, ir.status,
           p.name
    FROM import_receipts ir
    LEFT JOIN products p ON ir.product_id = p.id
    WHERE ir.id = ?
");

if (!$stmt) {
    die("SQL lỗi: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$stmt->bind_result(
    $id_db,
    $import_date,
    $product_id,
    $size,
    $quantity,
    $import_price,
    $total_value,
    $status,
    $product_name
);

if (!$stmt->fetch()) {
    die("Không tìm thấy phiếu nhập");
}

$stmt->close();

$can_edit = ($status === 'pending');

/* ======================
   2. UPDATE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_btn'])) {

    if (!$can_edit) {
        die("Phiếu này không được phép chỉnh sửa");
    }

    $new_size  = $_POST['size'] ?? '';
    $new_price = (float)($_POST['import_price'] ?? 0);
    $new_qty   = (int)($_POST['quantity'] ?? 0);
    $new_total = $new_price * $new_qty;

    $update_stmt = $conn->prepare("
        UPDATE import_receipts 
        SET size = ?, import_price = ?, quantity = ?, total_value = ?
        WHERE id = ? AND status = 'pending'
    ");

    if (!$update_stmt) {
        die("Lỗi update: " . $conn->error);
    }

    $update_stmt->bind_param("sdidi", $new_size, $new_price, $new_qty, $new_total, $id);

    if ($update_stmt->execute()) {
        header("Location: edit_import.php?id=$id&success=1");
        exit;
    } else {
        die("Update thất bại: " . $update_stmt->error);
    }
}

include "../admin_includes/loader.php";
include "../admin_includes/header.php";
?>

        <div class="hero">
        <div class="center-row text-center">
                <h1 class="glow">Chỉnh sửa phiếu nhập kho</h1>
        </div>
        </div>
<div class="container mt-4">
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Cập nhật thành công</div>
    <?php endif; ?>

    <form method="POST">
        <div class="card p-4 mb-3 <?= !$can_edit ? 'bg-light' : '' ?>">

            <h5>Phiếu #<?= $id_db ?></h5>
            <hr>

            <label class="fw-bold">Sản phẩm</label>
            <input class="form-control mb-2 bg-light"
                   value="<?= htmlspecialchars($product_name ?? 'Không có') ?>" readonly>

            <label class="fw-bold">Size</label>
            <input name="size" class="form-control mb-2"
                   value="<?= htmlspecialchars($size) ?>"
                   <?= !$can_edit ? 'readonly' : '' ?>>

            <label class="fw-bold">Giá nhập</label>
            <input name="import_price" type="number" class="form-control mb-2"
                   value="<?= $import_price ?>"
                   <?= !$can_edit ? 'readonly' : '' ?>>

            <label class="fw-bold">Số lượng</label>
            <input name="quantity" type="number" class="form-control mb-2"
                   value="<?= $quantity ?>"
                   <?= !$can_edit ? 'readonly' : '' ?>>

            <label class="fw-bold">Tổng tiền</label>
            <input class="form-control mb-2 bg-light"
                   value="<?= number_format($total_value) ?> VNĐ" readonly>

            <label class="fw-bold">Trạng thái</label>
            <div class="mb-2">
                <?php if($status=="pending"): ?>
                    <span class="badge bg-warning text-dark">Đang chờ</span>
                <?php elseif($status=="completed"): ?>
                    <span class="badge bg-success">Đã hoàn thành</span>
                <?php else: ?>
                    <span class="badge bg-danger">Đã hủy</span>
                <?php endif; ?>
            </div>

        </div>

        <div class="mt-3">
            <a href="adminipd.php" class="btn btn-secondary">Quay lại</a>

            <?php if($can_edit): ?>
                <button type="submit" name="update_btn" class="btn btn-primary">
                    Lưu thay đổi
                </button>

                <a href="confirm_import.php?id=<?= $id ?>" class="btn btn-success"
                   onclick="return confirm('Xác nhận nhập kho?')">
                    Xác nhận
                </a>

                <a href="cancel_import.php?id=<?= $id ?>" class="btn btn-danger"
                   onclick="return confirm('Hủy phiếu?')">
                    Hủy
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include '../admin_includes/footer.php'; ?>