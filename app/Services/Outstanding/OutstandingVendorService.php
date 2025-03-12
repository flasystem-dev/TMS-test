<?php
namespace App\Services\Outstanding;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Vendor;

class OutstandingVendorService
{
    public static function getVendors($search)
    {
        if(empty($search)){
            return null;
        }

        $query = Vendor::select([
            'vendor.*',
            DB::raw('SUM(CASE WHEN order_data.client_id = 0 THEN order_data.misu_amount ELSE 0 END) as personal_misu_amount'),
            DB::raw('COUNT(DISTINCT CASE WHEN order_data.client_id = 0 THEN order_data.order_idx ELSE NULL END) as personal_misu_count'),
            DB::raw('SUM(CASE WHEN order_data.client_id != 0 THEN order_data.misu_amount ELSE 0 END) as client_misu_amount'),
            DB::raw('COUNT(DISTINCT CASE WHEN order_data.client_id != 0 THEN order_data.order_idx ELSE NULL END) as client_misu_count'),
            DB::raw('IFNULL(longTerm_misu.longTerm_misu_amount, 0) as longTerm_misu_amount'),
            DB::raw('IFNULL(longTerm_misu.longTerm_misu_count, 0) as longTerm_misu_count'),
            DB::raw('IFNULL(monthAgo_misu.monthAgo_misu_amount, 0) as monthAgo_misu_amount'),
            DB::raw('IFNULL(monthAgo_misu.monthAgo_misu_count, 0) as monthAgo_misu_count'),
            DB::raw('IFNULL(total_misu.total_misu_amount, 0) as total_misu_amount'),
            DB::raw('IFNULL(total_misu.total_misu_count, 0) as total_misu_count')
        ])
            ->join('order_data', function($join) use ($search) {
                $join->on('vendor.idx', '=', 'order_data.mall_code')
                    ->where('order_data.misu_amount', '>', 0)
                    ->where('order_data.brand_type_code', $search['brand'])
                    ->whereIn('order_data.payment_state_code', ['PSUD', 'PSOC'])
                    ->where('order_data.is_view', 1);

                switch ($search['date_type']) {
                    case 'order_time':
                    case 'create_ts':
                        $join->whereBetween("order_data." . $search['date_type'], [$search['start_date'], $search['end_date'] . " 23:59:59"]);
                        break;
                }
            })
            ->join('order_delivery', function($join) use ($search) {
                $join->on('order_data.order_idx', '=', 'order_delivery.order_idx')
                    ->where('order_delivery.is_balju', 1)
                    ->whereNot('order_delivery.delivery_state_code', 'DLCC');

                if ($search['date_type'] === 'delivery_date') {
                    $join->whereBetween('order_delivery.delivery_date', [$search['start_date'], $search['end_date']]);
                }
            })
            -> where('vendor.brand_type', 'like', $search['brand'].'%' );


        $today = Carbon::now()->format('Y-m-d');
        $oneYearAgo = Carbon::now()->subYear()->format('Y-m-d'); // 1년 전

        $addWhere_brand = "AND order_data.brand_type_code = '{$search['brand']}'";

        // 장기 미수 계산 추가
        $lastMonthEnd = Carbon::now()->subMonths(2)->lastOfMonth() -> format('Y-m-d'); // 전전달 마지막 날

        $longTermMisu_addWhere = "";
        switch ($search['date_type']) {
            case 'order_time':
            case 'create_ts':
                $longTermMisu_addWhere = "AND order_data." . $search['date_type'] . " BETWEEN '" . $oneYearAgo . "' AND '" . $lastMonthEnd . " 23:59:59'";
                break;
            case 'delivery_date':
                $longTermMisu_addWhere = "AND order_delivery." . $search['date_type'] . " BETWEEN '" . $oneYearAgo . "' AND '" . $lastMonthEnd . "'";
                break;
        }

        $query->leftJoin(DB::raw("
            (SELECT order_data.mall_code, 
                    SUM(order_data.misu_amount) as longTerm_misu_amount,
                    COUNT(order_data.order_idx) as longTerm_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             {$addWhere_brand}
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$longTermMisu_addWhere}
             GROUP BY order_data.mall_code
            ) as longTerm_misu
        "), 'vendor.idx', '=', 'longTerm_misu.mall_code') ;
        
        // 전체 미수 계산 추가
        $totalMisu_addWhere = "";
        switch ($search['date_type']) {
            case 'order_time':
            case 'create_ts':
                $totalMisu_addWhere = "AND order_data." . $search['date_type'] . " BETWEEN '" . $oneYearAgo . "' AND '" . $today . " 23:59:59'";
                break;
            case 'delivery_date':
                $totalMisu_addWhere = "AND order_delivery." . $search['date_type'] . " BETWEEN '" . $oneYearAgo . "' AND '" . $today . "'";
                break;
        }
        $query->leftJoin(DB::raw("
            (SELECT order_data.mall_code, 
                    SUM(order_data.misu_amount) as total_misu_amount,
                    COUNT(order_data.order_idx) as total_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             {$addWhere_brand}
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$totalMisu_addWhere}
             GROUP BY order_data.mall_code
            ) as total_misu
        "), 'vendor.idx', '=', 'total_misu.mall_code') ;

        // 전달 미수 계산 추가
        $monthAgoStart = Carbon::now()->subMonth()->firstOfMonth()->format('Y-m-d'); // 지난달 첫날
        $monthAgoEnd = Carbon::now()->subMonth()->lastOfMonth()->format('Y-m-d'); // 지난달 마지막

        $monthAgoMisu_addWhere = "";
        switch ($search['date_type']) {
            case 'order_time':
            case 'create_ts':
                $monthAgoMisu_addWhere = "AND order_data." . $search['date_type'] . " BETWEEN '" . $monthAgoStart . "' AND '" . $monthAgoEnd . " 23:59:59'";
                break;
            case 'delivery_date':
                $monthAgoMisu_addWhere = "AND order_delivery." . $search['date_type'] . " BETWEEN '" . $monthAgoStart . "' AND '" . $monthAgoEnd . "'";
                break;
        }

        $query->leftJoin(DB::raw("
            (SELECT order_data.mall_code, 
                    SUM(order_data.misu_amount) as monthAgo_misu_amount,
                    COUNT(order_data.order_idx) as monthAgo_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             {$addWhere_brand}
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$monthAgoMisu_addWhere}
             GROUP BY order_data.mall_code
            ) as monthAgo_misu
        "), 'vendor.idx', '=', 'monthAgo_misu.mall_code') ;


        $query -> groupBy('vendor.idx');
        $query ->havingRaw('SUM(order_data.misu_amount) > 0');
        $query -> orderBy('total_misu_amount', 'desc');

        return $query -> get();
    }


}