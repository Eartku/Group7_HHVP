<div class="popular-product">
    <div class="container">
        <div class="row">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <?php if (!empty($popularProducts)): ?>
                <?php foreach ($popularProducts as $row): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex hover-div-zoom">
                            <div class="thumbnail">
                                <img src="images/<?= htmlspecialchars($row['image']) ?>"
                                     alt="<?= htmlspecialchars($row['name']) ?>"
                                     class="img-fluid" />
                            </div>
                            <div class="pt-3">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <p><?= htmlspecialchars($row['description'] ?? 'Sản phẩm chất lượng cao.') ?></p>
                                <p><a href="<?= BASE_URL ?>/index.php?url=shop&id=<?= $row['id'] ?>">Xem thêm</a></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-12">Không có sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>