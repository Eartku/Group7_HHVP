<?php
class ProductModel extends Model {

    public static function count(int $categoryId = 0, string $search = ''): int {
        $db     = Database::getInstance();
        $wheres = [];
        if ($categoryId > 0) $wheres[] = "p.category_id = ?";
        if ($search !== '')  $wheres[] = "p.name LIKE ?";
        $where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';

        $stmt       = $db->prepare("SELECT COUNT(*) as total FROM products p $where");
        $searchLike = $search !== '' ? '%' . $search . '%' : '';

        if ($categoryId > 0 && $search !== '') $stmt->bind_param("is", $categoryId, $searchLike);
        elseif ($categoryId > 0)               $stmt->bind_param("i",  $categoryId);
        elseif ($search !== '')                $stmt->bind_param("s",  $searchLike);

        $stmt->execute();
        return (int) $stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getList(int $categoryId = 0, int $limit = 8, int $offset = 0, string $search = ''): array {
        $db     = Database::getInstance();
        $wheres = [];
        if ($categoryId > 0) $wheres[] = "p.category_id = ?";
        if ($search !== '')  $wheres[] = "p.name LIKE ?";
        $where = $wheres ? 'WHERE ' . implode(' AND ', $wheres) : '';

        $sql  = "
            SELECT p.id, p.name, p.base_img,
                   COALESCE(ROUND(AVG(il.import_price) * 1.1), 0) AS sale_price
            FROM products p
            LEFT JOIN inventory_logs il ON il.product_id = p.id
            $where
            GROUP BY p.id
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";
        $stmt       = $db->prepare($sql);
        $searchLike = $search !== '' ? '%' . $search . '%' : '';

        if ($categoryId > 0 && $search !== '') {
            $stmt->bind_param("isii", $categoryId, $searchLike, $limit, $offset);
        } elseif ($categoryId > 0) {
            $stmt->bind_param("iii",   $categoryId, $limit, $offset);
        } elseif ($search !== '') {
            $stmt->bind_param("sii",  $searchLike, $limit, $offset);
        } else {
            $stmt->bind_param("ii",   $limit, $offset);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById(int $id): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}