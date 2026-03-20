<?php
require_once "../config/db.php";

/* LẤY DANH MỤC */
$categories = $conn->query("SELECT * FROM categories");

/* XỬ LÝ FORM */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = trim($_POST["name"]);
    $category_id = (int)$_POST["category"];
    $description = $_POST["description"];
    $quantity    = (int)$_POST["quantity"];
    $profit_rate = (float)$_POST["profit_rate"];
    $status      = (int)$_POST["status"];

    /* ========= UPLOAD ẢNH ========= */
    $imageName = "no-image.png"; // ảnh mặc định

    if (!empty($_FILES["image"]["name"])) {
        $imageName = time() . "_" . $_FILES["image"]["name"];
        $target = "../images/" . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    }

    /* ========= INSERT PRODUCT ========= */
    $stmt = $conn->prepare("
        INSERT INTO products 
        (name, category_id, description, image, profit_rate, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sissdi",
        $name,
        $category_id,
        $description,
        $imageName,
        $profit_rate,
        $status
    );

    if ($stmt->execute()) {

        $product_id = $stmt->insert_id;

        /* ========= THÊM TỒN KHO BAN ĐẦU ========= */
        if ($quantity > 0) {

            $stmt2 = $conn->prepare("
                INSERT INTO inventory 
                (product_id, quantity, avg_import_price, price_adjust)
                VALUES (?, ?, 0, 0)
            ");

            $stmt2->bind_param("ii", $product_id, $quantity);
            $stmt2->execute();
        }

        header("Location: ../admin_product/sshop.php");
        exit;

    } else {
        echo "Lỗi thêm sản phẩm: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    <title>Thêm sản phẩm</title>
</head>

<body>

<div class="container mt-5">
<h2 class="text-center mb-4" >Thêm sản phẩm</h2>

<form method="POST" enctype="multipart/form-data">

<!-- Tên -->
<div class="mb-3">
<label>Tên sản phẩm</label>
<input type="text" name="name" class="form-control" required>
</div>

<!-- Danh mục -->
<div class="mb-3">
<label>Danh mục</label>
<select name="category" class="form-control">
<?php while($row = $categories->fetch_assoc()): ?>
<option value="<?= $row['id'] ?>">
<?= $row['name'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<!-- Số lượng -->
<div class="mb-3">
<label>Số lượng ban đầu</label>
<input type="number" name="quantity" class="form-control" value="0">
</div>

<!-- Lợi nhuận -->
<div class="mb-3">
<label>Tỉ lệ lợi nhuận (%)</label>
<input type="number" step="0.1" name="profit_rate" class="form-control" value="0">
</div>

<!-- Trạng thái -->
<div class="mb-3">
<label>Trạng thái</label>
<select name="status" class="form-control">
<option value="1">Hiển thị (Đang bán)</option>
<option value="0">Ẩn (Ngừng bán)</option>
</select>
</div>

<!-- Ảnh -->
<div class="mb-3">
<label>Hình ảnh</label>
<input type="file" name="image" class="form-control">
</div>

<!-- Mô tả -->
<div class="mb-3">
<label>Mô tả</label>
<textarea name="description" class="form-control" rows="4"></textarea>
</div>

<button class="btn btn-success">Thêm sản phẩm</button>
<a href="sshop.php" class="btn btn-secondary">Quay lại</a>

</form>
</div>

<?php include '../admin_includes/footer.php'; ?>
</body>
</html>