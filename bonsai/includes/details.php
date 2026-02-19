<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Sản phẩm không hợp lệ");
}

$id = (int)$_GET['id'];

/* ===== LẤY SẢN PHẨM ===== */
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Sản phẩm không tồn tại");
}

$product = $result->fetch_assoc();

/* ===== XỬ LÝ ẢNH ===== */
$images = [];
if (!empty($product['image'])) {
    $images = explode(",", $product['image']);
}
$mainImage = !empty($images[0]) ? trim($images[0]) : "no-image.png";

/* ===== SẢN PHẨM LIÊN QUAN ===== */
$related = $conn->query("
    SELECT id, name, price, image
    FROM products
    WHERE id != $id
    ORDER BY RAND()
    LIMIT 4
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?></title>
<?php include '../includes/loader.php'; ?>
</head>

<body class="bg-light">

<?php include '../includes/header.php'; ?>

<div class="container py-5" style ="margin-top: 60px;">

    <div class="row">

        <!-- HÌNH ẢNH -->
        <div class="col-md-6">

            <img id="mainImage"
                 src="../images/<?= htmlspecialchars($mainImage) ?>"
                 class="img-fluid rounded shadow mb-3 w-100"
                 style="aspect-ratio:1/1; object-fit:cover;">

            <?php if(count($images) > 1): ?>
            <div id="thumbs" class="d-flex gap-2 flex-wrap">
                <?php foreach($images as $img): ?>
                <img src="../images/<?= htmlspecialchars(trim($img)) ?>"
                     class="thumb rounded"
                     style="width:80px;height:80px;object-fit:cover;cursor:pointer;">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>

        <!-- THÔNG TIN -->
        <div class="col-md-6">

            <h2><?= htmlspecialchars($product['name']) ?></h2>

            <h4 class="text-danger my-3">
                <?= number_format($product['price'],0,',','.') ?> đ
            </h4>

            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <div class="mb-3">
                <label>Số lượng</label>
                <input type="number"
                       id="qtyInput"
                       value="1"
                       min="1"
                       class="form-control"
                       style="width:120px;">
            </div>

            <button class="btn btn-success w-100"
                    onclick="addToCart(<?= $id ?>)">
                Thêm vào giỏ hàng
            </button>

        </div>

    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <hr class="my-5">

    <h4 class="mb-4">Sản phẩm liên quan</h4>

    <div class="row">
        <?php while($row = $related->fetch_assoc()): 
            $img = !empty($row['image']) ? explode(",", $row['image'])[0] : "no-image.png";
        ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="details.php?id=<?= $row['id'] ?>">
                    <img src="../images/<?= htmlspecialchars(trim($img)) ?>"
                         class="card-img-top"
                         style="aspect-ratio:1/1;object-fit:cover;">
                </a>
                <div class="card-body text-center">
                    <h6><?= htmlspecialchars($row['name']) ?></h6>
                    <p class="text-danger">
                        <?= number_format($row['price'],0,',','.') ?> đ
                    </p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

</div>

<div id="toast"></div>

<script src="../js/cartmsg.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    document.querySelectorAll('#thumbs .thumb').forEach(img => {
        img.addEventListener('click', function(){
            document.getElementById('mainImage').src = this.src;
        });
    });

});
</script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
