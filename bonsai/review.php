<?php
require_once "db.php";

$isLogin = isset($_SESSION['user']);

$sql = "SELECT id, name, category_id, image, price FROM products ORDER BY RAND() LIMIT 7";
$result = $conn->query($sql);
?>

<div class="product-section">
  <div class="container">
    <div class="row">

      <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
        <h2 class="mb-4 section-title">Nuôi dưỡng từ thiên nhiên.</h2>
        <p class="mb-4">Mang hơi thở thiên nhiên vào từng góc nhỏ.</p>
      </div>

      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-12 col-md-4 col-lg-3 mb-5">
            <div class="product-item">

              <!-- Ảnh -->
              <?php if ($isLogin): ?>
                <a href="details.php?id=<?= $row['id'] ?>">
                  <img src="images/<?= htmlspecialchars($row['image']) ?>" class="img-fluid product-thumbnail">
                </a>
              <?php else: ?>
                <a href="#" onclick="needLogin(event)">
                  <img src="images/<?= htmlspecialchars($row['image']) ?>" class="img-fluid product-thumbnail">
                </a>
              <?php endif; ?>

              <h3 class="product-title"><?= htmlspecialchars($row['name']) ?></h3>
              <strong class="product-price"><?= number_format($row['price']) ?>đ</strong>

              <div class="mt-2">
                <?php if ($isLogin): ?>
                  <a href="cart.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                    <img src="images/cart.svg">
                  </a>
                  <a href="details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                    Chi tiết
                  </a>
                <?php else: ?>
                  <a href="#" class="btn btn-sm btn-secondary" onclick="needLogin(event)">
                    <img src="images/cart.svg">
                  </a>
                  <a href="#" class="btn btn-sm btn-secondary" onclick="needLogin(event)">
                    Chi tiết
                  </a>
                <?php endif; ?>
              </div>

            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="col-12">Không có sản phẩm nào.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>
function needLogin(e){
  e.preventDefault();
  alert("⚠ Bạn cần đăng nhập để sử dụng chức năng này!");
}
</script>
