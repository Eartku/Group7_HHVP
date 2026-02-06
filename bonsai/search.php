<!-- Start Search Bar -->
<?php
require_once "db.php";
// Lấy danh sách loại cây cho filter
$categories = [];
$cat_result = $conn->query("SELECT id, name FROM categories");
if ($cat_result && $cat_result->num_rows > 0) {
  while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row;
  }
}

// Kiểm tra xem đang ở shop page với category cụ thể hay không
$current_category = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<form class="flex-grow-1" action="#" method="get">
  <div class="search-wrapper">
    <a href="#" class="search-btn" onclick="searchProduct(); return false;">
      <span class="material-symbols-outlined">search</span>
    </a>

    <input 
      id="searchInput"
      class="search-input form-control" 
      type="search" 
      placeholder="Tìm kiếm sản phẩm..." 
      autocomplete="off"
      onkeyup="searchProduct()"
    />
    
    <!-- Dropdown kết quả tìm kiếm -->
    <div id="searchResults" class="search-results dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; max-height: 300px; overflow-y: auto; z-index: 1000; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    </div>

    <!-- nút filter -->
    <input type="checkbox" id="show-filter" hidden />
    <label for="show-filter" class="filter-btn">
      <span class="material-symbols-outlined">tune</span>
    </label>

    <!-- dropdown filter -->
    <div class="filter-options" style="top: 375px; right: -59%;">
      <ul>
        <!-- Nhóm 1: loại cây từ database -->
        <div class="mb-2">
          <label class="form-label small mb-1" style="font-size: 1.25em;"><b>Loại cây</b></label>
          <select id="categoryFilter" class="form-select form-select-sm" onchange="applyFilter()">
            <option value="">Tất cả</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($current_category === $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>     
        <!-- Nhóm 2: theo giá -->
        <div class="mb-2">
          <label class="form-label small mb-1" style="font-size: 1.25em;"><b>Theo khoảng giá</b></label>
          <select id="priceFilter" class="form-select form-select-sm" onchange="applyFilter()">
            <option value="">Tất cả giá</option>
            <option value="20000-50000">Từ 20k - 50k</option>
            <option value="50000-100000">Từ 50k - 100k</option>
            <option value="100000-999999">Trên 100k</option>
          </select>
        </div>        
      </ul>
    </div>
  </div> 
</form>

<script>
function searchProduct() {
  const query = document.getElementById('searchInput').value.trim();
  const category = document.getElementById('categoryFilter')?.value || '';
  const price = document.getElementById('priceFilter')?.value || '';
  const resultsDiv = document.getElementById('searchResults');
  
  if (query.length < 2) {
    resultsDiv.style.display = 'none';
    return;
  }
  
  let url = 'search_api.php?q=' + encodeURIComponent(query);
  if (category) url += '&category=' + category;
  if (price) {
    const [min, max] = price.split('-');
    url += '&min_price=' + min + '&max_price=' + max;
  }
  
  fetch(url)
    .then(response => response.json())
    .then(data => {
      if (data.length === 0) {
        resultsDiv.innerHTML = '<div style="padding: 10px; color: #999;">Không tìm thấy sản phẩm</div>';
      } else {
        resultsDiv.innerHTML = data.map(item => 
          `<div style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer;" onclick="selectProduct(${item.id}, '${item.name.replace(/'/g, "\\'")}')">
            <strong>${item.name}</strong>
            <br><small style="color: #999;">${item.price ? item.price : ''}</small>
          </div>`
        ).join('');
      }
      resultsDiv.style.display = 'block';
    })
    .catch(error => console.error('Error:', error));
}

function applyFilter() {
  // Kích hoạt lại search khi thay đổi filter
  const query = document.getElementById('searchInput').value.trim();
  if (query.length >= 2) {
    searchProduct();
  }
}

function selectProduct(id, name) {
  document.getElementById('searchInput').value = name;
  document.getElementById('searchResults').style.display = 'none';
  window.location.href = 'details.php?id=' + id;
}

// Đóng dropdown khi click bên ngoài
document.addEventListener('click', function(event) {
  const searchWrapper = document.querySelector('.search-wrapper');
  const resultsDiv = document.getElementById('searchResults');
  if (!searchWrapper.contains(event.target)) {
    resultsDiv.style.display = 'none';
  }
});
</script>

<!-- End Search Bar -->
