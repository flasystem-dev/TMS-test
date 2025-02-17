<?php
namespace App\Services\Specification;

use App\Services\Vendor\AdjustmentService;
use App\QueryBuilders\Specification\SpecificationQueryBuilder;

class SpecificationService
{
    public static function getSpecificationWithVendor($id)
    {
        $specification = SpecificationQueryBuilder::createQuery();

        $specification = SpecificationQueryBuilder::joinVendor($specification);

        $specification = SpecificationQueryBuilder::findId($specification, $id);

        return $specification->first();
    }

    public static function get_specificationsForSend($search)
    {
        $year = $search['year'] ?? date('Y');
        $month = $search['month'] ?? date('m');
        $brand = $search['brand'] ?? "BTCS";
        $column = $search['search'] ?? "";
        $word = $search['word'] ?? "";

        return SpecificationQueryBuilder::specificationsForSend($year, $month, $brand, $column, $word);
    }

    public static function makeSpecification($search)
    {
        $year = $search['year'] ?? date('Y');
        $month = $search['month'] ?? date('m');
        $deposit_date = $search['deposit_date'];

        $vendors = AdjustmentService::get_monthVendorAdjustment($search);

        if ($vendors && $vendors->isNotEmpty()) {
            foreach ($vendors as $vendor) {
                SpecificationQueryBuilder::upsert_specification($vendor, $year, $month, $deposit_date);
            }
        }
    }
}