<?php
// Profile Controller - Personal info, change password, avatar
class ProfileController extends Controller {

    public function index(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];
        $user   = UserModel::findById($userId);

        if (!$user) {
            include __DIR__ . '/../views/errors/404.php';
            exit();
        }

        $this->view('profile', [
            'user'     => $user
        ]);
    }

    public function update(): void {
        $this->requireLogin();
        $userId = $_SESSION['user']['id'];
        $user   = UserModel::findById($userId);
        $errors = [];

        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $address  = trim($_POST['address']  ?? '');

        if ($fullname === '') $errors[] = 'Họ tên không được để trống';
        if ($email    === '') $errors[] = 'Email không được để trống';

        $avatarFileName = $user['avatar'] ?? '';
        // ── Xử lý xóa avatar ──
        if (($_POST['delete_avatar'] ?? '0') === '1') {
            // Xóa file vật lý nếu có
            if (!empty($user['avatar'])) {
                $oldFile = __DIR__ . '/../uploads/avatars/' . $user['avatar'];
                if (file_exists($oldFile)) @unlink($oldFile);
            }
            $avatarFileName = '';
        } else {
            $avatarFileName = $user['avatar'] ?? '';

            // ── Upload avatar mới ──
            if (!empty($_FILES['avatar']['name'])) {
                $uploadDir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $ext     = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $allowed)) {
                    $errors[] = 'Chỉ cho phép JPG, JPEG, PNG, GIF, WEBP';
                } else {
                    // Xóa ảnh cũ trước khi lưu ảnh mới
                    if (!empty($user['avatar'])) {
                        $oldFile = $uploadDir . $user['avatar'];
                        if (file_exists($oldFile)) @unlink($oldFile);
                    }
                    $avatarFileName = time() . '_' . basename($_FILES['avatar']['name']);
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $avatarFileName);
                }
            }
        }

        if (!empty($errors)) {
            $this->view('profile', [
                'user'   => $user,
                'errors' => $errors,
            ]);
            return;
        }

        UserModel::updateProfile($userId, [
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
            'address'  => $address,
        ]);

        // Cập nhật avatar nếu có thay đổi
        if ($avatarFileName !== ($user['avatar'] ?? '')) {
            UserModel::updateAvatar($userId, $avatarFileName);
        }

        $this->redirect(BASE_URL . '/index.php?url=profile&success=1');
    }

    public function changePassword(): void {
        $this->requireLogin();
        $userId  = $_SESSION['user']['id'];
        $user    = UserModel::findById($userId);
        $errors  = [];

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';

        if ($current === '' || $new === '') {
            $errors[] = 'Vui lòng nhập đầy đủ mật khẩu';
        } elseif (!password_verify($current, $user['password'])) {
            $errors[] = 'Mật khẩu hiện tại không đúng';
        }

        if (!empty($errors)) {
            $this->view('profile', [
                'user'     => $user,
                'errors'   => $errors
            ]);
            return;
        }

        UserModel::updatePassword($userId, password_hash($new, PASSWORD_DEFAULT));
        $this->redirect('../app/index.php?url=profile&success=2');
    }
}
