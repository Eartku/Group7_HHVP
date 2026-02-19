function showToast(message) {
    const toast = document.getElementById("toast");
    toast.innerText = message;
    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2500);
}

function addToCart(id){

    let qtyInput = document.getElementById("qtyInput");
    let qty = qtyInput ? qtyInput.value : 1;

    fetch('../includes/add_to_cart.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+id+'&qty='+qty
    })
    .then(res=>res.text())
    .then(data=>{
        showToast("✔ Đã thêm vào giỏ hàng");
    })
    .catch(()=>{
        showToast("❌ Có lỗi xảy ra");
    });
}
