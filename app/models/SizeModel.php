<?php
class SizeModel {
    public static function getAll(): array {
        $db     = Database::getInstance();
        $result = $db->query("SELECT id AS size_id, size_name AS size, price_adjust FROM size ORDER BY id");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getById(int $sizeId): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id AS size_id, size_name AS size, price_adjust FROM size WHERE id = ?");
        $stmt->bind_param("i", $sizeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Thêm size mới.
     */
    public static function create(string $sizeName, float $priceAdjust): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO size (size_name, price_adjust) VALUES (?, ?)");
        $stmt->bind_param("sd", $sizeName, $priceAdjust);
        return $stmt->execute();
    }

    /**
     * Cập nhật size.
     */
    public static function update(int $sizeId, string $sizeName, float $priceAdjust): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE size SET size_name = ?, price_adjust = ? WHERE id = ?");
        $stmt->bind_param("sdi", $sizeName, $priceAdjust, $sizeId);
        return $stmt->execute();
    }

    /**
     * Xóa size (chuyển sang status 'inactive').
     */
    public static function delete(int $sizeId): bool {
        // Trước khi xóa, kiểm tra xem có tồn tại inventory nào đang dùng size này không
        if (InventoryModel::existsBySizeId($sizeId)) {
            return false; // Không xóa được vì có tồn tại inventory liên quan
        }

        // Nếu không có inventory nào liên quan, tiến hành xóa
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM size WHERE id = ?");
        $stmt->bind_param("i", $sizeId);
        return $stmt->execute();
    }
}