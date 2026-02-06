<?php
require_once 'db.php';
require_once "auth.php"; // ← CHẶN Ở ĐÂY

$id = (int)($_GET['id'] ?? 0);

$result = $conn->query("SELECT * FROM products WHERE id = $id");
if (!$result || $result->num_rows == 0) {
    die("Sản phẩm không tồn tại");
}

$product = $result->fetch_assoc();


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die('Sản phẩm không hợp lệ');
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
  die('Sản phẩm không tồn tại');
}
$product = $result->fetch_assoc();

// Prepare images array (support JSON gallery or separate columns)
$images = [];
if (!empty($product['images'])) {
  $decoded = json_decode($product['images'], true);
  if (is_array($decoded) && count($decoded) > 0) {
    $images = $decoded;
  }
}
if (empty($images)) {
  if (!empty($product['image'])) $images[] = $product['image'];
  if (!empty($product['image2'])) $images[] = $product['image2'];
  if (!empty($product['image3'])) $images[] = $product['image3'];
}
if (empty($images)) {
  $images[] = 'placeholder.png';
}
$images = array_values(array_unique($images));

$name = $product['name'] ?? 'Sản phẩm';
$raw_description = $product['description'] ?? $product['desc'] ?? '';
$allowed_tags = '<p><br><strong><em><ul><li><ol><b><i><u>';
if (trim($raw_description) === '') {
  $description_html = 'Không có mô tả cho sản phẩm này.';
} else {
  $description_html = nl2br(strip_tags($raw_description, $allowed_tags));
}
$price = isset($product['price']) ? (float)$product['price'] : 0;
$old_price = isset($product['old_price']) ? (float)$product['old_price'] : null;
$discount = null;
if ($old_price && $old_price > $price) {
  $discount = round((1 - ($price / $old_price)) * 100);
}
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="logo.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BonSai | <?= htmlspecialchars($name) ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=search" />
    <link href="css/tiny-slider.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/hover.css" rel="stylesheet" />
    <link href="css/animation.css" rel="stylesheet" />
  </head>

  <body>
    <?php include 'header.php'; ?>

    <div class="hero">
      <div class="center-row" style="scale: 1; text-align: center">
        <h1 class="glow">CHI TIẾT SẢN PHẨM</h1>
      </div>
    </div>

    <div class="container product-detail">
      <div class="row product-main-content">
        <div class="col-md-5 product-image-col">
          <div class="product-image-wrapper">
            <img id="mainImage" src="images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($name) ?>" class="img-fluid rounded shadow" style="width:410px; height:410px; object-fit:cover;" />
          </div>

          <div class="mt-3 d-flex justify-content-center" id="thumbs">

          </div>
        </div>

        <div class="col-md-7 product-info-col">
          <div>
            <h1 class="product-title"><?= htmlspecialchars($name) ?></h1>
            <div>
              <span class="product-price"><?= number_format($price) ?>đ</span>
              <?php if ($old_price && $old_price > $price): ?>
                <span class="old-price"><?= number_format($old_price) ?>đ</span>
                <span class="discount">-<?= $discount ?>%</span>
              <?php endif; ?>
            </div>

            <p class="mt-3"><?= $description_html ?></p>

            <fieldset class="mb-3">
              <legend>Size:</legend>
              <div class="size-selector">
                <label>
                  <input type="radio" name="size" value="small" checked />
                  <span class="size-btn-label">Size nhỏ</span>
                </label>
                <label>
                  <input type="radio" name="size" value="medium" />
                  <span class="size-btn-label">Size vừa</span>
                </label>
                <label>
                  <input type="radio" name="size" value="large" />
                  <span class="size-btn-label">Size to</span>
                </label>
              </div>
            </fieldset>

            <h6>Số lượng:</h6>
            <input type="number" value="1" min="1" class="form-control w-25 mb-3" data-product-id="<?= $id ?>" />
          </div>

          <div class="d-flex gap-3 product-actions">
            <button onclick="addToCartAjax(<?= $id ?>)" class="btn-cart btn-lg">🛒 Thêm vào giỏ hàng</button>
            <?php if (isset($_SESSION['user'])): ?>
              <a href="checkout.html" class="btn-buy btn-lg">Mua Ngay</a>
            <?php else: ?>
              <a href="#" class="btn-buy btn-lg" onclick="checkLogin(event)">Mua Ngay</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/ajax.js"></script>
    <script>
      // Thumbnail click → đổi ảnh lớn
      document.querySelectorAll('#thumbs .thumb').forEach(t => t.addEventListener('click', e => {
        document.getElementById('mainImage').src = e.target.src;
      }));

      function checkLogin(e){ e.preventDefault(); window.location.href = 'login.php'; }
    </script>
  </body>
</html>
