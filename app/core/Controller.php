<?php
class Controller {

    protected function view(string $path, array $data = []): void {
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../views/' . $path . '/index.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            die("View không tồn tại: $path");
        }
        include $viewFile;
        $content = ob_get_clean();

        // Nếu view tự quản lý layout (như guest/index.php), in thẳng ra
        if (!empty($noLayout)) {
            echo $content;
            return;
        }

        $pageTitle = $pageTitle ?? APP_NAME;
        include __DIR__ . '/../views/layouts/main.php';
    }

    protected function redirect(string $url): void {
        header("Location: " . $url);
        exit();
    }

    protected function post(string $key, string $default = ''): string {
        return trim($_POST[$key] ?? $default);
    }

    protected function get(string $key, string $default = ''): string {
        return trim($_GET[$key] ?? $default);
    }
    // Controller.php
    protected function requireLogin(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect(BASE_LOGIN_PATH);
            exit();
        }
    }

    protected function requireAdmin(): void {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            include __DIR__ . '/../views/errors/403.php';
            exit();
        }
    }
    protected function adminView(string $path, array $data = []): void {
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../views/' . $path . '/index.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            die("View không tồn tại: $path");
        }
        include $viewFile;
        $content = ob_get_clean();

        if (!empty($noLayout)) {
            echo $content;
            return;
        }

        $pageTitle = $pageTitle ?? APP_NAME;
        // ← dùng admin.php cho admin
        include __DIR__ . '/../views/layouts/admin.php';
    }
}