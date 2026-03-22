<?php
class AdminProductController extends Controller {

    public function index(): void {
        $this->requireAdmin();
        $search      = trim($_GET['search']        ?? '');
        $categoryId  = (int)($_GET['category']     ?? 0);
        $statusFilter = trim($_GET['status_filter'] ?? '');
        $stockFilter  = trim($_GET['stock_filter']  ?? '');
        $page        = max(1, (int)($_GET['page']  ?? 1));
        $limit       = 8;
        $offset      = ($page - 1) * $limit;

        // Lấy tên category đang lọc để hiển thị filter tag
        $catName = '';
        if ($categoryId > 0) {
            $cat     = CategoryModel::getById($categoryId);
            $catName = $cat['name'] ?? '';
        }

        $total      = ProductModel::countAll($categoryId, $search, $statusFilter, $stockFilter);
        $totalPages = max(1, (int)ceil($total / $limit));
        $products   = ProductModel::getListAdmin($categoryId, $limit, $offset, $search, $statusFilter, $stockFilter);
        $categories = CategoryModel::getAll();

        $this->adminView('admin/products/index', [
            'products'     => $products,
            'categories'   => $categories,
            'search'       => $search,
            'categoryId'   => $categoryId,
            'catName'      => $catName,
            'statusFilter' => $statusFilter,
            'stockFilter'  => $stockFilter,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'total'        => $total,
        ]);
    }
    public function restore(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            ProductModel::updateStatus($id, 'active');
        }
        $this->redirect(BASE_URL . '/index.php?url=admin-products');
    }

    public function create(): void {
        $this->requireAdmin();
        $error = '';
        $data  = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => trim($_POST['name']        ?? ''),
                'category_id' => (int)($_POST['category']   ?? 0),
                'description' => trim($_POST['description'] ?? ''),
                'profit_rate' => (float)($_POST['profit_rate'] ?? 0),
                'status'      => $_POST['status'] ?? 'active',
                'image'       => '',
            ];

            // Upload ảnh
            if (!empty($_FILES['image']['name'])) {
                $imageName = time() . '_' . basename($_FILES['image']['name']);
                $target    = __DIR__ . '/../../images/' . $imageName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $data['image'] = $imageName;
                }
            }

            if (empty($data['name'])) {
                $error = 'Vui lòng nhập tên sản phẩm.';
            } else {
                $ok = ProductModel::create($data);
                if ($ok) {
                    $this->redirect(BASE_URL . '/index.php?url=admin-products&created=1');
                    return;
                }
                $error = 'Thêm sản phẩm thất bại. Vui lòng thử lại.';
            }
        }

        $this->adminView('admin/products/create', [
            'categories' => CategoryModel::getAll(),
            'data'       => $data,
            'error'      => $error,
        ]);
    }

    public function edit(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(404);
            $this->abort(404);
            return;
        }

        $product = ProductModel::getById($id);
        if (!$product) {
            http_response_code(404);
            $this->abort(404);
            return;
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'        => trim($_POST['name']          ?? ''),
                'category_id' => (int)($_POST['category']     ?? 0),
                'description' => trim($_POST['description']   ?? ''),
                'profit_rate' => (float)($_POST['profit_rate'] ?? 0),
                'status'      => $_POST['status'] ?? 'active',
            ];

            // Upload ảnh mới
            if (!empty($_FILES['new_image']['name'])) {
                $imageName = time() . '_' . basename($_FILES['new_image']['name']);
                $target    = __DIR__ . '/../../images/' . $imageName;
                if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target)) {
                    ProductModel::updateImage($id, $imageName);
                }
            }

            $ok = ProductModel::update($id, $data);
            if ($ok) {
                $success = 'Cập nhật sản phẩm thành công.';
                $product = ProductModel::getById($id); // reload
            } else {
                $error = 'Cập nhật thất bại. Vui lòng thử lại.';
            }
        }

        // Tính tổng tồn kho
        $sizes = ProductModel::getSizes($id);
        $stock = array_sum(array_column($sizes, 'stock'));

        $this->adminView('admin/products/edit', [
            'product'    => $product,
            'categories' => CategoryModel::getAll(),
            'stock'      => $stock,
            'success'    => $success,
            'error'      => $error,
        ]);
    }

    public function delete(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            ProductModel::updateStatus($id, 'inactive');
        }
        $this->redirect(BASE_URL . '/index.php?url=admin-products');
    }
    public static function updateStatus(int $id, string $status): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

}