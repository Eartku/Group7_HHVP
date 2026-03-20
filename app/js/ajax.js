// ajax.js
(() => {

    /* ===============================
       CONFIG
    =============================== */

    const API = {
        cart: 'ajax_handler.php',
        filter: 'filter_products.php'
    };

    /* ===============================
       UTILITIES
    =============================== */

    const escapeHTML = str =>
        String(str).replace(/[&<>"']/g, m =>
            ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])
        );

    const showNotification = (message, type = "info") => {

        const wrapper = document.createElement("div");
        wrapper.className = `custom-alert ${type}`;
        wrapper.innerHTML = `
            <div class="custom-alert-content">
                ${escapeHTML(message)}
                <button class="custom-alert-close">&times;</button>
            </div>
        `;

        document.body.appendChild(wrapper);

        setTimeout(() => wrapper.classList.add("show"), 10);

        const remove = () => {
            wrapper.classList.remove("show");
            setTimeout(() => wrapper.remove(), 300);
        };

        wrapper.querySelector(".custom-alert-close")
               .addEventListener("click", remove);

        setTimeout(remove, 4000);
    };

    /* ===============================
       ADD TO CART
    =============================== */

    const addToCart = async (productId, quantity = 1) => {

        try {

            const params = new URLSearchParams({
                action: "addToCart",
                product_id: productId,
                quantity: quantity
            });

            const res = await fetch(API.cart, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params.toString()
            });

            const data = await res.json();

            if (data.success) {
                showNotification(data.message, "success");

                if (window.updateCartCount) {
                    window.updateCartCount(data.cartCount);
                }

            } else {
                showNotification(data.message, "error");
            }

        } catch (err) {
            console.error(err);
            showNotification("Có lỗi xảy ra", "error");
        }
    };

    /* ===============================
       FILTER PRODUCTS
    =============================== */

    const filterProducts = async (categoryId = 0, minPrice = 0, maxPrice = 999999) => {

        try {

            const params = new URLSearchParams();

            if (categoryId > 0) params.append("category", categoryId);
            if (minPrice > 0) params.append("min_price", minPrice);
            if (maxPrice < 999999) params.append("max_price", maxPrice);

            const res = await fetch(`${API.filter}?${params.toString()}`);
            const products = await res.json();

            renderProducts(products);

        } catch (err) {
            console.error(err);
            showNotification("Lỗi khi tải sản phẩm", "error");
        }
    };

    /* ===============================
       RENDER PRODUCTS
    =============================== */

    const renderProducts = products => {

        const container = document.getElementById("productsContainer");
        if (!container) return;

        if (!products.length) {
            container.innerHTML = `
                <p class="col-12 text-muted">
                    Không tìm thấy sản phẩm nào
                </p>
            `;
            return;
        }

        container.innerHTML = products.map(product => `
            <div class="col-12 col-md-4 col-lg-3 mb-4">
                <div class="product-item">

                    <a href="details.php?id=${product.id}">
                        <img src="images/${escapeHTML(product.image)}"
                             class="img-fluid product-thumbnail"
                             alt="${escapeHTML(product.name)}">
                    </a>

                    <h3 class="product-title">
                        ${escapeHTML(product.name)}
                    </h3>

                    <strong class="product-price">
                        ${escapeHTML(product.price)}
                    </strong>

                    <div class="mt-2 d-flex gap-2">
                        <button 
                            class="btn btn-sm btn-success add-to-cart-btn"
                            data-id="${product.id}">
                            🛒
                        </button>

                        <a href="details.php?id=${product.id}"
                           class="btn btn-sm btn-outline-success">
                           Chi tiết
                        </a>
                    </div>

                </div>
            </div>
        `).join("");
    };

    /* ===============================
       EVENT DELEGATION
    =============================== */

    document.addEventListener("click", e => {

        const btn = e.target.closest(".add-to-cart-btn");
        if (!btn) return;

        const productId = btn.dataset.id;
        if (!productId) return;

        addToCart(productId);
    });

    /* ===============================
       EXPOSE GLOBAL (nếu cần)
    =============================== */

    window.filterProducts = filterProducts;

})();