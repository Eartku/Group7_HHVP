<?php
class InventoryModel extends Model {

    // Lấy danh sách phiếu nhập + phân trang
    public static function getImports(int $limit = 10, int $offset = 0): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT ir.*, p.name AS product_name
            FROM import_receipts ir
            LEFT JOIN products p ON p.id = ir.created_by
            ORDER BY ir.id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function countImports(): int {
        $db = Database::getInstance();
        return (int)$db->query("SELECT COUNT(*) AS total FROM import_receipts")
                       ->fetch_assoc()['total'];
    }

    // Lấy chi tiết 1 phiếu + items
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
            SELECT il.*, p.name AS product_name, s.size_name AS size
            FROM inventory_logs il
            JOIN products p ON p.id = il.product_id
            JOIN size s     ON s.id = il.size_id
            WHERE il.receipt_id = ?
        ");
        $stmt->bind_param("i", $receiptId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tạo phiếu nhập + items
    public static function createImport(int $userId, string $note, array $items): int {
        $db = Database::getInstance();

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

            $stmt = $db->prepare("
                INSERT INTO inventory_logs
                    (receipt_id, product_id, size_id, type, quantity, import_price, note)
                VALUES (?, ?, ?, 'import', ?, ?, ?)
            ");
            $note2 = "Nhập kho phiếu #$receiptId";
            $stmt->bind_param("iiidis", $receiptId, $productId, $sizeId, $qty, $price, $note2);
            $stmt->execute();
        }

        return $receiptId;
    }

    // Xác nhận phiếu nhập → cập nhật inventory
    public static function confirmImport(int $receiptId): bool {
        $db = Database::getInstance();
        $db->begin_transaction();

        try {
            // Kiểm tra status
            $stmt = $db->prepare("SELECT status FROM import_receipts WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $receiptId);
            $stmt->execute();
            $receipt = $stmt->get_result()->fetch_assoc();

            if (!$receipt || $receipt['status'] !== 'pending') {
                throw new Exception("Phiếu không hợp lệ hoặc đã xử lý");
            }

            // Lấy items
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

                // Kiểm tra đã có trong inventory chưa
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
                    // Cập nhật giá nhập trung bình có trọng số
                    $oldQty  = $inv['quantity'];
                    $oldAvg  = $inv['avg_import_price'];
                    $newQty  = $oldQty + $qty;
                    $newAvg  = ($oldQty * $oldAvg + $qty * $price) / $newQty;

                    $stmt3 = $db->prepare("
                        UPDATE inventory
                        SET quantity = ?, avg_import_price = ?
                        WHERE product_id = ? AND size_id = ?
                    ");
                    $stmt3->bind_param("idii", $newQty, $newAvg, $productId, $sizeId);
                    $stmt3->execute();
                } else {
                    // Thêm mới
                    $stmt3 = $db->prepare("
                        INSERT INTO inventory (product_id, size_id, quantity, avg_import_price)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt3->bind_param("iiid", $productId, $sizeId, $qty, $price);
                    $stmt3->execute();
                }
            }

            // Cập nhật status phiếu
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

    // Hủy phiếu
    public static function cancelImport(int $receiptId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE import_receipts SET status = 'cancelled' WHERE id = ? AND status = 'pending'
        ");
        $stmt->bind_param("i", $receiptId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public static function getStatusBadge(string $status): string {
        return match($status) {
            'pending'   => '<span class="badge bg-warning text-dark">Đang xử lý</span>',
            'confirmed' => '<span class="badge bg-success">Đã xác nhận</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
            default     => '<span class="badge bg-secondary">Không rõ</span>',
        };
    }
    public static function existsBySizeId(int $sizeId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM inventory WHERE size_id = ?");
        $stmt->bind_param("i", $sizeId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)$row['total'] > 0;
    }
}