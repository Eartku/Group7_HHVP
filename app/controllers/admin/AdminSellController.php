<?php
class AdminSellController extends Controller {

    public function index(): void {
        $this->requireAdmin();

        $search_type  = $_GET['search_type']      ?? 'name';
        $search_value = trim($_GET['search_value'] ?? '');
        $search_done  = isset($_GET['do_search']);
        $search_error = '';
        $categoryId   = (int)($_GET['category_id'] ?? 0);
        $costRange    = $_GET['cost_range']         ?? '';
        $profitRange  = $_GET['profit_range']       ?? '';
        $page         = max(1, (int)($_GET['page']  ?? 1));
        $limit        = 20;
        $offset       = ($page - 1) * $limit;

        // Validate search
        if ($search_done && $search_value !== '') {
            if ($search_type === 'id' && !ctype_digit($search_value)) {
                $search_error = 'Mã sản phẩm phải là số nguyên dương.';
            }
            if ($search_type === 'category_id' && !ctype_digit($search_value)) {
                $search_error = 'Mã phân loại phải là số nguyên dương.';
            }
        }

        $products    = [];
        $total_rows  = 0;
        $total_pages = 1;

        if (!$search_error) {
            $result      = self::fetchProducts($search_type, $search_value, $search_done, $categoryId, $costRange, $profitRange, $limit, $offset);
            $products    = $result['data'];
            $total_rows  = $result['total'];
            $total_pages = max(1, (int)ceil($total_rows / $limit));
        }

        $categories = CategoryModel::getAllActive();

        $this->adminView('admin/sell/index', [
            'products'     => $products,
            'categories'   => $categories,
            'search_type'  => $search_type,
            'search_value' => $search_value,
            'search_done'  => $search_done,
            'search_error' => $search_error,
            'category_id'  => $categoryId,
            'cost_range'   => $costRange,
            'profit_range' => $profitRange,
            'total_rows'   => $total_rows,
            'total_pages'  => $total_pages,
            'page'         => $page,
        ]);
    }

    public function update(): void {
        $this->requireAdmin();

        $type        = $_POST['type']               ?? '';
        $id          = (int)($_POST['id']           ?? 0);
        $profit_rate = (float)($_POST['profit_rate'] ?? 0);

        if ($id <= 0) {
            $this->redirect(BASE_URL . '/index.php?url=admin-sell&error=1');
            return;
        }

        $db = Database::getInstance();
        $ok = false;

        if ($type === 'product') {
            $stmt = $db->prepare("UPDATE products SET profit_rate = ? WHERE id = ?");
            $stmt->bind_param("di", $profit_rate, $id);
            $ok = $stmt->execute();
        } elseif ($type === 'category') {
            $stmt = $db->prepare("UPDATE products SET profit_rate = ? WHERE category_id = ?");
            $stmt->bind_param("di", $profit_rate, $id);
            $ok = $stmt->execute();
        }

        // Giữ lại query string để quay về đúng trang/filter
        $back = $_POST['back_url'] ?? (BASE_URL . '/index.php?url=admin-sell');
        $this->redirect($back . ($ok ? '&updated=1' : '&error=1'));
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private static function fetchProducts(
        string $searchType,
        string $searchValue,
        bool   $searchDone,
        int    $categoryId,
        string $costRange,
        string $profitRange,
        int    $limit,
        int    $offset
    ): array {
        $db         = Database::getInstance();
        $whereParts = ["p.status = 'active'"];
        $params     = [];
        $types      = '';

        if ($searchDone && $searchValue !== '') {
            switch ($searchType) {
                case 'id':
                    $whereParts[] = 'p.id = ?';
                    $params[]     = (int)$searchValue;
                    $types       .= 'i';
                    break;
                case 'category_id':
                    $whereParts[] = 'p.category_id = ?';
                    $params[]     = (int)$searchValue;
                    $types       .= 'i';
                    break;
                default:
                    $whereParts[] = 'p.name LIKE ?';
                    $params[]     = '%' . $searchValue . '%';
                    $types       .= 's';
            }
        }

        if ($categoryId > 0) {
            $whereParts[] = 'p.category_id = ?';
            $params[]     = $categoryId;
            $types       .= 'i';
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $whereParts);

        $avgPriceSub = "(SELECT COALESCE(AVG(inv.avg_import_price), 0)
                         FROM inventory inv WHERE inv.product_id = p.id)";

        // HAVING
        $havingParts  = [];
        $havingParams = [];
        $havingTypes  = '';

        if ($costRange !== '') {
            [$costMin, $costMax] = self::parseRange($costRange, 0, PHP_INT_MAX);
            $havingParts[]  = 'avg_cost BETWEEN ? AND ?';
            $havingParams[] = $costMin;
            $havingParams[] = $costMax;
            $havingTypes   .= 'dd';
        }

        if ($profitRange !== '') {
            [$profitMin, $profitMax] = self::parseRange($profitRange, 0, PHP_INT_MAX);
            $havingParts[]  = 'p.profit_rate BETWEEN ? AND ?';
            $havingParams[] = $profitMin;
            $havingParams[] = $profitMax;
            $havingTypes   .= 'dd';
        }

        $havingSQL = $havingParts ? 'HAVING ' . implode(' AND ', $havingParts) : '';
        $allParams = array_merge($params, $havingParams);
        $allTypes  = $types . $havingTypes;

        // Count
        $countSQL = "
            SELECT COUNT(*) AS total FROM (
                SELECT p.id,
                       $avgPriceSub AS avg_cost,
                       p.profit_rate
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                $whereSQL
                GROUP BY p.id, p.profit_rate
                $havingSQL
            ) sub
        ";
        $stmtCount = $db->prepare($countSQL);
        if ($allTypes) $stmtCount->bind_param($allTypes, ...$allParams);
        $stmtCount->execute();
        $total = (int)$stmtCount->get_result()->fetch_assoc()['total'];

        // Data
        $dataParams = array_merge($allParams, [$limit, $offset]);
        $dataTypes  = $allTypes . 'ii';

        $dataSQL = "
            SELECT p.id, p.name, p.profit_rate,
                   c.name AS category_name,
                   $avgPriceSub AS avg_import_price,
                   ROUND(($avgPriceSub) * (1 + COALESCE(p.profit_rate, 0) / 100) / 1000) * 1000 AS sale_price
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            $whereSQL
            GROUP BY p.id, p.name, p.profit_rate, c.name
            $havingSQL
            ORDER BY p.id ASC
            LIMIT ? OFFSET ?
        ";
        $stmtData = $db->prepare($dataSQL);
        if ($dataTypes) $stmtData->bind_param($dataTypes, ...$dataParams);
        $stmtData->execute();
        $data = $stmtData->get_result()->fetch_all(MYSQLI_ASSOC);

        return ['data' => $data, 'total' => $total];
    }

    private static function parseRange(string $range, float $defaultMin, float $defaultMax): array {
        if ($range === '') return [$defaultMin, $defaultMax];
        $parts = explode('-', $range, 2);
        return [(float)($parts[0] ?? $defaultMin), (float)($parts[1] ?? $defaultMax)];
    }
}