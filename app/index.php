<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/config/app.php';    
require_once __DIR__ . '/core/autoload.php';

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
        session_destroy();
        header('Location: /app/index.php'); 
        // hoặc đơn giản hơn:
        break;
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
    case '/search':
        $c = new SearchController();
        $c->index();
        break;
    default:
        http_response_code(404);
        include __DIR__ . '/views/errors/404.php';
        break;
}
?>