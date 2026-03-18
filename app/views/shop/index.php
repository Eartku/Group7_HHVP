<div class="hero">
  <div class="center-row text-center">
    <h1 class="glow"><?= htmlspecialchars($catName) ?></h1>
    <span style="color:aliceblue;">Tạo nên một không gian nhỏ xinh!</span>
  </div>
</div>

<div class="container mt-5">
  <div class="row">
    <?php foreach ($products as $p):
      $id          = $p['id'];
      $name        = htmlspecialchars($p['name'] ?? '');
      $image       = $p['image'] ?? 'default.png';
      $imagePath   = '/app/uploads/products/' . htmlspecialchars($image);
      $price       = number_format($p['sale_price'] ?? 0, 0, ',', '.');
      $isOutOfStock = ($p['sale_price'] ?? 0) == 0;
    ?>
      <div class="col-12 col-md-6 col-lg-3 mb-5">
        <div class="product-item text-center h-100">

          <a href="/app/index.php?url=product&id=<?= $id ?>">
            <img src="<?= $imagePath ?>"
                 class="product-thumbnail img-fluid"
                 loading="lazy"
                 alt="<?= $name ?>">
          </a>

          <h3 class="product-title mt-3"><?= $name ?></h3>

          <strong class="product-price d-block mb-1">
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
              <img src="/app/images/cart.svg" width="18" alt="cart">
            </button>

            <a href="/app/index.php?url=product&id=<?= $id ?>"
               class="btn btn-sm btn-outline-dark">
              Chi tiết
            </a>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- PHÂN TRANG -->
  <?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-center mt-4">

        <?php
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = http_build_query($queryParams);
        $queryString = $queryString ? '?' . $queryString . '&' : '?';
        ?>

        <?php if ($page > 1): ?>
            <a href="<?= $queryString ?>page=<?= $page - 1 ?>"
               class="btn btn-sm btn-outline-dark mx-1">
                &laquo; Trước
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= $queryString ?>page=<?= $i ?>"
               class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= $queryString ?>page=<?= $page + 1 ?>"
               class="btn btn-sm btn-outline-dark mx-1">
                Sau &raquo;
            </a>
        <?php endif; ?>

    </div>
<?php endif; ?>

</div>

<div id="toast"></div>