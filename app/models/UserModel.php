<?php
// User Model - users table
// app/models/UserModel.php
class UserModel extends Model {
    public static function getAvatar(int $userId): string {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!empty($user['avatar'])) {
            return "uploads/avatars/" . htmlspecialchars($user['avatar']);
        }

        return "images/user.png"; // mặc định
    }
    public static function findByUsername(string $username): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function findByID(string $ID): ?array {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, fullname, address, phone, email, password, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public static function create(array $data): bool {
        $db = Database::getInstance();

        $username = $data['username']         ?? '';
        $email    = $data['email']            ?? '';
        $password = $data['password']         ?? '';
        $fullname = $data['fullname']         ?? '';
        $phone    = $data['phone']            ?? '';
        $avatar   = $data['avatar']           ?? '';
        $address  = $data['address']          ?? '';
        

        $sql  = "INSERT INTO users (username, email, password, fullname, phone, avatar, address)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssss", $username, $email, $password, $fullname, $phone, $address, $avatar);

        return $stmt->execute();
    }

    public static function updateAvatar(int $userId, string $filename): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $userId);
        return $stmt->execute();
    }

    public static function updatePassword(int $userId, string $hashedPassword): bool {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }

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
}
