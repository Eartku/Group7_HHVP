<?php /* app/views/admin/customers/index.php */ ?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="ui-title mb-0">Quản lý khách hàng</h2>
        <a href="<?= BASE_URL ?>/index.php?url=admin-customers-create"
           class="ui-btn sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm tài khoản
        </a>
    </div>

    <!-- Search card -->
    <div class="ui-card mb-4">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <h5>Tìm kiếm khách hàng</h5>
        </div>
        <div class="ui-card-body">
            <form method="GET" action="<?= BASE_URL ?>/index.php" id="searchForm" autocomplete="off">
                <input type="hidden" name="url" value="admin-customers">

                <!-- Search type tabs -->
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <?php
                    $searchTabs = [
                        'name'  => 'Họ tên',
                        'id'    => 'Mã KH',
                        'email' => 'Email',
                        'phone' => 'Số ĐT',
                    ];
                    foreach ($searchTabs as $val => $lbl): ?>
                    <label class="ui-size-btn <?= ($search_type ?? 'name') === $val ? 'selected' : '' ?>"
                           style="cursor:pointer">
                        <input type="radio" name="search_type" value="<?= $val ?>"
                               <?= ($search_type ?? 'name') === $val ? 'checked' : '' ?>
                               style="display:none"
                               onchange="updatePlaceholder('<?= $val ?>')">
                        <?= $lbl ?>
                    </label>
                    <?php endforeach; ?>
                </div>

                <!-- Input row -->
                <div class="d-flex gap-2 flex-wrap">
                    <div class="ui-input-group flex-grow-1">
                        <input type="text" name="search_value" id="search_value"
                               placeholder="Nhập tên khách hàng..."
                               value="<?= htmlspecialchars($search_value ?? '') ?>">
                        <button type="submit" name="do_search" value="1" class="ui-input-addon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                    <button type="submit" name="do_search" value="1" class="ui-btn sm">
                        Lọc
                    </button>
                    <?php if (!empty($search_done)): ?>
                    <a href="<?= BASE_URL ?>/index.php?url=admin-customers"
                       class="ui-btn-outline sm">
                        ✕ Xóa lọc
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Hint -->
                <div id="search_hint" class="ui-subtitle mt-2 mb-0"
                     style="font-size:12px">
                </div>
            </form>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (!empty($search_done) && !empty($search_error)): ?>
    <div class="ui-alert warning mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <?= htmlspecialchars($search_error) ?>
    </div>
    <?php endif; ?>

    <!-- Result badge -->
    <?php if (!empty($search_done) && empty($search_error)): ?>
    <div class="ui-alert info mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        Tìm thấy <strong><?= $total_rows ?? 0 ?></strong> kết quả
        <?php if (!empty($search_value)): ?>
            cho "<strong><?= htmlspecialchars($search_value) ?></strong>"
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Table card -->
    <?php if (empty($search_error)): ?>
    <div class="ui-card mb-0">
        <div class="ui-card-head">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <h5>Danh sách khách hàng</h5>
        </div>
        <div style="overflow-x:auto">
            <table class="ui-table admin-head">
                <thead>
                    <tr>
                        <th>Mã KH</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th class="center">Trạng thái</th>
                        <th class="center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $has_rows = false;
                if (!empty($customers)):
                    foreach ($customers as $row):
                        $has_rows = true;
                        $fullname = htmlspecialchars($row['fullname']);
                        if (!empty($search_done) && ($search_type ?? '') === 'name' && !empty($search_value)) {
                            $kw = preg_quote(htmlspecialchars($search_value), '/');
                            $fullname = preg_replace("/($kw)/iu",
                                '<mark style="background:#fef9c3;padding:0 2px;border-radius:3px">$1</mark>',
                                $fullname);
                        }
                        $statusMap = [
                            'active'   => ['class' => 'confirmed', 'label' => 'Hoạt động'],
                            'warning'  => ['class' => 'warning',   'label' => 'Cảnh báo'],
                            'inactive' => ['class' => 'cancelled', 'label' => 'Bị khóa'],
                        ];
                        $s = $statusMap[$row['status']] ?? ['class' => 'neutral', 'label' => $row['status']];
                ?>
                <tr>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:var(--brand)">
                            #C<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?>
                        </span>
                    </td>
                    <td style="font-weight:600"><?= $fullname ?></td>
                    <td class="muted"><?= htmlspecialchars($row['email']) ?></td>
                    <td class="muted"><?= htmlspecialchars($row['phone']) ?></td>
                    <td class="center">
                        <span class="ui-badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                    </td>
                    <td class="center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?= BASE_URL ?>/index.php?url=admin-customers-edit&id=<?= $row['id'] ?>"
                               class="ui-btn-outline sm">
                                Chỉnh sửa
                            </a>
                            <?php if ($row['status'] === 'active'): ?>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-customers&action=lock&id=<?= $row['id'] ?>"
                               class="ui-btn sm"
                               style="background:linear-gradient(135deg,#718096,#4a5568)"
                               onclick="return confirm('Khóa tài khoản này?')">
                                Khóa
                            </a>
                            <?php else: ?>
                            <a href="<?= BASE_URL ?>/index.php?url=admin-customers&action=unlock&id=<?= $row['id'] ?>"
                               class="ui-btn sm"
                               style="background:linear-gradient(135deg,#4f8ef7,#2563eb)"
                               onclick="return confirm('Mở khóa tài khoản này?')">
                                Mở
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php
                    endforeach;
                endif;
                if (!$has_rows): ?>
                <tr>
                    <td colspan="6">
                        <div class="ui-empty py-4">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <h4>Chưa có khách hàng nào</h4>
                            <p>Thêm tài khoản mới để bắt đầu</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (!empty($total_pages) && $total_pages > 1): ?>
        <div class="ui-card-body pt-0">
            <div class="ui-pagination">
                <?php if ($page > 1): ?>
                <a href="<?= page_url(1) ?>" class="ui-page-btn">«</a>
                <a href="<?= page_url($page - 1) ?>" class="ui-page-btn">‹</a>
                <?php endif; ?>

                <?php
                $range = 2;
                $start = max(1, $page - $range);
                $end   = min($total_pages, $page + $range);
                if ($start > 1) echo '<span class="ui-page-btn" style="pointer-events:none;opacity:.4">…</span>';
                for ($i = $start; $i <= $end; $i++):
                ?>
                <a href="<?= page_url($i) ?>"
                   class="ui-page-btn <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor;
                if ($end < $total_pages) echo '<span class="ui-page-btn" style="pointer-events:none;opacity:.4">…</span>';
                ?>

                <?php if ($page < $total_pages): ?>
                <a href="<?= page_url($page + 1) ?>" class="ui-page-btn">›</a>
                <a href="<?= page_url($total_pages) ?>" class="ui-page-btn">»</a>
                <?php endif; ?>
            </div>
            <p class="text-center ui-subtitle mb-0">
                Trang <strong><?= $page ?></strong> / <strong><?= $total_pages ?></strong>
                &nbsp;|&nbsp; Tổng <strong><?= $total_rows ?></strong> khách hàng
            </p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<script>
const hints = {
    name:  'Nhập tên hoặc một phần họ tên — VD: nguyen',
    id:    'Nhập số ID — VD: 1 hoặc 12',
    email: 'Nhập địa chỉ email — VD: example@gmail.com',
    phone: 'Nhập số điện thoại — VD: 0901234567',
};
const placeholders = {
    name:  'Nhập tên khách hàng...',
    id:    'Nhập mã số ID...',
    email: 'Nhập địa chỉ email...',
    phone: 'Nhập số điện thoại...',
};
function updatePlaceholder(type) {
    document.getElementById('search_value').placeholder = placeholders[type] || 'Nhập từ khóa...';
    document.getElementById('search_hint').textContent = hints[type] || '';
    document.querySelectorAll('.ui-size-btn').forEach(b => b.classList.remove('selected'));
    event.target.closest('.ui-size-btn').classList.add('selected');
}
document.addEventListener('DOMContentLoaded', () => {
    const checked = document.querySelector('input[name="search_type"]:checked');
    const type = checked ? checked.value : 'name';
    document.getElementById('search_value').placeholder = placeholders[type] || '';
    document.getElementById('search_hint').textContent = hints[type] || '';
});
</script>