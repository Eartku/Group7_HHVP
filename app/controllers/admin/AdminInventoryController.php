<?php
class AdminInventoryController extends Controller{

    public function index(): void {
        $this->requireAdmin();

        // ===== TỒN KHO =====
        $invPage      = max(1, (int)($_GET['inv_page'] ?? 1));
        $invProductId = (int)($_GET['inv_product_id'] ?? 0);
        $invTime      = $_GET['inv_time'] ?? date('Y-m-d'); // Mặc định là ngày hôm nay
        $invLimit     = 10;
        $invOffset    = ($invPage - 1) * $invLimit;

        // Lấy tên sản phẩm để hiển thị lại trong input
        $invProductName = '';
        if ($invProductId > 0) {
            $allProducts = ProductModel::getList(0, 999, 0);
            foreach ($allProducts as $p) {
                if ((int)$p['id'] === $invProductId) {
                    $invProductName = $p['name'];
                    break;
                }
            }
        }

        if (!empty($invTime)) {
            // Tra cứu theo thời điểm: tính tồn kho từ inventory_logs
            $inventory     = InventoryModel::getInventoryAtTime($invLimit, $invOffset, $invProductId, $invTime);
            $invTotal      = InventoryModel::countInventoryAtTime($invProductId, $invTime);
        } else {
            // Mặc định: lấy từ bảng inventory hiện tại
            $inventory     = InventoryModel::getInventoryList($invLimit, $invOffset, '', '', '', $invProductId);
            $invTotal      = InventoryModel::countInventory('', '', $invProductId);
        }
        $invTotalPages = (int)ceil($invTotal / $invLimit);

        // ===== HẾT HÀNG =====
        $outPage   = max(1, (int)($_GET['out_page'] ?? 1));
        $outLimit  = 10;
        $outOffset = ($outPage - 1) * $outLimit;
        $threshold = (int)($_GET['threshold'] ?? 0);

        $outStock      = InventoryModel::getOutOfStock($outLimit, $outOffset, '', '', $threshold);
        $outTotal      = InventoryModel::countOutOfStock('', '', $threshold);
        $outTotalPages = (int)ceil($outTotal / $outLimit);

        // ===== LOG XUẤT/NHẬP =====
        $logFrom = $_GET['log_from'] ?? '';
        $logTo   = $_GET['log_to']   ?? '';
        $logPage = max(1, (int)($_GET['log_page'] ?? 1));

        // Nếu có filter ngày thì lấy nhóm theo ngày
        if (!empty($logFrom) || !empty($logTo)) {
            // Lấy tất cả logs trong khoảng ngày, không phân trang (nhóm theo ngày)
            $logs = InventoryModel::getLogs($logFrom, $logTo, '', 5000, 0);
            
            // Nhóm logs theo ngày
            $logsByDate = [];
            foreach ($logs as $log) {
                $dateKey = date('Y-m-d', strtotime($log['created_at']));
                if (!isset($logsByDate[$dateKey])) {
                    $logsByDate[$dateKey] = [
                        'rows'         => [],
                        'total_import' => 0,
                        'total_export' => 0,
                    ];
                }
                $logsByDate[$dateKey]['rows'][] = $log;
                if ($log['type'] === 'import') {
                    $logsByDate[$dateKey]['total_import'] += (int)$log['quantity'];
                } else {
                    $logsByDate[$dateKey]['total_export'] += (int)$log['quantity'];
                }
            }
            // Sắp xếp ngày mới nhất trước
            krsort($logsByDate);
            
            // Gán các biến cho view
            $logTotal = 0;
            $logTotalPages = 0;
            $logCurrentPage = 1;
        } else {
            // Mặc định: lấy logs có phân trang
            $logLimit = 15;
            $logOffset = ($logPage - 1) * $logLimit;
            $logs = InventoryModel::getLogs('', '', '', $logLimit, $logOffset);
            $logTotal = InventoryModel::countLogs();
            $logTotalPages = ceil($logTotal / $logLimit);
            $logCurrentPage = $logPage;
            $logsByDate = []; // không dùng nhóm theo ngày
        }

        // ===== SẢN PHẨM cho autocomplete =====
        $products = ProductModel::getList(0, 999, 0);
        $sizes    = SizeModel::getAll();

        $this->adminView('admin/inventory/index', [
            // Tồn kho
            'inventory'      => $inventory,
            'invPage'        => $invPage,
            'invTotalPages'  => $invTotalPages,
            'invProductId'   => $invProductId,
            'invProductName' => $invProductName,
            'invTime'        => $invTime,
            // Hết hàng
            'outStock'       => $outStock,
            'outPage'        => $outPage,
            'outTotalPages'  => $outTotalPages,
            'outTotal'       => $outTotal,
            'threshold'      => $threshold,
            // Log
            'logs'           => $logs,        
            'logsByDate'     => $logsByDate,
            'logFrom'        => $logFrom,
            'logTo'          => $logTo,
            'logTotal'       => $logTotal ?? 0,      
            'logTotalPages'  => $logTotalPages ?? 0, 
            'logCurrentPage' => $logCurrentPage ?? 1, 
            // Chung
            'products'       => $products,
            'sizes'          => $sizes,
            // Legacy (còn dùng ở form khác)
            'categoryId'     => '',
            'status'         => '',
            'sort'           => '',
            'categories'     => CategoryModel::getAll(),
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
        $this->adminView('admin/inventory/detail', [
            'receipt' => $receipt,
            'items'   => $items,
        ]);
    }

    public function store(): void {
        $this->requireAdmin();
        $note       = trim($_POST['note'] ?? '');
        $userId     = $_SESSION['user']['id'];
        $importDate = $_POST['import_date'] ?? date('Y-m-d H:i:s');

        if(!empty($importDate)){
            $importDate = date('Y-m-d H:i:s', strtotime($importDate));
        }else{
            $importDate = date('Y-m-d H:i:s');
        }
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

            $receiptId = InventoryModel::createImport(
                $userId,
                $note,
                $items,
                $importDate
            );
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
        $id      = (int)($_GET['id'] ?? 0);
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
        $id      = (int)($_GET['id'] ?? 0);
        $receipt = InventoryModel::getImportById($id);
        $importDate = $_POST['import_date'] ?? $receipt['created_at'];

        if(!empty($importDate)){
            $importDate = date('Y-m-d H:i:s', strtotime($importDate));
        }else{
            $importDate = $receipt['created_at'];
        }
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
            InventoryModel::updateImport($id, $items, $importDate);
        }
        $this->redirect(BASE_URL . '/index.php?url=admin-inventory-detail&id=' . $id . '&updated=1');
    }

    public function create(): void {
        $this->requireAdmin();
        $products = ProductModel::getList(0, 999, 0);
        $sizes    = SizeModel::getAll();

        $page         = max(1, (int)($_GET['page'] ?? 1));
        $filterStatus = trim($_GET['filter_status'] ?? '');
        $filterFrom   = trim($_GET['filter_from']   ?? '');
        $filterTo     = trim($_GET['filter_to']     ?? '');
        $limit        = 10;
        $offset       = ($page - 1) * $limit;

        $imports    = InventoryModel::getImports($limit, $offset, $filterStatus, $filterFrom, $filterTo);
        $total      = InventoryModel::countImports($filterStatus, $filterFrom, $filterTo);
        $totalPages = max(1, (int)ceil($total / $limit));

        $this->adminView('admin/inventory/create', [
            'products'     => $products,
            'sizes'        => $sizes,
            'imports'      => $imports,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'filterStatus' => $filterStatus,
            'filterFrom'   => $filterFrom,
            'filterTo'     => $filterTo,
        ]);
    }
}