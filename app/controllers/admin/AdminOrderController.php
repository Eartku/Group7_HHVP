<?php
class AdminOrderController extends Controller {

    public function index(): void {
        $this->requireAdmin();

        $search          = trim($_GET['search']        ?? '');
        $status_filter   = trim($_GET['status']        ?? '');
        $start_date      = trim($_GET['start_date']    ?? '');
        $end_date        = trim($_GET['end_date']       ?? '');
        $ward_filter     = trim($_GET['ward']           ?? '');   // ← MỚI
        $district_filter = trim($_GET['district']       ?? '');   // ← MỚI
        $province_filter = trim($_GET['province']       ?? '');   // ← MỚI
        $current_page    = max(1, (int)($_GET['page']   ?? 1));
        $per_page        = 10;

        [$orders, $total] = $this->fetchOrders(
            $search, $status_filter, $start_date, $end_date,
            $ward_filter, $district_filter, $province_filter,   // ← MỚI
            $per_page, ($current_page - 1) * $per_page
        );

        $total_pages     = max(1, (int)ceil($total / $per_page));
        $address_options = OrderModel::getAddressFilterOptions(); // ← MỚI

        $this->adminView('admin/orders/index', [
            'orders'          => $orders,
            'total'           => $total,
            'total_pages'     => $total_pages,
            'current_page'    => $current_page,
            'search'          => $search,
            'status_filter'   => $status_filter,
            'start_date'      => $start_date,
            'end_date'        => $end_date,
            'ward_filter'     => $ward_filter,       // ← MỚI
            'district_filter' => $district_filter,   // ← MỚI
            'province_filter' => $province_filter,   // ← MỚI
            'address_options' => $address_options,   // ← MỚI
        ]);
    }

    public function detail(): void {
        $this->requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->abort(404);
            return;
        }

        $order = OrderModel::getById($id);
        if (!$order) {
            $this->abort(404);
            return;
        }

        $items    = OrderModel::getItems($id);
        $subtotal = array_sum(array_column($items, 'row_total'));
        $badge    = OrderModel::getStatusBadge($order['status']);

        $this->adminView('admin/orders/detail', [
            'order'    => $order,
            'items'    => $items,
            'subtotal' => $subtotal,
            'badge'    => $badge,
            'updated'  => isset($_GET['updated']),
        ]);
    }

    public function updateStatus(): void {
    $this->requireAdmin();
        $id     = (int)($_GET['id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($id > 0) {
            OrderModel::updateStatus($id, $status); // ✅
        }

        $this->redirect(BASE_URL . '/index.php?url=admin-orders-detail&id=' . $id . '&updated=1');
    }

    // ── Private helper ──
    private function fetchOrders(
        string $search,
        string $status,
        string $start_date,
        string $end_date,
        string $ward,        // ← MỚI
        string $district,    // ← MỚI
        string $province,    // ← MỚI
        int    $limit,
        int    $offset
    ): array {
        $db     = Database::getInstance();
        $wheres = [];
        $params = [];
        $types  = '';

        if ($search !== '') {
            $like     = '%' . $search . '%';
            $wheres[] = '(o.id LIKE ? OR o.fullname LIKE ? OR o.email LIKE ?)';
            array_push($params, $like, $like, $like);
            $types   .= 'sss';
        }
        if ($status !== '') {
            $wheres[] = 'o.status = ?';
            $params[] = $status;
            $types   .= 's';
        }
        if ($start_date !== '') {
            $wheres[] = 'DATE(o.created_at) >= ?';
            $params[] = $start_date;
            $types   .= 's';
        }
        if ($end_date !== '') {
            $wheres[] = 'DATE(o.created_at) <= ?';
            $params[] = $end_date;
            $types   .= 's';
        }

        // ── Filter địa chỉ: so sánh TRIM(SUBSTRING_INDEX(...)) ──────────────
        // address = "số nhà/đường, phường, quận, tỉnh"  (index 1, 2, 3)
        if ($ward !== '') {
            $wheres[] = "TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.address,',',2),',',-1)) = ?";
            $params[] = $ward;
            $types   .= 's';
        }
        if ($district !== '') {
            $wheres[] = "TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(o.address,',',3),',',-1)) = ?";
            $params[] = $district;
            $types   .= 's';
        }
        if ($province !== '') {
            $wheres[] = "TRIM(SUBSTRING_INDEX(o.address,',',-1)) = ?";
            $params[] = $province;
            $types   .= 's';
        }
        // ────────────────────────────────────────────────────────────────────

        $where_sql = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';

        // Count
        $stmt = $db->prepare("SELECT COUNT(DISTINCT o.id) AS total FROM orders o $where_sql");
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $total = (int)$stmt->get_result()->fetch_assoc()['total'];

        // Data  (phần SELECT giữ nguyên)
        $sql = "
            SELECT
                o.id, o.fullname, o.email, o.phone,
                o.status, o.created_at,
                o.total_price, o.payment_method,
                o.address, o.note, o.shipping_fee,
                COALESCE(
                    GROUP_CONCAT(DISTINCT p.name ORDER BY oi.id SEPARATOR ', '),
                    '—'
                ) AS san_pham
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            LEFT JOIN products    p  ON p.id        = oi.product_id
            $where_sql
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $data_params = array_merge($params, [$limit, $offset]);
        $data_types  = $types . 'ii';

        $stmt = $db->prepare($sql);
        $stmt->bind_param($data_types, ...$data_params);
        $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [$orders, $total];
    }
    
}