<!-- Start Popular Product Section -->
<?php
require_once "../config/db.php";
$sql = "SELECT id, name, image FROM products ORDER BY id DESC LIMIT 3";
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
            <p><a href="details.php?id=<?= htmlspecialchars($row['id']) ?>">Xem thêm</a></p>
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
