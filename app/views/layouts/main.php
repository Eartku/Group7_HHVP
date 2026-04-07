<?php
$categories = CategoryModel::getAll();
$userAvatar = isset($_SESSION['user'])
    ? UserModel::getAvatar($_SESSION['user']['id'])
    : 'images/user.png';

$noHeader = isset($noHeader) && $noHeader === true;
$noLayout = isset($noLayout) && $noLayout === true;
$appendix = isset($appendix) && $appendix === true;


// Nếu noLayout = true → render thẳng content, không có html wrapper
if ($noLayout) {
    echo $content ?? '';
    return;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'partials/head.php'; ?>
    <title><?= $pageTitle ?? 'BonSai' ?></title>
</head>
<body>
    <?php if (!$noHeader): ?>
        <?php include 'partials/header.php'; ?>
    <?php endif; ?>

    <?= $content ?? '' ?>
    <?php if ($appendix): ?>
        <?php include 'partials/section.php'; ?>
        <?php include 'partials/news.php'; ?>
    <?php endif; ?>
    <?php if (!$noHeader): ?>
        <?php include 'partials/footer.php'; ?>
    <?php endif; ?>
</body>
</html>