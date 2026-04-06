<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/autoload.php';
require_once __DIR__ . '/core/Database.php';

$url    = '/' . trim($_GET['url'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

switch ($url) {

    // ── Public ──
    case '/':
        $c = new GuestController();
        $c->index();
        break;

    case '/login':
        $c = new AuthController();
        $method === 'POST' ? $c->login() : $c->loginForm();
        break;

    case '/register':
        $c = new AuthController();
        $method === 'POST' ? $c->register() : $c->registerForm();
        break;

    case '/logout':
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php');
        exit();

    // ── User ──
    case '/home':
        $c = new HomeController();
        $c->index();
        break;

    case '/profile':
        $c = new ProfileController();
        if ($method === 'POST' && isset($_POST['update_profile'])) {
            $c->update();
        } elseif ($method === 'POST' && isset($_POST['change_password'])) {
            $c->changePassword();
        } else {
            $c->index();
        }
        break;

    case '/shop':
        $c = new ShopController();
        $c->index();
        break;

    case '/search':
        $c = new ShopController();
        $c->search();
        break;
    case '/shop-suggest':
        $c = new ShopController();
        $c->suggest();
        break;

    case '/product-detail':
        $c = new ProductController();
        $c->detail();
        break;
    case  '/product-sizes':
        $c = new ProductController();
        $c->sizes();
        break;
    // ── Cart ──
    case '/cart':
        $c = new CartController();
        $c->index();
        break;

    case '/cart-add':
        header('Content-Type: application/json');
        $productId = (int)($_POST['product_id'] ?? 0);
        $sizeId    = (int)($_POST['size_id']    ?? 0);
        $qty       = max(1, (int)($_POST['qty'] ?? 1));
        if ($productId > 0) {
            $userId = $_SESSION['user']['id'] ?? 0;
            $cartId = CartModel::getOrCreate($userId);
            $ok     = CartModel::addItem($cartId, $productId, $sizeId, $qty);
            echo json_encode([
                'ok'      => $ok,
                'success' => $ok,
                'message' => $ok ? 'Đã thêm vào giỏ hàng!' : 'Thêm thất bại',
            ]);
        } else {
            echo json_encode(['ok' => false, 'success' => false, 'message' => 'Sản phẩm không hợp lệ']);
        }
        exit;

    // ── Checkout ──
    case '/checkout':
        $c = new CheckoutController();
        $method === 'POST' ? $c->process() : $c->index();
        break;

    case '/checkout-thankyou':
        $c = new CheckoutController();
        $c->thankyou();
        break;

    // ── Orders ──
    case '/orders-history':
        $c = new OrderController();
        $c->index();
        break;

    case '/orders-detail':
        $c = new OrderController();
        $c->detail();
        break;

    // ── API ──
    case '/api-sizes':
        header('Content-Type: application/json');
        $productId = (int)($_GET['product_id'] ?? 0);
        echo json_encode($productId > 0 ? ProductModel::getSizes($productId) : []);
        exit;

    // ── Admin Auth ──
    case '/admin-login':
        $c = new AuthController();
        $method === 'POST' ? $c->adminLogin() : $c->adminLoginForm();
        break;

    // ── Admin Dashboard ──
    case '/admin':
        $c = new AdminDashboardController();
        $c->index();
        break;

    // ── Admin Customers ──
    case '/admin-customers':
        $c = new AdminCustomerController();
        $c->index();
        break;

    case '/admin-customers-create':
        $c = new AdminCustomerController();
        $method === 'POST' ? $c->create() : $c->create();
        break;

    case '/admin-customers-edit':
        $c = new AdminCustomerController();
        $c->edit();
        break;

    case '/admin-customers-delete':
        $c = new AdminCustomerController();
        $c->delete();
        break;

    // ── Admin Products ──
    case '/admin-products':
        $c = new AdminProductController();
        $c->index();
        break;

    case '/admin-products/create':
        $c = new AdminProductController();
        $c->create();
        break;

    case '/admin-products/edit':
        $c = new AdminProductController();
        $c->edit();
        break;

    case '/admin-products/delete':
        $c = new AdminProductController();
        $c->delete();
        break;

    // ── Admin Inventory ──
    case '/admin-inventory':
        $c = new AdminInventoryController();
        $c->index();
        break;

    case '/admin-inventory-create':
        $c = new AdminInventoryController();
        $method === 'POST' ? $c->store() : $c->create();
        break;

    case '/admin-inventory-detail':
        $c = new AdminInventoryController();
        $c->detail();
        break;

    case '/admin-inventory-edit':
        $c = new AdminInventoryController();
        $c->edit();
        break;

    case '/admin-inventory-update':
        $c = new AdminInventoryController();
        $c->update();
        break; 
    case '/admin-inventory-confirm':
        $c = new AdminInventoryController();
        $c->confirm();
        break;

    case '/admin-inventory-cancel':
        $c = new AdminInventoryController();
        $c->cancel();
        break;
    case '/admin-orders':
        $c = new AdminOrderController();
        $c->index();
        break;

    case '/admin-orders-detail':
        $c = new AdminOrderController();
        $c->detail();
        break;

    case '/admin-orders-update':
        $c = new AdminOrderController();
        $c->updateStatus();
        break;

    case '/admin-sell':
        $c = new AdminSellController();
        $method === 'POST' ? $c->update() : $c->index();
        break;
    case '/admin-products/restore':
        $c = new AdminProductController();
        $c->restore();
        break;
    case '/admin-sizes':
        $c = new AdminSizeController();
        $method === 'POST' ? $c->create() : $c->index();
        break;

    case '/admin-sizes-edit':
        $c = new AdminSizeController();
        $c->edit();
        break;

    case '/admin-sizes-delete':
        $c = new AdminSizeController();
        $c->delete();
        break;
    case '/admin-categories':
        $c = new AdminCategoryController();
        $c->index();
        break;
    case '/admin-categories-create':
        $c = new AdminCategoryController();
        $c->create();
        break;

    case '/admin-categories-edit':
        $c = new AdminCategoryController();
        $c->edit();
        break;

    case '/admin-categories-delete':
        $c = new AdminCategoryController();
        $c->delete();
        break;
    case '/admin-categories-restore':
        $c = new AdminCategoryController();
        $c->restore();
        break;
    case '/admin-products/destroy':
        $c = new AdminProductController();
        $c->destroy();
        break;
    // ── 404 ──
    default:
        http_response_code(404);
        include __DIR__ . '/../app/views/errors/404.php';
        break;
}
?>