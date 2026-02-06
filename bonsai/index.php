
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>BonSai | Guest</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/hover.css" rel="stylesheet">
</head>

<body>

<!-- ================= HEADER ================= -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Bonsai navigation bar">
  <div class="container">
    <a class="navbar-brand" href="index.php">BonSai<span>🌱</span></a>

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
    <div class="collapse navbar-collapse" id="navbarsFurni" style="margin-left: 1000px;">

        <!-- Người dùng -->
        <li class="nav-item">
          <input type="checkbox" id="user-toggle" hidden />
          <label for="user-toggle" class="hover-box-2">
            <div class="front"><img src="images/login.svg" style="width:30px; height:30px;"/></div>
            <a href="login.php" class="back" onclick="event.stopPropagation();">Đăng nhập</a>
          </label>
        </li>

        <!-- Tìm kiếm -->
        <li>
          <input type="checkbox" id="search-toggle" hidden />
          <label for="search-toggle" class="hover-box-2">
            <div class="front"><img src="images/register.svg" style="width:25px; height:25px;"/></div>
            <a href="register.php" class="back" onclick="event.stopPropagation();">Đăng ký</a>
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

<!-- ================= HERO ================= -->
<div class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-5">
        <h1>BonSai – Không gian xanh cho bạn</h1>
        <p>Đăng nhập để mua sắm và trải nghiệm đầy đủ tính năng.</p>

          <a href="login.php" class="btn btn-success">Đăng nhập</a>
      </div>

      <div class="col-lg-7">
        <img src="images/bluesucc.gif" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<!-- ================= SẢN PHẨM ================= -->
<?php include 'review.php'; ?>

<!-- ================= FOOTER ================= -->
<?php include 'footer.php'; ?>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
        </li>

      </ul>
    