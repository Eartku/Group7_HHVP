<?php if (!empty($products)): ?>
    <?php foreach ($products as $p):

        $id   = (int)$p['id'];
        $name = htmlspecialchars($p['name']);

        // ===== TÍNH GIÁ BÁN =====
        $importPrice = (float)($p['import_price'] ?? 0); 
        $profitRate  = (float)($p['profit_rate'] ?? 0);

        $salePrice = $importPrice * (1 + $profitRate / 100);
        $price = number_format((float)($p['sale_price'] ?? 0), 0, ',', '.');

        // ===== STOCK =====
        $stock = (int)($p['total_stock'] ?? 0);
        $isOutOfStock = $stock <= 0;

        // ===== IMAGE =====
        $imageList = !empty($p['image']) ? explode(",", $p['image']) : [];
        $firstImage = trim($imageList[0] ?? "");
        $imagePath = $firstImage
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

            <div class="<?= $isOutOfStock ? 'text-danger' : 'text-success' ?> mb-2">
                <?= $isOutOfStock ? 'Hết hàng' : 'Còn hàng' ?>
            </div>

            <div class="d-flex justify-content-center gap-2">

                <button
                    <?= $isOutOfStock ? 'disabled' : '' ?>
                    onclick="<?= $isOutOfStock ? '' : "addToCart($id)" ?>"
                    class="btn btn-sm <?= $isOutOfStock ? 'btn-secondary' : 'btn-dark' ?>">
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
    <div class="col-12 text-center text-muted py-5">
        <h4>Không tìm thấy sản phẩm nào</h4>
        <p>Vui lòng thử lại với bộ lọc khác</p>
    </div>
<?php endif; ?>