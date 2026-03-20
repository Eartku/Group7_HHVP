<?php
class CartController extends Controller {

    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];
        $cartId = CartModel::getOrCreate($userId);

        // Xóa item
        if (isset($_GET['remove'])) {
            CartModel::removeItem((int)$_GET['remove'], $cartId);
            $this->redirect(BASE_URL . '/index.php?url=cart');
            return;
        }

        // Cập nhật cart
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $db->begin_transaction();
            try {
                foreach ($_POST['qty'] ?? [] as $itemId => $qty) {
                    $itemId = (int)$itemId;
                    $qty    = max(1, (int)$qty);
                    $sizeId = (int)($_POST['size_id'][$itemId] ?? 0);
                    if ($sizeId > 0) {
                        CartModel::updateItem($itemId, $cartId, $sizeId, $qty);
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
            }
            $this->redirect(BASE_URL . '/index.php?url=cart');
            return;
        }

        $items       = CartModel::getItems($cartId);
        $canCheckout = CartModel::canCheckout($cartId);
        $total       = array_sum(array_map(
            fn($i) => $i['price'] * $i['quantity'], $items
        ));

        $this->view('cart', [
            'items'       => $items,
            'total'       => $total,
            'canCheckout' => $canCheckout,
            'cartId'      => $cartId,
        ]);
    }
}