<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? 0;

/* ========= LẤY SẢN PHẨM ========= */
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product){
    die("Không tìm thấy sản phẩm");
}

/* ========= LẤY TỒN KHO ========= */
$stock = 0;
$inv = $conn->prepare("SELECT SUM(quantity) as qty FROM inventory WHERE product_id=?");
$inv->bind_param("i", $id);
$inv->execute();
$rowInv = $inv->get_result()->fetch_assoc();
$stock = (int)($rowInv['qty'] ?? 0);

/* ========= UPDATE ========= */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name        = trim($_POST["name"]);
    $category    = (int)$_POST["category"];
    $description = $_POST["description"];
    $profit_rate = (float)$_POST["profit_rate"];
    $status      = (int)$_POST["status"];
    $quantity    = (int)$_POST["quantity"];

    /* ========= ẢNH ========= */
    if(!empty($_FILES["new_image"]["name"])){

        $image = time() . "_" . $_FILES["new_image"]["name"];
        $target = "../images/" . $image;

        move_uploaded_file($_FILES["new_image"]["tmp_name"], $target);

    } else {
        $image = $product["image"];
    }

    /* ========= UPDATE PRODUCT ========= */
    $stmt = $conn->prepare("
        UPDATE products
        SET name=?, category_id=?, description=?, image=?, profit_rate=?, status=?
        WHERE id=?
    ");

    $stmt->bind_param(
        "sissdii",
        $name,
        $category,
        $description,
        $image,
        $profit_rate,
        $status,
        $id
    );

    $stmt->execute();

    /* ========= CẬP NHẬT TỒN KHO ========= */
    if ($quantity != $stock) {

        $diff = $quantity - $stock;

        if ($diff != 0) {
            $stmt2 = $conn->prepare("
                INSERT INTO inventory (product_id, quantity, avg_import_price, price_adjust)
                VALUES (?, ?, 0, 0)
            ");
            $stmt2->bind_param("ii", $id, $diff);
            $stmt2->execute();
        }
    }

    header("Location: sshop.php");
    exit;
}

/* ========= DANH MỤC ========= */
$cats = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    <title>Sửa sản phẩm</title>
</head>

<body>

<div class="container mt-5">
<h2 class="text-center mb-4">Sửa sản phẩm</h2>

<form method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate()">

<!-- ID -->
<div class="mb-3">
<label>ID sản phẩm</label>
<input type="text" class="form-control" value="SP<?= str_pad($product['id'],3,'0',STR_PAD_LEFT) ?>" disabled>
</div>

<!-- Tên -->
<div class="mb-3">
<label>Tên sản phẩm</label>
<input type="text" name="name" class="form-control"
value="<?= htmlspecialchars($product['name']) ?>" required>
</div>

<!-- Danh mục -->
<div class="mb-3">
<label>Danh mục</label>
<select name="category" class="form-control">
<?php while($c=$cats->fetch_assoc()): ?>
<option value="<?= $c['id'] ?>"
<?= $product['category_id']==$c['id']?'selected':'' ?>>
<?= $c['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- Số lượng -->
<div class="mb-3">
<label>Số lượng hiện tại</label>
<input type="number" name="quantity" class="form-control" value="<?= $stock ?>">
</div>

<!-- Lợi nhuận -->
<div class="mb-3">
<label>Tỉ lệ lợi nhuận (%)</label>
<input type="number" step="0.1" name="profit_rate"
value="<?= $product['profit_rate'] ?>" class="form-control">
</div>

<!-- Trạng thái -->
<div class="mb-3">
<label>Trạng thái</label>
<select name="status" class="form-control">
<option value="1" <?= $product['status']==1?'selected':'' ?>>Đang bán</option>
<option value="0" <?= $product['status']==0?'selected':'' ?>>Ngừng bán</option>
</select>
</div>

<!-- Ảnh -->
<div class="mb-3">
<label>Ảnh hiện tại</label><br>
<img src="../images/<?= $product['image'] ?>" width="120" class="mb-2">
</div>

<div class="mb-3">
<label>Ảnh mới</label>
<input type="file" name="new_image" class="form-control">
</div>

<!-- Mô tả -->
<div class="mb-3">
<label>Mô tả</label>
<textarea name="description" class="form-control" rows="4">
<?= $product['description'] ?? '' ?>
</textarea>
</div>

<button class="btn btn-warning">Cập nhật</button>
<a href="sshop.php" class="btn btn-secondary">Quay lại</a>

</form>
</div>

<script>
function confirmUpdate(){
    return confirm("Bạn có chắc muốn cập nhật?");
}
</script>

<?php include '../admin_includes/footer.php'; ?>
</body>
</html>