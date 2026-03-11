<?php
require_once "../config/db.php";

/* ======================
   LẤY ID PHIẾU
====================== */

$id = $_GET['id'] ?? 0;

if(!$id){
    die("Thiếu ID phiếu nhập");
}

/* ======================
   LẤY DỮ LIỆU PHIẾU
====================== */

$stmt = $conn->prepare("
SELECT ir.*, p.name AS product_name
FROM import_receipts ir
LEFT JOIN products p ON ir.product_id = p.id
WHERE ir.id = ?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if(!$row){
    die("Không tìm thấy phiếu nhập");
}

/* ======================
   LOAD GIAO DIỆN
====================== */

include "../admin_includes/loader.php";
include "../admin_includes/header.php";
?>

<div class="container">

<h3 class="mb-3">Chi tiết phiếu nhập</h3>

<div class="card p-4 mb-3" style="opacity:0.6">

<h5>Thông tin phiếu</h5>

<label>Sản phẩm</label>
<input class="form-control"
value="<?= $row['product_name'] ?? '' ?>"
readonly>

<label>Size</label>
<input class="form-control"
value="<?= $row['size'] ?? '' ?>"
readonly>

<label>Giá nhập</label>
<input class="form-control"
value="<?= $row['import_price'] ?? '' ?>"
readonly>

<label>Số lượng</label>
<input class="form-control"
value="<?= $row['quantity'] ?? '' ?>"
readonly>

<label>Tổng giá trị</label>
<input class="form-control"
value="<?= $row['total_value'] ?? '' ?>"
readonly>

<label>Trạng thái</label>

<?php
$status = $row['status'] ?? 'pending';

if($status=="pending"){
    echo '<span class="badge bg-warning text-dark">Đang xử lý</span>';
}
elseif($status=="completed"){
    echo '<span class="badge bg-success">Đã hoàn thành</span>';
}
elseif($status=="cancelled"){
    echo '<span class="badge bg-danger">Đã hủy</span>';
}
?>

</div>

<div class="mt-3">

<a href="adminipd.php" class="btn btn-secondary">
Quay lại
</a>

<?php if($status=="pending"){ ?>

<a href="confirm_import.php?id=<?=$row['id']?>"
class="btn btn-success"
onclick="return confirm('Xác nhận nhập kho?')">
Xác nhận nhập
</a>

<a href="cancel_import.php?id=<?=$row['id']?>"
class="btn btn-danger"
onclick="return confirm('Bạn chắc chắn muốn hủy phiếu?')">
Hủy phiếu
</a>

<?php } ?>

<?php if($status=="completed"){ ?>

<button class="btn btn-success" disabled>
Đã xác nhận
</button>

<?php } ?>

<?php if($status=="cancelled"){ ?>

<button class="btn btn-danger" disabled>
Phiếu đã hủy
</button>

<?php } ?>

</div>

</div>

<?php include "fffooter.php"; ?>