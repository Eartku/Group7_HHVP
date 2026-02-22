<?php
require_once "../config/db.php";

/* =============================
   LẤY 15 SẢN PHẨM RANDOM
   TÍNH GIÁ TỪ INVENTORY_LOGS
============================= */

$sql = "
SELECT 
    p.id,
    p.name,
    p.image,
    COALESCE(ROUND(AVG(il.import_price) * 1.1), 0) AS sale_price
FROM products p
LEFT JOIN inventory_logs il 
    ON il.product_id = p.id
GROUP BY p.id
ORDER BY RAND()
LIMIT 15
";

$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Showcase</title>

<style>
.showcase-wrapper {
    position: relative;
    width: 100%;
    height: 600px;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    perspective: 1500px;
}
.showcase {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
}
.card {
    position: absolute;
    width: 400px;
    height: 500px;
    background: #ffffffee;
    border-radius: 25px;
    padding: 20px;
    transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
    backface-visibility: hidden;
    left: 50%;
    top: 50%;
    display: flex;
    flex-direction: column;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}
.img-container {
    width: 100%;
    height: 320px;
    overflow: hidden;
    border-radius: 18px;
    margin-bottom: 15px;
    background: #f0f0f0;
}
.product-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.product-title {
    font-size: 2rem;
    color: #1b4d3e;
    font-weight: 700;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.product-price {
    color: #10b981;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 15px;
}
.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: #ffffff;
    color: #1b4d3e;
    border: none;
    font-size: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.nav-btn:hover {
    background: #1b4d3e;
    color: #ffffff;
}
#prev { left: 40px; }
#next { right: 40px; }

@media (max-width: 768px) {
    .card { width: 240px; height: 380px; }
    #prev { left: 10px; }
    #next { right: 10px; }
}
</style>
</head>

<body>

<div class="showcase-wrapper">

<?php if (count($products) > 1): ?>
    <button id="prev" class="nav-btn">❮</button>
<?php endif; ?>

<div class="showcase" id="showcase">

<?php if (!empty($products)): ?>

<?php foreach ($products as $p):

    $id    = (int)$p['id'];
    $name  = htmlspecialchars($p['name']);
    $price = number_format((float)$p['sale_price'], 0, ',', '.');

    $imageList = !empty($p['image']) ? explode(",", $p['image']) : [];
    $firstImage = trim($imageList[0] ?? "");

    $imagePath = $firstImage
        ? "../images/" . htmlspecialchars($firstImage)
        : "https://via.placeholder.com/300x300?text=No+Image";
?>

<div class="card">
    <div class="img-container">
        <img src="<?= $imagePath ?>"
             class="product-thumbnail"
             alt="<?= $name ?>"
             loading="lazy"
             onerror="this.onerror=null;this.src='https://via.placeholder.com/300x300?text=No+Image';">
    </div>

    <h3 class="product-title"><?= $name ?></h3>

    <div class="product-price"><?= $price ?>đ</div>

    <a href="../includes/details.php?id=<?= $id ?>"
       class="btn btn-success shadow-sm">
        Chi tiết
    </a>
</div>

<?php endforeach; ?>

<?php else: ?>

<div style="text-align:center;color:#777;">
    Không có sản phẩm để hiển thị
</div>

<?php endif; ?>

</div>

<?php if (count($products) > 1): ?>
    <button id="next" class="nav-btn">❯</button>
<?php endif; ?>

</div>

<script>
const cards = document.querySelectorAll(".card");

if (cards.length > 0) {

    let current = Math.floor(cards.length / 2);

    function updatePositions() {
        cards.forEach((card, index) => {

            let offset = index - current;
            const absOffset = Math.abs(offset);

            const x = offset * 230;
            const z = -absOffset * 150;
            const rotateY = offset * -20;
            const scale = 1 - absOffset * 0.15;
            const opacity = absOffset > 2 ? 0 : (1 - absOffset * 0.3);
            const zIndex = 100 - absOffset;

            card.style.transform = `
                translate(calc(-50% + ${x}px), -50%)
                translateZ(${z}px)
                rotateY(${rotateY}deg)
                scale(${scale})
            `;

            card.style.zIndex = zIndex;
            card.style.opacity = opacity;
            card.style.pointerEvents = offset === 0 ? "auto" : "none";
            card.style.filter = absOffset > 0 ? `blur(${absOffset}px)` : "none";
        });
    }

    document.getElementById("prev")?.addEventListener("click", () => {
        current = (current > 0) ? current - 1 : cards.length - 1;
        updatePositions();
    });

    document.getElementById("next")?.addEventListener("click", () => {
        current = (current < cards.length - 1) ? current + 1 : 0;
        updatePositions();
    });

    cards.forEach((card, index) => {
        card.addEventListener("click", () => {
            current = index;
            updatePositions();
        });
    });

    updatePositions();
}
</script>

</body>
</html>