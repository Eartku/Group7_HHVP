<?php 
$noLayout = true;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>BonSai | Guest</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link href="css/hover.css" rel="stylesheet">
  <link href="css/tiny-slider.css" rel="stylesheet" />
  <link href="css/animation.css" rel="stylesheet" />
  <link href="<?= BASE_URL ?>/css/theme-gradient.css" rel="stylesheet" />
</head>

<body>

<!-- ================= HEADER ================= -->
<!-- ================= HEADER ================= -->
<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Bonsai navigation bar">
  <div class="container">
    <a class="navbar-brand" href="../app/index.php">BonSai<span>🌱</span></a>

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
      <ul class="navbar-nav ms-auto">
          <li class="nav-item">
              <a href="#" class="nav-link d-flex align-items-center gap-2"
                onclick="showLoginAlert(event)">
                  <img src="images/cart.png" style="width:25px; height:25px;"/> Giỏ hàng
              </a>
          </li>
          <li class="nav-item">
              <a href="../app/index.php?url=login" class="nav-link d-flex align-items-center gap-2">
                  <img src="images/login.svg" style="width:30px; height:30px;"/> Đăng nhập
              </a>
          </li>
          <li class="nav-item">
              <a href="../app/index.php?url=register" class="nav-link d-flex align-items-center gap-2">
                  <img src="images/register.svg" style="width:25px; height:25px;"/> Đăng ký
              </a>
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
        <a href="../app/index.php?url=login" class="btn btn-success me-2">Đăng nhập</a>
        <a href="../app/index.php?url=register" class="btn btn-outline-light">Đăng ký</a>
      </div>
      <div class="col-lg-7">
        <img src="images/bluesucc.gif" class="img-fluid">
      </div>
    </div>
  </div>
</div>

<!-- ================= TẠI SAO CHỌN CHÚNG TÔI ================= -->
<div class="why-choose-section">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-lg-6">
        <h2 class="section-title">Tại sao lại chọn chúng tôi?</h2>
        <p>
          Chúng tôi mang đến cây cảnh khỏe mạnh, được chăm sóc kỹ lưỡng, đảm
          bảo chất lượng và dịch vụ tận tâm. Mỗi sản phẩm đều được tư vấn
          phù hợp với không gian và phong cách của bạn, cùng chính sách bảo
          hành, giao hàng nhanh và hỗ trợ chăm sóc sau khi mua.
        </p>

        <div class="row my-5">
          <div class="col-6 col-md-6">
            <div class="feature hover-div-zoom">
              <div class="icon"><img src="images/truck.svg" alt="Image" class="img-fluid" /></div>
              <h3>Nhanh chóng & Free Shipping</h3>
              <p>Chúng tôi đảm bảo sản phẩm sẽ an toàn và toàn vẹn trước cửa nhà bạn.</p>
            </div>
          </div>
          <div class="col-6 col-md-6">
            <div class="feature hover-div-zoom">
              <div class="icon"><img src="images/bag.svg" alt="Image" class="img-fluid" /></div>
              <h3>Dễ dàng để mua</h3>
              <p>Đây là trang mua bán uy tín số một hàng đầu Việt Nam.</p>
            </div>
          </div>
          <div class="col-6 col-md-6">
            <div class="feature hover-div-zoom">
              <div class="icon"><img src="images/support.svg" alt="Image" class="img-fluid" /></div>
              <h3>Hỗ trợ 24/7</h3>
              <p>Với đội ngũ chuyên nghiệp và ân cần không ngại khó khăn.</p>
            </div>
          </div>
          <div class="col-6 col-md-6">
            <div class="feature hover-div-zoom">
              <div class="icon"><img src="images/return.svg" alt="Image" class="img-fluid" /></div>
              <h3>Hoàn trả miễn phí</h3>
              <p>Có thể hoàn trả nếu hỏng hóc và hoàn toàn miễn phí sau 1 tháng.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="img-wrap slider">
          <img src="images/P1.jpg" class="active" />
          <img src="images/P2.jpg" />
          <img src="images/P3.jpg" />
          <img src="images/P4.jpg" />
          <img src="images/P5.jpg" />
          <img src="images/P6.jpg" />
          <img src="images/P7.jpg" />
        </div>
        <script src="js/slider.js"></script>
      </div>
    </div>
  </div>
</div>

<!-- ================= FOOTER ================= -->
<footer class="footer-section">
  <div class="container relative">
    <div class="border-top copyright">
      <div class="row pt-4">
        <div class="col-lg-6">
          <p class="mb-2 text-center text-lg-start">
            Copyright &copy; <span id="year"></span>
            — Designed by <a href="pages/group7.html">Group 7</a>
          </p>
        </div>
        <div class="col-lg-6 text-center text-lg-end">
          <ul class="list-unstyled d-inline-flex ms-auto">
            <li class="me-4"><a href="#">Terms</a></li>
            <li><a href="#">Privacy</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</footer>
<!-- Modal thông báo đăng nhập -->
<div id="loginModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
     z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:32px 28px;
                max-width:360px; width:90%; text-align:center; box-shadow:0 8px 32px rgba(0,0,0,.2)">
        <div style="font-size:48px; margin-bottom:12px">🔒</div>
        <h5 style="margin-bottom:8px">Bạn chưa đăng nhập</h5>
        <p style="color:#666; font-size:14px; margin-bottom:20px">
            Vui lòng đăng nhập hoặc đăng ký để sử dụng tính năng này.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="../app/index.php?url=login"
               class="btn btn-dark">Đăng nhập</a>
            <a href="../app/index.php?url=register"
               class="btn btn-outline-dark">Đăng ký</a>
        </div>
        <button onclick="closeLoginModal()"
                style="margin-top:14px; background:none; border:none;
                       color:#aaa; font-size:13px; cursor:pointer">
            Đóng
        </button>
    </div>
</div>

<script>
function showLoginAlert(e) {
    if (e) e.preventDefault();
    document.getElementById('loginModal').style.display = 'flex';
}
function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}
// Bấm ra ngoài modal để đóng
document.getElementById('loginModal').addEventListener('click', function(e) {
    if (e.target === this) closeLoginModal();
});
</script>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/tiny-slider.js"></script>
<script src="js/custom.js"></script>
</body>
</html>