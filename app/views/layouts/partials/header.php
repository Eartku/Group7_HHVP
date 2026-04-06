<nav class="custom-navbar navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>../index.php?url=home">BonSai<span>🌱</span></a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarsFurni">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsFurni">

            <!-- CATEGORIES -->
            <ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0" style="margin-right: 50px;">
                <li class="nav-item hover-text-glow">
                    <a class="nav-link <?= !isset($_GET['category']) || $_GET['category'] == 0 ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/index.php?url=shop"
                       style="font-size:18px; color:white; font-family:'Times New Roman',Times,serif;">
                        Tất cả
                    </a>
                </li>
                <?php foreach (CategoryModel::getAllActive() as $cat): ?>
                <li class="nav-item hover-text-glow">
                    <a class="nav-link <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active' : '' ?>"
                       href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $cat['id'] ?>"
                       style="font-size:18px; color:white; font-family:'Times New Roman',Times,serif;">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- CTA -->
            <ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">

                <?php if (isset($_SESSION['user'])): ?>
                    <!-- ĐÃ LOGIN -->
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?url=cart" style="width:80px;">
                            <span style="font-size:20px; color:white; font-family:'Times New Roman',Times,serif;" class="hover-text-glow">Giỏ hàng</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?url=shop" style="width:80px; margin-left:20px;">
                            <span style="font-size:20px; color:white; font-family:'Times New Roman',Times,serif;" class="hover-text-glow">Mua sắm</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <input type="checkbox" id="user-toggle" hidden />
                        <label for="user-toggle" class="hover-box-2 hover-text-glow"
                               style="width:30px; height:30px; border-radius:50%; position:relative;">
                            <?php
                                $raw = UserModel::getAvatar($_SESSION['user']['id']);
                                $avatarSrc = BASE_URL . '../' . $raw;
                            ?>
                            <img src="<?= htmlspecialchars($avatarSrc) ?>"
                                 style="width:30px;height:30px;border-radius:50%;object-fit:cover;" />
                        </label>
                        <ul class="dropdown">
                            <li><a href="<?= BASE_URL ?>/index.php?url=profile">Hồ sơ cá nhân</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=orders-history">Lịch sử đơn hàng</a></li>
                            <li><a href="<?= BASE_URL ?>/index.php?url=logout">Đăng xuất</a></li>
                        </ul>
                    </li>

                <?php else: ?>
                    <!-- CHƯA LOGIN -->
                    <li>
                        <a href="#" onclick="showLoginAlert(event)" style="width:80px;">
                            <span style="font-size:20px; color:white; font-family:'Times New Roman',Times,serif;" class="hover-text-glow">Giỏ hàng</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?url=login" style="width:80px; margin-left:20px;">
                            <span style="font-size:20px; color:white; font-family:'Times New Roman',Times,serif;" class="hover-text-glow">Đăng nhập</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?url=register" style="width:80px; margin-left:20px;">
                            <span style="font-size:20px; color:white; font-family:'Times New Roman',Times,serif;" class="hover-text-glow">Đăng ký</span>
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<!-- Modal chặn guest -->
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
            <a href="<?= BASE_URL ?>/index.php?url=login" class="btn btn-dark">Đăng nhập</a>
            <a href="<?= BASE_URL ?>/index.php?url=register" class="btn btn-outline-dark">Đăng ký</a>
        </div>
        <button onclick="closeLoginModal()"
                style="margin-top:14px; background:none; border:none; color:#aaa; font-size:13px; cursor:pointer">
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
document.getElementById('loginModal').addEventListener('click', function(e) {
    if (e.target === this) closeLoginModal();
});

// Dropdown avatar
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