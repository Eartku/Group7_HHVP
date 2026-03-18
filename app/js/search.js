(function() {

    // DOM Elements
    const input = document.getElementById("searchInput");
    const results = document.getElementById("searchResults");
    const categoryFilter = document.getElementById("categoryFilter");
    const minPriceInput = document.getElementById("minPrice");
    const maxPriceInput = document.getElementById("maxPrice");
    const searchForm = document.getElementById("searchFilterForm");

    let debounceTimer;
    let controller = null;
    let currentIndex = -1;

    // Cache for query results
    const cache = new Map();

    /* =========================
       Utility Functions
    ========================= */

    const debounce = (fn, delay = 300) => {
        return (...args) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fn(...args), delay);
        };
    };

    const escapeHTML = str =>
        str.replace(/[&<>"']/g, m =>
            ({ '&':'&amp;','<':'<','>':'>','"':'"',"'":'&#39;' }[m])
        );

    const highlightKeyword = (text, keyword) => {
        if (!keyword) return text;
        const regex = new RegExp(`(${keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, "gi");
        return text.replace(regex, '<span class="highlight">$1</span>');
    };

    const show = () => results.classList.add("active");
    const hide = () => {
        results.classList.remove("active");
        currentIndex = -1;
    };

    const renderMessage = (className, message) => {
        results.innerHTML = `<div class="${className}">${message}</div>`;
        show();
    };

    const renderResults = (data, keyword) => {
        if (!data.length) {
            renderMessage("result-empty", "Không tìm thấy");
            return;
        }

        results.innerHTML = data.map(item => `
            <div class="result-item" data-id="${item.id}">
                <div class="result-name">
                    ${highlightKeyword(escapeHTML(item.name), keyword)}
                </div>
                <div class="result-price">
                    ${item.price_formatted || item.price}
                </div>
            </div>
        `).join("");

        currentIndex = -1;
        show();
    };

    /* =========================
       Get Filter Values
    ========================= */

    const getFilterParams = () => {
        const params = new URLSearchParams();
        
        const query = input.value.trim();
        if (query.length >= 2) {
            params.set('q', query);
        }
        
        const category = categoryFilter ? categoryFilter.value : 0;
        if (category > 0) {
            params.set('category', category);
        }
        
        const minPrice = minPriceInput ? minPriceInput.value.trim() : '';
        if (minPrice && parseFloat(minPrice) >= 0) {
            params.set('min_price', minPrice);
        }
        
        const maxPrice = maxPriceInput ? maxPriceInput.value.trim() : '';
        if (maxPrice && parseFloat(maxPrice) >= 0) {
            params.set('max_price', maxPrice);
        }
        
        return params.toString();
    };

    const getCacheKey = () => {
        return input.value.trim() + '|' + 
               (categoryFilter ? categoryFilter.value : '0') + '|' +
               (minPriceInput ? minPriceInput.value : '') + '|' +
               (maxPriceInput ? maxPriceInput.value : '');
    };

    /* =========================
       Search Logic
    ========================= */

    const search = async () => {
        const query = input.value.trim();
        const cacheKey = getCacheKey();

        // Need at least 2 characters or some filter
        if (query.length < 2 && !categoryFilter?.value && 
            !minPriceInput?.value && !maxPriceInput?.value) {
            hide();
            return;
        }

        // Check cache
        if (cache.has(cacheKey)) {
            renderResults(cache.get(cacheKey), query);
            return;
        }

        if (controller) controller.abort();
        controller = new AbortController();

        renderMessage("result-loading", "Đang tìm...");

        try {
            const params = getFilterParams();
            const url = "search_api.php" + (params ? "?" + params : "");
            
            const res = await fetch(url, { signal: controller.signal });

            if (!res.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await res.json();

            cache.set(cacheKey, data);

            if (!data.length) {
                renderMessage("result-empty", "Không tìm thấy");
                return;
            }

            renderResults(data, query);

        } catch (err) {
            if (err.name !== "AbortError") {
                console.error("Search error:", err);
                renderMessage("result-error", "Lỗi tìm kiếm");
            }
        }
    };

    /* =========================
       Event Listeners
    ========================= */

    // Search input with debounce
    input.addEventListener("input", debounce(search, 300));

    // Category filter change
    if (categoryFilter) {
        categoryFilter.addEventListener("change", debounce(search, 300));
    }

    // Price inputs with debounce
    if (minPriceInput) {
        minPriceInput.addEventListener("input", debounce(search, 500));
    }
    
    if (maxPriceInput) {
        maxPriceInput.addEventListener("input", debounce(search, 500));
    }

    // Keyboard navigation
    input.addEventListener("keydown", e => {

        const items = results.querySelectorAll(".result-item");
        if (!items.length) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
        }

        if (e.key === "ArrowUp") {
            e.preventDefault();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
        }

        if (e.key === "Enter" && currentIndex >= 0) {
            e.preventDefault();
            window.location.href = 
                "details.php?id=" + items[currentIndex].dataset.id;
        }

        if (e.key === "Escape") {
            hide();
        }

        items.forEach(item => item.classList.remove("active"));
        if (currentIndex >= 0) {
            items[currentIndex].classList.add("active");
        }

    });

    // Click on result item
    results.addEventListener("click", e => {
        const item = e.target.closest(".result-item");
        if (!item) return;

        window.location.href = "details.php?id=" + item.dataset.id;
    });

    // Click outside to hide results
    document.addEventListener("click", e => {
        if (!e.target.closest(".search-wrapper")) {
            hide();
        }
    });

    // Form submission - prevent default for live search
    if (searchForm) {
        searchForm.addEventListener("submit", (e) => {
            // Let form submit normally for page search
            // Live search is just for quick results dropdown
        });
    }

})();
