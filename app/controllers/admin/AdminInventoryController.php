<?php
class AdminInventoryController extends Controller{

    public function index(): void {
        $this->requireAdmin();

        // Phiếu nhập
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $limit      = 10;
        $offset     = ($page - 1) * $limit;
        $imports    = InventoryModel::getImports($limit, $offset); // giữ nguyên, mặc định rỗng
        $total      = InventoryModel::countImports();              // giữ nguyên
        $totalPages = (int)ceil($total / $limit);

        // Log xuất/nhập
        $logFrom  = $_GET['log_from'] ?? '';
        $logTo    = $_GET['log_to']   ?? '';
        $logPage  = max(1, (int)($_GET['log_page'] ?? 1));
        $logLimit = 15;

        $logTotal      = InventoryModel::countLogs($logFrom, $logTo);
        $logTotalPages = max(1, (int)ceil($logTotal / $logLimit));
        $logPage       = min($logPage, $logTotalPages);
        $logs          = InventoryModel::getLogs($logFrom, $logTo, $logLimit, ($logPage - 1) * $logLimit);

        $this->adminView('admin/inventory/index', [
            'imports'       => $imports,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'logs'          => $logs,
            'logFrom'       => $logFrom,
            'logTo'         => $logTo,
            'logPage'       => $logPage,
            'logTotalPages' => $logTotalPages,
        ]);
    }

    public function detail(): void {
        $this->requireAdmin();
        $id      = (int)($_GET['id'] ?? 0);
        $receipt = InventoryModel::getImportById($id);
        if (!$receipt) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            return;
        }
        $items = InventoryModel::getImportItems($id);
        $this->adminView('admin/inventory/detail', [ // ✅ adminView
            'receipt' => $receipt,
            'items'   => $items,
        ]);
    }

    

    public function store(): void { // ✅ tách POST ra riêng
        $this->requireAdmin();
        $note       = trim($_POST['note'] ?? '');
        $userId     = $_SESSION['user']['id'];
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

        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-create&error=empty');
    }

    public function confirm(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $ok = InventoryModel::confirmImport($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id
            . ($ok ? '&confirmed=1' : '&error=1'));
    }

    public function cancel(): void {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        InventoryModel::cancelImport($id);
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id . '&cancelled=1');
    }
    public function edit(): void {
    $this->requireAdmin();

    $id = (int)($_GET['id'] ?? 0);

    $receipt = InventoryModel::getImportById($id);
    if (!$receipt) {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
        return;
    }

    if ($receipt['status'] !== 'pending') {
        $_SESSION['error'] = "Phiếu đã xác nhận, không thể chỉnh sửa!";
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id);
        return;
    }
    $items    = InventoryModel::getImportItems($id);
    $products = ProductModel::getList(0, 999, 0);
    $sizes    = SizeModel::getAll();

    $this->adminView('admin/inventory/edit', [
        'receipt'  => $receipt,
        'items'    => $items,
        'products' => $products,
        'sizes'    => $sizes,
    ]);
}
    public function update(): void {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        $receipt = InventoryModel::getImportById($id);
        if (!$receipt || $receipt['status'] !== 'pending') {
            die("Không thể cập nhật phiếu!");
        }

        $productIds = $_POST['product_id'] ?? [];
        $sizeIds    = $_POST['size_id'] ?? [];
        $prices     = $_POST['price'] ?? [];
        $qtys       = $_POST['quantity'] ?? [];

        $items = [];

        foreach ($productIds as $i => $productId) {
            if (empty($productId) || empty($sizeIds[$i])) continue;

            $items[] = [
                'product_id' => (int)$productId,
                'size_id'    => (int)$sizeIds[$i],
                'price'      => (float)$prices[$i],
                'quantity'   => (int)$qtys[$i],
            ];
        }

        if (!empty($items)) {
            InventoryModel::updateImport($id, $items);
        }

        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id . '&updated=1');
    }
    public function create(): void {
        $this->requireAdmin();
        $products = ProductModel::getList(0, 999, 0);
        $sizes    = SizeModel::getAll();

        $page          = max(1, (int)($_GET['page']          ?? 1));
        $filterStatus  = trim($_GET['filter_status']         ?? '');
        $filterFrom    = trim($_GET['filter_from']           ?? '');
        $filterTo      = trim($_GET['filter_to']             ?? '');
        $limit         = 10;
        $offset        = ($page - 1) * $limit;

        // ✅ Truyền filter vào đây — đây là chỗ bị thiếu
        $imports    = InventoryModel::getImports($limit, $offset, $filterStatus, $filterFrom, $filterTo);
        $total      = InventoryModel::countImports($filterStatus, $filterFrom, $filterTo);
        $totalPages = max(1, (int)ceil($total / $limit));

        $this->adminView('admin/inventory/create', [
            'products'      => $products,
            'sizes'         => $sizes,
            'imports'       => $imports,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'filterStatus'  => $filterStatus,
            'filterFrom'    => $filterFrom,
            'filterTo'      => $filterTo,
        ]);
    }
}