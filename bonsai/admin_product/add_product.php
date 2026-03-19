<?php
require_once "../config/db.php";

/* LẤY DANH MỤC */
$categories = $conn->query("SELECT * FROM categories");

/* XỬ LÝ THÊM SẢN PHẨM */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $category = $_POST["category"];
    $description = $_POST["description"];

    /* xử lý upload ảnh */
    $image = $_FILES["image"]["name"];
    $target = "../img/" . $image;

    move_uploaded_file($_FILES["image"]["tmp_name"], $target);

    $stmt = $conn->prepare("
        INSERT INTO products (name, category_id, image, description)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("siss", $name, $category, $image, $description);

    if ($stmt->execute()) {

        header("Location: ../admin_product/sshop.php");
        exit;

    } else {

        echo "Lỗi thêm sản phẩm";

    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    
    <title>
        BonSai | Shop
    </title>
</head>

<body>
        <div class="hero">
        <div class="center-row text-center">
        <h1 class="glow">Phiếu Thêm sản phẩm</h1>
        </div>
        </div>
<div  class="container mt-5">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Tên sản phẩm</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

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

<div class="mb-3">

<label>Upload ảnh</label>

<input
type="file"
name="image"
class="form-control">

</div>

<div class="mb-3">

<label>Mô tả sản phẩm</label>

<textarea
name="description"
class="form-control"
rows="4"
placeholder="Nhập mô tả sản phẩm"></textarea>

</div>

<button class="btn btn-success">

Thêm sản phẩm

</button>

<a href="sshop.php" class="btn btn-secondary">

Quay lại

</a>

</form>
</div>
<?php include '../admin_includes/footer.php'; ?>
</body>
</html>