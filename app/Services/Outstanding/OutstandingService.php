<?php
namespace App\Services\Outstanding;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Vendor;
use Illuminate\Support\Facades\Schema;

class OutstandingService
{
    public static function getOrders($search)
    {
        if(empty($search)){
            return null;
        }

        $query = Orderdata::with(['delivery', 'payments', 'vendor'])
            -> whereHas('delivery', function($query) {
                $query->where('is_balju', 1);
                $query->whereNot('delivery_state_code', 'DLCC');
            })
            -> whereIn('payment_state_code', ['PSUD', 'PSOC'])
            -> where('brand_type_code', $search['brand'])
            -> where('misu_amount', '>', 0)
            -> where('is_view', 1);

        switch ($search['date_type']) {
            case 'delivery_date' :
                $query -> whereHas('delivery', function($query) use($search) {
                    $query->whereBetween('delivery_date', [$search['start_date'], $search['end_date']]);
                });
                break;
            case 'order_time' :
            case 'create_ts' :
                $query-> whereBetween($search['date_type'], [$search['start_date'], $search['end_date']." 23:59:59"]);
                break;

        }

        if(!empty($search['search_word1'])){
            switch ($search['search1']) {
                case 'all':

                    break;
                case 'od_id':
                    $query -> where('od_id', $search['search_word1']);
                    break;
                case 'rep_name':
                    $query -> whereHas('vendor', function($query) use ($search) {
                        $query->where('rep_name', 'like', '%'.$search['search_word1'].'%');
                    });
                    break;
            }
        }
        $query -> orderBy('create_ts', 'desc');
        return $query -> paginate(15) ->withQueryString();
    }

    public static function getVendors($search)
    {
        if(empty($search)){
            return null;
        }

        $query = Vendor::select([
                'vendor.*',
                DB::raw('SUM(order_data.misu_amount) as total_misu_amount'),
                DB::raw('count(order_data.order_idx) as order_count'),
                DB::raw('SUM(CASE WHEN order_data.client_id = 0 THEN order_data.misu_amount ELSE 0 END) as personal_misu_amount'),
                DB::raw('COUNT(CASE WHEN order_data.client_id = 0 THEN order_data.order_idx ELSE NULL END) as personal_misu_count'),
                DB::raw('SUM(CASE WHEN order_data.client_id != 0 THEN order_data.misu_amount ELSE 0 END) as client_misu_amount'),
                DB::raw('COUNT(CASE WHEN order_data.client_id != 0 THEN order_data.order_idx ELSE NULL END) as client_misu_count'),
                DB::raw('SUM(CASE WHEN order_delivery.delivery_date < DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN order_data.misu_amount ELSE 0 END) as past_misu_amount'),
                DB::raw('COUNT(CASE WHEN order_delivery.delivery_date < DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN order_data.order_idx ELSE NULL END) as past_misu_count')
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

        $query -> groupBy('vendor.idx');
        $query ->havingRaw('SUM(order_data.misu_amount) > 0');
        $query -> orderBy('total_misu_amount', 'desc');
        return $query -> paginate(15) ->withQueryString();
    }
}