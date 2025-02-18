<?php
namespace App\Services\Vendor;

use Carbon\Carbon;

use App\QueryBuilders\Statistics\CalculateSalesQueryBuilder;
use App\Models\TotalInformations;

class SchedulerService {

    // 매출 정보 계산하기 ( 최근 2개월 )
    public static function calc_sales() {
        try {
            for ($i = 0; $i < 3; $i++) {
                $targetDate = Carbon::now()->subMonth($i);
                $year = $targetDate->year;
                $month = $targetDate->month;

                // 배송날 기준 매출 계산 & Upsert
                $list = CalculateSalesQueryBuilder::calculateSalesQuery($year, $month, 'delivery');
                CalculateSalesQueryBuilder::upsert_totalInformation($year, $month, 'delivery', $list);

                // 주문일 기준 매출 계산 & Upsert
                $list2 = CalculateSalesQueryBuilder::calculateSalesQuery($year, $month, 'order');
                CalculateSalesQueryBuilder::upsert_totalInformation($year, $month, 'order', $list2);
            }

        }catch (\Exception $e){
            \Log::error("[매출 계산 스케쥴러 실패]");
            \Log::error($e);
        }
    }


    // 카드 금액 갱신
    public static function calc_cardAmount() {
        $year = date('Y');
        $month = date('m');
        $brand = ['BTCS', 'BTFC'];
        try {
            $card_list = CalculateSalesQueryBuilder::calculate_cardAmount_brand($year, $month, $brand);
            foreach ($card_list as $data) {
                CalculateSalesQueryBuilder::upsert_cardAmount($year, $month, $data);
            }

        }catch (\Exception $e){
            \Log::error("카드 결제 계산 실패 - 스케줄러");
            \Log::error($year."-".$month);
            \Log::error($e);
        }

    }
}