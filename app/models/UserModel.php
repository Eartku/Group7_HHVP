<?php
// User Model - users table
// app/models/UserModel.php
class UserModel extends Model {

    /**
     * Lấy đường dẫn avatar của user.
     * Không htmlspecialchars ở đây — escape tại view khi render HTML.
     */
    public static function getAvatar(int $userId): string {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!empty($user['avatar'])) {
            return "uploads/avatars/" . $user['avatar']; // raw path, escape tại view
        }

        return "images/user.png";
    }
    // Thêm method mới
    public static function findByEmail(string $email): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Tìm user theo username.
     * Bao gồm status để kiểm tra blocked/inactive tại controller.
     */
    public static function findByUsername(string $username): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id, username, password, role, status FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Tìm user theo ID — dùng thống nhất thay cho getUserByID().
     */
    public static function findById(int $userId): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT id, username, fullname, address, phone, email, password, role, avatar, status, created_at
            FROM users WHERE id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Tạo user mới.
     * role và status được set explicit thay vì phụ thuộc DB DEFAULT.
     */
    public static function create(array $data): bool {
        $db = Database::getInstance();

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $fullname = $data['fullname'] ?? '';
        $address  = $data['address']  ?? '';
        $phone    = $data['phone']    ?? '';
        $email    = $data['email']    ?? '';

        $sql = "INSERT INTO users (username, password, fullname, address, phone, email, role, status)
                VALUES (?, ?, ?, ?, ?, ?, 'customer', 'active')";

        $stmt = $db->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed: " . $db->error);
            return false;
        }

        $stmt->bind_param("ssssss",
            $username,
            $password,
            $fullname,
            $address,
            $phone,
            $email
        );

        $result = $stmt->execute();

        if (!$result) {
            if ($db->errno === 1062) {
                return false; // Duplicate username hoặc email
            }
            error_log("Execute failed: " . $stmt->error);
            return false;
        }

        return true;
    }

    /**
     * Cập nhật avatar.
     */
    public static function updateAvatar(int $userId, string $filename): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $userId);
        return $stmt->execute();
    }

    /**
     * Cập nhật mật khẩu.
     */
    public static function updatePassword(int $userId, string $hashedPassword): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }

    /**
     * Cập nhật thông tin cá nhân.
     */
    public static function updateProfile(int $userId, array $data): bool {
        $db = Database::getInstance();

        $email    = $data['email']    ?? '';
        $fullname = $data['fullname'] ?? '';
        $phone    = $data['phone']    ?? '';
        $address  = $data['address']  ?? '';

        $sql  = "UPDATE users SET email = ?, fullname = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssi", $email, $fullname, $phone, $address, $userId);

        return $stmt->execute();
    }

    /**
     * Đếm tổng số user active (dùng cho admin dashboard).
     */
    public static function count(): int {
        $db     = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) AS total FROM users WHERE status = 'active'");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    /**
     * Đếm theo từng status (dùng cho admin thống kê).
     */
    public static function countByStatus(): array {
        $db     = Database::getInstance();
        $result = $db->query(
            "SELECT status, COUNT(*) AS total FROM users GROUP BY status"
        );
        $counts = ['active' => 0, 'blocked' => 0, 'inactive' => 0];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['status']] = (int)$row['total'];
        }
        return $counts;
    }
    public static function getByID(int $userId): ?array {
        return self::findById($userId);
    }
    public static function getAll(): array {
        $db     = Database::getInstance();
        $result = $db->query(
            "SELECT id, username, fullname, email, phone, address, role, status FROM users"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public static function updateStatus(int $userId, string $status): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $userId);
        return $stmt->execute();
    }
    public static function delete(int $userId): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
    public static function getByRole(string $role): array {
        $db     = Database::getInstance();
        $stmt   = $db->prepare(
            "SELECT id, username, fullname, email, phone, address, role, status
             FROM users WHERE role = ?"
        );
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public static function searchCustomers(
        string $searchType  = 'name',
        string $searchValue = '',
        int    $limit       = 15,
        int    $offset      = 0
    ): array {
        $db     = Database::getInstance();
        $wheres = ["role = 'customer'"];
        $params = [];
        $types  = '';

        if ($searchValue !== '') {
            $like = '%' . $searchValue . '%';
            switch ($searchType) {
                case 'id':
                    $wheres[] = "id = ?";
                    $params[] = (int)$searchValue;
                    $types   .= 'i';
                    break;
                case 'email':
                    $wheres[] = "email LIKE ?";
                    $params[] = $like;
                    $types   .= 's';
                    break;
                case 'phone':
                    $wheres[] = "phone LIKE ?";
                    $params[] = $like;
                    $types   .= 's';
                    break;
                default: // name
                    $wheres[] = "fullname LIKE ?";
                    $params[] = $like;
                    $types   .= 's';
                    break;
            }
        }

        $where = 'WHERE ' . implode(' AND ', $wheres);

        // Count
        $stmtCount = $db->prepare("SELECT COUNT(*) AS total FROM users $where");
        if ($types) $stmtCount->bind_param($types, ...$params);
        $stmtCount->execute();
        $total = (int)$stmtCount->get_result()->fetch_assoc()['total'];

        // Data
        $params[] = $limit;
        $params[] = $offset;
        $types   .= 'ii';

        $stmt = $db->prepare("
            SELECT id, username, fullname, email, phone, address, status, created_at
            FROM users
            $where
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return ['data' => $rows, 'total' => $total];
    }
}