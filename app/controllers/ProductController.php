<?php
class ProductController extends Controller {

    public function index(): void {
        $this->requireLogin();

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
        $totalProducts = ProductModel::count($categoryId);
        $totalPages    = ceil($totalProducts / $limit);
        $products      = ProductModel::getList($categoryId, $limit, $offset);

        $this->view('shop', [
            'products'   => $products,
            'catName'    => $catName,
            'categoryId' => $categoryId,
            'page'       => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function detail(): void {
        $this->requireLogin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            return;
        }

        $product = ProductModel::getDetail($id);
        if (!$product) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            return;
        }

        $sizes      = ProductModel::getSizes($id);
        $images     = ProductModel::getImages($id);
        $related    = ProductModel::getRelated($id);
        $totalStock = array_sum(array_column($sizes, 'quantity'));
        $mainImage  = !empty($images[0]) ? $images[0] : $product['base_img'];

        $this->view('shop/detail', [
            'product'    => $product,
            'sizes'      => $sizes,
            'images'     => $images,
            'mainImage'  => $mainImage,
            'related'    => $related,
            'totalStock' => $totalStock,
        ]);
    }
}