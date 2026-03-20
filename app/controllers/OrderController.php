<?php
class OrderController extends Controller {

    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];
        $orders = OrderModel::getByUserId($userId);
        $pages  = OrderModel::groupByStatus($orders);

        $this->view('order/history', [
            'pages' => $pages,
        ]);
    }

    public function detail(): void {
        $this->requireLogin();
        $orderId = (int)($_GET['id'] ?? 0);
        $userId  = $_SESSION['user']['id'];

        $order = OrderModel::getById($orderId);
        if (!$order || $order['user_id'] != $userId) {
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
        // row_total được tính trong OrderModel::getItems() qua price * quantity
        $subtotal = array_sum(array_column($items, 'row_total'));
        $badge    = OrderModel::getStatusBadge($order['status']);

        $this->view('order/detail', [
            'order'    => $order,   // sửa lỗi syntax 'order' Asc =>
            'items'    => $items,
            'subtotal' => $subtotal,
            'badge'    => $badge,
        ]);
    }
}