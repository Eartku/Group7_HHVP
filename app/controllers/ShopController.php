<?php
class ShopController extends Controller {

    public function index(): void {
        $this->requireLogin();

        $search     = trim($_GET['search'] ?? '');
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page       = isset($_GET['page'])     ? (int)$_GET['page']     : 1;
        if ($page < 1) $page = 1;

        $limit  = 8;
        $offset = ($page - 1) * $limit;

        // Lấy tên category
        $catName = 'Tất cả sản phẩm';
        if ($categoryId > 0) {
            $cat = CategoryModel::getById($categoryId);
            if ($cat) $catName = $cat['name'];
        }

        // Đếm tổng + lấy sản phẩm
        $totalProducts = ProductModel::count($categoryId, $search);
        $totalPages    = ceil($totalProducts / $limit);
        $products = ProductModel::getList($categoryId, $limit, $offset, $search);

        $this->view('shop', [
            'products'   => $products,
            'catName'    => $catName,
            'categoryId' => $categoryId,
            'page'       => $page,
            'totalPages' => $totalPages,
        ]);
    }
}