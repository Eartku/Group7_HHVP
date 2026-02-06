// AJAX Add to Cart Function
function addToCartAjax(productId) {
  const quantity = document.querySelector(`input[data-product-id="${productId}"]`)?.value || 1;
  
  fetch('ajax_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=addToCart&product_id=' + productId + '&quantity=' + quantity
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification(data.message, 'success');
      // Cập nhật giỏ hàng nếu cần
      if (window.updateCartCount) {
        updateCartCount(data.cartCount);
      }
    } else {
      showNotification(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Có lỗi xảy ra', 'error');
  });
}

// Notification Function
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '9999';
  notification.style.minWidth = '300px';
  notification.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  
  document.body.appendChild(notification);
  
  // Tự động xóa sau 4 giây
  setTimeout(() => {
    notification.remove();
  }, 4000);
}

// AJAX Filter Products
function filterProducts(categoryId = 0, minPrice = 0, maxPrice = 999999) {
  const params = new URLSearchParams();
  if (categoryId > 0) params.append('category', categoryId);
  if (minPrice > 0) params.append('min_price', minPrice);
  if (maxPrice < 999999) params.append('max_price', maxPrice);
  
  fetch('filter_products.php?' + params.toString())
    .then(response => response.json())
    .then(products => {
      renderProducts(products);
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Lỗi khi tải sản phẩm', 'error');
    });
}

// Render Products
function renderProducts(products) {
  const container = document.getElementById('productsContainer');
  if (!container) return;
  
  if (products.length === 0) {
    container.innerHTML = '<p class="col-12">Không tìm thấy sản phẩm nào</p>';
    return;
  }
  
  container.innerHTML = products.map(product => `
    <div class="col-12 col-md-4 col-lg-3 mb-5">
      <div class="product-item">
        <a href="details.php?id=${product.id}">
          <img src="images/${product.image}" class="img-fluid product-thumbnail" />
        </a>
        <h3 class="product-title">${product.name}</h3>
        <strong class="product-price">${product.price}</strong>
        
        <div class="mt-2">
          <button onclick="addToCartAjax(${product.id})" class="btn btn-sm btn-success add-to-cart">
            <img src="images/cart.svg" alt="Giỏ hàng" />
          </button>
          <a href="details.php?id=${product.id}" class="btn btn-sm btn-success">Chi tiết</a>
        </div>
      </div>
    </div>
  `).join('');
}
