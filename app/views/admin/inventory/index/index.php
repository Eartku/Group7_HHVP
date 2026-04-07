    <?php /* app/views/admin/inventory/index.php */ ?>

        <div class="container-fluid py-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="ui-title mb-0">Quản lý nhập kho</h2>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['confirmed'])): ?>
            <div class="ui-alert success mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Đã xác nhận phiếu nhập kho thành công.
            </div>
            <?php elseif (isset($_GET['cancelled'])): ?>
            <div class="ui-alert warning mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Đã hủy phiếu nhập.
            </div>
            <?php elseif (isset($_GET['error'])): ?>
            <div class="ui-alert danger mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                Thao tác thất bại. Vui lòng thử lại.
            </div>
            <?php endif; ?>

            <div class="ui-tabs mb-3">
                <button class="tab-btn active" data-tab="inventory">
                    <i class="fa fa-box"></i> Tồn kho
                </button>
                <button class="tab-btn" data-tab="logs">
                    <i class="fa fa-exchange-alt"></i> Xuất / Nhập
                </button>
                <button class="tab-btn" data-tab="out">
                    <i class="fa fa-exclamation-triangle"></i> Hết hàng
                </button>
            </div>

            <!-- ═══════════════════════════════════════
                BẢNG TỒN KHO
            ═══════════════════════════════════════ -->
            <div id="tab-inventory" class="tab-content active">
                <div class="ui-card mb-4">
                    <div class="ui-card-head">
                        <h5>Danh sách tồn kho</h5>
                    </div>

                    <div style="overflow-x:auto">
                        <!-- Filter tồn kho -->
                        <!-- Filter tồn kho -->
                        <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                            <form method="GET" action="<?= BASE_URL ?>/index.php" id="inv-filter-form">
                                <input type="hidden" name="url" value="admin-inventory">
                                <input type="hidden" name="tab" value="inventory">

                                <div class="row g-3 align-items-end">
                                    <!-- Tìm kiếm sản phẩm (có autocomplete + dropdown) -->
                                    <div class="col-md-4">
                                        <label class="ui-label">Sản phẩm</label>
                                        <div class="product-search-wrapper" style="position:relative">
                                            <!-- Ô tìm kiếm - bỏ nút mũi tên -->
                                            <input type="text" id="inv-product-search"
                                                class="ui-input"
                                                placeholder=" tên sản phẩm "
                                                autocomplete="off"
                                                value="<?= htmlspecialchars($invProductName ?? '') ?>"
                                                style="padding-right: 10px;">
                                            
                                            <input type="hidden" name="inv_product_id" id="inv-product-id"
                                                value="<?= htmlspecialchars($invProductId ?? '') ?>">
                                            
                                            <!-- Autocomplete dropdown -->
                                            <div id="inv-product-dropdown" class="inv-autocomplete-list"></div>
                                            
                                            <!-- Danh sách sản phẩm dạng select box -->
                                            <div id="product-list-dropdown" class="product-select-dropdown" style="display: none;">
                                                <div class="product-select-header">
                                                    <input type="text" id="product-search-input" 
                                                        class="ui-input" 
                                                        placeholder="Tìm kiếm sản phẩm..."
                                                        style="margin: 10px; width: calc(100% - 20px);">
                                                </div>
                                                <div class="product-select-body" id="product-select-body">
                                                    <?php foreach ($products as $product): ?>
                                                    <div class="product-select-item" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                                                        <span class="product-name"><?= htmlspecialchars($product['name']) ?></span>
                                                        <span class="product-id">#<?= $product['id'] ?></span>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tra cứu tại thời điểm -->
                                    <div class="col-md-3">
                                        <label class="ui-label">Tại thời điểm</label>
                                        <input type="date" name="inv_time" class="ui-input"
                                            value="<?= htmlspecialchars($invTime ?? '') ?>">
                                    </div>

                                    <div class="col-md-3 d-flex gap-2 align-items-end">
                                        <button class="ui-btn sm flex-grow-1">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                                style="width:14px;height:14px;stroke:#fff">
                                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                            </svg>
                                            Lọc
                                        </button>
                                        <?php if (!empty($invProductId) || !empty($invTime)): ?>
                                            <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&tab=inventory"
                                            class="ui-btn-outline sm">✕ Xóa</a>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </form>
                        </div>

                        <table class="ui-table admin-head">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Size</th>
                            <?php if (!empty($invTime)): ?>
                            <th>Tại thời điểm</th>
                            <th class="right">Số lượng</th>
                            <?php else: ?>
                            <th class="right">Số lượng</th>
                            <?php endif; ?>
                            <th class="right">Giá nhập TB</th>
                        </tr>
                        </thead>
                            <tbody>
                                <?php if (empty($inventory)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                </tr>
                                <?php else: foreach ($inventory as $inv): ?>
                                <tr>
                                    <td>#<?= $inv['product_id'] ?: '—' ?></td>
                                    <td><?= htmlspecialchars($inv['product_name']) ?></td>
                                    <td>
                                        <span class="ui-badge neutral">
                                            <?= htmlspecialchars($inv['category_name'] ?? '—') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($inv['size_name'] ?? '—') ?></td>
                                    <?php if (!empty($invTime)): ?>
                                    <td style="font-size:13px;color:var(--text-muted)">
                                        <?= date('d/m/Y', strtotime($invTime)) ?>
                                        <small style="display:block; font-size:11px;">
                                        </small>
                                    </td>
                                    <?php endif; ?>
                                    <td class="right" style="<?= $inv['quantity'] == 0 ? 'color:red;font-weight:700' : 'font-weight:600' ?>">
                                        <?= number_format($inv['quantity']) ?>
                                    </td>
                                    <td class="right"><?= number_format($inv['avg_import_price']) ?>đ</td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($invTotalPages) && $invTotalPages > 1): ?>
                        <div class="ui-card-body">
                            <div class="ui-pagination">
                            <?php
                                $invQs = '?url=admin-inventory&tab=inventory'
                                    . '&inv_product_id=' . urlencode($invProductId ?? '')
                                    . '&inv_time=' . urlencode($invTime ?? '')
                                    . '&inv_page=';
                            ?>
                            <?php if ($invPage > 1): ?>
                                <a href="<?= BASE_URL ?>/index.php<?= $invQs . ($invPage - 1) ?>" class="ui-page-btn">‹</a>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $invTotalPages; $i++): ?>
                                <a href="<?= BASE_URL ?>/index.php<?= $invQs . $i ?>"
                                class="ui-page-btn <?= $i == $invPage ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            <?php if ($invPage < $invTotalPages): ?>
                                <a href="<?= BASE_URL ?>/index.php<?= $invQs . ($invPage + 1) ?>" class="ui-page-btn">›</a>
                            <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div> <!-- END tab inventory -->

            <!-- ═══════════════════════════════════════
                BẢNG HẾT HÀNG
            ═══════════════════════════════════════ -->
            <div id="tab-out" class="tab-content">
                <div class="ui-card mb-4">

                    <div class="ui-card-head" style="display:flex;justify-content:space-between;align-items:center">
                        <h5>Sản phẩm hết hàng / sắp hết</h5>
                        <span style="font-size:13px;color: #000000">
                            Tổng <strong style="color: #bd0024"><?= $outTotal ?></strong> sản phẩm hết / sắp hết hàng
                        </span>
                    </div>
                    <!-- Filter chỉ còn threshold -->
                    <!-- Filter chỉ còn threshold -->
                    <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                        <form method="GET" action="<?= BASE_URL ?>/index.php" id="out-filter-form">
                            <input type="hidden" name="url" value="admin-inventory">
                            <input type="hidden" name="tab" value="out">

                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="ui-label">Cảnh báo dưới (số lượng)</label>
                                    <input type="number"
                                        name="threshold"
                                        class="ui-input"
                                        min="0"
                                        value="<?= htmlspecialchars($threshold ?? 0) ?>"
                                        placeholder="Nhập ngưỡng cảnh báo">
                                </div>

                                <div class="col-md-2 d-flex gap-2 align-items-end">
                                    <button class="ui-btn sm flex-grow-1">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                            style="width:14px;height:14px;stroke:#fff">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                        </svg>
                                        Lọc
                                    </button>
                                    <?php if (!empty($threshold)): ?>
                                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&tab=out"
                                        class="ui-btn-outline sm">✕ Xóa</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table: sắp xếp số lượng cao → thấp, bỏ cột trạng thái -->
                    <div style="overflow-x:auto">
                        <table class="ui-table admin-head">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Size</th>
                                    <th class="right">Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($outStock)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có sản phẩm hết hàng</td>
                                </tr>
                            <?php else: foreach ($outStock as $inv): ?>
                                <tr>
                                    <td>#<?= $inv['product_id'] ?></td>
                                    <td><?= htmlspecialchars($inv['product_name']) ?></td>
                                    <td>
                                        <span class="ui-badge neutral">
                                        <?= htmlspecialchars($inv['category_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($inv['size_name']) ?></td>
                                    <td class="right" style="color:red;font-weight:700">
                                        <?= number_format($inv['quantity']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($outTotalPages) && $outTotalPages > 1): ?>
                        <div class="ui-card-body">
                            <div class="ui-pagination">
                                <?php
                                $qs = '?url=admin-inventory&tab=out'
                                    . '&threshold=' . urlencode($threshold ?? 0)
                                    . '&out_page=';
                                ?>
                                <?php if ($outPage > 1): ?>
                                    <a href="<?= BASE_URL ?>/index.php<?= $qs . ($outPage - 1) ?>" class="ui-page-btn">‹</a>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $outTotalPages; $i++): ?>
                                    <a href="<?= BASE_URL ?>/index.php<?= $qs . $i ?>"
                                    class="ui-page-btn <?= $i == $outPage ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                                <?php if ($outPage < $outTotalPages): ?>
                                    <a href="<?= BASE_URL ?>/index.php<?= $qs . ($outPage + 1) ?>" class="ui-page-btn">›</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ═══════════════════════════════════════
                BẢNG LOG XUẤT/NHẬP KHO (nhóm theo ngày)
            ═══════════════════════════════════════ -->
            <!-- ═══════════════════════════════════════
                BẢNG LOG XUẤT/NHẬP KHO
            ═══════════════════════════════════════ -->
            <div id="tab-logs" class="tab-content">
                <div class="ui-card mb-0">
                    <div class="ui-card-head">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                        </svg>
                        <h5>Lịch sử xuất / nhập kho</h5>
                    </div>

                    <!-- Filter: từ ngày - đến ngày -->
                    <div class="ui-card-body" style="border-bottom:1px solid var(--border)">
                        <form method="GET" action="<?= BASE_URL ?>/index.php" id="logs-filter-form">
                            <input type="hidden" name="url" value="admin-inventory">
                            <input type="hidden" name="tab" value="logs">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <div class="ui-field mb-0">
                                        <label class="ui-label">Từ ngày</label>
                                        <input type="date" name="log_from" class="ui-input"
                                            value="<?= htmlspecialchars($logFrom ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="ui-field mb-0">
                                        <label class="ui-label">Đến ngày</label>
                                        <input type="date" name="log_to" class="ui-input"
                                            value="<?= htmlspecialchars($logTo ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="ui-btn sm flex-grow-1">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                            style="width:14px;height:14px;stroke:#fff">
                                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                        </svg>
                                        Lọc
                                    </button>
                                    <?php if (!empty($logFrom) || !empty($logTo)): ?>
                                        <a href="<?= BASE_URL ?>/index.php?url=admin-inventory&tab=logs"
                                        class="ui-btn-outline sm">✕ Xóa</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Hiển thị logs -->
                    <!-- Hiển thị logs -->
                    <div style="overflow-x:auto; padding: 16px">
                        <?php if (empty($logs) && empty($logsByDate)): ?>
                            <div class="ui-empty py-4">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                </svg>
                                <h4>Không có bản ghi nào</h4>
                                <p>Chưa có dữ liệu xuất nhập kho</p>
                            </div>
                        <?php else: ?>
                            
                            <?php if (!empty($logFrom) || !empty($logTo)): ?>
                                <!-- CHẾ ĐỘ TÌM KIẾM: Hiển thị nhóm theo ngày -->
                                <?php foreach ($logsByDate as $dateKey => $dateData): ?>
                                <div class="log-day-group mb-4">
                                    <div class="log-day-header">
                                        <span class="log-day-label">
                                            <i class="fa fa-calendar-alt"></i>
                                            Ngày <?= date('d/m/Y', strtotime($dateKey)) ?>
                                        </span>
                                    </div>
                                    <table class="ui-table admin-head" style="margin-bottom:0">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Size</th>
                                                <th class="center" style="width:100px">Loại</th>
                                                <th class="right" style="width:100px">Số lượng</th>
                                                <th style="width:220px">Ghi chú</th>
                                                <th style="width:140px">Thời gian</th>
                                                <th style="width:140px" class="center">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($dateData['rows'] as $log): ?>
                                        <tr>
                                            <td style="font-weight:600"><?= htmlspecialchars($log['product_name'] ?? '—') ?></td>
                                            <td><span class="ui-badge neutral"><?= htmlspecialchars($log['size_name'] ?? '—') ?></span></td>
                                            <td class="center">
                                                <?php if ($log['type'] === 'import'): ?>
                                                <span class="ui-badge success">↓ Nhập</span>
                                                <?php else: ?>
                                                <span class="ui-badge warning">↑ Xuất</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="right" style="font-weight:700">
                                                <?= $log['type'] === 'import' ? '+' : '-' ?>
                                                <?= number_format((int)$log['quantity'], 0, ',', '.') ?>
                                            </td>
                                            <td class="muted" style="font-size:13px"><?= htmlspecialchars($log['note'] ?? '—') ?></td>
                                            <td class="muted" style="font-size:13px">
                                                <?= !empty($log['created_at']) ? date('d/m/Y H:i', strtotime($log['created_at'])) : '—' ?>
                                            </td>
                                            <td class="center">
                                                <?php if ($log['type'] === 'export' && !empty($log['order_id'])): ?>
                                                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders-detail&id=<?= (int)$log['order_id'] ?>"
                                                        class="ui-btn-outline sm">📦 Đơn hàng</a>
                                                <?php elseif ($log['type'] === 'import' && !empty($log['receipt_id'])): ?>
                                                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= (int)$log['receipt_id'] ?>"
                                                        class="ui-btn sm">📥 Phiếu nhập</a>
                                                <?php else: ?>
                                                    <span class="muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <div class="log-day-summary">
                                        <span class="summary-import">↓ Tổng nhập: <strong>+<?= number_format($dateData['total_import']) ?></strong></span>
                                        <span class="summary-export">↑ Tổng xuất: <strong>-<?= number_format($dateData['total_export']) ?></strong></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                            <?php else: ?>
                                <!-- CHẾ ĐỘ MẶC ĐỊNH: Bảng thường + phân trang -->
                                <table class="ui-table admin-head">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Sản phẩm</th>
                                            <th>Size</th>
                                            <th class="center">Loại</th>
                                            <th class="right">Số lượng</th>
                                            <th>Ghi chú</th>
                                            <th>Thời gian</th>
                                            <th class="center">Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                        <?php else: foreach ($logs as $log): ?>
                                        <tr>
                                            <td>#<?= $log['id'] ?></td>
                                            <td style="font-weight:600"><?= htmlspecialchars($log['product_name'] ?? '—') ?></td>
                                            <td><span class="ui-badge neutral"><?= htmlspecialchars($log['size_name'] ?? '—') ?></span></td>
                                            <td class="center">
                                                <?php if ($log['type'] === 'import'): ?>
                                                <span class="ui-badge success">↓ Nhập</span>
                                                <?php else: ?>
                                                <span class="ui-badge warning">↑ Xuất</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="right" style="font-weight:700">
                                                <?= $log['type'] === 'import' ? '+' : '-' ?>
                                                <?= number_format((int)$log['quantity'], 0, ',', '.') ?>
                                            </td>
                                            
                                            <td class="muted" style="font-size:13px"><?= htmlspecialchars($log['note'] ?? '—') ?></td>
                                            <td class="muted" style="font-size:13px">
                                                <?= !empty($log['created_at']) ? date('d/m/Y H:i', strtotime($log['created_at'])) : '—' ?>
                                            </td>
                                            <td class="center">
                                                <?php if ($log['type'] === 'export' && !empty($log['order_id'])): ?>
                                                    <a href="<?= BASE_URL ?>/index.php?url=admin-orders-detail&id=<?= (int)$log['order_id'] ?>"
                                                        class="ui-btn-outline sm">📦 Đơn</a>
                                                <?php elseif ($log['type'] === 'import' && !empty($log['receipt_id'])): ?>
                                                    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory-detail&id=<?= (int)$log['receipt_id'] ?>"
                                                        class="ui-btn sm">📥 Phiếu</a>
                                                <?php else: ?>
                                                    <span class="muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                                
                                <!-- Phân trang cho chế độ mặc định -->
                                <?php if (isset($logTotalPages) && $logTotalPages > 1): ?>
                                <div class="ui-pagination" style="margin-top: 20px;">
                                    <?php
                                    $logQs = '?url=admin-inventory&tab=logs&log_page=';
                                    ?>
                                    <?php if ($logCurrentPage > 1): ?>
                                        <a href="<?= BASE_URL ?>/index.php<?= $logQs . ($logCurrentPage - 1) ?>" class="ui-page-btn">‹</a>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $logTotalPages; $i++): ?>
                                        <a href="<?= BASE_URL ?>/index.php<?= $logQs . $i ?>"
                                        class="ui-page-btn <?= $i == $logCurrentPage ? 'active' : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    <?php if ($logCurrentPage < $logTotalPages): ?>
                                        <a href="<?= BASE_URL ?>/index.php<?= $logQs . ($logCurrentPage + 1) ?>" class="ui-page-btn">›</a>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div><!-- /container -->

    <!-- Dữ liệu sản phẩm cho autocomplete -->
    <script>
    // Dữ liệu sản phẩm cho autocomplete
    const invProducts = <?= json_encode(array_map(fn($p) => ['id' => $p['id'], 'name' => $p['name']], $products ?? []), JSON_UNESCAPED_UNICODE) ?>;

    // ── Quản lý dropdown danh sách sản phẩm ──
    (function() {
        const input = document.getElementById('inv-product-search');
        const hiddenId = document.getElementById('inv-product-id');
        const productListDropdown = document.getElementById('product-list-dropdown');
        const productSearchInput = document.getElementById('product-search-input');
        const productSelectBody = document.getElementById('product-select-body');

        if (!input) return;

        // Chọn sản phẩm từ danh sách
        function selectProduct(id, name) {
            input.value = name;
            hiddenId.value = id;
            productListDropdown.style.display = 'none';
        }

        // Lọc sản phẩm trong danh sách
        function filterProductList(searchText) {
            const items = productSelectBody.querySelectorAll('.product-select-item');
            const searchLower = searchText.toLowerCase().trim();
            
            items.forEach(item => {
                const name = item.querySelector('.product-name').textContent.toLowerCase();
                const id = item.getAttribute('data-id');
                if (searchLower === '' || name.includes(searchLower) || id.includes(searchLower)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Hiển thị dropdown danh sách sản phẩm
        function showProductList() {
            productListDropdown.style.display = 'block';
            // Focus vào ô tìm kiếm trong dropdown
            setTimeout(() => {
                if (productSearchInput) {
                    productSearchInput.focus();
                }
            }, 100);
        }

        // Ẩn dropdown
        function hideProductList() {
            productListDropdown.style.display = 'none';
            // Reset ô tìm kiếm khi đóng
            if (productSearchInput) {
                productSearchInput.value = '';
                filterProductList('');
            }
        }

        // Xử lý click chọn sản phẩm từ danh sách
        if (productSelectBody) {
            productSelectBody.querySelectorAll('.product-select-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    selectProduct(id, name);
                    hideProductList();
                });
            });
        }

        // Lọc sản phẩm khi gõ vào ô tìm kiếm trong dropdown
        if (productSearchInput) {
            productSearchInput.addEventListener('input', function(e) {
                e.stopPropagation();
                filterProductList(this.value);
            });
            
            // Ngăn sự kiện click lan ra ngoài
            productSearchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // KHI CLICK VÀO Ô INPUT - HIỂN THỊ DANH SÁCH
        input.addEventListener('click', function(e) {
            e.stopPropagation();
            showProductList();
        });

        // Ngăn không cho input mất focus khi click vào dropdown
        productListDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.product-search-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                hideProductList();
            }
        });
        
        // Nếu có sẵn giá trị, hiển thị tên sản phẩm
        if (input.value.trim() !== '') {
            // Giữ nguyên giá trị
        }
    })();

    // ── Tab logic (giữ nguyên) ──
    const params = new URLSearchParams(window.location.search);
    const currentTab = params.get('tab') || 'inventory';

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.tab === currentTab) btn.classList.add('active');
    });
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    const activeTab = document.getElementById('tab-' + currentTab);
    if (activeTab) activeTab.classList.add('active');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
            const url = new URL(window.location);
            url.searchParams.set('tab', btn.dataset.tab);
            window.history.replaceState({}, '', url);
        });
    });
    </script>

    <style>
    /* Ẩn / hiện tab */
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .ui-tabs {
        display: flex;
        gap: 10px;
    }

    .tab-btn {
        border: none;
        padding: 8px 16px;
        border-radius: 999px;
        background: #f1f1f1;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .tab-btn:hover { background: #e2e2e2; transform: translateY(-1px); }
    .tab-btn.active {
        background: linear-gradient(135deg, #4CAF50, #2e7d32);
        color: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .tab-btn i { font-size: 13px; }

    /* Autocomplete */
    .inv-autocomplete-list {
        position: absolute;
        top: 100%;
        left: 0; right: 0;
        background: #fff;
        border: 1px solid var(--border, #ddd);
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        z-index: 999;
        max-height: 220px;
        overflow-y: auto;
        display: none;
    }
    .inv-autocomplete-item {
        padding: 8px 14px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.15s;
    }
    .inv-autocomplete-item:hover { background: #f0f9f0; color: #2e7d32; }

    /* Nhóm ngày log */
    .log-day-group {
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
    }
    .log-day-header {
        background: linear-gradient(90deg, #f0faf0, #e8f5e9);
        padding: 10px 16px;
        border-bottom: 1px solid var(--border, #e5e7eb);
    }
    .log-day-label {
        font-weight: 700;
        font-size: 14px;
        color: #2e7d32;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .log-day-summary {
        padding: 10px 16px;
        background: #fafafa;
        border-top: 1px solid var(--border, #e5e7eb);
        display: flex;
        gap: 24px;
        font-size: 13px;
    }
    .summary-import { color: #2e7d32; }
    .summary-export { color: #e65100; }

    /* Pagination */
    .ui-pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    .ui-page-btn {
        padding: 6px 12px;
        border-radius: 8px;
        background: #f1f1f1;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .ui-page-btn:hover { background: #e0e0e0; }
    .ui-page-btn.active {
        background: linear-gradient(135deg, #4CAF50, #2e7d32);
        color: #fff;
        font-weight: 600;
    }
    /* Dropdown danh sách sản phẩm */
    .product-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--border, #ddd);
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 400px;
        overflow: hidden;
        margin-top: 4px;
    }

    .product-select-header {
        border-bottom: 1px solid #eee;
        padding: 8px;
    }

    .product-select-body {
        max-height: 340px;
        overflow-y: auto;
    }

    .product-select-item {
        padding: 10px 16px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }

    .product-select-item:hover {
        background: #f0f9f0;
    }

    .product-select-item .product-name {
        font-weight: 500;
        color: #333;
    }

    .product-select-item .product-id {
        font-size: 12px;
        color: #999;
    }

    .dropdown-toggle-btn {
        cursor: pointer;
        font-size: 12px;
        transition: transform 0.2s;
    }

    .dropdown-toggle-btn.active {
        transform: translateY(-50%) rotate(180deg);
    }
    </style>