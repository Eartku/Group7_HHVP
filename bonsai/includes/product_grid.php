<?php if (!empty($products)): ?>
    <?php foreach ($products as $p):

        $id   = (int)$p['id'];
        $name = htmlspecialchars($p['name']);

        /* ========= STOCK ========= */
        $stock = (int)($p['total_stock'] ?? 0);
        $isOutOfStock = $stock <= 0;

        /* ========= GIÁ ========= */
        $avgImport  = (float)($p['avg_import_price'] ?? 0);
        $profitRate = (float)($p['profit_rate'] ?? 0);

        // Nếu bạn có price_adjust trong query thì lấy thêm
        $priceAdjust = (float)($p['price_adjust'] ?? 0);

        $rawSalePrice = ($avgImport + $priceAdjust) * (1 + $profitRate / 100);

        // Làm tròn nghìn
        $salePrice = round($rawSalePrice, -3);

        $price = number_format($salePrice, 0, ',', '.');
        /* ========= IMAGE ========= */
        $imageList  = !empty($p['image']) ? explode(",", $p['image']) : [];
        $firstImage = trim($imageList[0] ?? "");
        $imagePath  = $firstImage
            ? "../images/" . htmlspecialchars($firstImage)
            : "https://via.placeholder.com/400x400?text=No+Image";
        // echo "<pre>";
        // echo "AVG: $avgImport\n";
        // echo "ADJUST: $priceAdjust\n";
        // echo "PROFIT: $profitRate\n";
        // echo "RAW: $rawSalePrice\n";
        // echo "</pre>";
    ?>

    <div class="col-12 col-md-6 col-lg-3 mb-5">
        <div class="product-item text-center h-100">

            <a href="../includes/details.php?id=<?= $id ?>">
                <img src="<?= $imagePath ?>"
                     class="product-thumbnail img-fluid"
                     loading="lazy"
                     alt="<?= $name ?>">
            </a>

            <h3 class="product-title mt-3"><?= $name ?></h3>

            <!-- Giá bán -->
            <strong class="product-price d-block mb-1">
                <?= $price ?>đ
            </strong>

            <!-- Trạng thái kho -->
            <div class="<?= $isOutOfStock ? 'text-danger' : 'text-success' ?> mb-2">
                <?= $isOutOfStock ? 'Hết hàng' : 'Còn hàng' ?>
            </div>

            <div class="d-flex justify-content-center gap-2">

                <button
                    <?= $isOutOfStock ? 'disabled' : '' ?>
                    onclick="<?= $isOutOfStock ? '' : "addToCart($id)" ?>"
                    class="btn btn-sm <?= $isOutOfStock ? 'btn-secondary' : 'btn-dark' ?>">
                    <img src="../images/cart.svg" width="18" alt="cart">
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