<?php
session_start();
require "../config/db.php";
$errors = [];
$username = $email = $fullname = $phone = $address = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $fullname = trim($_POST["fullname"] ?? "");
    $phone    = trim($_POST["phone"] ?? "");
    $address  = trim($_POST["address"] ?? "");

    // Mật khẩu tự động: loại bỏ khoảng trắng trong username + phone rồi ghép + @
    $usernameClean = preg_replace('/\s+/', '', $username);
    $phoneClean    = preg_replace('/\s+/', '', $phone);
    $password = $usernameClean . $phoneClean . "@";

    // Validate
    if ($username === "") $errors['username'] = "Vui lòng nhập tên đăng nhập";
    if ($fullname === "")  $errors['fullname'] = "Vui lòng nhập họ và tên";

    if ($email === "") {
        $errors['email'] = "Vui lòng nhập email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ (ví dụ: example@gmail.com)";
    }

    if ($phone === "") {
        $errors['phone'] = "Vui lòng nhập số điện thoại để tạo mật khẩu";
    } elseif (!preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/', $phoneClean)) {
        $errors['phone'] = "Số điện thoại không hợp lệ (phải là số Việt Nam 10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09)";
    }

    if ($address === "") $errors['address'] = "Vui lòng nhập địa chỉ";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = "customer";

        $sql  = "INSERT INTO users (username, email, password, fullname, phone, address, role)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $email, $hash, $fullname, $phone, $address, $role);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'username' => $username]);
            exit();
        } else {
            $errors['general'] = "Username hoặc Email đã tồn tại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include "../admin_includes/loader.php"; ?>
    <style>
        #register-page {
            padding: 60px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #register-page .register-container {
            background: #ffffff;
            max-width: 500px;
            width: 90%;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            margin-top: -36px;
            position: relative;
            z-index: 5;
        }

        #register-page .register-form-title {
            color: #2c3e50;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
            text-transform: uppercase;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }

        #register-page .reg-form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        #register-page .reg-form-group label {
            display: block;
            color: #444;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        #register-page input[type="text"],
        #register-page input[type="email"] {
            width: 100%;
            height: 45px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            color: #333;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        #register-page input:focus {
            border-color: #3b5d50;
            box-shadow: 0 0 5px rgba(59,93,80,0.2);
            outline: none;
        }

        /* Ô readonly tự động điền */
        #register-page input[readonly] {
            background-color: #eef5f2;
            color: #3b5d50;
            font-weight: 600;
            cursor: not-allowed;
        }

        /* Hộp xem trước mật khẩu */
        #password-preview-box {
            display: none;
            background: #eef5f2;
            border: 1px dashed #3b5d50;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 8px;
            font-size: 14px;
            color: #2c3e50;
        }

        #password-preview-box span {
            font-weight: 700;
            color: #3b5d50;
            font-size: 15px;
        }

        #register-page .btn-submit-reg {
            width: 100%;
            padding: 14px;
            background: #3b5d50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        #register-page .btn-submit-reg:hover {
            background: #2f4a40;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        #register-page .error-text {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        #register-page .general-error-msg {
            color: red;
            text-align: center;
            background: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Viền đỏ khi input lỗi */
        #register-page input.input-error {
            border-color: #e74c3c !important;
            box-shadow: 0 0 5px rgba(231,76,60,0.2);
        }
    </style>
</head>

<body>
    <?php include "../admin_includes/header.php"; ?>

<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Tạo tài khoản khách hàng</h1>
    </div>
</div>

<div id="register-page">
    <div class="register-container">
        <h2 class="register-form-title">TẠO TÀI KHOẢN</h2>

        <?php if (!empty($errors['general'])): ?>
            <p class="general-error-msg"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

        <form id="createForm" method="post">

            <div class="reg-form-group">
                <label>Tên đăng nhập <span style="color:red">*</span></label>
                <input type="text" name="username" id="username"
                       value="<?= htmlspecialchars($username) ?>"
                       placeholder="Nhập tên đăng nhập" required>
                <small class="error-text"><?= $errors['username'] ?? '' ?></small>
            </div>

            <div class="reg-form-group">
                <label>Họ và tên <span style="color:red">*</span></label>
                <input type="text" name="fullname" id="fullname"
                       value="<?= htmlspecialchars($fullname) ?>"
                       placeholder="Tự động theo tên đăng nhập" readonly>
                <small class="error-text"><?= $errors['fullname'] ?? '' ?></small>
            </div>

            <div class="reg-form-group">
                <label>Email <span style="color:red">*</span></label>
                <input type="email" name="email" id="email"
                       value="<?= htmlspecialchars($email) ?>"
                       placeholder="Nhập email" required>
                <small class="error-text" id="email-error"><?= $errors['email'] ?? '' ?></small>
            </div>

            <div class="reg-form-group">
                <label>Số điện thoại <span style="color:red">*</span></label>
                <input type="text" name="phone" id="phone"
                       value="<?= htmlspecialchars($phone) ?>"
                       placeholder="Nhập số điện thoại (VD: 0912345678)" required>
                <small class="error-text" id="phone-error"><?= $errors['phone'] ?? '' ?></small>
            </div>

            <!-- Xem trước mật khẩu tự động -->
            <div id="password-preview-box">
                🔑 Mật khẩu tự động: <span id="password-preview"></span>
            </div>

            <div class="reg-form-group" style="margin-top:16px">
                <label>Địa chỉ <span style="color:red">*</span></label>
                <input type="text" name="address"
                       value="<?= htmlspecialchars($address) ?>"
                       placeholder="Nhập địa chỉ" required>
                <small class="error-text"><?= $errors['address'] ?? '' ?></small>
            </div>

            <button type="button" class="btn-submit-reg" onclick="confirmCreate()">Tạo</button>
        </form>
    </div>
</div>

<?php include '../admin_includes/footer.php'; ?>

<script>
    const usernameInput = document.getElementById("username");
    const fullnameInput = document.getElementById("fullname");
    const phoneInput    = document.getElementById("phone");
    const emailInput    = document.getElementById("email");
    const previewBox    = document.getElementById("password-preview-box");
    const previewSpan   = document.getElementById("password-preview");
    const phoneError    = document.getElementById("phone-error");
    const emailError    = document.getElementById("email-error");

    // Regex kiểm tra số điện thoại Việt Nam
    const phoneRegex = /^(0[35789])[0-9]{8}$/;

    // Regex kiểm tra email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Hàm loại bỏ khoảng trắng
    function removeSpaces(str) {
        return str.replace(/\s+/g, '');
    }

    // 1. Khi gõ username → fullname tự động theo
    usernameInput.addEventListener("input", function () {
        fullnameInput.value = this.value;
        updatePasswordPreview();
    });

    // 2. Khi gõ phone → validate + cập nhật xem trước mật khẩu
    phoneInput.addEventListener("input", function () {
        const raw = this.value.trim();
        const clean = removeSpaces(raw);

        if (raw === "") {
            phoneError.textContent = "";
            phoneInput.classList.remove("input-error");
        } else if (!phoneRegex.test(clean)) {
            phoneError.textContent = "Số điện thoại không hợp lệ (10 chữ số, bắt đầu 03/05/07/08/09)";
            phoneInput.classList.add("input-error");
        } else {
            phoneError.textContent = "";
            phoneInput.classList.remove("input-error");
        }

        updatePasswordPreview();
    });

    // 3. Khi gõ email → validate realtime
    emailInput.addEventListener("input", function () {
        const val = this.value.trim();

        if (val === "") {
            emailError.textContent = "";
            emailInput.classList.remove("input-error");
        } else if (!emailRegex.test(val)) {
            emailError.textContent = "Email không hợp lệ (ví dụ: example@gmail.com)";
            emailInput.classList.add("input-error");
        } else {
            emailError.textContent = "";
            emailInput.classList.remove("input-error");
        }
    });

    // 4. Cập nhật hiển thị mật khẩu (không có khoảng trắng)
    function updatePasswordPreview() {
        const u = removeSpaces(usernameInput.value.trim());
        const p = removeSpaces(phoneInput.value.trim());

        if (u && p) {
            previewSpan.textContent = u + p + "@";
            previewBox.style.display = "block";
        } else {
            previewBox.style.display = "none";
        }
    }

    // 5. Xác nhận tạo tài khoản
    function confirmCreate() {
        const username  = usernameInput.value.trim();
        const phone     = removeSpaces(phoneInput.value.trim());
        const email     = emailInput.value.trim();

        // Kiểm tra rỗng
        if (!username || !phone) {
            alert("Vui lòng nhập đầy đủ tên đăng nhập và số điện thoại.");
            return;
        }

        // Kiểm tra định dạng phone
        if (!phoneRegex.test(phone)) {
            phoneError.textContent = "Số điện thoại không hợp lệ (10 chữ số, bắt đầu 03/05/07/08/09)";
            phoneInput.classList.add("input-error");
            phoneInput.focus();
            return;
        }

        // Kiểm tra định dạng email
        if (!emailRegex.test(email)) {
            emailError.textContent = "Email không hợp lệ (ví dụ: example@gmail.com)";
            emailInput.classList.add("input-error");
            emailInput.focus();
            return;
        }

        if (confirm("Bạn có chắc muốn tạo tài khoản này chứ?")) {
            const formData = new FormData(document.getElementById("createForm"));

            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Đã tạo tài khoản " + data.username + " thành công!");
                    window.location.href = "customermanage.php";
                } else {
                    alert("Có lỗi xảy ra. Vui lòng thử lại.");
                    location.reload();
                }
            })
            .catch(() => {
                document.getElementById("createForm").submit();
            });
        }
    }
</script>

</body>
</html>