<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include '../includes/loader.php'; ?>
    <title>
        BonSai
    </title>
</head>

<body>
    <!-- Start Header/Navigation -->
    <?php include '../includes/header.php'; ?>
    <!-- End Header/Navigation -->
    <!-- Start Hero Section -->
    <div class="hero">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        <h1 class="hover-text-glow">BonSai - không gian xanh cho bạn!</h1>
                        <p class="mb-4 hover-text-glow-small">
                            "Trong mỗi bước đi cùng thiên nhiên, ta nhận được nhiều hơn
                            những gì mình tìm kiếm." - Trích John Muir
                        </p>

                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        <img src="../images/bluesucc.gif" class="img-fluid" style="margin-left: 80px; margin-top: 40px;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hero">

    </div>
    <!-- End Hero Section -->

    <!-- Start Product Section -->
    <h2 class="section-title" style="margin-top: 20px; text-align: center;">Tiêu biểu</h2>
    <?php include '../includes/show.php'; ?>

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
                                    <img src="../images/truck.svg" alt="Image" class="img-fluid" />
                                </div>
                                <h3>Nhanh chóng & Free Shipping</h3>
                                <p>Chúng tôi đảm bảo sản phẩm sẽ an toàn và toàn vẹn trước cửa nhà bạn.</p>
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
                                    <img src="../images/support.svg" alt="Image" class="img-fluid" />
                                </div>
                                <h3>Hỗ trợ 24/7</h3>
                                <p>Với đội ngũ chuyên nghiệp và ân cần không ngại khó khăn.</p>
                            </div>
                        </div>

                        <div class="col-6 col-md-6">
                            <div class="feature hover-div-zoom">
                                <div class="icon">
                                    <img src="../images/return.svg" alt="Image" class="img-fluid" />
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
                            src="../images/P1.jpg" class="active"
                        />
                        <img
                            src="../images/P2.jpg"
                        />
                        <img
                            src="../images/P3.jpg"
                        />
                        <img
                            src="../images/P4.jpg"
                        />
                        <img
                            src="../images/P5.jpg"
                        />
                        <img
                            src="../images/P6.jpg"
                        />
                        <img
                            src="../images/P7.jpg"
                        />
                    </div>
                    <script src="../js/slider.js"></script>
                </div>
            </div>
        </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start Popular Product Section -->
    <?php include '../includes/popular.php'; ?>
    <!-- End Popular Product Section -->

    <!-- Start Blog Section -->
    <?php include '../includes/blog.php'; ?>
    <!-- End Blog Section -->

    <!-- Start Footer Section -->
    <?php include '../includes/footer.php'; ?>
    <!-- End Footer Section -->

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/tiny-slider.js"></script>
    <script src="../js/custom.js"></script>
</body>
</html>
