<?php
require_once "../config/db.php";

/* ======================
   0. KIỂM TRA ID
====================== */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Thiếu ID");

/* ======================
   1. LẤY ENUM SIZE
====================== */
$size_result = $conn->query("SHOW COLUMNS FROM import_receipts LIKE 'size'");
$row_size = $size_result->fetch_assoc();

preg_match("/^enum\((.*)\)$/", $row_size['Type'], $matches);
$sizes = isset($matches[1]) ? explode(",", $matches[1]) : ['S','M','L'];

/* ======================
   2. LẤY DỮ LIỆU
====================== */
$stmt = $conn->prepare("
    SELECT ir.id, ir.import_date, ir.product_id, ir.size, 
           ir.quantity, ir.import_price, ir.total_value, ir.status,
           p.name
    FROM import_receipts ir
    LEFT JOIN products p ON ir.product_id = p.id
    WHERE ir.id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$product_name = '';
$total_value = 0;

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
    die("Không tìm thấy phiếu");
}
$stmt->close();

$can_edit = ($status === 'pending');

/* ======================
   3. UPDATE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_btn'])) {

    if (!$can_edit) {
        die("Không được phép sửa");
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

    $update_stmt->bind_param("sdidi", $new_size, $new_price, $new_qty, $new_total, $id);

    if ($update_stmt->execute()) {
        header("Location: edit_import.php?id=$id&success=1");
        exit;
    } else {
        die("Update lỗi");
    }
}

include "../admin_includes/loader.php";
include "../admin_includes/header.php";
?>

<div class="container mt-4">

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">Cập nhật thành công</div>
<?php endif; ?>

    <form method="POST">

        <div class="card p-4">

        <h5>Phiếu #<?= $id_db ?></h5>
        <hr>

        <label>Sản phẩm</label>
            <input class="form-control mb-2 bg-light"
                value="<?= htmlspecialchars($product_name) ?>" readonly>

        <label>Size</label>
            <select name="size" class="form-control mb-2" <?= !$can_edit ? 'disabled' : '' ?>>
                <?php foreach($sizes as $s): 
                    $s = trim($s, "'");
                    ?>
                    <option value="<?= $s ?>" <?= ($s == $size) ? 'selected' : '' ?>>
                        Size <?= $s ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <label>Giá nhập</label>
            <input name="import_price" type="number" class="form-control mb-2"
                value="<?= $import_price ?>"
                    <?= !$can_edit ? 'readonly' : '' ?>>

        <label>Số lượng</label>
            <input name="quantity" type="number" class="form-control mb-2"
                value="<?= $quantity ?>"
                <?= !$can_edit ? 'readonly' : '' ?>>

        <label>Tổng tiền</label>
            <input class="form-control mb-2 bg-light"
                value="<?= number_format($total_value) ?> VNĐ" readonly>

        <label>Trạng thái</label>
        <div class="mb-2">
            <?php if($status=="pending"): ?>
                <span class="badge bg-warning">Đang chờ</span>
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

        <a href="confirm_import.php?id=<?= $id ?>" 
            class="btn btn-success"
                onclick="return confirm('Xác nhận nhập kho?')">
                    Xác nhận
        </a>

        <a href="cancel_import.php?id=<?= $id ?>" 
            class="btn btn-danger"
                onclick="return confirm('Hủy phiếu?')">
                    Hủy
        </a>
    <?php endif; ?>

    </div>

    </form>

</div>

<?php include '../admin_includes/footer.php'; ?>