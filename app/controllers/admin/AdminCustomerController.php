<?php
class AdminCustomerController extends Controller{

        public function index(): void {
        $this->requireAdmin();

        $search_type  = $_GET['search_type']  ?? 'name';
        $search_value = trim($_GET['search_value'] ?? '');
        $search_done  = isset($_GET['do_search']);
        $search_error = '';
        $page         = max(1, (int)($_GET['page'] ?? 1));
        $limit        = 15;
        $offset       = ($page - 1) * $limit;

        // Xử lý lock/unlock
        if (isset($_GET['action'], $_GET['id'])) {
            $targetId  = (int)$_GET['id'];
            $action    = $_GET['action'];
            if ($targetId > 0 && in_array($action, ['lock', 'unlock'])) {
                $newStatus = $action === 'lock' ? 'inactive' : 'active';
                UserModel::updateStatus($targetId, $newStatus);
            }
            $this->redirect(BASE_URL . '/index.php?url=admin-customers');
            return;
        }

        // Validate search
        if ($search_done) {
            if ($search_type === 'id' && $search_value !== '' && !ctype_digit($search_value)) {
                $search_error = 'Mã KH phải là số nguyên dương.';
            }
            if ($search_type === 'phone' && $search_value !== ''
                && !preg_match('/^[0-9]{9,11}$/', $search_value)) {
                $search_error = 'Số điện thoại không hợp lệ.';
            }
            if ($search_type === 'email' && $search_value !== ''
                && !filter_var($search_value, FILTER_VALIDATE_EMAIL)) {
                $search_error = 'Email không hợp lệ.';
            }
        }

        $customers   = [];
        $total_rows  = 0;
        $total_pages = 1;

        if (!$search_error) {
            // ✅ Dùng search thực sự, không load all rồi filter
            $result      = UserModel::searchCustomers($search_type, $search_value, $limit, $offset);
            $customers   = $result['data'];
            $total_rows  = $result['total'];
            $total_pages = max(1, (int)ceil($total_rows / $limit));
        }

        $this->adminView('admin/customers/index', [
            'customers'    => $customers,
            'search_type'  => $search_type,
            'search_value' => $search_value,
            'search_done'  => $search_done,
            'search_error' => $search_error,
            'total_rows'   => $total_rows,
            'total_pages'  => $total_pages,
            'page'         => $page,
        ]);
    }

    public function create(): void {
        $this->requireAdmin();

        $errors   = [];
        $username = $fullname = $email = $phone = $address = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? $username);
            $email    = trim($_POST['email']    ?? '');
            $phone    = trim($_POST['phone']    ?? '');
            $address  = trim($_POST['address']  ?? '');

            // Validate
            if (!$username) $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            if (!$email)    $errors['email']    = 'Vui lòng nhập email';
            if (!$phone)    $errors['phone']    = 'Vui lòng nhập số điện thoại';
            if (!$address)  $errors['address']  = 'Vui lòng nhập địa chỉ';

            if (UserModel::findByUsername($username)) {
                $errors['username'] = 'Tên đăng nhập đã tồn tại';
            }
            if (UserModel::findByEmail($email)) {
                $errors['email'] = 'Email đã được sử dụng';
            }

            if (empty($errors)) {
                $password = password_hash(
                    $username . preg_replace('/\s+/', '', $phone) . '@',
                    PASSWORD_BCRYPT
                );
                $ok = UserModel::create([
                    'username' => $username,
                    'password' => $password,
                    'fullname' => $fullname ?: $username,
                    'email'    => $email,
                    'phone'    => $phone,
                    'address'  => $address,
                ]);
                if ($ok) {
                    $this->redirect(BASE_URL . '/index.php?url=admin-customers&created=1');
                    return;
                }
                $errors['general'] = 'Tạo tài khoản thất bại. Vui lòng thử lại.';
            }
        }

        $this->adminView('admin/customers/create', [
            'errors'   => $errors,
            'username' => $username,
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
            'address'  => $address,
        ]);
    }

    public function edit(): void {
        $this->requireAdmin();

        $customerId = (int)($_GET['id'] ?? 0);
        if ($customerId <= 0) {
            http_response_code(404);
            $this->abort(404);
            return;
        }

        // ✅ dùng findById thay vì getById (không tồn tại trong model)
        $user = UserModel::findById($customerId);
        if (!$user || $user['role'] !== 'customer') {
            http_response_code(404);
            $this->abort(404);
            return;
        }

        $error   = '';
        $success = '';

        // Đổi status qua GET (từ nút trong edit view)
        if (isset($_GET['set_status'])) {
            $newStatus = $_GET['set_status'];
            if (in_array($newStatus, ['active', 'warning', 'inactive'])) {
                UserModel::updateStatus($customerId, $newStatus);
                $this->redirect(
                    BASE_URL . '/index.php?url=admin-customers-edit&id=' . $customerId . '&updated=1'
                );
                return;
            }
        }

        if (isset($_GET['updated'])) {
            $success = 'Cập nhật trạng thái thành công.';
            // Reload user sau khi update
            $user = UserModel::findById($customerId);
        }

        // Lưu form POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email']    ?? '');
            $phone    = trim($_POST['phone']    ?? '');
            $address  = trim($_POST['address']  ?? '');
            $password = trim($_POST['password'] ?? '');

            UserModel::updateProfile($customerId, [
                'fullname' => $fullname,
                'email'    => $email,
                'phone'    => $phone,
                'address'  => $address,
            ]);

            if ($password !== '') {
                UserModel::updatePassword($customerId, password_hash($password, PASSWORD_BCRYPT));
            }

            $success = 'Cập nhật thông tin thành công.';
            $user    = UserModel::findById($customerId); // reload
        }

        $avatarPath = BASE_URL . '/' . UserModel::getAvatar($customerId);

        $this->adminView('admin/customers/edit', [
            'user'       => $user,
            'avatarPath' => $avatarPath,
            'success'    => $success,
            'error'      => $error,
        ]);
    }
    public function delete(): void {
        $this->requireAdmin();

        $customerId = (int)($_GET['id'] ?? 0);
        if ($customerId > 0) {
            UserModel::updateStatus($customerId, 'inactive');
        }
        $this->redirect(BASE_URL . '/index.php?url=admin-customers');
    }
}