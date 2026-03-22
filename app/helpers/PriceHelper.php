<?php
class PriceHelper {

    public static function sqlAvgImport(string $alias = 'avg_import_price'): string {
        // WAC = tổng (giá × số lượng) / tổng số lượng, gộp tất cả size
        return "
            IFNULL((
                SELECT SUM(i.avg_import_price * i.quantity)
                    / NULLIF(SUM(i.quantity), 0)
                FROM inventory i
                WHERE i.product_id = p.id
            ), 0) AS $alias
        ";
    }

    public static function sqlSalePrice(string $alias = 'sale_price'): string {
        $expr = "
            CASE
                WHEN IFNULL((
                    SELECT SUM(i.quantity)
                    FROM inventory i
                    WHERE i.product_id = p.id
                ), 0) = 0
                THEN 0   -- chưa có hàng trong kho → trả về 0 → hiển thị 'Liên hệ'
                ELSE
                    ROUND(
                        (
                            SELECT SUM(i.avg_import_price * i.quantity)
                                / NULLIF(SUM(i.quantity), 0)
                            FROM inventory i
                            WHERE i.product_id = p.id
                        )
                        * (1 + p.profit_rate / 100)
                        + IFNULL((
                            SELECT MIN(s.price_adjust)
                            FROM size s
                        ), 0)
                    , -3)
            END
        ";
        return $alias !== '' ? "$expr AS $alias" : $expr;
    }

    public static function calcSalePrice(
        float $wac,           // avg_import_price từ DB (đã là WAC)
        float $profitRate,
        float $priceAdjust = 0  // price_adjust của size được chọn
    ): float {
        if ($wac <= 0) return 0;
        return round($wac * (1 + $profitRate / 100) + $priceAdjust, -3);
    }

    public static function sqlTotalStock(string $alias = 'total_stock'): string {
        return "
            IFNULL((
                SELECT SUM(quantity)
                FROM inventory
                WHERE product_id = p.id
            ), 0) AS $alias
        ";
    }
}