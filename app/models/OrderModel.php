<?php
// Order Model - orders + order_items tables
// app/models/OrderModel.php
class OrderModel extends Model {

    /**
     * Lấy danh sách order theo user.
     */
    public static function getByUserId(int $userId): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy chi tiết 1 order.
     */
    public static function getById(int $orderId): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function count(): int {
        $db     = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) AS total FROM orders");
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }
        return 0;
    }
    /**
     * Lấy items của order.
     * row_total được tính bằng price * quantity trong query
     * vì order_items không có cột row_total.
     */
    public static function getItems(int $orderId): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                oi.id,
                oi.order_id,
                oi.product_id,
                oi.size_id,
                oi.quantity,
                oi.price,
                (oi.price * oi.quantity) AS row_total,
                p.name,
                p.base_img,
                s.size_name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN size s     ON s.id = oi.size_id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Nhóm orders theo status cho trang history.
     */
    public static function groupByStatus(array $orders): array {
        $pages = [
            'all'      => $orders,
            'processing' => [],
            'processed'  => [],
            'shipping'   => [],
            'shipped'    => [],
            'cancelled'  => [],
        ];
        foreach ($orders as $order) {
            $status = $order['status'] ?? 'processing';
            if (isset($pages[$status])) {
                $pages[$status][] = $order;
            }
        }
        return $pages;
    }

    /**
     * Huỷ đơn hàng — chỉ cho phép khi status = 'processing'.
     */

    
    public static function cancel(int $orderId, int $userId): bool {
    $db = Database::getInstance();
    $db->begin_transaction();
    try {
        // Kiểm tra đơn hợp lệ + lock
        $stmt = $db->prepare("
            SELECT id, status FROM orders
            WHERE id = ? AND user_id = ? AND (status = 'processing' OR status = 'processed')
            FOR UPDATE
        ");
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            throw new Exception("Đơn không hợp lệ hoặc không thể hủy");
        }

        // Cập nhật trạng thái
        $stmt2 = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        $stmt2->bind_param("i", $orderId);
        $stmt2->execute();

        // Lấy items để hoàn kho
        $stmt3 = $db->prepare("
            SELECT product_id, size_id, quantity, price
            FROM order_items WHERE order_id = ?
        ");
        $stmt3->bind_param("i", $orderId);
        $stmt3->execute();
        $items = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($items)) {
            throw new Exception("Không tìm thấy sản phẩm trong đơn");
        }

        foreach ($items as $item) {
            // Hoàn kho
            $stmt4 = $db->prepare("
                UPDATE inventory SET quantity = quantity + ?
                WHERE product_id = ? AND size_id = ?
            ");
            $stmt4->bind_param("iii",
                $item['quantity'], $item['product_id'], $item['size_id']
            );
            $stmt4->execute();

            // Ghi log trả hàng
            $note  = "Trả hàng về kho do hủy đơn #$orderId";
            $stmt5 = $db->prepare("
                INSERT INTO inventory_logs
                    (order_id, product_id, size_id, type, quantity, import_price, note)
                VALUES (?, ?, ?, 'import', ?, ?, ?)
            ");
            $stmt5->bind_param("iiidds",
                $orderId,
                $item['product_id'],
                $item['size_id'],
                $item['quantity'],
                $item['price'],
                $note
            );
            $stmt5->execute();
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollback();
        error_log("OrderModel::cancel failed: " . $e->getMessage());
        return false;
    }
}
    /**
     * Trả về badge CSS class và label theo status.
     */
    public static function getStatusBadge(string $status): array {
        $map = [
            'processing' => ['class' => 'processing', 'label' => 'Đang xử lý'],
            'processed'  => ['class' => 'processed',  'label' => 'Đã xử lý'],
            'shipping'   => ['class' => 'shipping',   'label' => 'Đang giao'],
            'shipped'    => ['class' => 'shipped',     'label' => 'Đã giao'],
            'cancelled'  => ['class' => 'cancelled',   'label' => 'Đã huỷ'],
        ];
        return $map[$status] ?? ['class' => 'neutral', 'label' => $status];
    }
    public static function updateStatus(int $orderId, string $status): bool {
        $allowed = ['processing', 'processed', 'shipping', 'shipped', 'cancelled'];
        if (!in_array($status, $allowed)) return false;

        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }
}