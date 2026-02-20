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

/* ===== LẤY TỒN KHO THEO SIZE ===== */
$invStmt = $conn->prepare("
    SELECT size, quantity, price_adjust
    FROM inventory
    WHERE product_id = ?
");
$invStmt->bind_param("i", $id);
$invStmt->execute();
$invResult = $invStmt->get_result();

$sizes = [];
$totalStock = 0;

while ($row = $invResult->fetch_assoc()) {
    $sizes[$row['size']] = [
        'quantity' => (int)$row['quantity'],
        'adjust'   => (float)$row['price_adjust']
    ];
    $totalStock += (int)$row['quantity'];
}

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

    <div class="container py-5" style="margin-top: 60px;">

        <div class="row">

            <!-- HÌNH ẢNH -->
            <div class="col-md-6">

                <img id="mainImage"
                     src="../images/<?= htmlspecialchars($mainImage) ?>"
                     class="img-fluid rounded shadow mb-3 w-100"
                     style="aspect-ratio:1/1; object-fit:cover;">

                <?php if (count($images) > 1): ?>
                    <div id="thumbs" class="d-flex gap-2 flex-wrap">
                        <?php foreach ($images as $img): ?>
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

                <h4 id="priceDisplay"
                    class="text-danger my-3"
                    data-base="<?= $product['price'] ?>">
                    <?= number_format($product['price'], 0, ',', '.') ?> đ
                </h4>

                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <div class="mb-3">
                    <label>Size</label>
                    <select id="sizeSelect" class="form-control" style="width:150px;">
                        <?php foreach (['S', 'M', 'L'] as $size):
                            $qty = $sizes[$size]['quantity'] ?? 0;
                            $adjust = $sizes[$size]['adjust'] ?? 0;
                            ?>
                            <option
                                value="<?= $size ?>"
                                data-adjust="<?= $adjust ?>"
                                data-stock="<?= $qty ?>"
                                <?= $qty <= 0 ? 'disabled' : '' ?>>

                                <?= $size ?>
                                <?php if ($adjust > 0): ?>
                                    (+<?= number_format($adjust, 0, ',', '.') ?>đ)
                                <?php endif; ?>
                                (<?= $qty > 0 ? $qty . " còn" : "Hết hàng" ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Số lượng</label>
                    <input type="number"
                           id="qtyInput"
                           value="1"
                           min="1"
                           class="form-control"
                           style="width:120px;">
                </div>

                <?php if ($totalStock <= 0): ?>
                    <div class="text-danger mb-3">Sản phẩm hiện đã hết hàng</div>
                <?php else: ?>
                    <div class="text-success mb-3">Còn <?= $totalStock ?> sản phẩm</div>
                <?php endif; ?>

                <button
                    class="btn w-100 <?= $totalStock <= 0 ? 'btn-secondary' : 'btn-success' ?>"
                    <?= $totalStock <= 0 ? 'disabled' : '' ?>
                    onclick="<?= $totalStock <= 0 ? '' : "addToCart($id)" ?>">
                    Thêm vào giỏ hàng
                </button>

            </div>

        </div>

        <!-- SẢN PHẨM LIÊN QUAN -->
        <hr class="my-5">

        <h4 class="mb-4">Sản phẩm liên quan</h4>

        <div class="row">
            <?php while ($row = $related->fetch_assoc()):
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
                                <?= number_format($row['price'], 0, ',', '.') ?> đ
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

        document.addEventListener("DOMContentLoaded", function(){

            const sizeSelect = document.getElementById("sizeSelect");
            const priceDisplay = document.getElementById("priceDisplay");
            const qtyInput = document.getElementById("qtyInput");

            function formatMoney(number){
                return number.toLocaleString('vi-VN') + " đ";
            }

            function updatePrice(){
                const basePrice = parseFloat(priceDisplay.dataset.base);
                const selected = sizeSelect.options[sizeSelect.selectedIndex];

                const adjust = parseFloat(selected.dataset.adjust) || 0;
                const stock = parseInt(selected.dataset.stock) || 0;

                const finalPrice = basePrice + adjust;

                priceDisplay.innerText = formatMoney(finalPrice);

                // giới hạn số lượng theo size
                qtyInput.max = stock;
                if(parseInt(qtyInput.value) > stock){
                    qtyInput.value = stock;
                }
            }

            sizeSelect.addEventListener("change", updatePrice);

            updatePrice(); // chạy lần đầu
        });
    </script>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
