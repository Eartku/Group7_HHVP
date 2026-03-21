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

        $shippingFee = 50000;
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

                $validatedItems = [];

                foreach ($items as $item) {

                    $productId = $item['product_id'] ?? $item['id'] ?? 0;
                    $sizeId    = $item['size_id'];
                    $qty       = $item['quantity'];

                    $product = ProductModel::findById($productId);
                    if (!$product) {
                        throw new Exception("Sản phẩm '{$item['name']}' không còn tồn tại.");
                    }
                    $stock = ProductModel::getStock($productId, $sizeId);

                    if ($stock <= 0) {
                        throw new Exception("Sản phẩm '{$item['name']}' đã hết hàng.");
                    }
                    if ($stock < $qty) {
                        throw new Exception("Sản phẩm '{$item['name']}' chỉ còn {$stock} sản phẩm.");
                    }
                    $validatedItems[] = [
                        'product_id' => $productId,
                        'size_id'    => $sizeId,
                        'name'       => $item['name'],
                        'quantity'   => $qty,
                        'price'      => $item['price'],
                        'subtotal'   => $item['price'] * $qty,
                        ];
                    }

                    $orderId = CheckoutModel::placeOrder(
                        $userId,
                        compact('fullname', 'email', 'phone', 'address', 'note', 'payment'),
                        $validatedItems,
                        $shippingFee
                    );
                    if ($orderId) {
                        CartModel::clear($cartId);
                        $this->redirect(BASE_URL . "/index.php?url=checkout-thankyou&id={$orderId}");
                    } else {
                        throw new Exception("Đặt hàng thất bại. Vui lòng thử lại.");
                    }
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
    public function process(): void {
        // Chuyển hướng POST về index() để xử lý
        $this->index();
    }
    public function thankyou(): void {
        $this->requireLogin();
        
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$orderId) {
            $this->redirect(BASE_URL . '/index.php?url=shop');
            return;
        }
        
        // Kiểm tra order thuộc về user đang đăng nhập
        $order = OrderModel::getById($orderId);
        if (!$order || $order['user_id'] !== (int)$_SESSION['user']['id']) {
            $this->redirect(BASE_URL . '/index.php?url=shop');
            return;
        }
        
        $this->view('checkout/thankyou', [
            'orderId' => $orderId,
            'order'   => $order,
        ]);
    }
}