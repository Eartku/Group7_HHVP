<!--
* Bootstrap 5
* Template Name: Plantaris
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="author" content="Untree.co" />
    <link rel="shortcut icon" href="logo.png" />

    <meta name="description" content="" />
    <meta name="keywords" content="bootstrap, bonsai, plant, shop" />

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="css/tiny-slider.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/hover.css" rel="stylesheet" />
    <link href="css/animation.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="css/cartMessage.css"/>

    <title>BonSai | Home</title>
  </head>

  <body>
    
    <!-- Start Header/Navigation -->
     <?php include 'header.php'; ?>

    <!-- End Header/Navigation -->

    <!-- Start Hero Section -->
     <div class="hero">
    <div class="container">
      <div class="row justify-content-between">
        <div class="col-lg-5">
          <div class="intro-excerpt">
            <h1 class="hover-text-glow">BonSai - không gian xanh cho bạn!</h1>
            <p class="mb-4 hover-text-glow-small">
              “Trong mỗi bước đi cùng thiên nhiên, ta nhận được nhiều hơn
              những gì mình tìm kiếm.” - Trích John Muir
            </p>
            <?php include 'search.php'; ?>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="hero-img-wrap">
            <img src="images/bluesucc.gif" class="img-fluid" style="margin-left: 80px; margin-top: 40px;" />
          </div>
        </div>
      </div>
    </div>
  </div>
    <div class="hero">
      
    </div>
    <!-- End Hero Section -->

    <!-- Start Product Section -->
    <?php include 'review.php'; ?>
    <!-- End Product Section -->

    <!-- Start Why Choose Us Section -->
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
                <div class="icon">
                  <img src="images/truck.svg" alt="Image" class="img-fluid" />
                </div>
                <h3>Nhanh chóng & Free Shipping</h3>
                <p>Chúng tôi đảm bảo sản phẩm sẽ an toàn và toàn vẹn trước cửa nhà bạn.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature hover-div-zoom">
                <div class="icon">
                  <img src="images/bag.svg" alt="Image" class="img-fluid" />
                </div>
                <h3>Dễ dàng để mua</h3>
                <p>Đây là trang mua bán uy tín số một hàng đầu Việt Nam.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature hover-div-zoom">
                <div class="icon">
                  <img src="images/support.svg" alt="Image" class="img-fluid" />
                </div>
                <h3>Hỗ trợ 24/7</h3>
                <p>Với đội ngũ chuyên nghiệp và ân cần không ngại khó khăn.</p>
              </div>
            </div>

            <div class="col-6 col-md-6">
              <div class="feature hover-div-zoom">
                <div class="icon">
                  <img src="images/return.svg" alt="Image" class="img-fluid" />
                </div>
                <h3>Hoàn trả miễn phí</h3>
                <p>Có thể hoàn trả nếu hỏng hóc và hoàn toàn miễn phí sau 1 tháng.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
            <div class="img-wrap slider" >
              <img
                src="images/P1.jpg" class="active"
              />
              <img
                src="images/P2.jpg"
              />
              <img
                src="images/P3.jpg"
              />
              <img
                src="images/P4.jpg"
              />
              <img
                src="images/P5.jpg"
              />
              <img
                src="images/P6.jpg"
              />
              <img
                src="images/P7.jpg"
              />
            </div>
            <script src="../js/slider.js"></script>
          </div>
        </div>
      </div>
    </div>
  <!-- End Why Choose Us Section -->

  <!-- Start Popular Product Section -->
  <?php include 'popular.php'; ?>
  <!-- End Popular Product Section -->

    <!-- Start Blog Section -->
    <?php include 'blog.php'; ?>
    <!-- End Blog Section -->

    <!-- Start Footer Section -->
    <?php include 'footer.php'; ?>
    <!-- End Footer Section -->

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/tiny-slider.js"></script>
    <script src="js/custom.js"></script>
  </body>
</html>
