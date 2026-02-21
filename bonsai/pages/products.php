<?php
$category_id = $_GET['category'] ?? 0;
$page = $_GET['page'] ?? 1;

include "../includes/product_query.php";
?>

<div class="product-section py-5">
    <div class="container">
        <div class="row">
            <?php include "../includes/product_grid.php"; ?>
        </div>

        <?php include "../includes/pagination.php"; ?>
    </div>
</div>

<script src="../js/cartmsg.js"></script>