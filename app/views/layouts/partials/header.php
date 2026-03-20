<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>../index.php?url=home">BonSai<span>🌱</span></a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarsFurni">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">

            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
            </ul>

            <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">


                <li>
                    <input type="checkbox" id="cart-toggle" hidden />
                    <label for="cart-toggle" class="hover-box-2">
                        <div class="front"><img src="<?= BASE_URL ?>/images/cart.png" /></div>
                        <!-- ✅ link giỏ hàng -->
                        <a href="<?= BASE_URL ?>/index.php?url=cart" class="back">Giỏ hàng</a>
                    </label>
                </li>

                <li>
                    <input type="checkbox" id="search-toggle" hidden />
                    <label for="search-toggle" class="hover-box-2">
                        <div class="front"><img src="<?= BASE_URL ?>/images/bag.png" /></div>
                        <!-- ✅ link tìm kiếm -->
                        <a href="<?= BASE_URL ?>/index.php?url=shop" class="back">Mua sắm</a>
                    </label>
                </li>

                <li class="nav-item" style="margin-left:20px">
                    <input type="checkbox" id="user-toggle" hidden />
                    <label for="user-toggle" class="hover-box-2">
                        <div class="front">
                            <?php
                            $avatarSrc = BASE_URL . '/images/user.png'; // mặc định chưa login

                            if (isset($_SESSION['user'])) {
                                $raw = UserModel::getAvatar($_SESSION['user']['id']);
                                // getAvatar trả về "uploads/avatars/xxx.png" hoặc "images/user.png"
                                // → chỉ cần thêm BASE_URL ở đầu
                                $avatarSrc = BASE_URL . '/' . $raw;
                            }
                            ?>
                            <img src="<?= htmlspecialchars($avatarSrc) ?>"
                                 style="width:30px;height:30px;border-radius:50%;object-fit:cover;" />
                        </div>
                        <div class="back"><span>Người dùng</span></div>
                    </label>

                    <ul class="dropdown" style="background-color:black;">
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="<?= BASE_URL ?>/index.php?url=profile">Hồ sơ cá nhân</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=orders-history">Lịch sử đơn hàng</a></li>
                            <li>
                                <a href="<?= BASE_URL ?>/index.php?url=logout">
                                    <img src="<?= BASE_URL ?>/images/exit.svg"> Đăng xuất
                                </a>
                            </li>
                        <?php else: ?>
                            <li><a href="<?= BASE_URL ?>/index.php?url=login">Đăng nhập</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=register">Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
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
        if (cb.checked && !parentLi.matches(':hover')) startCloseTimer();
    });
    parentLi.addEventListener('mouseenter', () => clearTimeout(timer));
    parentLi.addEventListener('mouseleave', () => {
        if (cb.checked) startCloseTimer();
    });
});
</script>