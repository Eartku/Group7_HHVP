<?php
class ShopController extends Controller {

    public function index(): void {
        $this->requireLogin();

        $search     = trim($_GET['search'] ?? '');
        $categoryId = (int)($_GET['category'] ?? 0);
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $limit      = 8;
        $offset     = ($page - 1) * $limit;

        $catName = 'Tất cả sản phẩm';
        if ($categoryId > 0) {
            $cat = CategoryModel::getById($categoryId);
            if ($cat) $catName = $cat['name'];
        }

        $totalProducts = ProductModel::count($categoryId, $search);
        $totalPages    = ceil($totalProducts / $limit);
        $products      = ProductModel::getList($categoryId, $limit, $offset, $search);
        $categories    = CategoryModel::getAll();

        $this->view('shop', [
            'products'   => $products,
            'categories' => $categories,
            'catName'    => $catName,
            'categoryId' => $categoryId,
            'page'       => $page,
            'totalPages' => $totalPages,
            'search'     => $search,
        ]);
    }

    public function search(): void {
        $this->index();
    }
}