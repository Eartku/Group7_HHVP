<?php
class SizeModel extends Model {

    public static function getAll(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT id, size_name, price_adjust
            FROM size
            ORDER BY id ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getById(int $sizeId): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, size_name, price_adjust
            FROM size WHERE id = ?
        ");
        $stmt->bind_param("i", $sizeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function create(string $sizeName, float $priceAdjust): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO size (size_name, price_adjust) VALUES (?, ?)
        ");
        $stmt->bind_param("sd", $sizeName, $priceAdjust);
        return $stmt->execute();
    }

    public static function update(int $sizeId, string $sizeName, float $priceAdjust): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            UPDATE size SET size_name = ?, price_adjust = ? WHERE id = ?
        ");
        $stmt->bind_param("sdi", $sizeName, $priceAdjust, $sizeId);
        return $stmt->execute();
    }

    public static function delete(int $sizeId): bool {
        // Không xóa nếu đang có inventory dùng size này
        if (InventoryModel::existsBySizeId($sizeId)) {
            return false;
        }
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM size WHERE id = ?");
        $stmt->bind_param("i", $sizeId);
        return $stmt->execute();
    }

    public static function count(): int {
        $db     = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) AS total FROM size");
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    public static function exists(int $sizeId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM size WHERE id = ?");
        $stmt->bind_param("i", $sizeId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }

    public static function nameExists(string $sizeName, int $excludeId = 0): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) AS total FROM size
            WHERE size_name = ? AND id != ?
        ");
        $stmt->bind_param("si", $sizeName, $excludeId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }
}