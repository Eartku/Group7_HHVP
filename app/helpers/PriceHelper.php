<?php
class PriceHelper {

    public static function calcSalePrice(
        float $avgImportPrice,
        float $profitRate,
        float $priceAdjust = 0
    ): float {
        if ($avgImportPrice <= 0) return 0;
        return round($avgImportPrice * (1 + $profitRate / 100) + $priceAdjust, -3);
    }

    public static function format(float $price): string {
        return $price > 0
            ? number_format($price, 0, ',', '.') . 'đ'
            : 'Liên hệ';
    }

    public static function sqlAvgImport(string $alias = 'avg_import_price'): string {
        return "
            IFNULL((
                SELECT SUM(import_price * quantity) / NULLIF(SUM(quantity), 0)
                FROM inventory_logs
                WHERE product_id = p.id
                  AND type = 'import'
            ), 0) AS $alias
        ";
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

    // ✅ Bỏ subquery size WHERE product_id — size không còn product_id
    // Dùng MIN(price_adjust) từ toàn bảng size làm giá khởi điểm
    public static function sqlSalePrice(string $alias = 'sale_price'): string {
        return "
            ROUND(
                IFNULL((
                    SELECT SUM(import_price * quantity) / NULLIF(SUM(quantity), 0)
                    FROM inventory_logs
                    WHERE product_id = p.id
                      AND type = 'import'
                ), 0)
                * (1 + p.profit_rate / 100)
                + IFNULL((
                    SELECT MIN(price_adjust) FROM size
                ), 0)
            , -3) AS $alias
        ";
    }
}