<?php
class ShopController extends Controller {

    public function index(): void {
        

        $search     = trim($_GET['search']    ?? '');
        $categoryId = (int)($_GET['category'] ?? 0);
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $priceMin   = max(0, (int)($_GET['price_min'] ?? 0));
        $priceMax   = max(0, (int)($_GET['price_max'] ?? 0));
        $limit      = 8;
        $offset     = ($page - 1) * $limit;

        // Đảm bảo min không lớn hơn max
        if ($priceMin > 0 && $priceMax > 0 && $priceMin > $priceMax) {
            [$priceMin, $priceMax] = [$priceMax, $priceMin];
        }

        $catName = 'Tất cả sản phẩm';
        if ($categoryId > 0) {
            $cat = CategoryModel::getById($categoryId);
            if ($cat) $catName = $cat['name'];
        }

        $totalProducts = ProductModel::count($categoryId, $search, $priceMin, $priceMax);
        $totalPages    = max(1, ceil($totalProducts / $limit));
        $products      = ProductModel::getList($categoryId, $limit, $offset, $search, $priceMin, $priceMax);
        $categories    = CategoryModel::getAllActive();

        $this->view('shop', [
            'products'   => $products,
            'categories' => $categories,
            'catName'    => $catName,
            'categoryId' => $categoryId,
            'page'       => $page,
            'totalPages' => $totalPages,
            'search'     => $search,
            'priceMin'   => $priceMin,
            'priceMax'   => $priceMax,
        ]);
    }

    public function search(): void {
        $this->index();
    }
    public function suggest(): void {
        header('Content-Type: application/json');
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) { echo '[]'; return; }
        $products = ProductModel::suggest($q, 8);
        echo json_encode($products);
    }
}