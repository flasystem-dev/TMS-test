<?php
namespace App\Services\Statistics;

use App\Models\Specification;

class SpecificationService
{
    public static function vendor_specificationForYear($idx, $year)
    {
        return Specification::query()
            ->join('vendor', 'vendor.idx', '=', 'specification.mall_code')
            ->where('specification.mall_code', '=', $idx)
            ->where('specification.year', '=', $year)
            ->orderBy('specification.month', 'desc')
            ->get();
    }
}