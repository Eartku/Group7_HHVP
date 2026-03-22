<?php
class AdminCategoryController extends Controller {

    public function index(): void {
        $this->requireAdmin();

        $alertMap = [
            'created'   => ['success', 'Thêm danh mục thành công.'],
            'updated'   => ['success', 'Cập nhật danh mục thành công.'],
            'deleted'   => ['success', 'Đã xóa danh mục thành công.'],
            'inuse'     => ['warning', 'Không thể xóa — danh mục còn sản phẩm.'],
            'duplicate' => ['warning', 'Tên danh mục đã tồn tại.'],
            'failed'    => ['danger',  'Thao tác thất bại. Vui lòng thử lại.'],
            'empty'     => ['warning', 'Tên danh mục không được để trống.'],
        ];

        $alertKey = $_GET['success'] ?? $_GET['error'] ?? '';
        $alert    = isset($alertMap[$alertKey]) ? [
            'type' => $alertMap[$alertKey][0],
            'msg'  => $alertMap[$alertKey][1],
        ] : null;

        $categories = CategoryModel::getAll();

        $this->adminView('admin/categories/index', [
            'categories' => $categories,
            'alert'      => $alert,
        ]);
    }

    public function create(): void {
        $this->requireAdmin();

        $old     = [];
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $old         = ['name' => $name, 'description' => $description];

            if ($name === '') {
                $error = 'Tên danh mục không được để trống.';
            } elseif (CategoryModel::nameExists($name)) {
                $error = 'Tên danh mục đã tồn tại.';
            } else {
                $image = '';
                if (!empty($_FILES['image']['name'])) {
                    $imageName = time() . '_' . basename($_FILES['image']['name']);
                    $target    = __DIR__ . '/../../images/' . $imageName;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $error = 'Upload ảnh thất bại. Vui lòng thử lại.';
                    } else {
                        $image = $imageName;
                    }
                }

                if (!$error) {
                    $ok = CategoryModel::create($name, $description, $image);
                    if ($ok) {
                        // Redirect để tránh resubmit khi F5
                        $this->redirect(BASE_URL . '/index.php?url=admin-categories&success=created');
                        return;
                    }
                    $error = 'Thêm danh mục thất bại. Vui lòng thử lại.';
                }
            }
        }

        // GET hoặc POST lỗi → render form
        $this->adminView('admin/categories/create', [
            'old'     => $old,
            'error'   => $error,
            'success' => $success,
        ]);
    }

    public function edit(): void {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->abort(404);
            return;
        }

        $category = CategoryModel::getById($id);
        if (!$category) {
            $this->abort(404);
            return;
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $status      = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active'; // whitelist

            if ($name === '') {
                $error = 'Tên danh mục không được để trống.';
            } elseif (CategoryModel::nameExists($name, $id)) {
                $error = 'Tên danh mục đã tồn tại.';
            } else {
                // Upload ảnh mới
                $image = '';
                if (!empty($_FILES['image']['name'])) {
                    $imageName = time() . '_' . basename($_FILES['image']['name']);
                    $target    = __DIR__ . '/../../../images/' . $imageName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $image = $imageName;
                    }
                }

                $ok = CategoryModel::update($id, $name, $description, $image, $status);
                if ($ok) {
                    $success  = 'Cập nhật danh mục thành công.';
                    $category = CategoryModel::getById($id); // reload
                } else {
                    $error = 'Cập nhật thất bại. Vui lòng thử lại.';
                }
            }
        }

        $this->adminView('admin/categories/edit', [
            'category' => $category,
            'success'  => $success,
            'error'    => $error,
            'statuses' => ['active' => 'Kích hoạt', 'inactive' => 'Vô hiệu hóa'],
        ]);
    }

    public function delete(): void {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(BASE_URL . '/index.php?url=admin-categories&error=failed');
            return;
        }

        $ok = CategoryModel::delete($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-categories'
            . ($ok ? '&success=deleted' : '&error=inuse'));
    }
    public function restore(): void {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect(BASE_URL . '/index.php?url=admin-categories&error=failed');
            return;
        }

        $ok = CategoryModel::restore($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-categories'
            . ($ok ? '&success=restored' : '&error=failed'));
    }
}