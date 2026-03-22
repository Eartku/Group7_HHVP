<?php
class CategoryModel extends Model {

    public static function getAll(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT c.id, c.name, c.description, c.image, c.status,
                   COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
            GROUP BY c.id
            ORDER BY c.id ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function getById(int $id): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function count(): int {
        $db     = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) AS total FROM categories");
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    public static function create(string $name, string $description = '', string $image = ''): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO categories (name, description, image)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $name, $description, $image);
        return $stmt->execute();
    }
    public static function update(int $id, string $name, string $description = '', string $image = '', string $status = 'active'): bool {
        $db = Database::getInstance();
        if ($image !== '') {
            $stmt = $db->prepare("UPDATE categories SET name = ?, description = ?, image = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $description, $image, $status, $id);
        } else {
            $stmt = $db->prepare("UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $status, $id);
        }
        return $stmt->execute();
    }

   public static function delete(int $id): bool {
        $db   = Database::getInstance();

        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ? AND status = 'active'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ((int)$stmt->get_result()->fetch_assoc()['total'] > 0) return false;

        $stmt = $db->prepare("UPDATE categories SET status = 'inactive' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public static function restore(int $id): bool {
        $db   = Database::getInstance();

        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM products WHERE category_id = ? AND status = 'inactive'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ((int)$stmt->get_result()->fetch_assoc()['total'] > 0) return false;

        $stmt = $db->prepare("UPDATE categories SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function nameExists(string $name, int $excludeId = 0): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT COUNT(*) AS total FROM categories
            WHERE name = ? AND id != ?
        ");
        $stmt->bind_param("si", $name, $excludeId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }

    public static function getStats(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT c.id, c.name, COUNT(p.id) AS total
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
            GROUP BY c.id
            ORDER BY total DESC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public static function exists(int $id): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) AS total FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }
    public static function getAllActive(): array {
        $db     = Database::getInstance();
        $result = $db->query("
            SELECT c.id, c.name, c.description, c.image, c.status,
                COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
            WHERE c.status = 'active'
            GROUP BY c.id
            ORDER BY c.id ASC
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}