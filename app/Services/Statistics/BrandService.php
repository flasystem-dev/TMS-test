<?php

namespace App\Services\Statistics;

use Illuminate\Support\Facades\DB;

class BrandService
{
    // 당일 매출 건수, 합계 (브랜드)
    public static function today_sales($dateType) {

        $list = DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> select(
                'o.brand_type_code as brand',
                DB::raw('COUNT(o.order_idx) as order_count'),
                DB::raw('SUM(o.total_amount - o.discount_amount) as order_amount'),
            )
            ->where('o.is_view', 1)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC')
            ->groupBy('o.brand_type_code');

        if($dateType==="order") {
            $list = $list
                ->whereBetween('o.order_time', [date('Y-m-d'), date('Y-m-d')." 23:59:59"]);
        }elseif ($dateType==="delivery") {
            $list = $list
                ->whereBetween('d.delivery_date', [date('Y-m-d'), date('Y-m-d')." 23:59:59"]);
        }
        return $list->get();
    }

    // 당월 매출 건수, 합계 (브랜드)
    public static function monthly_sales($dateType) {

        return DB::table('total_information')
            -> select(
                'brand',
                DB::raw('SUM(order_count) as order_count'),
                DB::raw('SUM(order_amount) as order_amount')
            )
            -> where('year', date('Y'))
            -> where('month', date('m'))
            -> where('date_type', $dateType)
            -> groupBy('brand')
            -> get();
    }

    // 금년 매출 건수, 합계 (브랜드)
    public static function yearly_sales($dateType) {

        return DB::table('total_information')
            -> select(
                'brand', 'year',
                DB::raw('SUM(order_count) as order_count'),
                DB::raw('SUM(order_amount) as order_amount')
            )
            -> where('year', date('Y'))
            -> where('date_type', $dateType)
            -> groupBy('brand', 'year')
            -> get();
    }

    // 작년 매출 건수, 합계 (브랜드)
    public static function yearAgo_sales($dateType) {

        return DB::table('total_information')
            ->select(
                'brand', 'year',
                DB::raw('SUM(order_count) as order_count'),
                DB::raw('SUM(order_amount) as order_amount'),
            )
            -> where('year', date('Y')-1)
            -> where('date_type', $dateType)
            -> groupBy('brand', 'year')
            -> get();
    }

    // 일 매출 데이터 (풀캘린더)
    public static function day_sales($start, $end, $brand, $dateType) {
        $list = DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> select(
                'o.brand_type_code as brand',
                DB::raw('COUNT(o.order_idx) as order_count'),
                DB::raw('SUM(o.total_amount - o.discount_amount) as order_amount'),
                DB::raw('DATE(o.order_time) as order_date'),
                DB::raw('DATE(d.delivery_date) as delivery_date')
            )
            ->where('o.is_view', 1)
            ->where('o.brand_type_code', $brand)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC');

        if($dateType==="order") {
            $list = $list
                ->whereBetween('o.order_time', [$start, $end." 23:59:59"])
                ->groupBy('o.brand_type_code', DB::raw('DATE(o.order_time)'));
        }elseif ($dateType==="delivery") {
            $list = $list
                ->whereBetween('d.delivery_date', [$start, $end." 23:59:59"])
                ->groupBy('o.brand_type_code', DB::raw('DATE(d.delivery_date)'));
        }
        return $list -> get();
    }

    // 해당 월 매출 건수, 합계 (풀캘린더)
    public static function month_sales($year, $month, $brand, $dateType) {

        return DB::table('total_information')
            -> select(
                DB::raw('SUM(order_count) as order_count'),
                DB::raw('SUM(order_amount) as order_amount')
            )
            -> where('year', $year)
            -> where('month', $month)
            -> where('brand', $brand)
            -> where('date_type', $dateType)
            -> first();
    }

    // 브랜드 월 매출 ( 차트 )
    public static function monthly_sales_brand($brand, $year , $dateType) {

        return DB::table('total_information')
            -> select(
                'month',
                DB::raw('SUM(order_amount) as order_amount')
            )
            -> where('brand', $brand)
            -> where('year', $year)
            -> where('date_type', $dateType)
            -> groupBy('month')
            -> orderBy('month')
            -> get();
    }
}