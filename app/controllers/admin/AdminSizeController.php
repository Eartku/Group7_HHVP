<?php
class AdminSizeController extends Controller {

    public function index(): void {
        $this->requireAdmin();
        $sizes   = SizeModel::getAll();
        $success = $_GET['success'] ?? '';
        $error   = $_GET['error']   ?? '';
        $this->adminView('admin/sizes/index', [
            'sizes'   => $sizes,
            'success' => $success,
            'error'   => $error,
        ]);
    }

    public function create(): void {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/index.php?url=admin-sizes');
            return;
        }

        $sizeName    = trim($_POST['size_name']    ?? '');
        $priceAdjust = (float)($_POST['price_adjust'] ?? 0);

        if ($sizeName === '') {
            $this->redirect(BASE_URL . '/index.php?url=admin-sizes&error=empty');
            return;
        }
        if (SizeModel::nameExists($sizeName)) {
            $this->redirect(BASE_URL . '/index.php?url=admin-sizes&error=duplicate');
            return;
        }

        $ok = SizeModel::create($sizeName, $priceAdjust);
        $this->redirect(BASE_URL . '/index.php?url=admin-sizes'
            . ($ok ? '&success=created' : '&error=failed'));
    }

    public function edit(): void {
        $this->requireAdmin();
        $sizeId = (int)($_GET['id'] ?? 0);
        if ($sizeId <= 0 || !SizeModel::exists($sizeId)) {
            $this->abort(404);
            return;
        }

        $size    = SizeModel::getById($sizeId);
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sizeName    = trim($_POST['size_name']    ?? '');
            $priceAdjust = (float)($_POST['price_adjust'] ?? 0);

            if ($sizeName === '') {
                $error = 'Tên size không được để trống.';
            } elseif (SizeModel::nameExists($sizeName, $sizeId)) {
                $error = 'Tên size đã tồn tại.';
            } else {
                $ok = SizeModel::update($sizeId, $sizeName, $priceAdjust);
                if ($ok) {
                    $success = 'Cập nhật size thành công.';
                    $size    = SizeModel::getById($sizeId);
                } else {
                    $error = 'Cập nhật thất bại. Vui lòng thử lại.';
                }
            }
        }

        $this->adminView('admin/sizes/edit', [
            'size'    => $size,
            'success' => $success,
            'error'   => $error,
        ]);
    }

    public function delete(): void {
        $this->requireAdmin();
        $sizeId = (int)($_GET['id'] ?? 0);

        if ($sizeId <= 0) {
            $this->redirect(BASE_URL . '/index.php?url=admin-sizes&error=invalid');
            return;
        }

        $ok = SizeModel::delete($sizeId);
        $this->redirect(BASE_URL . '/index.php?url=admin-sizes'
            . ($ok ? '&success=deleted' : '&error=inuse'));
    }
}