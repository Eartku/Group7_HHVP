<!-- Start Popular Product Section -->
<?php
require_once "../config/db.php";

$sql = "
    SELECT 
        p.id,
        p.name,
        p.image,
        p.profit_rate,
        COALESCE(
            ROUND(
                (
                    SUM(il.import_price * il.quantity) /
                    NULLIF(SUM(il.quantity),0)
                ) * (1 + p.profit_rate/100)
            , -3),0
        ) AS sale_price
    FROM products p
    LEFT JOIN inventory_logs il ON il.product_id = p.id
    GROUP BY p.id
    ORDER BY sale_price DESC
    LIMIT 3
";

$result = $conn->query($sql);
?>

<div class="popular-product">
    <div class="container">
        <div class="row">
            <h2 class="section-title">Sản phẩm nổi bật</h2>

            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex hover-div-zoom">
                            <div class="thumbnail">
                                <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="img-fluid" />
                            </div>
                            <div class="pt-3">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <p><?= htmlspecialchars($row['description'] ?? 'Sản phẩm chất lượng cao, được chăm sóc tỉ mỉ.') ?></p>
                                <p><a href="../includes/details.php?id=<?= htmlspecialchars($row['id']) ?>">Xem thêm</a></p>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            } else {
                echo '<p class="col-12">Không có sản phẩm nào.</p>';
            }
            ?>
        </div>
    </div>
</div>
<!-- End Popular Product Section -->
