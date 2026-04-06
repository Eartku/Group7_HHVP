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
                    <a href="<?= BASE_URL ?>/index.php?url=cart" style="width: 80px">
                        <span style= "font-size:20px; color: white; font-family: 'Times New Roman', Times, serif;" class="hover-text-glow">Giỏ hàng</span>
                    </a>
                </li>

                <li>
                    <a href="<?= BASE_URL ?>/index.php?url=shop" style="width: 80px; margin-left: 20px;">
                        <span style= "font-size:20px; color: white; font-family: 'Times New Roman', Times, serif;" class="hover-text-glow">Mua sắm</span>
                    </a>
                </li>

                <li class="nav-item">
                    <input type="checkbox" id="user-toggle" hidden />

                    <label for="user-toggle" class="hover-box-2 hover-text-glow" style="width:30px; height: 30px; border-radius: 50%; position:relative;">
        
                        <?php
                        $avatarSrc = BASE_URL . '../images/user.png';

                        if (isset($_SESSION['user'])) {
                            $raw = UserModel::getAvatar($_SESSION['user']['id']);
                            $avatarSrc = BASE_URL . '../' . $raw;
                        }
                        ?>
                        <img src="<?= htmlspecialchars($avatarSrc) ?>"
                            style="width:30px;height:30px;border-radius:50%;object-fit:cover;" />
                    </label>

                    <ul class="dropdown">
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="<?= BASE_URL ?>/index.php?url=profile">Hồ sơ cá nhân</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=orders-history">Lịch sử đơn hàng</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=logout">Đăng xuất</a></li>
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