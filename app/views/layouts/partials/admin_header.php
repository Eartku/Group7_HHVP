<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark"
     aria-label="Bonsai navigation bar">
    <div class="container">

        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php?url=admin">
            Admin
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarsFurni"
                aria-controls="navbarsFurni"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">

            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">

                <?php
                $currentUrl = $_GET['url'] ?? '';
                $navItems = [
                    'admin-customers'        => 'KHÁCH HÀNG',
                    'admin-categories'       => 'LOẠI SẢN PHẨM',
                    'admin-sizes'            => 'KÍCH CỠ',
                    'admin-products'         => 'SẢN PHẨM',
                    'admin-inventory-create' => 'NHẬP SẢN PHẨM',
                    'admin-sell'             => 'GIÁ BÁN',
                    'admin-orders'           => 'ĐƠN HÀNG',
                    'admin-inventory'        => 'KHO',
                ];
                foreach ($navItems as $url => $label):
                    $isActive = str_starts_with($currentUrl, $url);
                ?>
                <li>
                    <a href="<?= BASE_URL ?>/index.php?url=<?= $url ?>"
                       class="hover-text-glow-small <?= $isActive ? 'active' : '' ?>"
                       style="font-size:13px">
                        <?= $label ?>
                    </a>
                </li>
                <?php endforeach; ?>

            </ul>

            <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">

                <!-- Trang chủ admin -->

                <!-- Tên admin đang đăng nhập -->
                <li class="d-flex align-items-center ms-3">
                    <span class="text-white small">
                        <?= 'Admin: '.htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?>
                    </span>
                </li>

                <!-- Đăng xuất -->
                <li style="margin-left:10px">
                    <a class="nav-link hover-box"
                       href="<?= BASE_URL ?>/index.php?url=logout"
                       onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                        <div class="front">
                            <img src="<?= BASE_URL ?>/images/exit.svg">
                        </div>
                        <div class="back"><span>Đăng xuất</span></div>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>