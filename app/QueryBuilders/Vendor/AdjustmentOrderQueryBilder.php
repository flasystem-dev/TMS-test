<?php
namespace App\QueryBuilders\Vendor;

use App\QueryBuilders\Order\OrderDataQueryBuilder;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class AdjustmentOrderQueryBilder
{
########################################################################################################################
    ##############################################  정산 - 주문 쿼리 빌더  ################################################
    
    public static function joinDeliveryWithWhere($query, $year, $month)
    {
        $query ->join('order_delivery', function($query) use($year, $month) {
            $query->on('order_data.order_idx', '=', 'order_delivery.order_idx')
                ->where('order_delivery.is_balju', 1)
                ->whereNot('order_delivery.delivery_state_code', 'DLCC')
                ->whereYear('order_delivery.delivery_date', $year)
                ->whereMonth('order_delivery.delivery_date', $month);
        });
        return $query;
    }

    public static function orderSubSelect($query)
    {
        $query->select('order_data.mall_code as mall_code',
            'order_data.order_idx as order_idx',
            DB::raw('SUM(COALESCE(total_amount, 0)) as total_amount'),
            DB::raw('SUM(COALESCE(vendor_amount, 0)) as vendor_amount'),
            DB::raw('SUM(COALESCE(misu_amount, 0)) as misu_amount')
        );
        return $query;
    }

    public static function orderGroupBy($query)
    {
        $query ->groupBy('order_data.mall_code', 'order_data.order_idx');
        return $query;
    }
    
########################################################################################################################
    ##############################################  정산 - 상품(옵션) 쿼리 빌더  ###########################################

    public static function createItemDBQuery()
    {
        return DB::table('order_items');
    }

    public static function itemSubSelect($query)
    {
        $query ->select(
            'order_items.order_id as order_idx',
            'order_items.vendor_options_amount as vendor_options_amount',
        );
        return $query;
    }

    public static function itemGroupBy($query)
    {
        $query ->groupBy('order_items.order_id');
        return $query;
    }

}