<?php
$category_id = $_GET['category'] ?? 0;
$page = $_GET['page'] ?? 1;

include "pproduct_query.php";
?>

<div class="product-section py-5">
    <div class="container">
        <div class="row">
            <?php include "pproduct_grid.php"; ?>
        </div>

        <?php include "ppagination.php"; ?>
    </div>
</div>

<script src="../js/cartmsg.js"></script>