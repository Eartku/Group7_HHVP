<?php
class AdminInventoryController extends Controller {

    // Danh sách phiếu nhập
    public function index(): void {
        $this->requireAdmin();

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $imports    = InventoryModel::getImports($limit, $offset);
        $total      = InventoryModel::countImports();
        $totalPages = ceil($total / $limit);

        $this->view('admin/inventory/index', [
            'imports'    => $imports,
            'page'       => $page,
            'totalPages' => $totalPages,
        ]);
    }

    // Chi tiết phiếu
    public function detail(): void {
        $this->requireAdmin();

        $id      = (int)($_GET['id'] ?? 0);
        $receipt = InventoryModel::getImportById($id);

        if (!$receipt) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $items = InventoryModel::getImportItems($id);

        $this->view('admin/inventory/detail', [
            'receipt' => $receipt,
            'items'   => $items,
        ]);
    }

    // Form tạo phiếu nhập
    public function create(): void {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note      = trim($_POST['note'] ?? '');
            $userId    = $_SESSION['user']['id'];
            $productIds = $_POST['product_id'] ?? [];
            $sizeIds    = $_POST['size_id']    ?? [];
            $prices     = $_POST['price']      ?? [];
            $quantities = $_POST['quantity']   ?? [];

            $items = [];
            foreach ($productIds as $i => $productId) {
                if (empty($productId) || empty($sizeIds[$i])) continue;
                $items[] = [
                    'product_id' => (int)$productId,
                    'size_id'    => (int)$sizeIds[$i],
                    'price'      => (float)$prices[$i],
                    'quantity'   => (int)$quantities[$i],
                ];
            }

            if (!empty($items)) {
                $receiptId = InventoryModel::createImport($userId, $note, $items);
                $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $receiptId);
                return;
            }
        }

        $products   = ProductModel::getList(0, 999, 0);
        $this->view('admin/inventory/create', [
            'products' => $products,
            'sizes' => SizeModel::getAll(),
        ]);
    }

    // Xác nhận phiếu
    public function confirm(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $ok = InventoryModel::confirmImport($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id
            . ($ok ? '&confirmed=1' : '&error=1'));
    }

    // Hủy phiếu
    public function cancel(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        InventoryModel::cancelImport($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id);
    }
}