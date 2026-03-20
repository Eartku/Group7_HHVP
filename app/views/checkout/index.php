<div class="container py-5">

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/index.php?url=checkout">
        <div class="row">

            <!-- THÔNG TIN KHÁCH HÀNG -->
            <div class="col-md-7">
                <div class="card p-4 shadow-sm mb-4">
                    <h4>Thông tin khách hàng</h4>

                    <div class="mb-3">
                        <label>Họ và Tên *</label>
                        <input type="text" name="fullname" class="form-control"
                               value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Số điện thoại *</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>

                    <hr>
                    <h5>Địa chỉ giao hàng</h5>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="address_option"
                               id="savedAddr" value="saved" checked>
                        <label class="form-check-label" for="savedAddr">Dùng địa chỉ đã lưu</label>
                    </div>
                    <select name="saved_address" class="form-select mb-3">
                        <option value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                            <?= htmlspecialchars($user['address'] ?? 'Chưa có địa chỉ') ?>
                        </option>
                    </select>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="address_option"
                               id="newAddr" value="new">
                        <label class="form-check-label" for="newAddr">Nhập địa chỉ mới</label>
                    </div>
                    <input type="text" name="new_address" class="form-control mb-3"
                           placeholder="Nhập địa chỉ mới">

                    <div class="mb-3">
                        <label>Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <!-- ĐƠN HÀNG + THANH TOÁN -->
            <div class="col-md-5">
                <div class="card p-4 shadow-sm">
                    <h4>Đơn hàng của bạn</h4>

                    <table class="table">
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($item['name']) ?>
                                    <small class="text-muted">(<?= htmlspecialchars($item['size']) ?>)</small>
                                    × <?= $item['quantity'] ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td>Phí vận chuyển</td>
                                <td class="text-end"><?= number_format($shippingFee, 0, ',', '.') ?>đ</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Tổng cộng</td>
                                <td class="text-end text-danger">
                                    <?= number_format($grandTotal, 0, ',', '.') ?>đ
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>
                    <h5>Phương thức thanh toán</h5>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment"
                               value="cod" id="cod" checked>
                        <label class="form-check-label" for="cod">Thanh toán khi nhận hàng</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment"
                               value="bank" id="bank">
                        <label class="form-check-label" for="bank">Chuyển khoản ngân hàng</label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="radio" name="payment"
                               value="momo" id="momo">
                        <label class="form-check-label" for="momo">Thanh toán MoMo</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2">
                        ĐẶT HÀNG
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>