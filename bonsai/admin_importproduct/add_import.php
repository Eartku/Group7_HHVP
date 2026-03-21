<?php
require_once "../config/db.php";

/* ======================
   XỬ LÝ SUBMIT
====================== */
if($_SERVER['REQUEST_METHOD']=='POST'){

    $products = $_POST['product_id'];
    $sizes = $_POST['size'];
    $prices = $_POST['price'];
    $quantities = $_POST['quantity'];

    for($i=0;$i<count($products);$i++){

        $product = $products[$i];
        $size = $sizes[$i];
        $price = $prices[$i];
        $qty = $quantities[$i];

        $total = $price * $qty;

        $conn->query("
        INSERT INTO import_receipts
        (import_date,product_id,size,import_price,quantity,total_value,status)
        VALUES
        (NOW(),'$product','$size','$price','$qty','$total','pending')
        ");
    }

    header("Location:adminipd.php");
    exit;
}

include "../admin_includes/loader.php";
include "../admin_includes/header.php";
?>

<div class="hero">
    <div class="center-row text-center">
        <h1 class="glow">Phiếu Nhập sản phẩm</h1>
    </div>
</div>

<div class="container">

    <div class="text-end mb-3">
        <button type="button" onclick="addReceipt()" class="btn btn-success">
        + Thêm phiếu nhập
        </button>
    </div>

        <form method="POST">
            <div id="receiptContainer">
                <div class="card p-3 mb-3 receipt-box">

                    <h5>Phiếu nhập</h5>

                    <label>Sản phẩm</label>
                        <select name="product_id[]" class="form-control">
                            <?php
                                $res=$conn->query("SELECT id,name FROM products");
                                while($p=$res->fetch_assoc()){
                                    echo "<option value='".$p['id']."'>".$p['name']."</option>";
                                }
                            ?>
                        </select>

                    <label>Size</label>
                    <div class="mb-2">
                        <label class="me-3">
                            <input type="radio" name="size[]" value="S" required> S
                        </label>

                        <label class="me-3">
                            <input type="radio" name="size[]" value="M"> M
                        </label>

                        <label class="me-3">
                            <input type="radio" name="size[]" value="L"> L
                        </label>
                    </div>

                        <label>Giá nhập</label>
                            <input name="price[]" type="number" class="form-control price" oninput="calcTotal()">

                        <label>Số lượng</label>
                            <input name="quantity[]" type="number" class="form-control qty" oninput="calcTotal()">

                    <br>

                    <button type="button" onclick="removeReceipt(this)" class="btn btn-danger btn-sm">
                        Xóa phiếu
                    </button>
                </div>
            </div>

            <hr>

            <h5>Tổng kết</h5>

            <div class="row">
                <div class="col-md-4">
                    <label>Tổng sản phẩm</label>
                        <input id="totalProduct" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Tổng số lượng</label>
                        <input id="totalQty" class="form-control" readonly>
                </div>

                <div class="col-md-4">
                    <label>Tổng giá trị</label>
                        <input id="totalValue" class="form-control" readonly>
                </div>
            </div>

            <br>

            <button class="btn btn-primary">
                Xác nhận nhập
            </button>

            <a href="adminipd.php" class="btn btn-secondary">
                Quay lại
            </a>
        </form>
</div>

<script>
function addReceipt(){
    let container=document.getElementById("receiptContainer");
    let box=document.querySelector(".receipt-box").cloneNode(true);

    // reset input
    box.querySelectorAll("input").forEach(input => {
        if(input.type === "radio"){
            input.checked = false;
        } else {
            input.value = "";
        }
    });

    container.appendChild(box);
    calcTotal();
}

function removeReceipt(btn){
    btn.closest(".receipt-box").remove();
    calcTotal();
}

function calcTotal(){
    let prices=document.querySelectorAll(".price");
    let qtys=document.querySelectorAll(".qty");

    let totalProduct=prices.length;
    let totalQty=0;
    let totalValue=0;

    for(let i=0;i<prices.length;i++){
        let price=parseFloat(prices[i].value)||0;
        let qty=parseFloat(qtys[i].value)||0;

        totalQty+=qty;
        totalValue+=price*qty;
    }

    document.getElementById("totalProduct").value=totalProduct;
    document.getElementById("totalQty").value=totalQty;
    document.getElementById("totalValue").value=totalValue;
}
</script>

<?php include '../admin_includes/footer.php'; ?>