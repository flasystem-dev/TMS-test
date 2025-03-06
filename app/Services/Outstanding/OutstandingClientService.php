<?php
namespace App\Services\Outstanding;

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
            DB::raw('SUM(order_data.misu_amount) as total_misu_amount'),
            DB::raw('count(DISTINCT order_data.order_idx) as total_misu_count'),
            DB::raw('SUM(CASE WHEN order_delivery.delivery_date < DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), "%Y-%m-01") THEN order_data.misu_amount ELSE 0 END) as past_misu_amount'),
            DB::raw('COUNT(CASE WHEN order_delivery.delivery_date < DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), "%Y-%m-01") THEN order_data.order_idx ELSE NULL END) as past_misu_count')
        ])
            ->join('order_data', function($join) use ($search) {
                $join->on('clients.id', '=', 'order_data.client_id')
                    ->where('order_data.misu_amount', '>', 0)
                    ->whereIn('order_data.brand_type_code', $search['brand'])
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
            -> whereRaw("clients.brand REGEXP ?", $brand_pattern );

        $query->leftJoin('vendor', function($join) {
            $join->on('order_data.mall_code', '=', 'vendor.idx')
                ->whereIn('order_data.brand_type_code', ['BTCS', 'BTFC']);
        });

        $query->leftJoin('vendor_pass', function($join) {
            $join->on('order_data.mall_code', '=', 'vendor_pass.id')
                ->whereNotIn('order_data.brand_type_code', ['BTCS', 'BTFC']);
        });

        $query -> groupBy('order_data.mall_code');
        $query -> havingRaw('SUM(order_data.misu_amount) > 0');
        $query -> orderBy('total_misu_amount', 'desc');

        return $query -> get();
    }
}