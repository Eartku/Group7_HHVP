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
                                Chuyển khoản ngân hàng
                            </label>
                        </div>

                        <div class="ui-sum-row">
                            <label>
                                <input type="radio" name="payment" value="momo">
                                Thanh toán MoMo
                            </label>
                        </div>

                        <button type="submit" class="ui-btn full">
                            ĐẶT HÀNG
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>