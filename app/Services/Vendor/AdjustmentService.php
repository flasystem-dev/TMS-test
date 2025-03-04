<?php
namespace App\Services\Vendor;

use App\QueryBuilders\Vendor\AdjustmentQueryBuilder;
use App\QueryBuilders\Staticsics\CalculateSalesQueryBuilder;

class AdjustmentService
{
    public static function get_monthVendorAdjustment($search)
    {
        $year = $search['year'] ?? date('Y');
        $month = $search['month'] ?? date('m');
        $brand = isset($search['brand']) ? substr($search['brand'], 0, 4) : "BTCS";
        $idxes = $search['mall_code'] ?? "";
        $word = $search['word1'] ?? "";
        $column = $search['sw_1'] ?? "";

        $vendorSubQuery = AdjustmentQueryBuilder::vendorSubQuery($brand, $year, $month, $idxes, $column, $word);

        $orderSubQuery = AdjustmentQueryBuilder::orderSubQuery($brand, $year, $month);

        $itemSubQuery = AdjustmentQueryBuilder::itemSubQuery();

        return AdjustmentQueryBuilder::adjustmentQuery($vendorSubQuery, $orderSubQuery, $itemSubQuery, $year, $month);
    }

    public static function calculateUpsertCardAmount_brand($search)
    {
        $year = $search['year'];
        $month = $search['month'];
        $brand = [substr($search['brand'],0,4)];

        $card_list = CalculateSalesQueryBuilder::calculate_cardAmount_brand($year, $month, $brand);
        foreach ($card_list as $data) {
            CalculateSalesQueryBuilder::upsert_cardAmount($year, $month, $data);
        }
    }

    public static function calculateUpsertCardAmount_vendor($search)
    {
        $year = $search['year'];
        $month = $search['month'];
        $idx = $search['idx'];

        $data = CalculateSalesQueryBuilder::calculate_cardAmount_vendor($year, $month, $idx);
        CalculateSalesQueryBuilder::upsert_cardAmount($year, $month, $data);
    }
}