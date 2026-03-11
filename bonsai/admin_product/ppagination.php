<?php if ($totalPages > 1): ?>
    <div class="d-flex justify-content-center mt-4">

        <?php
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = http_build_query($queryParams);
        $queryString = $queryString ? '?' . $queryString . '&' : '?';
        ?>

        <?php if ($page > 1): ?>
            <a href="<?= $queryString ?>page=<?= $page - 1 ?>"
               class="btn btn-sm btn-outline-dark mx-1">
                &laquo; Trước
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= $queryString ?>page=<?= $i ?>"
               class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= $queryString ?>page=<?= $page + 1 ?>"
               class="btn btn-sm btn-outline-dark mx-1">
                Sau &raquo;
            </a>
        <?php endif; ?>

    </div>
<?php endif; ?>