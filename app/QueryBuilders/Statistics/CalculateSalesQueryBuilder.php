<?php
namespace App\QueryBuilders\Statistics;

use Illuminate\Support\Facades\DB;

use App\Models\TotalInformations;

class CalculateSalesQueryBuilder
{
    
    // 합계 계산하기
    public static function calculateSalesQuery($year, $month, $dateType)
    {
        $query = DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> join('order_items as i', 'o.order_idx', '=', 'i.order_id')
            -> select(
                'o.brand_type_code as brand',
                'o.mall_code as mall_code',
                DB::raw('COUNT(o.order_idx) as order_count'),
                DB::raw('SUM(COALESCE(o.total_amount, 0))           as order_amount'),
                DB::raw('SUM(COALESCE(o.pay_amount, 0 ))            as pay_amount'),
                DB::raw('SUM(COALESCE(o.misu_amount, 0 ))           as misu_amount'),
                DB::raw('SUM(COALESCE(o.vendor_amount, 0 ))         as vendor_amount'),
                DB::raw('SUM(COALESCE(i.vendor_options_amount, 0 ))  as option_amount'),
            )
            ->where('o.is_view', 1)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC')
            ->groupBy('o.brand_type_code', 'o.mall_code');

        if($dateType==="order") {
            $query = $query
                ->whereYear('o.order_time', $year)
                ->whereMonth('o.order_time', $month);
        }elseif ($dateType==="delivery") {
            $query = $query
                ->whereYear('d.delivery_date', $year)
                ->whereMonth('d.delivery_date', $month);
        }
        return $query -> get();
    }

    // 매출 데이터 upsert 하기
    public static function upsert_totalInformation($year, $month, $dateType, $list) {
        foreach ($list as $data) {

            TotalInformations::upsert(
                [   'brand' => $data->brand,
                    'mall_code' => $data->mall_code,
                    'date_type' => $dateType,
                    'year' => $year,
                    'month' => $month,
                    'order_count'   => (int)$data->order_count,
                    'order_amount'  => (int)$data->order_amount,
                    'pay_amount'    => (int)$data->pay_amount,
                    'misu_amount'   => (int)$data->misu_amount,
                    'vendor_amount' => (int)$data->vendor_amount,
                    'option_amount' => (int)$data->option_amount
                ],
                [ 'brand', 'mall_code', 'date_type', 'year', 'month' ],
                [ 'order_count', 'order_amount', 'pay_amount', 'misu_amount', 'vendor_amount', 'option_amount' ]
            );
        }
    }

    public static function calculate_cardAmount($year, $month)
    {
        return DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> leftJoin('order_payment as p', 'o.order_idx', '=', 'p.order_idx')
            -> select(
                'o.brand_type_code as brand','o.mall_code',
                // 결제 타입 카드, 수기결제
                DB::raw('SUM(CASE WHEN p.payment_type_code IN ("PTCD", "PTMN") AND p.payment_state_code != "PSCC" THEN p.payment_amount ELSE 0 END) as card_amount')
            )
            ->where('o.is_view', 1)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC')
            ->whereYear('d.delivery_date', $year)
            ->whereMonth('d.delivery_date', $month)
            ->groupBy('o.mall_code');
    }


    public static function calculate_cardAmount_brand($year, $month, $brand)
    {
        return self::calculate_cardAmount($year, $month)
            ->whereIn('o.brand_type_code', $brand)
            ->get();
    }

    public static function calculate_cardAmount_vendor($year, $month, $idx)
    {
        return self::calculate_cardAmount($year, $month)
            ->where('o.mall_code', $idx)
            ->first();
    }

    public static function upsert_cardAmount($year, $month, $data)
    {
        TotalInformations::updateOrCreate(
            ['brand' => $data->brand,
                'mall_code' => $data->mall_code,
                'year' => $year,
                'month' => $month,
                'date_type' => 'delivery'],
            ['card_amount' => $data->card_amount, 'updated_at'=> NOW()]
        );
    }


}

