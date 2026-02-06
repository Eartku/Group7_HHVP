<?php
require "db.php";

$errors = [];
$username = $email = $fullname = $phone = $address = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $fullname = trim($_POST["fullname"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $address  = trim($_POST["address"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    /* ===== SERVER VALIDATE ===== */

    if ($username === "") $errors['username'] = "Vui lòng nhập tên đăng nhập";
    if ($fullname === "") $errors['fullname'] = "Vui lòng nhập họ tên";

    if ($email === "") {
        $errors['email'] = "Vui lòng nhập email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ";
    }

    if ($address === "") $errors['address'] = "Vui lòng nhập địa chỉ";

    /* ===== PASSWORD RULE ===== */
    if ($password === "") {
        $errors['password'] = "Vui lòng nhập mật khẩu";
    } elseif (!preg_match(
        '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
        $password
    )) {
        $errors['password'] = "Mật khẩu ≥ 8 ký tự, gồm hoa, thường, số, ký tự đặc biệt";
    }

    if ($confirm === "") {
        $errors['confirm'] = "Vui lòng xác nhận mật khẩu";
    } elseif ($password !== $confirm) {
        $errors['confirm'] = "Mật khẩu không khớp";
    }

    /* ===== INSERT DB ===== */
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users
                (username, email, password, fullname, phone, address)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssss",
            $username, $email, $hash,
            $fullname, $phone, $address
        );

        if ($stmt->execute()) {
            header("Location: login.php?register=success");
            exit();
        } else {
            $errors['general'] = "❌ Username hoặc Email đã tồn tại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký</title>
<link href="css/css.css" rel="stylesheet">
</head>
<body>

<div class="container">
<h2 class="login-title">ĐĂNG KÝ TÀI KHOẢN</h2>

<?php if (!empty($errors['general'])): ?>
<p style="color:red;text-align:center"><?= $errors['general'] ?></p>
<?php endif; ?>

<form method="post" onsubmit="return checkRegisterForm()">

<div class="form-group">
    <label>
        Tên đăng nhập <span class="required">*</span>
    </label>
    <input type="text" name="username"
           value="<?= htmlspecialchars($username) ?>"
           placeholder="Nhập tên đăng nhập"
           required>
    <small><?= $errors['username'] ?? '' ?></small>
</div>

<div class="form-group">
    <label>
        Họ và tên <span class="required">*</span>
    </label>
    <input type="text" name="fullname" required
           value="<?= htmlspecialchars($fullname) ?>"
           placeholder="Nhập họ và tên">
    <small><?= $errors['fullname'] ?? '' ?></small>
</div>

<div class="form-group">
    <label>
        Email <span class="required">*</span>
    </label>
    <input type="email" name="email"
           value="<?= htmlspecialchars($email) ?>"
           placeholder="Nhập email">
    <small><?= $errors['email'] ?? '' ?></small>
</div>

<div class="form-group">
    <label>
        Số điện thoại 
    </label>
    <input type="text" name="phone"
           value="<?= htmlspecialchars($phone) ?>"
           placeholder="Nhập số điện thoại">
    <small><?= $errors['phone'] ?? '' ?></small>
</div>

<div class="form-group">
    <label>
        Địa chỉ <span class="required">*</span>
    </label>
    <input type="text" name="address"
           value="<?= htmlspecialchars($address) ?>"
           placeholder="Nhập địa chỉ" required>
    <small><?= $errors['address'] ?? '' ?></small>
</div>

<!-- PASSWORD -->
<div class="form-group password-group">
    <label>
        Mật khẩu <span class="required">*</span>
    </label>
    <input type="password"
           name="password"
           id="reg-password"
           placeholder="Nhập mật khẩu"
           required>
    <span class="toggle-password"
          onclick="togglePassword('reg-password', this)">
        <img src="images/hide.svg" width="22">
    </span>
    <small id="password-error"><?= $errors['password'] ?? '' ?></small>
</div>

<!-- CONFIRM -->
<div class="form-group password-group">
    <label>
        Xác nhận mật khẩu <span class="required">*</span>
    </label>
    <input type="password"
           name="confirm_password"
           id="reg-confirm"
           placeholder="Xác nhận mật khẩu"
           required>
    <span class="toggle-password"
          onclick="togglePassword('reg-confirm', this)">
        <img src="images/hide.svg" width="22">
    </span>
    <small><?= $errors['confirm'] ?? '' ?></small>
</div>

<button class="login-btn">Đăng ký</button>
</form>

<p style="text-align:center;margin-top:15px">
    Đã có tài khoản? <a href="login.php">Đăng nhập</a>
</p>
</div>

<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const img = el.querySelector("img");
    if (input.type === "password") {
        input.type = "text";
        img.src = "images/show.svg";
    } else {
        input.type = "password";
        img.src = "images/hide.svg";
    }
}

function validatePassword(p) {
    if (p.length < 8) return "Mật khẩu phải có ít nhất 8 ký tự, có chưuc hoa, thường, số, ký tự đặc biệt";
    if (!/[A-Z]/.test(p)) return "Có chữ hoa";
    if (!/[a-z]/.test(p)) return "Có chữ thường";
    if (!/[0-9]/.test(p)) return "Có số";
    if (!/[!@#$%^&*]/.test(p)) return "Có ký tự đặc biệt";
    return "";
}

function checkRegisterForm() {
    const p = document.getElementById("reg-password").value;
    const c = document.getElementById("reg-confirm").value;
    const n = document.getElementById("reg-username").value;

    if (n.trim() === "") {
        alert("Vui lòng nhập tên đăng nhập");
        return false;
    }
    if (p.trim() === "") {
        alert("Vui lòng nhập mật khẩu");
        return false;
    }
    if (c.trim() === "") {
        alert("Vui lòng xác nhận mật khẩu");
        return false;
    }

    const err = validatePassword(p);
    document.getElementById("password-error").textContent = err;

    if (err) return false;
    if (p !== c) {
        alert("Mật khẩu không khớp");
        return false;
    }
    return true;
}
</script>

</body>
</html>
