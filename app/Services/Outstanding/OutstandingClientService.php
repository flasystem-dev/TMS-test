<?php
namespace App\Services\Outstanding;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Client;

class OutstandingClientService
{
    public static function getClients($search)
    {
        if(empty($search)){
            return null;
        }
        $brand_pattern = implode('|', $search['brand']);

        $query = Client::select([
            'clients.*', 'order_data.brand_type_code',
            DB::raw("
                CASE 
                    WHEN order_data.brand_type_code IN ('BTCS', 'BTFC') THEN vendor.rep_name 
                    ELSE vendor_pass.name 
                END AS rep_name
            "),
            DB::raw('SUM(order_data.misu_amount) as misu_amount'),
            DB::raw('count(DISTINCT order_data.order_idx) as misu_count'),
            DB::raw('IFNULL(longTerm_misu.longTerm_misu_amount, 0) as longTerm_misu_amount'),
            DB::raw('IFNULL(longTerm_misu.longTerm_misu_count, 0) as longTerm_misu_count'),
            DB::raw('IFNULL(monthAgo_misu.monthAgo_misu_amount, 0) as monthAgo_misu_amount'),
            DB::raw('IFNULL(monthAgo_misu.monthAgo_misu_count, 0) as monthAgo_misu_count'),
            DB::raw('IFNULL(total_misu.total_misu_amount, 0) as total_misu_amount'),
            DB::raw('IFNULL(total_misu.total_misu_count, 0) as total_misu_count')
        ])
            ->join('order_data', function($join) use ($search) {
                $join->on('clients.id', '=', 'order_data.client_id')
                    ->where('order_data.misu_amount', '>', 0)
                    ->whereIn('order_data.brand_type_code', $search['brand'])
                    ->whereIn('order_data.payment_state_code', ['PSUD', 'PSOC'])
                    ->where('order_data.is_view', 1)
                    ->groupBy('order_data.client_id');

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
            -> whereRaw("clients.brand REGEXP ?", $brand_pattern );

        $query->leftJoin('vendor', function($join) {
            $join->on('order_data.mall_code', '=', 'vendor.idx')
                ->whereIn('order_data.brand_type_code', ['BTCS', 'BTFC']);
        });

        $query->leftJoin('vendor_pass', function($join) {
            $join->on('order_data.mall_code', '=', 'vendor_pass.id')
                ->whereNotIn('order_data.brand_type_code', ['BTCS', 'BTFC']);
        });

        $today = Carbon::now()->format('Y-m-d');
        $oneYearAgo = Carbon::now()->subYear()->format('Y-m-d'); // 1년 전

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
            (SELECT order_data.client_id,
                    order_data.mall_code,
                    order_data.brand_type_code,
                    SUM(order_data.misu_amount) as longTerm_misu_amount,
                    COUNT(DISTINCT order_data.order_idx) as longTerm_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$longTermMisu_addWhere}
             GROUP BY order_data.client_id, order_data.mall_code, order_data.brand_type_code
            ) as longTerm_misu
        "), function($join) {
            $join->on('clients.id', '=', 'longTerm_misu.client_id')
                ->on('order_data.mall_code', '=', 'longTerm_misu.mall_code')
                ->on('order_data.brand_type_code', '=', 'longTerm_misu.brand_type_code');
        });

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
            (SELECT order_data.client_id,
                    order_data.mall_code,
                    order_data.brand_type_code,
                    SUM(order_data.misu_amount) as monthAgo_misu_amount,
                    COUNT(order_data.order_idx) as monthAgo_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$monthAgoMisu_addWhere}
             GROUP BY order_data.client_id, order_data.mall_code, order_data.brand_type_code
            ) as monthAgo_misu
        "), function($join) {
            $join->on('clients.id', '=', 'monthAgo_misu.client_id')
                ->on('order_data.mall_code', '=', 'monthAgo_misu.mall_code')
                ->on('order_data.brand_type_code', '=', 'monthAgo_misu.brand_type_code');
        });

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
            (SELECT order_data.client_id,
                    order_data.mall_code,
                    order_data.brand_type_code,
                    SUM(order_data.misu_amount) as total_misu_amount,
                    COUNT(order_data.order_idx) as total_misu_count
             FROM order_data
             JOIN order_delivery
             ON order_data.order_idx = order_delivery.order_idx
             WHERE order_data.payment_state_code IN ('PSUD', 'PSOC')
             AND order_data.is_view = 1
             AND order_delivery.is_balju = 1
             AND order_delivery.delivery_state_code != 'DLCC'
             {$totalMisu_addWhere}
             GROUP BY order_data.client_id, order_data.mall_code, order_data.brand_type_code
            ) as total_misu
        "), function($join) {
            $join->on('clients.id', '=', 'total_misu.client_id')
                ->on('order_data.mall_code', '=', 'total_misu.mall_code')
                ->on('order_data.brand_type_code', '=', 'total_misu.brand_type_code');
        });

        // 검색어 필터 추가
        if(!empty($search['search_word1'])){
            $word = $search['search_word1'];

            if($search['search1'] === "all"){
                $query -> where(function($query) use ($word) {
                    $search_columns = [
                        'clients.name',
                        'clients.search_words'
                    ];
                    foreach ($search_columns as $column) {
                        switch ($column) {
                            case 'search_words':
                                $query->orWhereRaw("JSON_SEARCH($column, 'one', ?) IS NOT NULL", ["%$word%"]);
                                break;
                            default:
                                $query->orWhere($column, "like", "%$word%");
                        }
                    }
                });
            }else {
                $query->orWhere($search['search1'], "like", "%$word%");
            }
        }

        $query -> groupBy('clients.id', 'order_data.mall_code', 'order_data.brand_type_code');
        $query -> havingRaw('SUM(order_data.misu_amount) > 0');
        $query -> orderBy('clients.id', 'desc');

        return $query -> get();
    }
}