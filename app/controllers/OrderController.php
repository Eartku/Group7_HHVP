<?php
class OrderController extends Controller {

    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];
        $orders = OrderModel::getByUserId($userId);
        $pages  = OrderModel::groupByStatus($orders);
        $all = array_sum($pages);

        $this->view('order/history', [
            'pages' => $pages,
            'all' => $all
        ]);
    }

    public function detail(): void {
        $this->requireLogin();
        $orderId = (int)($_GET['id'] ?? 0);
        $userId  = $_SESSION['user']['id'];

        // ✅ Validate id trước
        if ($orderId <= 0) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            return;
        }

        $order = OrderModel::getById($orderId);

        // ✅ Không tồn tại hoặc không thuộc user này
        if (!$order || (int)$order['user_id'] !== (int)$userId) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
            $ok = OrderModel::cancel($orderId, $userId);
            $this->redirect(
                BASE_URL . '/index.php?url=orders-detail&id=' . $orderId
                . ($ok ? '&cancelled=1' : '&error=1')
            );
            return;
        }

        $items    = OrderModel::getItems($orderId);
        $subtotal = array_sum(array_column($items, 'row_total'));
        $badge    = OrderModel::getStatusBadge($order['status']);

        $this->view('order/detail', [
            'order'    => $order,
            'items'    => $items,
            'subtotal' => $subtotal,
            'badge'    => $badge,
        ]);
    }
}