<?php
require_once "db.php";

if (!isset($_GET['id'])) {
    die("Thiếu loại cây!");
}

$category_id = (int)$_GET['id'];

// Lấy thông tin loại
$cat_result = $conn->query("SELECT * FROM categories WHERE id = $category_id");
if (!$cat_result || $cat_result->num_rows == 0) {
    die("Loại cây không tồn tại!");
}
$cat = $cat_result->fetch_assoc();

// Lấy sản phẩm theo loại
$sql = "SELECT * FROM products WHERE category_id = $category_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="shortcut icon" href="logo.png" />
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/hover.css" rel="stylesheet" />
    <link href="css/animation.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <script src="js/ajax.js"></script>
    <title>BonSai | <?= htmlspecialchars($cat['name']) ?></title>
</head>
<body>

<?php include 'header.php'; ?>

<!-- Search Section -->
<div class="hero" style="padding: 30px 0;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php include 'search.php'; ?>
      </div>
    </div>
  </div>
</div>

<!-- Product Section -->
<div class="product-section">
  <div class="container">
    <div class="row mb-5">
      <h1 class="section-title">Danh sách <?= htmlspecialchars($cat['name']) ?></h1>
    </div>

    <div class="row">
      <?php 
        if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()): 
      ?>
      <div class="col-12 col-md-4 col-lg-3 mb-5">
        <div class="product-item">
          <a href="details.php?id=<?= htmlspecialchars($row['id']) ?>">
            <img src="images/<?= htmlspecialchars($row['image']) ?>" class="img-fluid product-thumbnail" />
          </a>
          <h3 class="product-title"><?= htmlspecialchars($row['name']) ?></h3>
          <strong class="product-price"><?= number_format($row['price']) ?>đ</strong>

          <!-- Thêm nút Chi tiết -->
          <div class="mt-2">
            <button onclick="addToCartAjax(<?= $row['id'] ?>)" class="btn btn-sm btn-success add-to-cart">
              <img src="images/cart.svg" alt="Giỏ hàng" />
            </button>
            <a href="details.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-sm btn-success">Chi tiết</a>
          </div>
        </div>
      </div>
      <?php 
          endwhile;
        } else {
          echo '<p class="col-12">Không có sản phẩm nào trong loại này.</p>';
        }
      ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/custom.js"></script>

</body>
</html>
