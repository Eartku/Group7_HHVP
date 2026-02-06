<!-- Start Footer Section -->
<footer class="footer-section">
  <div class="container relative">
    <div class="sofa-img">
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="subscription-form">
          <h3 class="d-flex align-items-center">
            <span class="me-1">
              <img src="images/envelope-outline.svg" alt="Image" class="img-fluid" />
            </span>
            <span>Nêu ý kiến cá nhân</span>
          </h3>

          <form id="feedbackForm" class="row g-3" method="POST" action="submit_feedback.php">
            <div class="col-12">
              <input type="text" class="form-control" name="name" placeholder="Nhập họ và tên" required aria-label="Họ và tên" />
            </div>
            <div class="col-12">
              <input type="email" class="form-control" name="email" placeholder="Nhập email" required aria-label="Email" />
            </div>
            <div class="col-12">
              <textarea class="form-control" name="message" rows="3" placeholder="Nhập ý kiến của bạn..." required aria-label="Ý kiến"></textarea>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary" aria-label="Gửi phản hồi">
                <span class="fa fa-paper-plane"></span> Gửi
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="border-top copyright">
      <div class="row pt-4">
        <div class="col-lg-6">
          <p class="mb-2 text-center text-lg-start">
            Copyright &copy; <span id="year"></span>
            — Designed by <a href="group7.html">Group 7</a>
          </p>
        </div>

        <div class="col-lg-6 text-center text-lg-end">
          <ul class="list-unstyled d-inline-flex ms-auto">
            <li class="me-4"><a href="#">Terms</a></li>
            <li><a href="#">Privacy</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Set current year
  const yearSpan = document.getElementById("year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }

  // Feedback form handler
  const feedbackForm = document.getElementById("feedbackForm");
  if (feedbackForm) {
    feedbackForm.addEventListener("submit", (e) => {
      e.preventDefault();
      
      // Client-side validation
      const name = feedbackForm.name.value.trim();
      const email = feedbackForm.email.value.trim();
      const message = feedbackForm.message.value.trim();
      
      if (!name || !email || !message) {
        alert("Vui lòng điền đầy đủ thông tin");
        return;
      }
      
      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
        alert("Vui lòng nhập email hợp lệ");
        return;
      }
      
      // Submit form
      feedbackForm.submit();
    });
  }
});
</script>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/tiny-slider.js"></script>
<script src="js/custom.js"></script>
