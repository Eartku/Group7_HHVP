<?php
class AdminSellController extends Controller {

    public function index(): void {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $categories  = CategoryModel::getAllActive();
        $total       = ProductModel::countAll(0, $search); // tổng để tính số trang
        $totalPages  = max(1, (int)ceil($total / $limit));
        $products    = ProductModel::getListAdmin(0, $limit, $offset, $search);

        $this->adminView('admin/sell/index', [
            'categories' => $categories,
            'products'   => $products,
            'search'     => $search,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
        ]);
    }

    public function update(): void {
        $this->requireAdmin();

        $type        = $_POST['type']        ?? '';
        $id          = (int)($_POST['id']    ?? 0);
        $profit_rate = (float)($_POST['profit_rate'] ?? 0);

        if ($id <= 0) {
            $this->redirect(BASE_URL . '/index.php?url=admin-sell&error=1');
            return;
        }

        if ($type === 'product') {
            // Cập nhật profit_rate của sản phẩm
            $db   = Database::getInstance();
            $stmt = $db->prepare("UPDATE products SET profit_rate = ? WHERE id = ?");
            $stmt->bind_param("di", $profit_rate, $id);
            $stmt->execute();

        } elseif ($type === 'category') {
            // Cập nhật profit_rate toàn bộ sản phẩm trong category
            $db   = Database::getInstance();
            $stmt = $db->prepare("UPDATE products SET profit_rate = ? WHERE category_id = ?");
            $stmt->bind_param("di", $profit_rate, $id);
            $stmt->execute();
        }

        $tab = $type === 'category' ? 'loai' : 'sanpham';
        $this->redirect(BASE_URL . '/index.php?url=admin-sell&tab=' . $tab . '&updated=1');
    }
}