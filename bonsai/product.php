<?php
session_start();
require_once "db.php";

$isLogin = isset($_SESSION['user']);

if (!isset($_GET['id'])) {
    die("Thiếu loại cây!");
}

$category_id = (int)$_GET['id'];

/* ===== LẤY THÔNG TIN LOẠI ===== */
$cat_stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
$cat_stmt->bind_param("i", $category_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();

if ($cat_result->num_rows === 0) {
    die("Loại cây không tồn tại!");
}
$cat = $cat_result->fetch_assoc();

/* ===== LẤY SẢN PHẨM THEO LOẠI ===== */
$prod_stmt = $conn->prepare(
    "SELECT id, name, image, price FROM products WHERE category_id = ?"
);
$prod_stmt->bind_param("i", $category_id);
$prod_stmt->execute();
$result = $prod_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Bonsai | <?= htmlspecialchars($cat['name']) ?></title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/hover.css" rel="stylesheet">
    <link href="css/animation.css" rel="stylesheet">
</head>
<body>

<?php include "header.php"; ?>

<!-- SEARCH -->
<div class="hero py-4">
  <div class="container">
    <?php include "search.php"; ?>
  </div>
</div>

<!-- PRODUCT LIST -->
<div class="product-section">
  <div class="container">

    <h1 class="section-title mb-5">
      <?= htmlspecialchars($cat['name']) ?>
    </h1>

    <div class="row">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-12 col-md-4 col-lg-3 mb-5">
            <div class="product-item">

              <?php if ($isLogin): ?>
                <a href="details.php?id=<?= $row['id'] ?>">
                  <img src="images/<?= htmlspecialchars($row['image']) ?>"
                       class="img-fluid product-thumbnail">
                </a>
              <?php else: ?>
                <a href="#" onclick="needLogin(event)">
                  <img src="images/<?= htmlspecialchars($row['image']) ?>"
                       class="img-fluid product-thumbnail">
                </a>
              <?php endif; ?>

              <h3 class="product-title">
                <?= htmlspecialchars($row['name']) ?>
              </h3>

              <strong class="product-price">
                <?= number_format($row['price']) ?>đ
              </strong>

              <div class="mt-2">
                <?php if ($isLogin): ?>
                  <button onclick="addToCartAjax(<?= $row['id'] ?>)"
                          class="btn btn-sm btn-success">
                    <img src="images/cart.svg">
                  </button>
                  <a href="details.php?id=<?= $row['id'] ?>"
                     class="btn btn-sm btn-success">
                    Chi tiết
                  </a>
                <?php else: ?>
                  <button class="btn btn-sm btn-secondary"
                          onclick="needLogin(event)">
                    <img src="images/cart.svg">
                  </button>
                  <a href="#" class="btn btn-sm btn-secondary"
                     onclick="needLogin(event)">
                    Chi tiết
                  </a>
                <?php endif; ?>
              </div>

            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Không có sản phẩm nào trong loại này.</p>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php include "footer.php"; ?>

<script>
function needLogin(e){
  e.preventDefault();
  alert("⚠ Bạn cần đăng nhập để sử dụng chức năng này!");
}
</script>

</body>
</html>
Compare this snippet from register.php: