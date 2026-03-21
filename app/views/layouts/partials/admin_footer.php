<!-- Wave transition -->
<div class="footer-wave">
</div>

<footer class="footer-section">

    <!-- Floating plant silhouette -->
    <img src="<?= BASE_URL ?>/images/senda.png"
         class="footer-plant" alt="">

    <div class="footer-body">
        <div class="container">
            <div class="row g-5">

                <!-- ── Brand ── -->
                <div class="col-lg-3 col-md-6">
                    <a href="<?= BASE_URL ?>/index.php?url=home" class="footer-logo">
                        BonSai<span style="-webkit-text-fill-color:#6b7c2e">.</span>
                    </a>
                    <p class="footer-tagline">
                        Mang đến không gian xanh tươi, thanh bình cho ngôi nhà của bạn.
                    </p>
                    <div class="social-row">
                        <a href="#" class="social-btn" title="Facebook">
                            <span class="fa fa-brands fa-facebook-f"></span>
                        </a>
                        <a href="#" class="social-btn" title="Instagram">
                            <span class="fa fa-brands fa-instagram"></span>
                        </a>
                        <a href="#" class="social-btn" title="Twitter">
                            <span class="fa fa-brands fa-twitter"></span>
                        </a>
                        <a href="#" class="social-btn" title="LinkedIn">
                            <span class="fa fa-brands fa-linkedin"></span>
                        </a>
                    </div>
                </div>

    <hr class="footer-divider">

    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start">
                    <p>
                        Copyright &copy; <span id="year"></span>
                        BonSai — Designed by
                        <a href="pages/group7.html">Group 7</a>
                        🌱
                    </p>
                </div>
                <div class="col-lg-6">
                    <ul class="footer-bottom-links">
                        <li><a href="#">Điều khoản sử dụng</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Hỗ trợ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const yearSpan = document.getElementById("year");
    if (yearSpan) yearSpan.textContent = new Date().getFullYear();

    const feedbackForm = document.getElementById("feedbackForm");
    if (feedbackForm) {
        feedbackForm.addEventListener("submit", e => {
            e.preventDefault();
            const emailInput = feedbackForm.querySelector('input[type="email"]');
            const email = emailInput?.value.trim() ?? '';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                emailInput?.focus();
                return;
            }
            const btn = feedbackForm.querySelector('.newsletter-btn');
            btn.textContent = '✓ Đã đăng ký!';
            btn.style.background = '#6b7c2e';
            emailInput.value = '';
            setTimeout(() => {
                btn.textContent = 'Đăng ký';
                btn.style.background = '';
            }, 3000);
        });
    }
});
</script>

<script src="<?= BASE_URL ?>/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/js/tiny-slider.js"></script>
<script src="<?= BASE_URL ?>/js/custom.js"></script>