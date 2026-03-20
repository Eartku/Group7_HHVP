<?php // From bonsai/admin_product/pproducts.php + pproduct_grid.php
$searchQuery = $search ? "?search=" . urlencode($search) . "&category=$categoryId" : "?category=$categoryId";
?>
<div class="product-section py-5">
    <div class="container">
        <h2>Quản Lý Sản Phẩm (Admin)</h2>
        
        <!-- Search & Filter -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="url" value="admin/products">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm sản phẩm..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-control">
                        <option value="0">Tất cả danh mục</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                </div>
            </div>
        </form>

        <!-- Products Grid -->
        <div class="row">
            <?php if (empty($products)): ?>
                <p>Không tìm thấy sản phẩm.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-item">
                            <img src="<?= htmlspecialchars($product['base_img'] ?? 'images/no-image.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <h5><?= htmlspecialchars($product['name']) ?></h5>
                            <p>Giá: <?= number_format($product['sale_price']) ?> VNĐ</p>
                            <p>Danh mục: <?= $product['category_name'] ?? 'N/A' ?></p>
                            <div class="btn-group">
                                <a href="?url=admin/products/edit&id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                                <a href="?url=admin/products/delete&id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination from bonsai/ppagination.php -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?url=admin/products&page=<?= $i ?>&<?= $searchQuery ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <a href="?url=admin/products/create" class="btn btn-success mt-3">Thêm sản phẩm mới</a>
    </div>
</div>

