<?php
require_once "../config/db.php";

/* ===============================
   1. LẤY CATEGORY
================================ */
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

/* ===============================
   2. PHÂN TRANG
================================ */
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 8;
$offset = ($page - 1) * $limit;

/* ===============================
   3. ĐẾM TỔNG SẢN PHẨM
================================ */
$countSql = "SELECT COUNT(*) as total FROM products";
if ($category_id > 0) {
    $countSql .= " WHERE category_id = $category_id";
}

$countResult = $conn->query($countSql);
$totalRow = $countResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

/* ===============================
   4. LẤY DANH SÁCH SẢN PHẨM
================================ */
$sql = "SELECT id, name, price, image FROM products";

if ($category_id > 0) {
    $sql .= " WHERE category_id = $category_id";
}

$sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<div class="product-section py-5">
    <div class="container">
        <div class="row">

        <?php if (!empty($products)): ?>
        <?php foreach ($products as $p): 

            $id = (int)$p['id'];
            $name = htmlspecialchars($p['name']);
            $price = number_format((float)$p['price'], 0, ',', '.');

            $imageList = !empty($p['image']) ? explode(",", $p['image']) : [];
            $firstImage = !empty($imageList[0]) ? trim($imageList[0]) : "";

            $imagePath = !empty($firstImage)
                ? "../images/" . htmlspecialchars($firstImage)
                : "https://via.placeholder.com/400x400?text=No+Image";
        ?>

        <div class="col-12 col-md-6 col-lg-3 mb-5">
            <div class="product-item text-center h-100">

                <a href="../includes/details.php?id=<?= $id ?>">
                    <img src="<?= $imagePath ?>"
                         class="product-thumbnail img-fluid"
                         loading="lazy">
                </a>

                <h3 class="product-title mt-3"><?= $name ?></h3>
                <strong class="product-price d-block mb-3">
                    <?= $price ?>đ
                </strong>

                <div class="d-flex justify-content-center gap-2">

                    <button onclick="addToCart(<?= $id ?>)"
                        class="btn btn-sm btn-dark">
                        <img src="../images/cart.svg" width="18">
                    </button>

                    <a href="../includes/details.php?id=<?= $id ?>"
                        class="btn btn-sm btn-outline-dark">
                        Chi tiết
                    </a>

                </div>

            </div>
        </div>

        <?php endforeach; ?>
        <?php else: ?>

        <div class="col-12 text-center text-muted">
            Không có sản phẩm
        </div>

        <?php endif; ?>

        </div>

        <!-- PHÂN TRANG -->
        <div class="d-flex justify-content-center mt-4">
        <?php if ($totalPages > 1): ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&category=<?= $category_id ?>"
                   class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        <?php endif; ?>
        </div>

    </div>
</div>
<script src="../js/cartmsg.js"></script>
