<?php
class AuthController extends Controller {

    public function loginForm(): void {
        $this->view('auth/login', [
            'noLayout' => true,
            'errors'   => [],
            'username' => ''
        ]);
    }

    public function login(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if ($username === '') $errors['username'] = 'Vui lòng nhập tên đăng nhập';
        if ($password === '') $errors['password'] = 'Vui lòng nhập mật khẩu';

        if (!empty($errors)) {
            $this->view('auth/login', [
                'noLayout' => true,
                'errors'   => $errors,
                'username' => $username
            ]);
            return;
        }

        $user = UserModel::findByUsername($username);

        if (!$user) {
            $this->view('auth/login', [
                'noLayout' => true,
                'errors'   => ['username' => 'Tài khoản không tồn tại'],
                'username' => $username
            ]);
            return;
        }

        if (!password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'noLayout' => true,
                'errors'   => ['password' => 'Mật khẩu không đúng'],
                'username' => $username
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id'       => $user['id'],
            'username' => $user['username'],
            'role'     => $user['role']
        ];

        $this->redirect(
            $user['role'] === 'admin'
                ? '/app/index.php?url=admin'
                : '/app/index.php?url=home'
        );
    }

    public function registerForm(): void {
        $this->view('auth/register', [
            'noLayout' => true,
            'errors'   => [], 'username' => '',
            'fullname' => '', 'email'    => '',
            'phone'    => '', 'address'  => '',
            'password' => '', 'confirm'  => ''
        ]);
    }

    public function register(): void {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email    = trim($_POST['email']    ?? '');
        $phone    = trim($_POST['phone']    ?? '');
        $address  = trim($_POST['address']  ?? '');
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $errors   = [];

        if ($username === '') $errors['username'] = 'Vui lòng nhập tên đăng nhập';
        if ($fullname === '') $errors['fullname'] = 'Vui lòng nhập họ tên';
        if ($email    === '') $errors['email']    = 'Vui lòng nhập email';
        if ($address  === '') $errors['address']  = 'Vui lòng nhập địa chỉ';
        if ($password === '') $errors['password'] = 'Vui lòng nhập mật khẩu';
        if ($password !== $confirm) $errors['confirm'] = 'Mật khẩu không khớp';

        if (!empty($errors)) {
            $this->view('auth/register', [
                'noLayout' => true,
                'errors'   => $errors,
                'username' => $username, 'fullname' => $fullname,
                'email'    => $email,    'phone'    => $phone,
                'address'  => $address
            ]);
            return;
        }

        if (UserModel::findByUsername($username)) {
            $this->view('auth/register', [
                'noLayout' => true,
                'errors'   => ['username' => 'Tên đăng nhập đã tồn tại'],
                'username' => $username, 'fullname' => $fullname,
                'email'    => $email,    'phone'    => $phone,
                'address'  => $address
            ]);
            return;
        }

        $ok = UserModel::create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'fullname' => $fullname,
            'email'    => $email,
            'phone'    => $phone,
            'address'  => $address,
        ]);

        if ($ok) {
            $this->redirect('/app/index.php?url=login');
        } else {
            $this->view('auth/register', [
                'noLayout' => true,
                'errors'   => ['general' => 'Username hoặc Email đã tồn tại'],
                'username' => $username, 'fullname' => $fullname,
                'email'    => $email,    'phone'    => $phone,
                'address'  => $address
            ]);
        }
    }
}