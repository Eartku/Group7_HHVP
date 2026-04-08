<?php /* app/views/admin/sell/index.php */ ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý Giá Bán</h2>
    </div>

    <?php if (isset($_GET['updated'])): ?>
        <div class="ui-alert success mb-4">Cập nhật % lợi nhuận thành công.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="ui-alert danger mb-4">Cập nhật thất bại. Vui lòng thử lại.</div>
    <?php endif; ?>

    <!-- ── Search ── -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <h5>Tìm kiếm sản phẩm</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url" value="admin-sell">
                <input type="hidden" name="do_search" value="1">
                <!-- Giữ filter khi search -->
                <input type="hidden" name="category_id"  value="<?= (int)$category_id ?>">
                <input type="hidden" name="cost_range"   value="<?= htmlspecialchars($cost_range) ?>">
                <input type="hidden" name="profit_range" value="<?= htmlspecialchars($profit_range) ?>">

                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Tìm theo</label>
                            <select name="search_type" class="ui-input">
                                <option value="name"        <?= $search_type === 'name'        ? 'selected' : '' ?>>Tên sản phẩm</option>
                                <option value="id"          <?= $search_type === 'id'          ? 'selected' : '' ?>>Mã sản phẩm</option>
                                <option value="category_id" <?= $search_type === 'category_id' ? 'selected' : '' ?>>Mã phân loại</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Từ khóa</label>
                            <input type="text" name="search_value" class="ui-input"
                                   value="<?= htmlspecialchars($search_value) ?>"
                                   placeholder="Nhập từ khóa tìm kiếm...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="ui-btn sm w-100">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" width="14" height="14" style="margin-right:4px">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            Tìm kiếm
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= BASE_URL ?>/index.php?url=admin-sell" class="ui-btn-outline sm w-100 d-block text-center">✕ Xóa tìm kiếm</a>
                    </div>
                </div>

                <?php if ($search_error): ?>
                    <div class="mt-2" style="color:var(--danger);font-size:13px"><?= htmlspecialchars($search_error) ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- ── Filter ── -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            <h5>Bộ lọc</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="url" value="admin-sell">
                <!-- Giữ search khi filter -->
                <?php if ($search_done): ?>
                    <input type="hidden" name="do_search"    value="1">
                    <input type="hidden" name="search_type"  value="<?= htmlspecialchars($search_type) ?>">
                    <input type="hidden" name="search_value" value="<?= htmlspecialchars($search_value) ?>">
                <?php endif; ?>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Phân loại</label>
                            <select name="category_id" class="ui-input">
                                <option value="">Tất cả</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= (int)$cat['id'] ?>"
                                        <?= $category_id === (int)$cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ui-field mb-0">
                            <label class="ui-label">Giá vốn (VNĐ)</label>
                            <select name="cost_range" class="ui-input">
                                <option value="">Tất cả</option>
                                <option value="0-30000"         <?= $cost_range === '0-30000'         ? 'selected' : '' ?>>0 – 30.000</option>
                                <option value="30000-80000"     <?= $cost_range === '30000-80000'     ? 'selected' : '' ?>>30.000 – 80.000</option>
                                <option value="80000-150000"    <?= $cost_range === '80000-150000'    ? 'selected' : '' ?>>80.000 – 150.000</option>
                                <option value="150000-999999999" <?= $cost_range === '150000-999999999' ? 'selected' : '' ?>>Trên 150.000</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="ui-field mb-0">
                            <label class="ui-label">% Lợi nhuận</label>
                            <select name="profit_range" class="ui-input">
                                <option value="">Tất cả</option>
                                <option value="0-10"  <?= $profit_range === '0-10'  ? 'selected' : '' ?>>Dưới 10%</option>
                                <option value="10-30" <?= $profit_range === '10-30' ? 'selected' : '' ?>>10% – 30%</option>
                                <option value="30-50" <?= $profit_range === '30-50' ? 'selected' : '' ?>>30% – 50%</option>
                                <option value="50-999" <?= $profit_range === '50-999' ? 'selected' : '' ?>>Trên 50%</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="ui-btn sm flex-grow-1">Lọc</button>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-sell" class="ui-btn-outline sm d-flex align-items-center px-3">✕</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Table ── -->
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
            <h5>Danh sách sản phẩm — <strong><?= $total_rows ?></strong> sản phẩm</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th style="width:60px">Mã</th>
                        <th>Tên sản phẩm</th>
                        <th>Phân loại</th>
                        <th class="right" style="width:140px">Giá vốn TB</th>
                        <th class="center" style="width:160px">% Lợi nhuận</th>
                        <th class="right" style="width:140px">Giá bán</th>
                        <th class="center" style="width:120px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="ui-empty py-4">
                                <h4>Không tìm thấy sản phẩm</h4>
                                <p>Thử thay đổi bộ lọc hoặc từ khóa</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                    <?php
                        // Build back URL để quay về đúng trang sau khi save
                        $backParams = http_build_query([
                            'url'          => 'admin-sell',
                            'page'         => $page,
                            'search_type'  => $search_type,
                            'search_value' => $search_value,
                            'category_id'  => $category_id,
                            'cost_range'   => $cost_range,
                            'profit_range' => $profit_range,
                        ] + ($search_done ? ['do_search' => '1'] : []));
                        $backUrl = BASE_URL . '/index.php?' . $backParams;
                    ?>
                    <tr>
                        <td><span style="font-family:monospace;font-weight:700;color:var(--brand)">#<?= (int)$p['id'] ?></span></td>
                        <td style="font-weight:600"><?= htmlspecialchars($p['name']) ?></td>
                        <td><span class="ui-badge info"><?= htmlspecialchars($p['category_name'] ?? '—') ?></span></td>
                        <td class="right price">
                            <?= $p['avg_import_price'] > 0
                                ? number_format($p['avg_import_price'], 0, ',', '.') . 'đ'
                                : '<span class="muted">—</span>' ?>
                        </td>
                        <td class="center">
                            <form method="POST" action="<?= BASE_URL ?>/index.php?url=admin-sell-update"
                                  class="d-inline-flex align-items-center gap-1">
                                <input type="hidden" name="type"     value="product">
                                <input type="hidden" name="id"       value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="back_url" value="<?= htmlspecialchars($backUrl) ?>">
                                <input type="number" name="profit_rate"
                                       class="ui-input"
                                       value="<?= number_format((float)$p['profit_rate'], 1, '.', '') ?>"
                                       min="0" max="999" step="0.1"
                                       style="text-align:center;width:80px">
                                <span class="muted" style="font-size:13px">%</span>
                                <button type="submit" class="ui-btn sm">Lưu</button>
                            </form>
                        </td>
                        <td class="right price">
                            <?= $p['sale_price'] > 0
                                ? number_format($p['sale_price'], 0, ',', '.') . 'đ'
                                : '<span class="muted">—</span>' ?>
                        </td>
                        <td class="center">—</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php
                        $pageParams = http_build_query([
                            'url'          => 'admin-sell',
                            'page'         => $i,
                            'search_type'  => $search_type,
                            'search_value' => $search_value,
                            'category_id'  => $category_id,
                            'cost_range'   => $cost_range,
                            'profit_range' => $profit_range,
                        ] + ($search_done ? ['do_search' => '1'] : []));
                    ?>
                    <a href="<?= BASE_URL ?>/index.php?<?= $pageParams ?>"
                       class="ui-page-btn<?= $i === $page ? ' active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>