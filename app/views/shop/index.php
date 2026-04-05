<?php /* app/views/shop/index.php */ ?>

<!-- ===== SMART SEARCH BAR ===== -->
<div class="container mt-4 mb-5">
<div class="ssb-wrap">

    <form method="GET" action="<?= BASE_URL ?>/index.php" id="ssbForm" autocomplete="off">
        <input type="hidden" name="url"      value="shop">
        <input type="hidden" name="category" id="ssb_category" value="<?= $categoryId ?>">
        <input type="hidden" name="price_min" id="ssb_price_min" value="<?= $priceMin ?: '' ?>">
        <input type="hidden" name="price_max" id="ssb_price_max" value="<?= $priceMax ?: '' ?>">

        <div class="ssb-bar">

            <!-- Search + Autocomplete -->
            <div class="ssb-search-wrap">
                <svg class="ssb-icon" viewBox="0 0 20 20" fill="none">
                    <circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M13 13l3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <input type="text" name="search" id="ssbInput" class="ssb-input"
                       placeholder="Tìm sản phẩm..."
                       value="<?= htmlspecialchars($search) ?>">
                <button type="button" id="ssbClear" class="ssb-clear <?= $search ? '' : 'hidden' ?>">✕</button>
                <ul id="ssbSuggest" class="ssb-suggest"></ul>
            </div>

            <div class="ssb-divider"></div>

            <!-- Category dropdown -->
            <div class="ssb-filter-wrap" id="ssbCatWrap">
                <button type="button" class="ssb-filter-btn <?= $categoryId > 0 ? 'active' : '' ?>" id="ssbCatBtn">
                    <svg viewBox="0 0 16 16" width="13" fill="currentColor">
                        <path d="M2 4h12v1.5H2V4zm2 3h8v1.5H4V7zm2 3h4v1.5H6V10z"/>
                    </svg>
                    <span id="ssbCatLabel"><?= $categoryId > 0 ? htmlspecialchars($catName) : 'Loại' ?></span>
                </button>

                <div class="ssb-panel" id="ssbCatPanel">
                    <div class="scp-list">
                        <label class="scp-item <?= $categoryId === 0 ? 'active' : '' ?>">
                            <input type="radio" name="_cat_pick" value="0" data-name="Loại" <?= $categoryId === 0 ? 'checked' : '' ?>>
                            <span>Tất cả</span>
                        </label>
                        <?php foreach ($categories as $cat): ?>
                        <label class="scp-item <?= $categoryId === (int)$cat['id'] ? 'active' : '' ?>">
                            <input type="radio" name="_cat_pick"
                                   value="<?= (int)$cat['id'] ?>"
                                   data-name="<?= htmlspecialchars($cat['name']) ?>"
                                   <?= $categoryId === (int)$cat['id'] ? 'checked' : '' ?>>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="ssb-divider"></div>

            <!-- Price dropdown -->
            <div class="ssb-filter-wrap" id="ssbPriceWrap">
                <button type="button" class="ssb-filter-btn <?= ($priceMin || $priceMax) ? 'active' : '' ?>" id="ssbPriceBtn">
                    <svg viewBox="0 0 16 16" width="13" fill="currentColor">
                        <path d="M1 3h14v1.5l-5 5V14H6V9.5L1 4.5V3z"/>
                    </svg>
                    <span id="ssbPriceLabel">
                        <?php if ($priceMin || $priceMax): ?>
                            <?= $priceMin ? number_format($priceMin,0,',','.') : '0' ?>–<?= $priceMax ? number_format($priceMax,0,',','.') : '∞' ?>đ
                        <?php else: ?>
                            Giá
                        <?php endif; ?>
                    </span>
                </button>

                <div class="ssb-panel" id="ssbPricePanel">
                    <div class="spp-row">
                        <div class="spp-field">
                            <label>Từ</label>
                            <input type="text" id="spp_min_d" placeholder="0"
                                   value="<?= $priceMin ? number_format($priceMin,0,',','.') : '' ?>">
                        </div>
                        <span class="spp-dash">—</span>
                        <div class="spp-field">
                            <label>Đến</label>
                            <input type="text" id="spp_max_d" placeholder="∞"
                                   value="<?= $priceMax ? number_format($priceMax,0,',','.') : '' ?>">
                        </div>
                    </div>
                    <div class="spp-quick">
                        <button type="button" data-min="0"      data-max="100000">Dưới 100k</button>
                        <button type="button" data-min="100000" data-max="300000">100–300k</button>
                        <button type="button" data-min="300000" data-max="500000">300–500k</button>
                        <button type="button" data-min="500000" data-max="0">Trên 500k</button>
                    </div>
                    <div class="spp-actions">
                        <button type="button" id="sppClear" class="spp-reset">Xóa</button>
                        <button type="button" id="sppApply" class="spp-apply">Áp dụng</button>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="ssb-submit">Tìm</button>

        </div>
    </form>

    <!-- Active filter tags -->
    <?php if ($search || $categoryId > 0 || $priceMin || $priceMax): ?>
    <div class="ssb-tags">
        <?php if ($categoryId > 0): ?>
        <span class="ssb-tag">
            <?= htmlspecialchars($catName) ?>
            <a href="<?= BASE_URL ?>/index.php?url=shop&search=<?= urlencode($search) ?>&price_min=<?= $priceMin ?>&price_max=<?= $priceMax ?>">✕</a>
        </span>
        <?php endif; ?>
        <?php if ($search): ?>
        <span class="ssb-tag">
            "<?= htmlspecialchars($search) ?>"
            <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $categoryId ?>&price_min=<?= $priceMin ?>&price_max=<?= $priceMax ?>">✕</a>
        </span>
        <?php endif; ?>
        <?php if ($priceMin || $priceMax): ?>
        <span class="ssb-tag">
            <?= $priceMin ? number_format($priceMin,0,',','.') : '0' ?>–<?= $priceMax ? number_format($priceMax,0,',','.') : '∞' ?>đ
            <a href="<?= BASE_URL ?>/index.php?url=shop&category=<?= $categoryId ?>&search=<?= urlencode($search) ?>">✕</a>
        </span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/index.php?url=shop" class="ssb-clear-all">Xóa tất cả</a>
    </div>
    <?php endif; ?>

</div>
</div>

<style>
.ssb-wrap { max-width: 860px; margin: 0 auto; }

.ssb-bar {
    display: flex; align-items: center;
    background: #fff; border: 1.5px solid #e0e0e0;
    border-radius: 40px; padding: 6px 6px 6px 16px; gap: 0;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: border-color .2s, box-shadow .2s;
}
.ssb-bar:focus-within { border-color: #111; box-shadow: 0 4px 20px rgba(0,0,0,.10); }

.ssb-divider { width: 1px; height: 22px; background: #e8e8e8; margin: 0 2px; flex-shrink: 0; }

/* Search */
.ssb-search-wrap {
    position: relative; display: flex; align-items: center;
    flex: 1; min-width: 0;
}
.ssb-icon { width: 16px; height: 16px; color: #999; flex-shrink: 0; margin-right: 8px; }
.ssb-input {
    border: none; outline: none; background: transparent;
    font-size: 14px; width: 100%; min-width: 80px; color: #111;
}
.ssb-input::placeholder { color: #bbb; }
.ssb-clear {
    border: none; background: none; cursor: pointer;
    color: #999; font-size: 12px; padding: 2px 6px;
    border-radius: 50%; transition: background .15s;
}
.ssb-clear:hover { background: #f0f0f0; color: #333; }
.ssb-clear.hidden { display: none; }

/* Autocomplete */
.ssb-suggest {
    position: absolute; top: calc(100% + 10px); left: -24px;
    background: #fff; border: 1.5px solid #e8e8e8;
    border-radius: 14px; list-style: none;
    margin: 0; padding: 6px 0;
    min-width: 260px; max-height: 280px; overflow-y: auto;
    box-shadow: 0 8px 24px rgba(0,0,0,.10);
    z-index: 1000; display: none;
}
.ssb-suggest.open { display: block; }
.ssb-suggest li {
    padding: 9px 16px; cursor: pointer; font-size: 13.5px; color: #333;
    display: flex; align-items: center; gap: 10px; transition: background .12s;
}
.ssb-suggest li:hover, .ssb-suggest li.active { background: #f5f5f5; }
.ssb-suggest li mark { background: none; color: #111; font-weight: 600; }
.ssb-suggest li .sug-price { margin-left: auto; color: #888; font-size: 12px; white-space: nowrap; }
.ssb-suggest .sug-empty { padding: 12px 16px; color: #bbb; font-size: 13px; pointer-events: none; }

/* Filter buttons (Loại + Giá dùng chung) */
.ssb-filter-wrap { position: relative; flex-shrink: 0; }
.ssb-filter-btn {
    border: none; background: transparent;
    padding: 5px 10px; border-radius: 20px;
    font-size: 12.5px; color: #555; cursor: pointer;
    display: flex; align-items: center; gap: 5px;
    transition: all .15s; white-space: nowrap;
}
.ssb-filter-btn:hover { background: #f5f5f5; color: #111; }
.ssb-filter-btn.active { background: #111; color: #fff; }

/* Shared panel */
.ssb-panel {
    display: none; position: absolute;
    top: calc(100% + 12px); right: 0;
    background: #fff; border: 1.5px solid #e8e8e8;
    border-radius: 16px; padding: 14px;
    min-width: 220px; box-shadow: 0 8px 24px rgba(0,0,0,.10);
    z-index: 999;
}
.ssb-panel.open { display: block; }

/* Category list */
.scp-list {
    display: flex; flex-direction: column; gap: 2px;
    max-height: 240px; overflow-y: auto;
}
.scp-item {
    display: flex; align-items: center;
    padding: 8px 10px; border-radius: 8px;
    cursor: pointer; font-size: 13px; color: #555;
    transition: background .12s; user-select: none;
}
.scp-item input[type=radio] { display: none; }
.scp-item:hover { background: #f5f5f5; color: #111; }
.scp-item.active { background: #f0f0f0; color: #111; font-weight: 500; }
.scp-item.active::after { content: '✓'; margin-left: auto; font-size: 12px; color: #111; }

/* Price panel */
.spp-row { display: flex; align-items: flex-end; gap: 8px; margin-bottom: 12px; }
.spp-field { flex: 1; }
.spp-field label { display: block; font-size: 11px; color: #999; margin-bottom: 4px; font-weight: 500; }
.spp-field input {
    width: 100%; border: 1.5px solid #e8e8e8; border-radius: 10px;
    padding: 7px 10px; font-size: 13px; outline: none; transition: border-color .15s;
    box-sizing: border-box;
}
.spp-field input:focus { border-color: #111; }
.spp-dash { color: #ccc; padding-bottom: 8px; font-size: 16px; }
.spp-quick { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
.spp-quick button {
    border: 1.5px solid #e8e8e8; background: transparent;
    border-radius: 20px; padding: 4px 10px;
    font-size: 11.5px; color: #666; cursor: pointer; transition: all .12s;
}
.spp-quick button:hover { border-color: #111; color: #111; }
.spp-actions { display: flex; gap: 8px; }
.spp-reset {
    border: 1.5px solid #e8e8e8; background: transparent;
    border-radius: 10px; padding: 7px 14px; font-size: 13px;
    cursor: pointer; color: #888; transition: all .12s;
}
.spp-reset:hover { border-color: #333; color: #333; }
.spp-apply {
    flex: 1; background: #111; color: #fff;
    border: none; border-radius: 10px; padding: 8px;
    font-size: 13px; cursor: pointer; transition: opacity .15s;
}
.spp-apply:hover { opacity: .85; }

/* Submit */
.ssb-submit {
    background: #111; color: #fff; border: none;
    border-radius: 30px; padding: 9px 22px; font-size: 13.5px;
    cursor: pointer; flex-shrink: 0; margin-left: 4px; transition: opacity .15s;
}
.ssb-submit:hover { opacity: .82; }

/* Tags */
.ssb-tags {
    display: flex; flex-wrap: wrap; gap: 6px;
    align-items: center; margin-top: 12px; padding: 0 8px;
}
.ssb-tag {
    display: inline-flex; align-items: center; gap: 5px;
    background: #f3f3f3; border-radius: 20px;
    padding: 4px 10px 4px 12px; font-size: 12.5px; color: #444;
}
.ssb-tag a { color: #999; text-decoration: none; font-size: 11px; }
.ssb-tag a:hover { color: #c00; }
.ssb-clear-all { font-size: 12px; color: #c00; text-decoration: none; margin-left: 4px; }
.ssb-clear-all:hover { text-decoration: underline; }

@media (max-width: 640px) {
    .ssb-bar { flex-wrap: wrap; border-radius: 16px; padding: 10px 12px; gap: 8px; }
    .ssb-divider { display: none; }
    .ssb-submit { width: 100%; text-align: center; margin: 0; }
    .ssb-panel { left: 0; right: auto; }
}
</style>

<script>
(function () {

/* ── Autocomplete ── */
const input    = document.getElementById('ssbInput');
const suggest  = document.getElementById('ssbSuggest');
const clearBtn = document.getElementById('ssbClear');
let debounce, activeIdx = -1;

input.addEventListener('input', function () {
    clearBtn.classList.toggle('hidden', !this.value);
    clearTimeout(debounce);
    const q = this.value.trim();
    if (!q) { closeSuggest(); return; }
    debounce = setTimeout(() => fetchSuggest(q), 200);
});

function fetchSuggest(q) {
    fetch(`<?= BASE_URL ?>/index.php?url=shop-suggest&q=${encodeURIComponent(q)}`)
        .then(r => r.json())
        .then(items => renderSuggest(q, items))
        .catch(() => {});
}

function renderSuggest(q, items) {
    if (!items.length) {
        suggest.innerHTML = '<li class="sug-empty">Không tìm thấy sản phẩm</li>';
        suggest.classList.add('open'); return;
    }
    const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    suggest.innerHTML = items.map(it => `
        <li data-name="${esc(it.name)}">
            <span>${esc(it.name).replace(re, '<mark>$1</mark>')}</span>
            ${it.price ? `<span class="sug-price">${it.price}đ</span>` : ''}
        </li>`).join('');
    suggest.classList.add('open');
    suggest.querySelectorAll('li[data-name]').forEach(li => {
        li.addEventListener('click', () => {
            input.value = li.dataset.name;
            clearBtn.classList.remove('hidden');
            closeSuggest();
            document.getElementById('ssbForm').submit();
        });
    });
    activeIdx = -1;
}

input.addEventListener('keydown', e => {
    const items = [...suggest.querySelectorAll('li[data-name]')];
    if (!items.length) return;
    if (e.key === 'ArrowDown')  { e.preventDefault(); setActive(items, ++activeIdx); }
    else if (e.key === 'ArrowUp')   { e.preventDefault(); setActive(items, --activeIdx); }
    else if (e.key === 'Escape')    { closeSuggest(); }
    else if (e.key === 'Enter' && activeIdx >= 0) {
        e.preventDefault();
        input.value = items[activeIdx].dataset.name;
        closeSuggest();
        document.getElementById('ssbForm').submit();
    }
});

function setActive(items, idx) {
    activeIdx = (idx + items.length) % items.length;
    items.forEach((li, i) => li.classList.toggle('active', i === activeIdx));
    if (items[activeIdx]) input.value = items[activeIdx].dataset.name;
}
function closeSuggest() { suggest.classList.remove('open'); activeIdx = -1; }
document.addEventListener('click', e => { if (!e.target.closest('.ssb-search-wrap')) closeSuggest(); });
clearBtn.addEventListener('click', () => { input.value = ''; clearBtn.classList.add('hidden'); closeSuggest(); input.focus(); });
function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

/* ── Dropdown helpers ── */
function openPanel(panel) {
    document.querySelectorAll('.ssb-panel.open').forEach(p => { if (p !== panel) p.classList.remove('open'); });
    panel.classList.toggle('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('.ssb-filter-wrap')) {
        document.querySelectorAll('.ssb-panel').forEach(p => p.classList.remove('open'));
    }
});

/* ── Category ── */
const catBtn    = document.getElementById('ssbCatBtn');
const catPanel  = document.getElementById('ssbCatPanel');
const catLabel  = document.getElementById('ssbCatLabel');
const catHidden = document.getElementById('ssb_category');

catBtn.addEventListener('click', e => { e.stopPropagation(); openPanel(catPanel); });

document.querySelectorAll('.scp-item input[type=radio]').forEach(radio => {
    radio.addEventListener('change', function () {
        const id   = this.value;
        const name = this.dataset.name || 'Loại';
        catHidden.value = id;
        catLabel.textContent = id === '0' ? 'Loại' : name;
        catBtn.classList.toggle('active', id !== '0');
        document.querySelectorAll('.scp-item').forEach(l => l.classList.remove('active'));
        this.closest('.scp-item').classList.add('active');
        catPanel.classList.remove('open');
        // Không submit — chờ ấn Tìm
    });
});

/* ── Price ── */
const priceBtn   = document.getElementById('ssbPriceBtn');
const pricePanel = document.getElementById('ssbPricePanel');
const priceLabel = document.getElementById('ssbPriceLabel');
const minD = document.getElementById('spp_min_d');
const maxD = document.getElementById('spp_max_d');
const minH = document.getElementById('ssb_price_min');
const maxH = document.getElementById('ssb_price_max');

priceBtn.addEventListener('click', e => { e.stopPropagation(); openPanel(pricePanel); });

function fmt(s) { const n = s.replace(/\D/g,''); return n ? Number(n).toLocaleString('vi-VN') : ''; }
[minD, maxD].forEach(inp => inp.addEventListener('input', function () { this.value = fmt(this.value); }));

document.querySelectorAll('.spp-quick button').forEach(btn => {
    btn.addEventListener('click', function () {
        const mn = +this.dataset.min, mx = +this.dataset.max;
        minD.value = mn ? mn.toLocaleString('vi-VN') : '';
        maxD.value = mx ? mx.toLocaleString('vi-VN') : '';
    });
});

document.getElementById('sppApply').addEventListener('click', () => {
    minH.value = minD.value.replace(/\D/g,'');
    maxH.value = maxD.value.replace(/\D/g,'');
    document.getElementById('ssbForm').submit();
});

document.getElementById('sppClear').addEventListener('click', () => {
    minD.value = maxD.value = minH.value = maxH.value = '';
    priceLabel.textContent = 'Giá';
    priceBtn.classList.remove('active');
    pricePanel.classList.remove('open');
    document.getElementById('ssbForm').submit();
});

})();
</script>

<!-- Products -->
<div class="product-section" style="height: 450px;">
    <div class="container">
        <div class="row">

        <?php if (empty($products)): ?>
            <div class="col-12 text-center text-muted py-5">
                <h4>Không tìm thấy sản phẩm nào</h4>
                <p>Vui lòng thử lại với bộ lọc khác</p>
                <a href="<?= BASE_URL ?>/index.php?url=shop" class="btn btn-dark mt-2">Xem tất cả</a>
            </div>

        <?php else: ?>
            <?php foreach ($products as $p):
                $id           = (int)$p['id'];
                $name         = htmlspecialchars($p['name']);
                $stock        = (int)($p['total_stock'] ?? 0);
                $isOutOfStock = $stock <= 0;
                $avgImport    = (float)($p['avg_import_price'] ?? 0);
                $profitRate   = (float)($p['profit_rate']      ?? 0);
                $priceAdjust  = (float)($p['price_adjust']     ?? 0);
                $salePrice    = round(($avgImport + $priceAdjust) * (1 + $profitRate / 100), -3);
                $price        = number_format($salePrice, 0, ',', '.');
                $imagePath    = !empty($p['image'])
                    ? BASE_URL . '/images/' . htmlspecialchars($p['image'])
                    : BASE_URL . '/images/placeholder.png';
            ?>
            <div class="col-12 col-md-6 col-lg-3 mb-5">
                <div class="product-item text-center h-100">
                    <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= $id ?>">
                        <img src="<?= $imagePath ?>" class="product-thumbnail img-fluid" loading="lazy" alt="<?= $name ?>">
                    </a>
                    <h3 class="product-title mt-3"><?= $name ?></h3>
                    <strong class="product-price d-block mb-1">
                        <?= $salePrice > 0 ? $price . 'đ' : 'Liên hệ' ?>
                    </strong>
                    <div class="<?= $isOutOfStock ? 'text-danger' : 'text-success' ?> mb-2 small">
                        <?= $isOutOfStock ? 'Hết hàng' : 'Còn hàng' ?>
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-sm <?= $isOutOfStock ? 'btn-secondary' : 'btn-dark' ?>"
                                <?= $isOutOfStock ? 'disabled' : '' ?>
                                data-product-id="<?= $id ?>">
                            <img src="<?= BASE_URL ?>/images/cart.svg" width="18" alt="cart">
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?url=product-detail&id=<?= $id ?>"
                           class="btn btn-sm btn-outline-dark">Chi tiết</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-2 mb-5">
            <?php
            $qp = $_GET; $qp['url'] = 'shop'; unset($qp['page']);
            $qs = '?' . http_build_query($qp) . '&';
            ?>
            <?php if ($page > 1): ?>
                <a href="<?= $qs ?>page=<?= $page - 1 ?>" class="btn btn-sm btn-outline-dark mx-1">&laquo; Trước</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= $qs ?>page=<?= $i ?>" class="btn btn-sm mx-1 <?= $i == $page ? 'btn-dark' : 'btn-outline-dark' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="<?= $qs ?>page=<?= $page + 1 ?>" class="btn btn-sm btn-outline-dark mx-1">Sau &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<div id="toast"></div>

<script>
document.querySelectorAll('[data-product-id]').forEach(btn => {
    btn.addEventListener('click', function () {
        fetch('<?= BASE_URL ?>/index.php?url=cart-add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${this.dataset.productId}&size_id=0&qty=1`
        })
        .then(r => r.json())
        .then(data => {
            const toast = document.getElementById('toast');
            if (!toast) return;
            toast.innerText = data.message ?? (data.ok ? 'Đã thêm vào giỏ!' : 'Thêm thất bại');
            toast.className = 'show ' + (data.ok ? 'success' : 'error');
            setTimeout(() => toast.className = '', 2500);
        });
    });
});
</script>