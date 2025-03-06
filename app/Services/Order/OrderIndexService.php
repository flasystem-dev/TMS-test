<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\DTOs\OrderProduct;
use App\QueryBuilders\Order\OrderIndexQueryBuilder;
use App\QueryBuilders\Order\OrderDataQueryBuilder;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;


class OrderIndexService
{
    public static function getOrderList($search)
    {
        $subQuery = OrderData::select('order_idx',   'od_id',                'mall_code',            'brand_type_code',          'order_number',         'group_code',
            'orderer_name',             'orderer_tel',          'orderer_phone',        'payment_type_code',        'payment_state_code',
            'payment_time',             'total_amount',         'discount_amount',      'admin_memo',               'create_ts',
            'goods_url',             'is_view',              'is_highlight',             'inflow',
            'order_quantity',           'order_time',  'handler' , 'is_new'
        );

        // 브랜드 검색
        $brands = ["BTCP", "BTCC", "BTSP", "BTBR", "BTOM", "BTCS", "BTFC"];
        $filtered_brand = array_filter($brands, fn($brand) => session($brand) === 'Y');
        if(!empty($filtered_brand)) {
            $subQuery->whereIn('order_data.brand_type_code',$filtered_brand);
        }
        // 주문제거
        if(Auth::user()->auth < 10) {
            $subQuery -> where('order_data.is_view', 1);
        }

        $query = OrderData::Query();

        $query = OrderIndexQueryBuilder::orderIndexJoinTable($query, $subQuery);

        if($search) {
            $order_columns = Schema::getColumnListing((new OrderData)->getTable());
            $delivery_columns = Schema::getColumnListing((new OrderDelivery)->getTable());

            // 나머지 검색
            $query = OrderIndexQueryBuilder::searchColumn($query, $search, $order_columns, $delivery_columns);

            // 1차 검색어
            if(!empty($search['word1']))
            {
                $query = OrderIndexQueryBuilder::searchWord($query, $search['sw_1'], $search['word1'], $order_columns, $delivery_columns);
            }

            // 2차 검색어
            if(!empty($search['word2']))
            {
                $query = OrderIndexQueryBuilder::searchWord($query, $search['sw_2'], $search['word2'], $order_columns, $delivery_columns);
            }

            // 취소주문 포함/미포함
            $query = OrderDataQueryBuilder::includeCancelOrder($query, $search);
        } else {
            // 기본 조건
            $query = OrderIndexQueryBuilder::withoutSearch($query);
        }

        // 검색 시 총 금액, 총 건수 계산
        $data = OrderIndexQueryBuilder::sumAmountCountOrders($query, $search);

        $query = OrderIndexQueryBuilder::querySelect($query);

        $query = OrderDataQueryBuilder::orderBy($query);

        $data['orders'] = OrderIndexQueryBuilder::paginate($query);

        return $data;
    }

    public static function countOrderData($data)
    {
        $data['newOrders'] = OrderIndexQueryBuilder::newOrders();
        $data['isNotBalju'] = OrderIndexQueryBuilder::todayTomorrowIsNotBalju();
        $data['cancelRequest'] = OrderIndexQueryBuilder::cancelRequest();
        $data['todayOrders'] = OrderIndexQueryBuilder::todayOrders();
        $data['todayDelivery'] = OrderIndexQueryBuilder::todayDelvery();

        return $data;
    }

    // 주문 데이터 벌크 다운로드 용 조회
    public static function order_bulk_excelDownload($search) {
        $order_sql = DB::table('order_data')
            -> where('brand_type_code', $search['excel_brand'])
            -> where('is_view', 1);

        $query = DB::table('order_data')
            -> fromSub($order_sql, 'order_data')
            -> join('order_delivery', 'order_data.order_idx', '=', 'order_delivery.order_idx');

        if($search['payment_state_code']!=="all") {
            $query -> where('order_data.payment_state_code', $search['payment_state_code']);
        }else if(isset($search['excel_except']) && in_array("PSCC", $search['excel_except'])) {
            $query -> whereNot('order_data.payment_state_code' ,"PSCC");
        }

        if($search['payment_type_code']!=="all") {
            $query -> where('order_data.payment_type_code', $search['payment_type_code']);
        }

        if($search['delivery_state_code']!=="all") {
            $query -> where('order_delivery.delivery_state_code', $search['delivery_state_code']);
        }else if(isset($search['excel_except']) && in_array("DLCC", $search['excel_except'])) {
            $query -> whereNot('order_delivery.delivery_state_code' ,"DLCC");
        }

        if($search['date_type']==="order_time") {
            $query -> whereBetween('order_data.order_time', [$search['excel_start_date'], $search['excel_end_date'] . ' 23:59:59']);
        }elseif ($search['date_type']==="delivery_date") {
            $query -> whereBetween('order_delivery.delivery_date', [$search['excel_start_date'], $search['excel_end_date']]);
        }
        return $query -> pluck('order_data.order_idx')
            -> toArray();
    }
}