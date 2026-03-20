<?php
class CategoryModel extends Model {
    public static function getAll(): array {
        $db     = Database::getInstance();
        $result = $db->query("SELECT id, name FROM categories");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getById(int $id): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id, name FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}
