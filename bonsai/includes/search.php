<form class="flex-grow-1" onsubmit="return false;">
    <div class="search-wrapper" style="position:relative;">

        <input
            id="searchInput"
            class="search-input form-control"
            type="search"
            placeholder="Tìm kiếm sản phẩm..."
            autocomplete="off"
        />

        <div id="searchResults"
            style="display:none; position:absolute; top:100%; left:0; right:0;
            background:white; border:1px solid #ccc; max-height:300px;
            overflow-y:auto; z-index:1000; border-radius:6px;">
        </div>

    </div>
</form>
<script>
    let timeout = null;

    const searchInput = document.getElementById("searchInput");
    const resultsDiv = document.getElementById("searchResults");

    searchInput.addEventListener("keyup", function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            doSearch();
        }, 300);
    });

    function doSearch() {
        const query = searchInput.value.trim();
        if (query.length < 2) {
            resultsDiv.style.display = "none";
            return;
        }

        resultsDiv.innerHTML = "<div style='padding:10px'>Đang tìm...</div>";
        resultsDiv.style.display = "block";

        fetch("search_api.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {

                if (data.length === 0) {
                    resultsDiv.innerHTML = "<div style='padding:10px;color:#888'>Không tìm thấy</div>";
                    return;
                }

                resultsDiv.innerHTML = data.map(item => `
                    <div class="result-item"
                        data-id="${item.id}"
                        style="padding:10px;border-bottom:1px solid #eee;cursor:pointer">
                        <strong>${item.name}</strong>
                        <br>
                        <small style="color:#888">${item.price ?? ''}</small>
                    </div>
                `).join("");

                document.querySelectorAll(".result-item").forEach(el => {
                    el.addEventListener("click", function () {
                        window.location.href = "details.php?id=" + this.dataset.id;
                    });
                });

            })
            .catch(err => {
                resultsDiv.innerHTML = "<div style='padding:10px;color:red'>Lỗi tìm kiếm</div>";
            });
    }

    // đóng khi click ngoài
    document.addEventListener("click", function(e){
        if (!e.target.closest(".search-wrapper")) {
            resultsDiv.style.display = "none";
        }
    });
</script>
