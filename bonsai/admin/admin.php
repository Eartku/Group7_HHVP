<?php
// session_start();

// // kiểm tra đăng nhập admin
// if (!isset($_SESSION['admin'])) {
//     header("Location: adminlogin/admin_login.php");
//     exit();
// }
?>

<!-- Trang admin -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../admin_includes/loader.php'; ?>
    <title>
        BonSai | Shop
    </title>
</head>
  <body>
    <?php include '../admin_includes/header.php'; ?>
    <!-- Start Header/Navigation -->

    <!-- End Header/Navigation -->

    <!-- Start Hero Section -->

    <!-- Start Product Section -->
    <div class="why-choose-section">
      <div class="container">
        <div class="row justify-content-between">
          <div class="col-lg-6">
            <h2 class="section-title">Châm ngôn của chúng tôi</h2>
            <p>
              Chúng tôi mang đến cây cảnh khỏe mạnh, được chăm sóc kỹ lưỡng, đảm
              bảo chất lượng và dịch vụ tận tâm. Mỗi sản phẩm đều được tư vấn
              phù hợp với không gian và phong cách của bạn, cùng chính sách bảo
              hành, giao hàng nhanh và hỗ trợ chăm sóc sau khi mua.
            </p>

            <div class="row my-5">
              <div class="col-6 col-md-6">
                <div class="feature hover-div-zoom">
                  <div class="icon">
                    <img src="../images/truck.svg" alt="Image" class="img-fluid" />
                  </div>
                  <h3>Nhanh chóng & Free Shipping</h3>
                  <p>
                    Chúng tôi đảm bảo sản phẩm sẽ an toàn và toàn vẹn trước cửa
                    nhà bạn.
                  </p>
                </div>
              </div>

              <div class="col-6 col-md-6">
                <div class="feature hover-div-zoom">
                  <div class="icon">
                    <img src="../images/bag.svg" alt="Image" class="img-fluid" />
                  </div>
                  <h3>Dễ dàng để mua</h3>
                  <p>Đây là trang mua bán uy tín số một hàng đầu Việt Nam.</p>
                </div>
              </div>

              <div class="col-6 col-md-6">
                <div class="feature hover-div-zoom">
                  <div class="icon">
                    <img
                      src="../images/support.svg"
                      alt="Image"
                      class="img-fluid"
                    />
                  </div>
                  <h3>Hỗ trợ 24/7</h3>
                  <p>
                    Với đội ngũ chuyên nghiệp và ân cần không ngại khó khăn.
                  </p>
                </div>
              </div>

              <div class="col-6 col-md-6">
                <div class="feature hover-div-zoom">
                  <div class="icon">
                    <img
                      src="../images/return.svg"
                      alt="Image"
                      class="img-fluid"
                    />
                  </div>
                  <h3>Hoàn trả miễn phí</h3>
                  <p>
                    Có thể hoàn trả nếu hỏng hóc và hoàn toàn miễn phí sau 1
                    tháng.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="img-wrap slider">
              <img src="../images/P1.jpg" class="active" />
              <img src="../images/P2.jpg" />
              <img src="../images/P3.jpg" />
              <img src="../images/P4.jpg" />
              <img src="../images/P5.jpg" />
              <img src="../images/P6.jpg" />
              <img src="../images/P7.jpg" />
            </div>
            <script src="../../js/slider.js"></script>
          </div>
        </div>
      </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start Popular Product Section -->
    <footer class="footer-section">
      <div class="container relative">
        <div class="sofa-img">
          <img
            src="../images/senda.png"
            style="scale: 0.6"
            alt="Image"
            class="img-fluid"
          />
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="subscription-form">
              <h3 class="d-flex align-items-center">
                <span class="me-1"
                  ><img
                    src="../images/envelope-outline.svg"
                    alt="Image"
                    class="img-fluid"
                /></span>
                <span>Admin</span>
              </h3>
            </div>
          </div>
        </div>

        <div class="border-top copyright">
          <div class="row pt-4">
            <div class="col-lg-6">
              <p class="mb-2 text-center text-lg-start">
                Copyright &copy;
                <script>
                  document.write(new Date().getFullYear());
                </script>
                . All Rights Reserved. — Designed with love by
                <a href="admin/group7.html">Group 7.co</a>
              </p>
            </div>

            <div class="col-lg-6 text-center text-lg-end">
              <ul class="list-unstyled d-inline-flex ms-auto">
                <li class="me-4"><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- End Footer Section -->
  </body>
</html>
