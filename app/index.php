<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();


require_once __DIR__ . '../config/app.php';
require_once __DIR__ . '../config/database.php'; 
require_once __DIR__ . '../core/autoload.php';
require_once __DIR__ . '../core/Database.php';

$url    = '/' . trim($_GET['url'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];
switch ($url) {
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
        // 1. Xóa data session
        $_SESSION = [];
        // 2. Xóa cookie PHPSESSID trên browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        // 3. Hủy session trên server
        session_destroy();
        // 4. Redirect về login — dùng constant, không hardcode
        header('Location: ../app/index.php');
        exit();

    case '/home':
        $c = new HomeController();
        $c->index();
        break;

    case '/profile':
        $c = new ProfileController();
        $method === 'POST' && isset($_POST['update_profile'])
            ? $c->update()
            : ($method === 'POST' && isset($_POST['change_password'])
                ? $c->changePassword()
                : $c->index());
        break;

    case '/orders-history':
        $c = new OrderController();
        $c->index();
        break;

    case '/cart':
        $c = new CartController();
        $c->index();
        break;

    case '/shop':
        $c = new ShopController();
        $c->index();
        break;

    case '/admin-products':
        $c = new AdminProductController();
        $method === 'POST' ? $c->create() : $c->index();
        break;

    case '/search':
        $c = new ShopController();
        $c->search();
        break;

    case '/admin':
        $c = new AdminDashboardController(); // ✓ sửa từ DashboardController
        $c->index();
        break;
    case '/product-detail':
        $c = new ProductController();
        $c->detail();
        break;
    case '/admin-inventory':
        $c = new AdminInventoryController();
        $c->index();
        break;
    case '/admin-inventory-create':
        $c = new AdminInventoryController();
        $c->create();
        break;

    case '/admin-inventory-detail':
        $c = new AdminInventoryController();
        $c->detail();
        break;

    case '/admin-inventory-confirm':
        $c = new AdminInventoryController();
        $c->confirm();
        break;
    case '/admin-login':
        $c = new AuthController();
        $method === 'POST' ? $c->adminLogin() : $c->adminLoginForm();
        break;
    case '/admin-inventory-cancel':
        $c = new AdminInventoryController();
        $c->cancel();
        break;
    case '/api-sizes':
        header('Content-Type: application/json');
        $productId = (int)($_GET['product_id'] ?? 0);
        echo json_encode($productId > 0 ? ProductModel::getSizes($productId) : []);
        exit;
    case '/cart-add':
        header('Content-Type: application/json');
        $productId = (int)($_POST['product_id'] ?? 0);
        $sizeId    = (int)($_POST['size_id'] ?? 0);
        $qty       = max(1, (int)($_POST['qty'] ?? 1));
        if ($productId > 0) {
            $userId = $_SESSION['user']['id'] ?? 0;
            $cartId = CartModel::getOrCreate($userId);
            $ok = CartModel::addItem($cartId, $productId, $sizeId, $qty);
            echo json_encode(['success' => $ok]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    default:
        http_response_code(404);
        include '../app/views/errors/404.php';
        break;
    }
?>