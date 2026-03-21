<?php
// Admin Product Controller - CRUD from bonsai/admin_product/*
class AdminProductController extends Controller {
    public function index(): void {
        // Bonsai pproducts.php logic: query + pagination (fixed SQLi)
        $this->requireAdmin();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $categoryId = (int)($_GET['category'] ?? 0);
        $search = trim($_GET['search'] ?? '');

        $db = Database::getInstance();

        // Prepared where for category/search
        $whereParams = [];
        $whereClause = [];
        if ($categoryId > 0) {
            $whereClause[] = 'category_id = ?';
            $whereParams[] = $categoryId;
        }
        if ($search) {
            $whereClause[] = 'name LIKE ?';
            $whereParams[] = "%$search%";
        }
        $whereSql = $whereClause ? 'WHERE ' . implode(' AND ', $whereClause) : '';

        $countSql = "SELECT COUNT(*) as total FROM products $whereSql";
        $stmt = $db->prepare($countSql);
        if (!empty($whereParams)) {
            $types = str_repeat('s', count($whereParams));
            $stmt->bind_param($types, ...$whereParams);
        }
        $stmt->execute();
        $totalProducts = $stmt->get_result()->fetch_assoc()['total'];
        $totalPages = ceil($totalProducts / $limit);

        $listSql = "SELECT * FROM products $whereSql ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($listSql);
        $types = (str_repeat('s', count($whereParams))) . 'ii';
        $params = array_merge($whereParams, [$limit, $offset]);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $categories = CategoryModel::getAll();

        $this->view('admin/products', [
            'products' => $products,
            'categories' => $categories,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'categoryId' => $categoryId
        ]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $image = $_POST['image'] ?? ''; // Handle upload later
            $description = $_POST['description'];

            ProductModel::create([
                'name' => $name,
                'category_id' => $category_id,
                'image' => $image,
                'description' => $description
            ]);

            $this->redirect('/app/index.php?url=admin-products');
        }

        $categories = CategoryModel::getAll();
        $this->view('admin/products/create', ['categories' => $categories]);
    }

}
?>

