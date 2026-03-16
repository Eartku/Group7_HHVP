<!-- Start Footer Section -->
<footer class="footer-section bg-dark text-light mt-5">
    <div class="container py-4">
        <div class="border-top pt-3 mt-3 text-center">
            <p class="mb-0">
                Copyright © <?= date("Y") ?> All Rights Reserved — Designed by
                <a href="group7.php" class="text-light">Group 7</a>
            </p>
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
