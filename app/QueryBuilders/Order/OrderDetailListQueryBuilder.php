<?php
namespace App\QueryBuilders\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;

class OrderDetailListQueryBuilder
{
    public static function orderDetailListJoinTable($query)
    {
        $query->join('order_delivery', 'order_data.order_idx', '=', 'order_delivery.order_idx')
            ->leftJoin('order_payment', 'order_data.order_idx', '=', 'order_payment.order_idx')
            ->leftJoin('vendor', 'order_data.mall_code', '=', 'vendor.idx')
            ->leftJoin('vendor_pass', 'order_data.mall_code', '=', 'vendor_pass.id');
        return $query;
    }

    public static function groupBy($query)
    {
        $query->groupBy('order_data.order_idx');
        return $query;
    }

    public static function querySelect($query)
    {
        $query ->select(
            'order_data.order_idx as order_idx', 'order_data.od_id as od_id', 'order_data.order_number as order_number', 'order_data.brand_type_code as brand_type_code', 'order_data.mall_code as mall_code',
            'order_data.create_ts as create_ts', 'order_data.orderer_name as orderer_name', 'order_data.orderer_phone as orderer_phone', 'order_data.orderer_tel as orderer_tel', 'order_data.group_code as group_code',
            'order_data.total_amount as total_amount', 'order_data.discount_amount as discount_amount', 'order_data.payment_time as payment_time', 'order_data.order_time as order_time',
            'order_data.payment_state_code as payment_state_code', 'order_data.payment_type_code as payment_type_code',
            'order_data.is_highlight as is_highlight', 'order_data.new_order_yn as new_order_yn', 'order_data.admin_memo as admin_memo', 'order_data.is_view as is_view',
            'order_data.order_quantity as order_quantity', 'order_data.goods_url as goods_url', 'order_data.inflow as inflow', 'order_data.handler as handler',

            'order_delivery.delivery_date as delivery_date', 'order_delivery.delivery_time as delivery_time',
            'order_delivery.receiver_name as receiver_name', 'order_delivery.receiver_phone as receiver_phone', 'order_delivery.receiver_tel as receiver_tel',
            'order_delivery.goods_name as goods_name', 'order_delivery.delivery_address as delivery_address', 'order_delivery.delivery_ribbon_left as delivery_ribbon_left',
            'order_delivery.delivery_state_code as delivery_state_code',
            'order_delivery.send_name as send_name', 'order_delivery.is_balju as is_balju',
            'order_delivery.delivery_photo as delivery_photo','order_delivery.delivery_photo2 as delivery_photo2','order_delivery.delivery_photo3 as delivery_photo3', 'order_delivery.delivery_photo4 as delivery_photo4',
            'order_delivery.delivery_insuName as delivery_insuName',

            'vendor.mall_name as vendor_mallName',      'vendor.rep_name as vendor_name',   'vendor.domain as vendor_domain',       'vendor.is_credit as vendor_isCredit',
            'vendor_pass.mall_name as pass_mallName',   'vendor_pass.name as pass_name',    'vendor_pass.domain as pass_domain',    'vendor_pass.is_credit as pass_isCredit',
        );
        return $query;
    }

    public static function searchWord($query, $column, $word, $order_columns, $delivery_columns)
    {
        $search_column = [
            'od_id',                    // 주문번호
            'order_number',             // 쇼핑몰 주문번호
            'orderer_name',             // 주문자명
            'orderer_phone',            // 주문자 핸드폰
            'receiver_name',            // 받는분
            'receiver_phone',           // 받는분 핸드폰
            'delivery_ribbon_left',     // 보내는분
            'deposit_name',             // 입금자명
            'rep_name',                 // 사업자명
            'name',                     // 사업자명
            'admin_memo',               // 관리자메모
            'payment_memo'              // 결제메모
        ];

        if($column!== "all") {
            switch ($column) {
                case "rep_name":
                    $query->where('vendor.rep_name', 'like', "%{$word}%");
                    break;
                case "name":
                    $query->where('vendor_pass.name', 'like', "%{$word}%");
                    break;
                case "deposit_name":
                case 'payment_memo':
                    $query->where("order_payment.{$column}", 'like', "%{$word}%");
                    break;
                default:
                    $query->whereRaw("REPLACE(" . self::change_tableColumn($order_columns, $delivery_columns, $column) . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                    break;
            }
        }else {
            $query -> where(function($query) use ($search_column,$word,$order_columns, $delivery_columns ){
                foreach ($search_column as $column) {
                    switch ($column) {
                        case 'rep_name':
                            $query->orWhere('vendor.rep_name', 'like', "%{$word}%");
                            break;
                        case 'name':
                            $query->orWhere('vendor_pass.name', 'like', "%{$word}%");
                            break;
                        case 'deposit_name':
                        case 'payment_memo':
                            $query->orWhere("order_payment.{$column}", 'like', "%{$word}%");
                            break;
                        case 'orderer_phone':
                        case 'receiver_phone':
                            $query->orWhereRaw(
                                "REPLACE(" . self::change_tableColumn($order_columns, $delivery_columns, $column) . ", '-', '') LIKE ?",
                                ["%" . str_replace('-', '', $word) . "%"]
                            );
                            break;
                        default :
                            $query->orWhere(self::change_tableColumn($order_columns, $delivery_columns, $column), 'like', "%{$word}%");
                    }
                }
            });
        }
        return $query;
    }

    public static function searchColumn($query, $search, $order_columns, $delivery_columns)
    {
        foreach ($search as $key => $value) {
            if(!empty($value)) {
                switch ($key) {
                    case "payment_type_code":
                    case "is_new":
                    case "is_highlight":
                    case "delivery_state_code":
                    case "inflow":
                        $query -> where(self::change_tableColumn($order_columns, $delivery_columns, $key), $value);
                        break;

                    case "date_type":
                        if(!empty($search['start_date']) && !empty($search['end_date'])) {
                            $query -> whereBetween(self::change_tableColumn($order_columns, $delivery_columns, $value), [$search['start_date']." 00:00:00", $search['end_date']." 23:59:59"]);
                        }
                        break;

                    case "payment_state_code":
                        if($value === 'PSCR') {
                            $query->whereIn('order_data.payment_state_code',['PSCR','PSER','PSRR']);
                        }elseif($value === 'PSCC') {
                            $query->whereIn('order_data.payment_state_code',['PSCC','PSEC','PSRC']);
                        }else {
                            $query->where('order_data.payment_state_code',$value);
                        }
                        break;
                    case "mall_code":
                        $commonCodes = Cache::get('common_codes', collect());
                        $openMarkets = $commonCodes->filter(function ($item) {
                            return $item->parent_code === 'ML';
                        })->pluck('code')->toArray();

                        $is_openMarket = Str::contains($value, $openMarkets);

                        if($is_openMarket) {
                            $query -> where('mall_code', $value);
                        }else {
                            $query -> where(function ($query) use ($openMarkets) {
                                $query -> whereNotIn('order_data.brand_type_code', ['BTCS', 'BTFC'])
                                    ->whereNotIn('order_data.mall_code', $openMarkets);
                            });
                        }
                        break;
                    default:
                        if(Str::contains($key, '-')) {
                            [$table, $column] = explode('-', $key);
                            $query -> where("{$table}.{$column}", $value);
                        }
                }
            }
        }
        return $query;
    }

    public static function searchAmount($query, $amount)
    {
        $query->where(DB::raw('COALESCE(order_data.total_amount, 0)'), '=', $amount);
        return $query;
    }

    public static function sumAmount($query)
    {
        $query_for_sum = clone $query;
        $result_data = $query_for_sum
            ->selectRaw('SUM(COALESCE(order_data.total_amount, 0)) as sum_amount')
            ->first();
        return $result_data -> sum_amount ?? 0;
    }

    public static function paginate($query)
    {
        $perPage = session('perPage') ?? 20;

        return $query ->paginate($perPage) -> withQueryString();
    }

    public static function selectedOrders($query)
    {
        $order_idxs = session('order_idx');

        $query -> whereIn('order_data.order_idx', $order_idxs);
        return $query;
    }

########################################################################################################################
    ######################################  order, delivery 테이블 컬럼 구분  #############################################
    protected static function change_tableColumn($order_columns, $delivery_columns, $column) {

        if(in_array($column, $order_columns)) {
            return "order_data.".$column;
        } elseif(in_array($column, $delivery_columns)){
            return "order_delivery.".$column;
        }else {
            return $column;
        }
    }
}