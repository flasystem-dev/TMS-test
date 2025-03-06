<?php
namespace App\Services\Specification;

use App\Services\Vendor\AdjustmentService;
use App\QueryBuilders\Specification\SpecificationQueryBuilder;
use Carbon\Carbon;

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

    public static function checkDateforEdit($year, $month)
    {
        // 오늘 기준 전달 (전월) 계산
        $lastMonth = Carbon::now()->subMonth()->startOfMonth(); // 전달의 1일

        // 입력된 연도, 월을 Carbon으로 변환
        $inputDate = Carbon::createFromDate($year, $month, 1); // 입력값의 1일

        // 비교: 입력된 날짜가 전달 이전인지 확인
        return !$inputDate->lt($lastMonth); // lt() → 미만 (less than)
    }
}