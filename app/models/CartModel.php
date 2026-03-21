<?php
class CartModel extends Model {

    // Lấy hoặc tạo cart cho user
    public static function getOrCreate(int $userId): int {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $cart = $stmt->get_result()->fetch_assoc();

        if ($cart) return $cart['id'];

        $stmt = $db->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $db->insert_id;
    }

    // Lấy items trong cart kèm thông tin sản phẩm + tồn kho
    public static function getItems(int $cartId): array {
        $db  = Database::getInstance();
        $sql = "
            SELECT
                ci.id,
                ci.quantity,
                ci.price,
                ci.product_id,
                p.name,
                p.base_img  AS image,
                s.size_name AS size,
                s.id        AS size_id,
                IFNULL(inv.quantity, 0) AS stock
            FROM cart_items ci
            JOIN products p   ON p.id  = ci.product_id
            JOIN size s       ON s.id  = ci.size_id
            LEFT JOIN inventory inv
                ON inv.product_id = ci.product_id
                AND inv.size_id   = ci.size_id
            WHERE ci.cart_id = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tính giá đúng theo profit_rate + price_adjust của size
    public static function calcPrice(int $productId, int $sizeId): float {
        $db  = Database::getInstance();
        $sql = "
            SELECT
                p.profit_rate,
                COALESCE(inv.avg_import_price, 0) AS avg_import_price,
                COALESCE(s.price_adjust, 0)       AS price_adjust
            FROM products p
            LEFT JOIN inventory inv
                ON inv.product_id = p.id
                AND inv.size_id   = ?
            LEFT JOIN size s ON s.id = ?
            WHERE p.id = ?
            LIMIT 1
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iii", $sizeId, $sizeId, $productId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return 0;

        return PriceHelper::calcSalePrice(
            $row['avg_import_price'],
            $row['profit_rate'],
            $row['price_adjust']
        );
    }

    // Thêm item vào cart
    public static function addItem(int $cartId, int $productId, int $sizeId, int $qty): bool {
        $db = Database::getInstance();

        // Nếu đã có item cùng product + size thì tăng qty
        $stmt = $db->prepare("
            SELECT id, quantity FROM cart_items
            WHERE cart_id = ? AND product_id = ? AND size_id = ?
        ");
        $stmt->bind_param("iii", $cartId, $productId, $sizeId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        $price = self::calcPrice($productId, $sizeId);

        if ($existing) {
            $newQty = $existing['quantity'] + $qty;
            $stmt   = $db->prepare("
                UPDATE cart_items SET quantity = ?, price = ? WHERE id = ?
            ");
            $stmt->bind_param("idi", $newQty, $price, $existing['id']);
        } else {
            $stmt = $db->prepare("
                INSERT INTO cart_items (cart_id, product_id, size_id, quantity, price)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiiid", $cartId, $productId, $sizeId, $qty, $price);
        }

        return $stmt->execute();
    }

    // Cập nhật số lượng + size
    public static function updateItem(int $itemId, int $cartId, int $sizeId, int $qty): bool {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT product_id FROM cart_items WHERE id = ? AND cart_id = ?");
        $stmt->bind_param("ii", $itemId, $cartId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        if (!$item) return false;

        $stmtStock = $db->prepare("
            SELECT quantity FROM inventory WHERE product_id = ? AND size_id = ?
        ");
        $stmtStock->bind_param("ii", $item['product_id'], $sizeId);
        $stmtStock->execute();
        $stock = $stmtStock->get_result()->fetch_assoc();
        if (!$stock || $stock['quantity'] <= 0) return false;

        $qty   = min($qty, $stock['quantity']);
        $price = self::calcPrice($item['product_id'], $sizeId);

        // Fix: "iidii" = 5 types, 5 biến
        $stmt = $db->prepare("
            UPDATE cart_items SET quantity = ?, size_id = ?, price = ?
            WHERE id = ? AND cart_id = ?
        ");
        $stmt->bind_param("iidii", $qty, $sizeId, $price, $itemId, $cartId);
        return $stmt->execute();
    }

    // Xóa item
    public static function removeItem(int $itemId, int $cartId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id = ?");
        $stmt->bind_param("ii", $itemId, $cartId);
        return $stmt->execute();
    }

    // Xóa toàn bộ cart (sau khi checkout)
    public static function clear(int $cartId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->bind_param("i", $cartId);
        return $stmt->execute();
    }

    // Kiểm tra có thể checkout không
    public static function canCheckout(int $cartId): bool {
        $db  = Database::getInstance();
        $sql = "
            SELECT ci.quantity, IFNULL(inv.quantity, 0) AS stock
            FROM cart_items ci
            LEFT JOIN inventory inv
                ON inv.product_id = ci.product_id
                AND inv.size_id   = ci.size_id
            WHERE ci.cart_id = ?
        ";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($rows)) return false;

        foreach ($rows as $r) {
            if ($r['stock'] <= 0 || $r['quantity'] > $r['stock']) return false;
        }
        return true;
    }
}