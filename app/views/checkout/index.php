<div class="container py-5">

    <?php if ($error): ?>
        <div class="ui-alert danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?url=checkout">
        <div class="checkout-layout">

            <!-- THÔNG TIN KHÁCH HÀNG -->
            <div>
                <div class="ui-card">
                    <div class="ui-card-head">
                        <h5>Thông tin khách hàng</h5>
                    </div>
                    <div class="ui-card-body">

                        <div class="ui-field">
                            <label class="ui-label">Họ và Tên <span class="req">*</span></label>
                            <input type="text" name="fullname" class="ui-input"
                                   value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label">Email <span class="req">*</span></label>
                            <input type="email" name="email" class="ui-input"
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>

                        <div class="ui-field">
                            <label class="ui-label">Số điện thoại <span class="req">*</span></label>
                            <input type="text" name="phone" class="ui-input"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                        </div>

                        <hr>

                        <h5 class="ui-title sm">Địa chỉ giao hàng</h5>

                        <div class="ui-label">
                            <label>
                                <input type="radio" name="address_option" value="saved" checked>
                                Dùng địa chỉ đã lưu
                            </label>
                            <select name="saved_address" class="ui-input">
                                <option value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                                    <?= htmlspecialchars($user['address'] ?? 'Chưa có địa chỉ') ?>
                                </option>
                            </select>
                        </div>

                        <div class="ui-label">
                            <label>
                                <input type="radio" name="address_option" value="new">
                                Nhập địa chỉ mới
                            </label>
                            <input type="text" name="new_address" class="ui-input"
                                   placeholder="Nhập địa chỉ mới">
                        </div>

                        <div class="ui-lable">
                            <label class="ui-label">Ghi chú</label>
                            <textarea name="note" class="ui-input textarea"></textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ĐƠN HÀNG + THANH TOÁN -->
            <div>
                <div class="ui-card">
                    <div class="ui-card-head">
                        <h5>Đơn hàng của bạn</h5>
                    </div>
                    <div class="ui-card-body">

                        <table class="ui-table">
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                        <span class="ui-table td muted">
                                            (<?= htmlspecialchars($item['size']) ?>)
                                        </span>
                                        × <?= $item['quantity'] ?>
                                    </td>
                                    <td class="right price">
                                        <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="ui-sum-row">
                            <span>Phí vận chuyển</span>
                            <span><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="ui-sum-row">
                            <span>Tổng cộng</span>
                            <span><?= number_format($grandTotal, 0, ',', '.') ?>đ</span>
                        </div>

                        <hr>

                       <h5 class="ui-title sm">Phương thức thanh toán</h5>

                    <div class="ui-sum-row">
                        <label>
                            <input type="radio" name="payment" value="cod" checked>
                            Thanh toán khi nhận hàng
                        </label>
                    </div>

                    <div class="ui-sum-row">
                        <label>
                            <input type="radio" name="payment" value="bank">
                            Chuyển khoản
                        </label>
                    </div>

                    <!-- Thông tin chuyển khoản -->
                    <div id="bank-info" style="display:none; background:#f8f9fa; border:1px solid #e0e0e0;
                        border-radius:8px; padding:14px 16px; margin:10px 0 14px; font-size:14px; line-height:2;">
                        <div>🏦 <strong>Ngân hàng:</strong> Vietcombank</div>
                        <div>💳 <strong>STK:</strong> 123456789</div>
                        <div>👤 <strong>Chủ TK:</strong> Phúc đẹp trai</div>
                        <div>💰 <strong>Số tiền:</strong>
                            <span style="color:#e53935; font-weight:600;">
                                <?= number_format($grandTotal, 0, ',', '.') ?>đ
                            </span>
                        </div>
                        <div>📝 <strong>Nội dung:</strong>
                            <span id="bank-content" style="color:#e53935; font-weight:600;">
                                <?php
                                $parts = [];
                                foreach ($items as $item) {
                                    $parts[] = $item['quantity'] . ' ' . $item['name'] . ' ' . $item['size'];
                                }
                                echo htmlspecialchars(implode(', ', $parts));
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="ui-sum-row">
                        <label>
                            <input type="radio" name="payment" value="momo">
                            Trực Tuyến
                        </label>
                    </div>

                        <button type="submit" class="ui-btn full">
                            ĐẶT HÀNG
                        </button>

                    </div>
                </div>
            </div>
                <script>
                document.querySelectorAll('input[name="payment"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        document.getElementById('bank-info').style.display =
                            this.value === 'bank' ? 'block' : 'none';
                    });
                });
                </script>
        </div>
    </form>
</div>