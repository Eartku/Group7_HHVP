===================================================
        BONSAI SHOP - DANH SÁCH MODEL METHODS
===================================================

---------------------------------------------------
UserModel
---------------------------------------------------
UserModel::findByUsername($username)    // đã có
UserModel::findById($id)
UserModel::create($data)                // đã có
UserModel::update($id, $data)           // cập nhật profile
UserModel::updatePassword($id, $hash)
UserModel::getAvatar($id)               // đã có

---------------------------------------------------
ProductModel
---------------------------------------------------
ProductModel::getAll()                  // lấy tất cả sản phẩm
ProductModel::getById($id)              // chi tiết 1 sản phẩm
ProductModel::getByCategory($catId)     // lọc theo danh mục
ProductModel::search($keyword)          // tìm kiếm
ProductModel::create($data)             // admin thêm sp
ProductModel::update($id, $data)        // admin sửa sp
ProductModel::delete($id)               // admin xóa sp
ProductModel::getFeatured()             // sp nổi bật

---------------------------------------------------
CategoryModel
---------------------------------------------------
CategoryModel::getAll()                 // đã có
CategoryModel::getById($id)
CategoryModel::create($data)
CategoryModel::update($id, $data)
CategoryModel::delete($id)

---------------------------------------------------
OrderModel
---------------------------------------------------
OrderModel::create($data)               // tạo đơn hàng
OrderModel::getByUser($userId)          // đơn của user
OrderModel::getById($id)
OrderModel::getAll()                    // admin xem tất cả
OrderModel::updateStatus($id, $status)  // cập nhật trạng thái

---------------------------------------------------
OrderItemModel
---------------------------------------------------
OrderItemModel::create($data)           // thêm sản phẩm vào đơn
OrderItemModel::getByOrder($orderId)    // ds sản phẩm trong đơn

---------------------------------------------------
CartModel (dùng DB thay vì session)
---------------------------------------------------
CartModel::getByUser($userId)
CartModel::addItem($userId, $productId, $qty)
CartModel::updateQty($id, $qty)
CartModel::removeItem($id)
CartModel::clearCart($userId)

===================================================
        ROUTER MAP (?url=)
===================================================

?url=               → GuestController->index()
?url=login          → AuthController->loginForm()
?url=register       → AuthController->registerForm()
?url=home           → HomeController->index()
?url=product        → ProductController->index()
?url=product/1      → ProductController->show(1)
?url=cart           → CartController->index()
?url=order          → OrderController->index()
?url=admin          → AdminController->index()

===================================================
        CẤU TRÚC THƯ MỤC
===================================================

app/
├── index.php                   ← Router trung tâm
├── config/
│   └── app.php                 ← Constants, DB config
├── core/
│   ├── Controller.php          ← Base controller
│   ├── Model.php               ← Base model
│   ├── Database.php            ← Kết nối DB
│   └── autoload.php            ← Tự động load class
├── controllers/
│   ├── AuthController.php      ← login, register
│   ├── GuestController.php     ← trang chủ guest
│   ├── HomeController.php      ← trang chủ user
│   ├── ProductController.php
│   ├── CartController.php
│   ├── OrderController.php
│   └── AdminController.php
├── models/
│   ├── UserModel.php
│   ├── ProductModel.php
│   ├── CategoryModel.php
│   ├── OrderModel.php
│   ├── OrderItemModel.php
│   └── CartModel.php
├── views/
│   ├── layouts/
│   │   └── main.php            ← Layout chung
│   ├── partials/
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── head.php
│   ├── guest/
│   │   └── index.php
│   ├── auth/
│   │   ├── login/index.php
│   │   └── register/index.php
│   ├── home/index.php
│   ├── product/index.php
│   ├── cart/index.php
│   ├── order/index.php
│   └── admin/index.php
├── css/
├── js/
├── images/
└── uploads/