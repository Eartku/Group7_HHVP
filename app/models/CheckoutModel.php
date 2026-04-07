<?php
class CheckoutModel {

    public static function placeOrder(int $userId, int $cartId, array $info, array $items, float $shippingFee): int {
        $db = Database::getInstance();
        $db->begin_transaction();

        try {
            // 1. Kiểm tra + lock kho
            foreach ($items as $item) {
                $stmt = $db->prepare("
                    SELECT quantity FROM inventory
                    WHERE product_id = ? AND size_id = ?
                    FOR UPDATE
                ");
                $stmt->bind_param("ii", $item['product_id'], $item['size_id']);
                $stmt->execute();
                $stock = $stmt->get_result()->fetch_assoc();

                if (!$stock) {
                    throw new Exception("Sản phẩm '{$item['name']}' không tồn tại trong kho.");
                }
                if ($stock['quantity'] < $item['quantity']) {
                    throw new Exception("Sản phẩm '{$item['name']}' không đủ số lượng trong kho.");
                }
            }

            // 2. Tạo order
            $grandTotal = array_sum(array_column($items, 'subtotal')) + $shippingFee;

            $stmt = $db->prepare("
                INSERT INTO orders
                    (user_id, fullname, email, phone, address, note,
                     payment_method, shipping_fee, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'processing')
            ");
            $stmt->bind_param(
                "issssssdd",
                $userId,
                $info['fullname'],
                $info['email'],
                $info['phone'],
                $info['address'],
                $info['note'],
                $info['payment'],
                $shippingFee,
                $grandTotal
            );
            $stmt->execute();
            $orderId = $db->insert_id;

            // 3. Insert items + trừ kho + log xuất
            foreach ($items as $item) {
                // Insert order_item
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, size_id, quantity, price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iiiid",
                    $orderId, $item['product_id'], $item['size_id'],
                    $item['quantity'], $item['price']
                );
                $stmt->execute();

                // Lấy avg_import_price
                $stmt2 = $db->prepare("
                    SELECT avg_import_price FROM inventory
                    WHERE product_id = ? AND size_id = ? FOR UPDATE
                ");
                $stmt2->bind_param("ii", $item['product_id'], $item['size_id']);
                $stmt2->execute();
                $inv      = $stmt2->get_result()->fetch_assoc();
                $avgPrice = (float)($inv['avg_import_price'] ?? 0);

                // Trừ kho
                $stmt3 = $db->prepare("
                    UPDATE inventory SET quantity = quantity - ?
                    WHERE product_id = ? AND size_id = ?
                ");
                $stmt3->bind_param("iii", $item['quantity'], $item['product_id'], $item['size_id']);
                $stmt3->execute();

                // Ghi log xuất kho
                $noteLog = "Xuất kho cho đơn hàng #$orderId";
                $stmt4   = $db->prepare("
                    INSERT INTO inventory_logs
                        (order_id, product_id, size_id, type, quantity, import_price, note)
                    VALUES (?, ?, ?, 'export', ?, ?, ?)
                ");
                
                $stmt4->bind_param("iiiids",   // ✅ i=order_id, i=product_id, i=size_id, i=quantity, d=avgPrice, s=note
                    $orderId,
                    $item['product_id'],
                    $item['size_id'],
                    $item['quantity'],
                    $avgPrice,
                    $noteLog
                );
                $stmt4->execute();
                if ($stmt4->error) {  // ← sau execute, không phải trước
                    throw new Exception("Log lỗi: " . $stmt4->error);
                }
            }

            // 4. Xóa cart dùng $cartId truyền vào — không dùng $items[0]['cart_id']
            $stmt = $db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
            $stmt->bind_param("i", $cartId);
            $stmt->execute();

            $db->commit();
            return $orderId;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}