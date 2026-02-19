<?php
// header.php
require_once "../config/db.php";
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);
$categories = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
  }
}
?>
<!-- Start Header/Navigation -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Bonsai navigation bar">
  <div class="container">
    <a class="navbar-brand" href="../pages/dashboard.php">BonSai<span>🌱</span></a>

    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarsFurni"
      aria-controls="navbarsFurni"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsFurni">
      <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
        <?php foreach ($categories as $cat): ?>
        <li><a href="../pages/shop.php?category=<?= (int)$cat['id'] ?>" class="hover-text-glow-small"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>

      <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">

        <!-- Trang chủ -->
        <li>
          <input type="checkbox" id="home-toggle" hidden />
          <label for="home-toggle" class="hover-box-2">
            <div class="front"><img src="../images/home.svg" /></div>
            <a href="../pages/dashboard.php" class="back" onclick="event.stopPropagation();">Trang chủ</a>
          </label>
        </li>

        <!-- Giỏ hàng -->
        <li>
          <input type="checkbox" id="cart-toggle" hidden />
          <label for="cart-toggle" class="hover-box-2">
            <div class="front"><img src="../images/cart.svg" /></div>
            <a href="../pages/cart.php" class="back" onclick="event.stopPropagation();">Giỏ hàng</a>
          </label>
        </li>

        <!-- Người dùng -->
        <li class="nav-item">
          <input type="checkbox" id="user-toggle" hidden />
          <label for="user-toggle" class="hover-box-2">
            <div class="front"><img src="../images/user.svg" /></div>
            <div class="back"><span>Người dùng</span></div>
          </label>
          <ul class="dropdown" style="background-color:black;">
            <li><a href="../pages/profile.php">Hồ sơ cá nhân</a></li>
            <li><a href="../pages/orders.php">Lịch sử đơn hàng</a></li>
            <li><a href="../pages/logout.php"><img src="../images/exit.svg" /> Đăng xuất</a></li>
          </ul>
        </li>

        <!-- Tìm kiếm -->
        <li>
          <input type="checkbox" id="search-toggle" hidden />
          <label for="search-toggle" class="hover-box-2">
            <div class="front"><img src="../images/search.svg" /></div>
            <a href="../pages/search.php" class="back" onclick="event.stopPropagation();">Tìm kiếm</a>
          </label>
        </li>

      </ul>
    </div>
  </div>
</nav>

<script>
const toggles = document.querySelectorAll('input[type="checkbox"]');

toggles.forEach(cb => {
  const parentLi = cb.closest('li');
  let timer;

  function startCloseTimer(ms = 1000) {
    clearTimeout(timer);
    timer = setTimeout(() => cb.checked = false, ms);
  }

  cb.addEventListener('change', () => {
    if (cb.checked && !parentLi.matches(':hover')) {
      startCloseTimer();
    }
  });

  parentLi.addEventListener('mouseenter', () => clearTimeout(timer));
  parentLi.addEventListener('mouseleave', () => {
    if (cb.checked) startCloseTimer();
  });
});
</script>
<!-- End Header -->
