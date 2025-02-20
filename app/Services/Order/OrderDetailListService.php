<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\DTOs\OrderProduct;
use App\QueryBuilders\Order\OrderDetailListQueryBuilder;
use App\QueryBuilders\Order\OrderDataQueryBuilder;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;

Class OrderDetailListService
{
    public static function getOrderList($search)
    {
        if(!$search) {
            return null;
        }

        $query = OrderDataQueryBuilder::createQuery();

        $query = OrderDataQueryBuilder::findBrand($query, $search['brand']);

        $query = OrderDataQueryBuilder::isView($query);

        $query = OrderDetailListQueryBuilder::orderDetailListJoinTable($query);

        $query = OrderDetailListQueryBuilder::groupBy($query);

        $query = OrderDetailListQueryBuilder::querySelect($query);

        $order_columns = Schema::getColumnListing((new OrderData)->getTable());
        $delivery_columns = Schema::getColumnListing((new OrderDelivery)->getTable());

        // 검색어
        if(!empty($search['search_word'])){
            $query = OrderDetailListQueryBuilder::searchWord($query, $search['search'], $search['search_word'], $order_columns, $delivery_columns);
        }

        // 나머지 검색
        $query = OrderDetailListQueryBuilder::searchColumn($query, $search, $order_columns, $delivery_columns);

        // 취소주문 포함/미포함
        $query = OrderDataQueryBuilder::includeCancelOrder($query, $search);

        // 금액 검색
        if(!empty($search['total_amount'])) {
            $query = OrderDetailListQueryBuilder::searchAmount($query, $search['total_amount']);
        }
        
        // 총 금액 계산
        $data['orders_amount'] = OrderDetailListQueryBuilder::sumAmount($query);

        $query = OrderDataQueryBuilder::orderBy($query);

        $data['orders'] = OrderDetailListQueryBuilder::paginate($query);

        return $data;
    }

    public static function getSelectedOrders()
    {
        $query = OrderDataQueryBuilder::createQuery();

        $query = OrderDetailListQueryBuilder::selectedOrders($query);

        $query = OrderDetailListQueryBuilder::orderDetailListJoinTable($query);

        $query = OrderDetailListQueryBuilder::groupBy($query);

        $query = OrderDetailListQueryBuilder::querySelect($query);

        // 총 금액 계산
        $data['orders_amount'] = OrderDetailListQueryBuilder::sumAmount($query);

        $query = OrderDataQueryBuilder::orderBy($query);

        $data['orders'] = OrderDetailListQueryBuilder::paginate($query);

        return $data;
    }
}