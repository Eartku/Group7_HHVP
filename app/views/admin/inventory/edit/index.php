<div class="container mt-4 pb-5">

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0" style="color:#3b5d50;font-weight:700">
            Chỉnh sửa phiếu nhập #<?= $receipt['id'] ?>
        </h3>
        <small class="text-muted">Cập nhật thông tin hàng hoá</small>
    </div>
    <a href="<?= BASE_URL ?>/index.php?url=admin-inventory"
       class="btn btn-secondary btn-sm">← Quay lại</a>
</div>

<form method="POST"
      id="importForm"
      action="<?= BASE_URL ?>/index.php?url=admin-inventory-update&id=<?= $receipt['id'] ?>">



<div class="card border-0 shadow-sm mb-4 p-3"
style="background:#eef6f3;border-radius:14px">
<div class="card border-0 shadow-sm mb-4">
       <div class="card-body">

              <div class="row">

                     <div class="col-md-4">
                            <label class="form-label fw-bold">Ngày nhập phiếu</label>

                            <input type="datetime-local"
                                   name="import_date"
                                   class="form-control"
                                   value="<?= date('Y-m-d\TH:i', strtotime($receipt['created_at'])) ?>">

                            <small class="text-muted">
                            Nếu không chỉnh sửa sẽ giữ nguyên ngày tạo ban đầu
                            </small>

                     </div>

              </div>

       </div>
</div>
<!-- NOTE -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <label class="form-label fw-bold">Ghi chú</label>
        <input type="text" name="note" class="form-control"
               value="<?= htmlspecialchars($receipt['note'] ?? '') ?>">
    </div>
</div>

<!-- ITEMS -->
<div class="card border-0 shadow-sm mb-4">
       <div class="card-header text-white" style="background:#3b5d50">
       Danh sách sản phẩm
       </div>

              <div class="card-body p-0">
                     <div id="itemContainer">

              <?php foreach ($items as $item): ?>
              <div class="item-row border-bottom px-3 py-3">
                     <div class="row g-2 align-items-center">

                     <!-- PRODUCT (readonly) -->
                     <div class="col-md-4">
                            <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
                            <input type="text"
                                   class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($item['product_name']) ?>"
                                   readonly>
                     </div>

                     <!-- SIZE -->
                     <div class="col-md-2">
                            <select name="size_id[]" class="form-select form-select-sm size-select">
                            <option value="">-- Size --</option>
                            <?php foreach ($sizes as $s): ?>
                            <option value="<?= $s['size_id'] ?>"
                            <?= $item['size_id'] == $s['size_id'] ? 'selected' : '' ?>>

                            <?= htmlspecialchars($s['size']) ?>

                            <?php if ($s['price_adjust'] > 0): ?>
                            (+<?= number_format($s['price_adjust'],0,',','.') ?>đ)
                            <?php endif; ?>

                            </option>
                            <?php endforeach; ?>
                            </select>
                     </div>

                     <!-- PRICE -->
                     <div class="col-md-2">
                            <div class="input-group input-group-sm">
                            <input type="text"
                                   class="form-control price-input text-end"
                                   value="<?= number_format($item['import_price'],0,',','.') ?>">
                            <input type="hidden"
                                   name="price[]"
                                   class="price-raw"
                                   value="<?= $item['import_price'] ?>">
                            <span class="input-group-text">đ</span>
                            </div>
                     </div>

                     <!-- QTY -->
                     <div class="col-md-1">
                     <input type="number"
                            name="quantity[]"
                            class="form-control form-control-sm qty-input text-center"
                            value="<?= $item['quantity'] ?>">
                     </div>

                     <!-- SUBTOTAL -->
                     <div class="col-md-1 text-center">
                     <span class="subtotal-display text-danger fw-bold">0đ</span>
                     </div>

                     </div>
              </div>
              <?php endforeach; ?>

              </div>
       </div>
</div>

<!-- TOTAL -->
<div class="card border-0 shadow-sm mb-4 text-white"
style="background:linear-gradient(135deg,#3b5d50,#4e7a66)">
       <div class="card-body py-3">
              <div class="row text-center">

                     <div class="col-md-4">
                            <div class="small opacity-75">Tổng dòng</div>
                            <div id="totalProduct" class="fw-bold fs-4">0</div>
                     </div>

                     <div class="col-md-4">
                            <div class="small opacity-75">Tổng số lượng</div>
                            <div id="totalQty" class="fw-bold fs-4">0</div>
                     </div>

                     <div class="col-md-4">
                            <div class="small opacity-75">Tổng giá trị</div>
                            <div id="totalValue" class="fw-bold fs-4">0đ</div>
                     </div>

              </div>
       </div>
</div>
</div>

<button type="submit" class="btn btn-primary">
       Cập nhật phiếu
</button>

</form>
</div>

<script>
const BASE_URL = "<?= BASE_URL ?>";
const stockCache = {};

/* ===================== */
function parsePrice(str){
return parseInt(String(str).replace(/\D/g,'')) || 0;
}

function formatPrice(num){
return num>0 ? num.toLocaleString('vi-VN') : '';
}

/* ===================== */
async function loadSizes(productId, sizeSelect, selected=null){

if(!productId) return;

if(!stockCache[productId]){
const res = await fetch(
`${BASE_URL}/index.php?url=api-sizes&product_id=${productId}`
);
stockCache[productId] = await res.json();
}

const sizes = stockCache[productId];

sizeSelect.innerHTML = '<option value="">-- Size --</option>';

sizes.forEach(s=>{

const opt = document.createElement('option');

opt.value = s.size_id;
opt.dataset.adjust = s.price_adjust ?? 0;

let label = s.size;

if(s.price_adjust>0){
label += ` (+${Number(s.price_adjust).toLocaleString('vi-VN')}đ)`
}

label += ` [Tồn: ${s.stock ?? 0}]`;

opt.textContent = label;

if(selected && selected == s.size_id){
opt.selected = true;
}

sizeSelect.appendChild(opt);

});

}

/* ===================== */
function bindPrice(row){

       const input = row.querySelector('.price-input');

       input.addEventListener('input',function(){
       const num = parsePrice(this.value);
       this.value = formatPrice(num);
       calcTotal();
       });

       input.addEventListener('focus',function(){
       this.value = parsePrice(this.value);
       });

       input.addEventListener('blur',function(){
       this.value = formatPrice(parsePrice(this.value));
       });

}

/* ===================== */
function calcTotal(){

       let total=0;
       let qty=0;
       let rows=0;

       document.querySelectorAll('.item-row').forEach(row=>{

       const price=parsePrice(
       row.querySelector('.price-input').value
       );

       const q=parseInt(
       row.querySelector('.qty-input').value
       )||0;

       const sub=price*q;

       total+=sub;
       qty+=q;
       rows++;

       row.querySelector('.subtotal-display').innerText=
       sub>0?sub.toLocaleString('vi-VN')+'đ':'0đ';

       });

       document.getElementById('totalProduct').innerText=rows;
       document.getElementById('totalQty').innerText=qty;
       document.getElementById('totalValue').innerText=
       total.toLocaleString('vi-VN')+'đ';

}

/* ===================== */
document.querySelectorAll('.item-row').forEach(async row=>{

       bindPrice(row);

       const productId =
       row.querySelector('input[name="product_id[]"]').value;

       const sizeSelect =
       row.querySelector('.size-select');

       const priceInput =
       row.querySelector('.price-input');

       const currentSize = sizeSelect.value;

       await loadSizes(productId,sizeSelect,currentSize);

       row.querySelector('.qty-input')
       .addEventListener('input',calcTotal);

});

/* ===================== */
document.getElementById('importForm')
       .addEventListener('submit',function(){

       document.querySelectorAll('.item-row')
       .forEach(row=>{

       const display=row.querySelector('.price-input');
       const raw=row.querySelector('.price-raw');

       raw.value=parsePrice(display.value);

});

});

calcTotal();
</script>