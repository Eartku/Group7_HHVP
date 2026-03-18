<?php
class HomeController extends Controller {
    public function index() {
        // Chưa login → redirect về guest
        if (!isset($_SESSION['user'])) {
            $this->redirect('/app/index.php');
            $this->requireLogin();
            return;
        }

        $this->view('home', [
            'user' => $_SESSION['user']
        ]);
    }
}
// ```

// Luồng hoàn chỉnh:
// ```
// Login thành công
//        ↓
// role = admin  →  ?url=admin  →  AdminController
// role = customer  →  ?url=home  →  HomeController