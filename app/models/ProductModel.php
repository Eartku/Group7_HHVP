<?php
class ProductModel extends Model {
    private static function buildPriceHaving(int $priceMin, int $priceMax, array &$params, string &$types): string {
        // Dùng expression trực tiếp thay vì alias vì HAVING không dùng được alias trong subquery
        $expr = PriceHelper::sqlSalePrice(''); // không có AS

        if ($priceMin > 0 && $priceMax > 0) {
            $params[] = $priceMin; $params[] = $priceMax; $types .= 'ii';
            return "HAVING ($expr) BETWEEN ? AND ?";
        }
        if ($priceMin > 0) {
            $params[] = $priceMin; $types .= 'i';
            return "HAVING ($expr) >= ?";
        }
        if ($priceMax > 0) {
            $params[] = $priceMax; $types .= 'i';
            return "HAVING ($expr) <= ?";
        }
        return '';
    }

   public static function count(int $categoryId = 0, string $search = '', int $priceMin = 0, int $priceMax = 0): int {
        $db     = Database::getInstance();
        $wheres = ["p.status = 'active'"];
        $params = []; $types = '';

        if ($categoryId > 0) { $wheres[] = "p.category_id = ?"; $params[] = $categoryId; $types .= 'i'; }
        if ($search !== '')  { $wheres[] = "p.name LIKE ?";     $params[] = '%'.$search.'%'; $types .= 's'; }

        $where = 'WHERE ' . implode(' AND ', $wheres);

        // Filter giá ở outer WHERE thay vì HAVING
        $outerWhere = '';
        if ($priceMin > 0 && $priceMax > 0) {
            $outerWhere   = 'WHERE sale_price BETWEEN ? AND ?';
            $params[] = $priceMin; $params[] = $priceMax; $types .= 'ii';
        } elseif ($priceMin > 0) {
            $outerWhere   = 'WHERE sale_price >= ?';
            $params[] = $priceMin; $types .= 'i';
        } elseif ($priceMax > 0) {
            $outerWhere   = 'WHERE sale_price <= ?';
            $params[] = $priceMax; $types .= 'i';
        }

        $stmt = $db->prepare("
            SELECT COUNT(*) AS total
            FROM (
                SELECT p.id,
                    " . PriceHelper::sqlSalePrice('sale_price') . "
                FROM products p
                $where
                GROUP BY p.id, p.profit_rate
            ) sub
            $outerWhere
        ");
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getList(int $categoryId = 0, int $limit = 8, int $offset = 0, string $search = '', int $priceMin = 0, int $priceMax = 0): array {
    $db     = Database::getInstance();
    $wheres = ["p.status = 'active'"];
    $params = []; $types = '';

    if ($categoryId > 0) { $wheres[] = "p.category_id = ?"; $params[] = $categoryId; $types .= 'i'; }
    if ($search !== '')  { $wheres[] = "p.name LIKE ?";     $params[] = '%'.$search.'%'; $types .= 's'; }

        $where = 'WHERE ' . implode(' AND ', $wheres);

        $outerWhere = '';
        if ($priceMin > 0 && $priceMax > 0) {
            $outerWhere   = 'WHERE sale_price BETWEEN ? AND ?';
            $params[] = $priceMin; $params[] = $priceMax; $types .= 'ii';
        } elseif ($priceMin > 0) {
            $outerWhere   = 'WHERE sale_price >= ?';
            $params[] = $priceMin; $types .= 'i';
        } elseif ($priceMax > 0) {
            $outerWhere   = 'WHERE sale_price <= ?';
            $params[] = $priceMax; $types .= 'i';
        }

        $params[] = $limit; $params[] = $offset; $types .= 'ii';

        $stmt = $db->prepare("
            SELECT *
            FROM (
                SELECT
                    p.id, p.name,
                    p.base_img    AS image,
                    p.description,
                    p.profit_rate,
                    c.name        AS category_name,
                    " . PriceHelper::sqlAvgImport() . ",
                    " . PriceHelper::sqlTotalStock() . ",
                    " . PriceHelper::sqlSalePrice('sale_price') . "
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                $where
                GROUP BY p.id, p.profit_rate, c.name, p.name, p.base_img, p.description
            ) sub
            $outerWhere
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param($types, ...$params);
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

    //  Bỏ product_id — size dùng chung toàn hệ thống
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

    public static function getRelated(int $productId, int $limit = 7): array {
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
        $image       = $data['image']       ?? '';  // ✅ sửa 'base_img' → 'image'
        $description = $data['description'] ?? '';
        $profit_rate = $data['profit_rate'] ?? 0;
        $status      = $data['status']      ?? 'active'; // ✅ thêm status

        $stmt = $db->prepare("
            INSERT INTO products (name, category_id, base_img, description, profit_rate, status)
            VALUES (?, ?, ?, ?, ?, ?)
        "); // ✅ thêm status vào SQL
        $stmt->bind_param("sissds", $name, $category_id, $image, $description, $profit_rate, $status);
        // ✅ thêm 's' cho status ở cuối
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
    public static function delete(int $id): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    // Đếm có filter search (đã có nhưng kiểm tra lại — dùng cho admin)
    public static function countAll(
        int    $categoryId   = 0,
        string $search       = '',
        string $statusFilter = '',
        string $stockFilter  = ''
    ): int {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = [];
        $types  = '';

        if ($categoryId > 0)      { $wheres[] = "p.category_id = ?"; $params[] = $categoryId; $types .= 'i'; }
        if ($search !== '')        { $wheres[] = "p.name LIKE ?";     $params[] = '%'.$search.'%'; $types .= 's'; }
        if ($statusFilter !== '')  { $wheres[] = "p.status = ?";      $params[] = $statusFilter; $types .= 's'; }
        if ($stockFilter === 'instock')    $wheres[] = "(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE product_id = p.id) > 0";
        if ($stockFilter === 'outofstock') $wheres[] = "(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE product_id = p.id) = 0";

        $where = 'WHERE ' . implode(' AND ', $wheres);
        $stmt  = $db->prepare("SELECT COUNT(*) as total FROM products p $where");
        if ($types) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    public static function getListAdmin(
        int    $categoryId   = 0,
        int    $limit        = 8,
        int    $offset       = 0,
        string $search       = '',
        string $statusFilter = '',
        string $stockFilter  = ''
    ): array {
        $db     = Database::getInstance();
        $wheres = ['1=1'];
        $params = [];
        $types  = '';

        if ($categoryId > 0)      { $wheres[] = "p.category_id = ?"; $params[] = $categoryId; $types .= 'i'; }
        if ($search !== '')        { $wheres[] = "p.name LIKE ?";     $params[] = '%'.$search.'%'; $types .= 's'; }
        if ($statusFilter !== '')  { $wheres[] = "p.status = ?";      $params[] = $statusFilter; $types .= 's'; }
        if ($stockFilter === 'instock')    $wheres[] = "(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE product_id = p.id) > 0";
        if ($stockFilter === 'outofstock') $wheres[] = "(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE product_id = p.id) = 0";

        $where = 'WHERE ' . implode(' AND ', $wheres);

        $sql = "
            SELECT
                p.id, p.name,
                p.base_img AS image,
                p.description,
                p.profit_rate,
                p.status,
                c.name AS category_name,
                " . PriceHelper::sqlAvgImport() . ",
                " . PriceHelper::sqlTotalStock() . ",
                " . PriceHelper::sqlSalePrice()  . "
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            $where
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;
        $types   .= 'ii';

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public static function updateStatus(int $id, string $status): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE products SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    // Dùng cho shop/public — chỉ lấy active
    
}