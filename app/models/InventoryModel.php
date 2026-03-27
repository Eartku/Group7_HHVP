<?php
class InventoryModel extends Model {

    public static function getImports(int $limit = 10, int $offset = 0, string $status = '', string $from = '', string $to = ''): array {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = []; $types = '';

        if ($status !== '') { $wheres[] = "ir.status = ?";              $params[] = $status; $types .= 's'; }
        if ($from   !== '') { $wheres[] = "DATE(ir.created_at) >= ?";   $params[] = $from;   $types .= 's'; }
        if ($to     !== '') { $wheres[] = "DATE(ir.created_at) <= ?";   $params[] = $to;     $types .= 's'; }

        $where     = 'WHERE ' . implode(' AND ', $wheres);
        $params[]  = $limit; $params[] = $offset; $types .= 'ii';

        $stmt = $db->prepare("
            SELECT ir.*, u.fullname AS created_by_name
            FROM import_receipts ir
            LEFT JOIN users u ON u.id = ir.created_by
            $where
            ORDER BY ir.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

        public static function countImports(string $status = '', string $from = '', string $to = ''): int {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = []; $types = '';

        if ($status !== '') { $wheres[] = "status = ?";            $params[] = $status; $types .= 's'; }
        if ($from   !== '') { $wheres[] = "DATE(created_at) >= ?"; $params[] = $from;   $types .= 's'; }
        if ($to     !== '') { $wheres[] = "DATE(created_at) <= ?"; $params[] = $to;     $types .= 's'; }

        $where = 'WHERE ' . implode(' AND ', $wheres);
        $stmt  = $db->prepare("SELECT COUNT(*) AS total FROM import_receipts $where");
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getImportById(int $id): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM import_receipts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function getImportItems(int $receiptId): array {
        $db   = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    il.*, 
                    p.name AS product_name, 
                    s.size_name AS size,
                    il.order_id
                FROM inventory_logs il
                JOIN products p ON p.id = il.product_id
                JOIN size s     ON s.id = il.size_id
                WHERE il.receipt_id = ?
            ");
        $stmt->bind_param("i", $receiptId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function createImport(int $userId, string $note, array $items): int {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO import_receipts (note, status, created_by)
            VALUES (?, 'pending', ?)
        ");
        $stmt->bind_param("si", $note, $userId);
        $stmt->execute();
        $receiptId = $db->insert_id;

        foreach ($items as $item) {
            $productId = (int)$item['product_id'];
            $sizeId    = (int)$item['size_id'];
            $price     = (float)$item['price'];
            $qty       = (int)$item['quantity'];
            $note2     = "Nhập kho phiếu #$receiptId";

            $stmt = $db->prepare("
                INSERT INTO inventory_logs
                    (receipt_id, product_id, size_id, type, quantity, import_price, note)
                VALUES (?, ?, ?, 'import', ?, ?, ?)
            ");
            $stmt->bind_param("iiidis", $receiptId, $productId, $sizeId, $qty, $price, $note2);
            $stmt->execute();
        }

        return $receiptId;
    }

    public static function confirmImport(int $receiptId): bool {
        $db = Database::getInstance();
        $db->begin_transaction();
        try {
            $stmt = $db->prepare("SELECT status FROM import_receipts WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $receiptId);
            $stmt->execute();
            $receipt = $stmt->get_result()->fetch_assoc();

            if (!$receipt || $receipt['status'] !== 'pending') {
                throw new Exception("Phiếu không hợp lệ hoặc đã xử lý");
            }

            $stmt = $db->prepare("
                SELECT product_id, size_id, quantity, import_price
                FROM inventory_logs WHERE receipt_id = ?
            ");
            $stmt->bind_param("i", $receiptId);
            $stmt->execute();
            $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $sizeId    = $item['size_id'];
                $qty       = $item['quantity'];
                $price     = $item['import_price'];

                $stmt2 = $db->prepare("
                    SELECT id, quantity, avg_import_price
                    FROM inventory
                    WHERE product_id = ? AND size_id = ?
                    FOR UPDATE
                ");
                $stmt2->bind_param("ii", $productId, $sizeId);
                $stmt2->execute();
                $inv = $stmt2->get_result()->fetch_assoc();

                if ($inv) {
                    $newQty = $inv['quantity'] + $qty;
                    $newAvg = ($inv['quantity'] * $inv['avg_import_price'] + $qty * $price) / $newQty;
                    $stmt3  = $db->prepare("
                        UPDATE inventory SET quantity = ?, avg_import_price = ?
                        WHERE product_id = ? AND size_id = ?
                    ");
                    $stmt3->bind_param("idii", $newQty, $newAvg, $productId, $sizeId);
                    $stmt3->execute();
                } else {
                    $stmt3 = $db->prepare("
                        INSERT INTO inventory (product_id, size_id, quantity, avg_import_price)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt3->bind_param("iiid", $productId, $sizeId, $qty, $price);
                    $stmt3->execute();
                }
            }

            $stmt = $db->prepare("UPDATE import_receipts SET status = 'confirmed' WHERE id = ?");
            $stmt->bind_param("i", $receiptId);
            $stmt->execute();

            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollback();
            error_log("confirmImport failed: " . $e->getMessage());
            return false;
        }
    }

    public static function cancelImport(int $receiptId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE import_receipts SET status = 'cancelled'
            WHERE id = ? AND status = 'pending'
        ");
        $stmt->bind_param("i", $receiptId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public static function getStatusBadge(string $status): string {
        return match($status) {
            'pending'   => '<span class="ui-badge warning">Đang xử lý</span>',
            'confirmed' => '<span class="ui-badge confirmed">Đã xác nhận</span>',
            'cancelled' => '<span class="ui-badge cancelled">Đã hủy</span>',
            default     => '<span class="ui-badge neutral">Không rõ</span>',
        };
    }

    public static function existsBySizeId(int $sizeId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM inventory WHERE size_id = ?");
        $stmt->bind_param("i", $sizeId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }
    public static function countLogs(string $from = '', string $to = ''): int {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = []; $types = '';

        if ($from !== '') { $wheres[] = "DATE(l.created_at) >= ?"; $params[] = $from; $types .= 's'; }
        if ($to   !== '') { $wheres[] = "DATE(l.created_at) <= ?"; $params[] = $to;   $types .= 's'; }

        $where = 'WHERE ' . implode(' AND ', $wheres);
        $stmt  = $db->prepare("SELECT COUNT(*) AS total FROM inventory_logs l $where");
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getLogs(string $from = '', string $to = '', int $limit = 15, int $offset = 0): array {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = []; $types = '';

        if ($from !== '') { $wheres[] = "DATE(l.created_at) >= ?"; $params[] = $from; $types .= 's'; }
        if ($to   !== '') { $wheres[] = "DATE(l.created_at) <= ?"; $params[] = $to;   $types .= 's'; }

        $where = 'WHERE ' . implode(' AND ', $wheres);
        $params[] = $limit; $params[] = $offset; $types .= 'ii';

            $stmt = $db->prepare("
                SELECT
                    l.id,
                    l.type,
                    l.quantity,
                    l.import_price,
                    l.note,
                    l.created_at,
                    l.order_id,
                    l.receipt_id,
                    p.name  AS product_name,
                    s.size_name
                FROM inventory_logs l
                LEFT JOIN products p ON p.id = l.product_id
                LEFT JOIN size s     ON s.id = l.size_id
                $where
                ORDER BY l.created_at DESC
                LIMIT ? OFFSET ?
            ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public static function updateImport(int $receiptId, array $items): bool {
    $db = Database::getInstance();
    $db->begin_transaction();

    try {
        // Xóa item cũ
        $stmt = $db->prepare("DELETE FROM inventory_logs WHERE receipt_id = ?");
        $stmt->bind_param("i", $receiptId);
        $stmt->execute();

        // Insert lại
        foreach ($items as $item) {
            $stmt = $db->prepare("
                INSERT INTO inventory_logs
                (receipt_id, product_id, size_id, type, quantity, import_price, note)
                VALUES (?, ?, ?, 'import', ?, ?, ?)
            ");

            $note = "Cập nhật phiếu #$receiptId";

            $stmt->bind_param(
                "iiidis",
                $receiptId,
                $item['product_id'],
                $item['size_id'],
                $item['quantity'],
                $item['price'],
                $note
            );

            $stmt->execute();
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
    }
    public static function createExportLog(array $item, int $orderId): void {
        $db = Database::getInstance();

        $stmt = $db->prepare("
            INSERT INTO inventory_logs
            (order_id, product_id, size_id, type, quantity, import_price, note)
            VALUES (?, ?, ?, 'export', ?, ?, ?)
        ");

        $note = "Xuất kho cho đơn hàng #$orderId";

        $stmt->bind_param(
            "iiidis",
            $orderId,
            $item['product_id'],
            $item['size_id'],
            $item['quantity'],
            $item['price'],
            $note
        );

        $stmt->execute();
    }
}