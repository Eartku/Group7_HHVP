<?php
class CheckoutController extends Controller {

    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];

        $user   = UserModel::findById($userId);
        $cartId = CartModel::getOrCreate($userId);
        $items  = CartModel::getItems($cartId);

        if (empty($items)) {
            $this->redirect(BASE_URL . '/index.php?url=cart');
            return;
        }

        $shippingFee = 20000;
        $subtotal    = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $grandTotal  = $subtotal + $shippingFee;
        $error       = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email']    ?? '');
            $phone    = trim($_POST['phone']    ?? '');
            $note     = trim($_POST['note']     ?? '');
            $payment  = $_POST['payment']       ?? 'cod';

            $address = ($_POST['address_option'] ?? '') === 'new'
                ? trim($_POST['new_address']   ?? '')
                : trim($_POST['saved_address'] ?? '');

            if (!$fullname || !$email || !$phone || !$address) {
                $error = "Vui lòng nhập đầy đủ thông tin bắt buộc.";
            } else {
                try {
                    // Bỏ cart_id — order_items không có cột này
                    // product_id lấy nhất quán qua product_id (CartModel::getItems trả về key 'product_id')
                    $orderItems = array_map(function ($item) {
                        return [
                            'product_id' => $item['product_id'] ?? $item['id'] ?? 0,
                            'size_id'    => $item['size_id'],
                            'name'       => $item['name'],
                            'quantity'   => $item['quantity'],
                            'price'      => $item['price'],
                            'subtotal'   => $item['price'] * $item['quantity'],
                        ];
                    }, $items);

                    $orderId = CheckoutModel::placeOrder(
                        $userId,
                        compact('fullname', 'email', 'phone', 'address', 'note', 'payment'),
                        $orderItems,
                        $shippingFee
                    );

                    $this->redirect(BASE_URL . '/index.php?url=thankyou&id=' . $orderId);
                    return;

                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $this->view('checkout', [
            'user'        => $user,
            'items'       => $items,
            'subtotal'    => $subtotal,
            'shippingFee' => $shippingFee,
            'grandTotal'  => $grandTotal,
            'error'       => $error,
        ]);
    }
}