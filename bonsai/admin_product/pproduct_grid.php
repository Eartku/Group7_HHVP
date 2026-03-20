<?php if (!empty($products)): ?>
    <?php foreach ($products as $p):

        $id   = (int)$p['id'];
        $name = htmlspecialchars($p['name']);

        /* ========= STOCK ========= */
        $stock = (int)($p['total_stock'] ?? 0);
        $isOutOfStock = $stock <= 0;

        /* ========= STATUS ========= */
        $status = (int)($p['status'] ?? 1);
        $isHidden = ($status === 0);

        /* ========= GIÁ ========= */
        $avgImport  = (float)($p['avg_import_price'] ?? 0);
        $profitRate = (float)($p['profit_rate'] ?? 0);
        $priceAdjust = (float)($p['price_adjust'] ?? 0);

        $rawSalePrice = ($avgImport + $priceAdjust) * (1 + $profitRate / 100);
        $salePrice = round($rawSalePrice, -3);
        $price = number_format($salePrice, 0, ',', '.');

        /* ========= IMAGE ========= */
        $imageList  = !empty($p['image']) ? explode(",", $p['image']) : [];
        $firstImage = trim($imageList[0] ?? "");
        $imagePath  = $firstImage
            ? "../images/" . htmlspecialchars($firstImage)
            : "https://via.placeholder.com/400x400?text=No+Image";
    ?>

    <div class="col-12 col-md-6 col-lg-3 mb-5">
        <div class="product-item text-center h-100">

            <img src="<?= $imagePath ?>"
                 class="product-thumbnail img-fluid"
                 alt="<?= $name ?>">

            <h3 class="product-title mt-3"><?= $name ?></h3>

            <!-- Mã -->
            <div class="text-muted small mb-1">
                Mã: SP<?= str_pad($id, 3, '0', STR_PAD_LEFT) ?>
            </div>

            <!-- Giá -->
            <strong class="product-price d-block mb-1">
                <?= $price ?>đ
            </strong>

            <!-- Kho -->
            <div class="<?= $isOutOfStock ? 'text-danger' : 'text-success' ?> mb-1">
                <?= $isOutOfStock ? 'Hết hàng' : 'Còn hàng' ?>
            </div>

            <!-- Trạng thái -->
            <span class="badge <?= $isHidden ? 'bg-secondary' : 'bg-success' ?> mb-2">
                <?= $isHidden ? 'Ngừng bán' : 'Đang bán' ?>
            </span>

            <!-- ACTION -->
            <div class="d-flex justify-content-center gap-2">

                <a href="edit_product.php?id=<?= $id ?>"
                   class="btn btn-sm btn-warning">
                   Sửa
                </a>

                <a href="delete_product.php?id=<?= $id ?>"
                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')"
                   class="btn btn-sm btn-danger">
                   Xóa
                </a>

            </div>

        </div>
    </div>

    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12 text-center text-muted py-5">
        <h4>Không tìm thấy sản phẩm nào</h4>
    </div>
<?php endif; ?>