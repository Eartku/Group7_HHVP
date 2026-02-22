<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id   = (int)$_POST['product_id'];
    $size         = trim($_POST['size']);
    $import_price = (float)$_POST['import_price'];
    $quantity     = (int)$_POST['quantity'];
    $note         = trim($_POST['note']);

    if ($product_id <= 0 || $quantity <= 0 || $import_price <= 0 || empty($size)) {
        die("Dữ liệu không hợp lệ");
    }

    $conn->begin_transaction();

    try {

        /* ===============================
           1️⃣ Lấy tồn kho hiện tại (LOCK)
        =============================== */
        $stmt = $conn->prepare("
            SELECT quantity, avg_import_price
            FROM inventory
            WHERE product_id = ? AND size = ?
            FOR UPDATE
        ");
        $stmt->bind_param("is", $product_id, $size);
        $stmt->execute();
        $result = $stmt->get_result();
        $inventory = $result->fetch_assoc();

        if ($inventory) {

            $oldQty = (int)$inventory['quantity'];
            $oldAvg = (float)$inventory['avg_import_price'];

            $newQty = $oldQty + $quantity;

            /* ===============================
               2️⃣ TÍNH MOVING AVERAGE
            =============================== */
            $newAvg = (
                ($oldQty * $oldAvg) + ($quantity * $import_price)
            ) / $newQty;

            /* ===============================
               3️⃣ UPDATE INVENTORY
            =============================== */
            $updateStmt = $conn->prepare("
                UPDATE inventory
                SET quantity = ?, avg_import_price = ?
                WHERE product_id = ? AND size = ?
            ");
            $updateStmt->bind_param("idis", $newQty, $newAvg, $product_id, $size);
            $updateStmt->execute();

        } else {

            /* ===============================
               4️⃣ INSERT MỚI NẾU CHƯA CÓ
            =============================== */
            $insertStmt = $conn->prepare("
                INSERT INTO inventory
                (product_id, size, quantity, avg_import_price, price_adjust)
                VALUES (?, ?, ?, ?, 0)
            ");
            $insertStmt->bind_param("isid", $product_id, $size, $quantity, $import_price);
            $insertStmt->execute();
        }

        /* ===============================
           5️⃣ GHI LOG SAU KHI UPDATE OK
        =============================== */
        $logStmt = $conn->prepare("
            INSERT INTO inventory_logs
            (product_id, size, type, quantity, import_price, note, created_at)
            VALUES (?, ?, 'import', ?, ?, ?, NOW())
        ");
        $logStmt->bind_param("isids", $product_id, $size, $quantity, $import_price, $note);
        $logStmt->execute();

        $conn->commit();

        echo "<p style='color:green;'>✅ Nhập kho thành công!</p>";

    } catch (Exception $e) {

        $conn->rollback();
        echo "<p style='color:red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
    }
}
?>
<h3>Demo Import</h3>
<form method="POST">
    Product ID:
    <input type="number" name="product_id" required><br><br>

    Size:
    <input type="text" name="size" required><br><br>

    Import Price:
    <input type="number" step="0.01" name="import_price" required><br><br>

    Quantity:
    <input type="number" name="quantity" required><br><br>

    Note:
    <input type="text" name="note"><br><br>

    <button type="submit">Nhập kho</button>
</form>