const slides = document.querySelectorAll(".slider img");
let current = 0;

setInterval(() => {
  slides[current].classList.remove("active");
  current = (current + 1) % slides.length;
  slides[current].classList.add("active");
}, 3000);

const isLoggedIn = false;

function checkLogin(event) {
  if (!isLoggedIn) {
    event.preventDefault(); // chặn chuyển trang

    // Bước 1: cảnh báo ban đầu
    alert("⚠️ Bạn phải đăng nhập trước khi thao tác!");

    // Bước 2: hỏi người dùng có muốn đăng nhập không
    const confirmLogin = confirm("Bạn có muốn đến trang đăng nhập không?");

    if (confirmLogin) {
      // Nếu người dùng bấm OK → chuyển sang trang đăng nhập
      window.location.href = "login.html";
    }
  }
}

function updateCart() {
  // Thông báo
  alert("Cập nhật giỏ hàng thành công!");

  // Nếu muốn, bạn có thể thêm code xử lý giỏ hàng ở đây
  // ví dụ: cập nhật số lượng sản phẩm, tính lại tổng, ...
}
function updateCart2() {
  alert("xóa sản phẩm thành công!");
}
