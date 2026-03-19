<?php
/**
 * Inventory Management API - BonSai
 * Endpoints:
 *   GET  ?action=stock_at_time&product_id=X&size=S&date=YYYY-MM-DD
 *   GET  ?action=import_export_report&product_id=X&size=S&from=YYYY-MM-DD&to=YYYY-MM-DD
 *   GET  ?action=low_stock_alert&threshold=N
 *   GET  ?action=products          (helper – list all products)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ─── DB CONFIG ─────────────────────────────────────────────────────────────
$host   = 'localhost';
$dbname = 'inventory';
$user   = 'root';
$pass   = '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

// ─── ROUTER ────────────────────────────────────────────────────────────────
$action = $_GET['action'] ?? '';

match ($action) {
    'stock_at_time'         => stockAtTime($pdo),
    'import_export_report'  => importExportReport($pdo),
    'low_stock_alert'       => lowStockAlert($pdo),
    'products'              => listProducts($pdo),
    default                 => badRequest("Unknown action: '$action'")
};


// ═══════════════════════════════════════════════════════════════════════════
// 1. TRA CỨU TỒN KHO TẠI MỘT THỜI ĐIỂM
// ═══════════════════════════════════════════════════════════════════════════
/**
 * Tính tồn kho của một sản phẩm+size tại một thời điểm bất kỳ.
 *
 * Logic:
 *   Lấy tất cả inventory_logs có created_at <= datetime chỉ định,
 *   cộng import, trừ export → ra tồn tại thời điểm đó.
 *
 * Params: product_id (required), size (required: S|M|L), date (required: YYYY-MM-DD [HH:MM:SS])
 */
function stockAtTime(PDO $pdo): void
{
    $productId = intval($_GET['product_id'] ?? 0);
    $size      = strtoupper(trim($_GET['size'] ?? ''));
    $date      = trim($_GET['date'] ?? '');

    if (!$productId || !in_array($size, ['S', 'M', 'L']) || !$date) {
        badRequest('Required: product_id, size (S|M|L), date (YYYY-MM-DD)');
    }

    // Append end-of-day time if only date supplied
    if (strlen($date) === 10) {
        $date .= ' 23:59:59';
    }

    // Validate datetime
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if (!$dt) {
        badRequest('Invalid date format. Use YYYY-MM-DD or YYYY-MM-DD HH:MM:SS');
    }

    // Sum up all movements up to that moment
    $sql = "
        SELECT
            SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END)  AS total_import,
            SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END)  AS total_export
        FROM inventory_logs
        WHERE product_id = :pid
          AND size        = :size
          AND created_at <= :dt
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pid' => $productId, ':size' => $size, ':dt' => $date]);
    $row = $stmt->fetch();

    $totalImport = (int)($row['total_import'] ?? 0);
    $totalExport = (int)($row['total_export'] ?? 0);
    $stockQty    = $totalImport - $totalExport;

    // Product info
    $p = $pdo->prepare("SELECT p.name, c.name AS category FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = :id");
    $p->execute([':id' => $productId]);
    $product = $p->fetch();

    // Detailed log breakdown at that moment
    $logSql = "
        SELECT id, type, quantity, import_price, note,
               DATE_FORMAT(created_at,'%d/%m/%Y %H:%i') AS created_at_fmt
        FROM   inventory_logs
        WHERE  product_id = :pid AND size = :size AND created_at <= :dt
        ORDER BY created_at ASC
    ";
    $logStmt = $pdo->prepare($logSql);
    $logStmt->execute([':pid' => $productId, ':size' => $size, ':dt' => $date]);
    $logs = $logStmt->fetchAll();

    // Build running balance for display
    $running = 0;
    foreach ($logs as &$log) {
        $running += ($log['type'] === 'import') ? $log['quantity'] : -$log['quantity'];
        $log['balance'] = $running;
        $log['quantity_display'] = ($log['type'] === 'import' ? '+' : '-') . $log['quantity'];
    }
    unset($log);

    echo json_encode([
        'success'       => true,
        'product_id'    => $productId,
        'product_name'  => $product['name'] ?? 'N/A',
        'category'      => $product['category'] ?? 'N/A',
        'size'          => $size,
        'query_datetime'=> $date,
        'total_import'  => $totalImport,
        'total_export'  => $totalExport,
        'stock_at_time' => $stockQty,
        'logs'          => $logs,
    ], JSON_UNESCAPED_UNICODE);
}


// ═══════════════════════════════════════════════════════════════════════════
// 2. BÁO CÁO NHẬP – XUẤT TRONG KHOẢNG THỜI GIAN
// ═══════════════════════════════════════════════════════════════════════════
/**
 * Params: product_id (optional – nếu bỏ thì báo cáo toàn bộ sản phẩm),
 *         size (optional), from (YYYY-MM-DD), to (YYYY-MM-DD)
 */
function importExportReport(PDO $pdo): void
{
    $productId = intval($_GET['product_id'] ?? 0);
    $size      = strtoupper(trim($_GET['size'] ?? ''));
    $from      = trim($_GET['from'] ?? '');
    $to        = trim($_GET['to'] ?? '');

    if (!$from || !$to) {
        badRequest('Required: from (YYYY-MM-DD), to (YYYY-MM-DD)');
    }

    $fromDt = $from . ' 00:00:00';
    $toDt   = $to   . ' 23:59:59';

    // Build WHERE clauses dynamically
    $conditions = ['il.created_at BETWEEN :from AND :to'];
    $params     = [':from' => $fromDt, ':to' => $toDt];

    if ($productId) {
        $conditions[] = 'il.product_id = :pid';
        $params[':pid'] = $productId;
    }
    if ($size && in_array($size, ['S', 'M', 'L'])) {
        $conditions[] = 'il.size = :size';
        $params[':size'] = $size;
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);

    // Summary per product+size
    $sql = "
        SELECT
            il.product_id,
            p.name                                                          AS product_name,
            c.name                                                          AS category,
            il.size,
            SUM(CASE WHEN il.type='import' THEN il.quantity ELSE 0 END)    AS total_import,
            SUM(CASE WHEN il.type='export' THEN il.quantity ELSE 0 END)    AS total_export,
            SUM(CASE WHEN il.type='import' THEN il.quantity ELSE 0 END)
              - SUM(CASE WHEN il.type='export' THEN il.quantity ELSE 0 END) AS net_change,
            COUNT(*)                                                        AS transaction_count
        FROM inventory_logs il
        JOIN products    p ON p.id = il.product_id
        LEFT JOIN categories c ON c.id = p.category_id
        $where
        GROUP BY il.product_id, il.size
        ORDER BY p.name, il.size
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $summary = $stmt->fetchAll();

    // Grand totals
    $grandImport = array_sum(array_column($summary, 'total_import'));
    $grandExport = array_sum(array_column($summary, 'total_export'));

    // Detailed transaction log
    $detailSql = "
        SELECT
            il.id,
            il.product_id,
            p.name                                         AS product_name,
            il.size,
            il.type,
            il.quantity,
            il.import_price,
            il.note,
            DATE_FORMAT(il.created_at,'%d/%m/%Y %H:%i')   AS created_at_fmt
        FROM inventory_logs il
        JOIN products p ON p.id = il.product_id
        $where
        ORDER BY il.created_at DESC
    ";
    $detailStmt = $pdo->prepare($detailSql);
    $detailStmt->execute($params);
    $details = $detailStmt->fetchAll();

    echo json_encode([
        'success'       => true,
        'period'        => ['from' => $from, 'to' => $to],
        'filter'        => [
            'product_id' => $productId ?: null,
            'size'       => $size ?: null,
        ],
        'grand_total'   => [
            'total_import' => $grandImport,
            'total_export' => $grandExport,
            'net_change'   => $grandImport - $grandExport,
        ],
        'summary'       => $summary,
        'details'       => $details,
    ], JSON_UNESCAPED_UNICODE);
}


// ═══════════════════════════════════════════════════════════════════════════
// 3. CẢNH BÁO SẮP HẾT HÀNG
// ═══════════════════════════════════════════════════════════════════════════
/**
 * Params: threshold (int, required) – số lượng tồn ≤ threshold thì cảnh báo
 *         include_out_of_stock (bool, optional, default true) – gồm cả hết hàng
 */
function lowStockAlert(PDO $pdo): void
{
    $threshold        = intval($_GET['threshold'] ?? -1);
    $includeOutOfStock = filter_var($_GET['include_out_of_stock'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

    if ($threshold < 0) {
        badRequest('Required: threshold (non-negative integer)');
    }

    $sql = "
        SELECT
            i.id            AS inventory_id,
            i.product_id,
            p.name          AS product_name,
            c.name          AS category,
            i.size,
            i.quantity      AS current_stock,
            i.avg_import_price,
            CASE
                WHEN i.quantity = 0 THEN 'out_of_stock'
                ELSE 'low_stock'
            END             AS alert_level,
            -- Last import date
            (SELECT DATE_FORMAT(MAX(il.created_at),'%d/%m/%Y %H:%i')
             FROM inventory_logs il
             WHERE il.product_id = i.product_id AND il.size = i.size AND il.type = 'import'
            )               AS last_import_date,
            -- Last export date
            (SELECT DATE_FORMAT(MAX(il.created_at),'%d/%m/%Y %H:%i')
             FROM inventory_logs il
             WHERE il.product_id = i.product_id AND il.size = i.size AND il.type = 'export'
            )               AS last_export_date
        FROM inventory i
        JOIN products    p ON p.id = i.product_id
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE i.quantity <= :threshold
        ORDER BY i.quantity ASC, p.name ASC
    ";

    // Exclude out-of-stock if not wanted
    if (!$includeOutOfStock) {
        $sql = str_replace('WHERE i.quantity <= :threshold', 'WHERE i.quantity > 0 AND i.quantity <= :threshold', $sql);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':threshold' => $threshold]);
    $alerts = $stmt->fetchAll();

    $outOfStock = array_filter($alerts, fn($r) => $r['current_stock'] == 0);
    $lowStock   = array_filter($alerts, fn($r) => $r['current_stock'] > 0);

    echo json_encode([
        'success'               => true,
        'threshold'             => $threshold,
        'include_out_of_stock'  => $includeOutOfStock,
        'total_alerts'          => count($alerts),
        'out_of_stock_count'    => count($outOfStock),
        'low_stock_count'       => count($lowStock),
        'alerts'                => array_values($alerts),
    ], JSON_UNESCAPED_UNICODE);
}


// ═══════════════════════════════════════════════════════════════════════════
// HELPER – DANH SÁCH SẢN PHẨM (dùng cho dropdown frontend)
// ═══════════════════════════════════════════════════════════════════════════
function listProducts(PDO $pdo): void
{
    $rows = $pdo->query("
        SELECT p.id, p.name, c.name AS category,
               GROUP_CONCAT(DISTINCT i.size ORDER BY i.size) AS available_sizes
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        LEFT JOIN inventory  i ON i.product_id = p.id
        GROUP BY p.id
        ORDER BY p.name
    ")->fetchAll();

    echo json_encode(['success' => true, 'products' => $rows], JSON_UNESCAPED_UNICODE);
}


// ─── UTILITY ───────────────────────────────────────────────────────────────
function badRequest(string $msg): void
{
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}
