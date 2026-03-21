<?php
class ProductModel extends Model {

    public static function count(int $categoryId = 0, string $search = ''): int {
        $db     = Database::getInstance();
        $wheres = ["p.status = 'active'"];
        if ($categoryId > 0) $wheres[] = "p.category_id = ?";
        if ($search !== '')  $wheres[] = "p.name LIKE ?";
        $where = 'WHERE ' . implode(' AND ', $wheres);

        $stmt       = $db->prepare("SELECT COUNT(*) as total FROM products p $where");
        $searchLike = $search !== '' ? '%' . $search . '%' : '';

        if ($categoryId > 0 && $search !== '') $stmt->bind_param("is", $categoryId, $searchLike);
        elseif ($categoryId > 0)               $stmt->bind_param("i",  $categoryId);
        elseif ($search !== '')                $stmt->bind_param("s",  $searchLike);

        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getList(int $categoryId = 0, int $limit = 8, int $offset = 0, string $search = ''): array {
        $db     = Database::getInstance();
        $wheres = ["p.status = 'active'"];
        if ($categoryId > 0) $wheres[] = "p.category_id = ?";
        if ($search !== '')  $wheres[] = "p.name LIKE ?";
        $where  = 'WHERE ' . implode(' AND ', $wheres);

        $sql = "
            SELECT
                p.id, p.name,
                p.base_img    AS image,
                p.description,
                p.profit_rate,
                c.name        AS category_name,
                " . PriceHelper::sqlAvgImport() . ",
                " . PriceHelper::sqlTotalStock() . ",
                " . PriceHelper::sqlSalePrice()  . "
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            $where
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $stmt       = $db->prepare($sql);
        $searchLike = $search !== '' ? '%' . $search . '%' : '';

        if ($categoryId > 0 && $search !== '') {
            $stmt->bind_param("isii", $categoryId, $searchLike, $limit, $offset);
        } elseif ($categoryId > 0) {
            $stmt->bind_param("iii",  $categoryId, $limit, $offset);
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

    public static function getDetail(int $id): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                p.*,
                c.name AS category_name,
                " . PriceHelper::sqlAvgImport() . ",
                " . PriceHelper::sqlTotalStock() . ",
                " . PriceHelper::sqlSalePrice()  . "
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.id = ? AND p.status = 'active'
            LIMIT 1
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    // ✅ Bỏ product_id — size dùng chung toàn hệ thống
    public static function getSizes(int $productId): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                s.id           AS size_id,
                s.size_name    AS size,
                s.price_adjust,
                COALESCE(inv.quantity, 0) AS stock
            FROM size s
            LEFT JOIN inventory inv
                ON inv.product_id = ? AND inv.size_id = s.id
            ORDER BY s.id ASC
        ");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getRelated(int $productId, int $limit = 4): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("
            SELECT
                p.id, p.name,
                p.base_img AS image,
                " . PriceHelper::sqlSalePrice() . "
            FROM products p
            WHERE p.id != ? AND p.status = 'active'
            GROUP BY p.id
            ORDER BY RAND()
            LIMIT ?
        ");
        $stmt->bind_param("ii", $productId, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getImages(int $productId): array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT image FROM product_img WHERE product_id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return array_column(
            $stmt->get_result()->fetch_all(MYSQLI_ASSOC),
            'image'
        );
    }

    public static function create(array $data): bool {
        $db          = Database::getInstance();
        $name        = $data['name']        ?? '';
        $category_id = $data['category_id'] ?? 0;
        $image       = $data['image']       ?? '';
        $description = $data['description'] ?? '';
        $profit_rate = $data['profit_rate'] ?? 0;

        $stmt = $db->prepare("
            INSERT INTO products (name, category_id, base_img, description, profit_rate)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sissd", $name, $category_id, $image, $description, $profit_rate);
        return $stmt->execute();
    }

    public static function update(int $id, array $data): bool {
        $db          = Database::getInstance();
        $name        = $data['name']        ?? '';
        $category_id = $data['category_id'] ?? 0;
        $description = $data['description'] ?? '';
        $profit_rate = $data['profit_rate'] ?? 0;
        $status      = $data['status']      ?? 'active';

        $stmt = $db->prepare("
            UPDATE products
            SET name = ?, category_id = ?, description = ?, profit_rate = ?, status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sisdsi", $name, $category_id, $description, $profit_rate, $status, $id);
        return $stmt->execute();
    }

    public static function updateImage(int $id, string $image): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET base_img = ? WHERE id = ?");
        $stmt->bind_param("si", $image, $id);
        return $stmt->execute();
    }
    public static function getStock(int $productId, int $sizeId): int {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT quantity FROM inventory WHERE product_id = ? AND size_id = ?");
        $stmt->bind_param("ii", $productId, $sizeId);
        $stmt->execute();
        return (int)($stmt->get_result()->fetch_assoc()['quantity'] ?? 0);
    }
    public static function updateStock(int $productId, int $sizeId, int $quantity): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ? AND size_id = ?");
        $stmt->bind_param("iii", $quantity, $productId, $sizeId);
        return $stmt->execute();
    }
    public static function findById($id): ?array {
    $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);   
        $stmt->execute();

        $result = $stmt->get_result();  
        return $result->fetch_assoc() ?: null;
    }
}