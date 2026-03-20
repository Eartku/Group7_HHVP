<?php
class AdminDashboardController extends Controller { // ← bỏ __ __

    public function index(): void {
        $this->requireAdmin();

        $totalUsers      = UserModel::count();
        $totalProducts   = ProductModel::count();
        $totalOrders     = OrderModel::count();
        $totalCategories = count(CategoryModel::getAll()); // ← thêm

        $categoryStats   = $this->getCategoryStats(); // ← thêm
        $orderStats      = $this->getOrderStats();    // ← thêm

        $this->adminView('../admin/dashboard/', [ // ← sửa đường dẫn
            'totalUsers'      => $totalUsers,
            'totalProducts'   => $totalProducts,
            'totalOrders'     => $totalOrders,
            'totalCategories' => $totalCategories, // ← thêm
            'categoryStats'   => $categoryStats,   // ← thêm
            'orderStats'      => $orderStats,       // ← thêm
        ]);
    }

    private function getCategoryStats(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT c.name, COUNT(p.id) AS total
            FROM categories c
            LEFT JOIN products p
                ON p.category_id = c.id AND p.status = 'active'
            GROUP BY c.id
            ORDER BY total DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    private function getOrderStats(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT status, COUNT(*) AS total
            FROM orders
            GROUP BY status
        ");
        if (!$result) return [];

        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[$row['status']] = (int)$row['total'];
        }
        return $stats;
    }
}