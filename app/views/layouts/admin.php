<?php
$noLayout = isset($noLayout) && $noLayout === true;
$noHeader = isset($noHeader) && $noHeader === true;

if ($noLayout) {
    echo $content ?? '';
    return;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
    <title><?= $pageTitle ?? 'Admin – BonSai' ?></title>
</head>
<body>
    <?php if (!$noHeader): ?>
        <?php include __DIR__ . '/partials/admin_header.php'; ?>
    <?php endif; ?>

    <div class="admin-wrapper">
        <?= $content ?? '' ?>
    </div>

    <?php if (!$noHeader): ?>
        <?php include __DIR__ . '/partials/admin_footer.php'; ?>
    <?php endif; ?>
</body>
</html>