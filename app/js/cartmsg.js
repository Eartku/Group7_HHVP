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

    let sizeSelect = document.getElementById("sizeSelect");
    let size = sizeSelect ? sizeSelect.value : 'S'; // mặc định S

    fetch('../includes/add_to_cart.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+id+'&qty='+qty+'&size='+size
    })
    .then(res=>res.text())
    .then(data=>{
        if(data === "success"){
            showToast("✔ Đã thêm vào giỏ hàng");
        } else {
            showToast("❌ " + data);
        }
    })
    .catch(()=>{
        showToast("❌ Có lỗi xảy ra");
    });
}


