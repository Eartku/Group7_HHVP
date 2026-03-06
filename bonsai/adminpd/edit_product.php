<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? 0;

/* lấy sản phẩm */
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if(!$product){
    die("Không tìm thấy sản phẩm");
}

/* update */
if($_SERVER["REQUEST_METHOD"] == "POST"){

$name = $_POST["name"];
$category = $_POST["category"];
$description = $_POST["description"];

/* nếu có ảnh mới thì dùng ảnh mới */
if(!empty($_FILES["new_image"]["name"])){

    $image = $_FILES["new_image"]["name"];
    $target = "../img/" . $image;

    move_uploaded_file($_FILES["new_image"]["tmp_name"], $target);

}else{

    $image = $product["image"];

}

$stmt = $conn->prepare("
UPDATE products
SET name=?, category_id=?, image=?, description=?
WHERE id=?
");

$stmt->bind_param("sissi",$name,$category,$image,$description,$id);

if($stmt->execute()){
    header("Location: sshop.php");
    exit;
}

}

/* lấy danh mục */
$cats = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html>
<head>
<title>Sửa sản phẩm</title>
<link rel="stylesheet" href="../css/bootstrap.min.css">
</head>

<body class="container mt-5">

<h2>Sửa sản phẩm</h2>

<form method="POST" enctype="multipart/form-data" onsubmit="return confirmUpdate()">

<div class="mb-3">
<label>Tên sản phẩm</label>
<input type="text"
name="name"
value="<?= htmlspecialchars($product['name']) ?>"
class="form-control">
</div>

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

<div class="mb-3">
<label>Ảnh hiện tại</label><br>

<img src="../img/<?= $product['image'] ?>"
width="120"
class="mb-2">

<input type="text"
name="image"
value="<?= $product['image'] ?>"
class="form-control">
</div>

<div class="mb-3">
<label>Ảnh mới của sản phẩm</label>

<input type="file"
name="new_image"
class="form-control">
</div>

<div class="mb-3">
<label>Mô tả sản phẩm</label>

<textarea
name="description"
class="form-control"
rows="4"><?= $product['description'] ?? '' ?></textarea>

</div>

<button class="btn btn-warning">
Cập nhật
</button>

<a href="sshop.php" class="btn btn-secondary">
Quay lại
</a>

</form>
<script>
function confirmUpdate(){

return confirm("Bạn có chắc muốn cập nhật sản phẩm này không?");

}
</script>
</body>
</html>