<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use Illuminate\Support\Facades\Schema;

class OutstandingTransactionService
{
    public static function getTransaction($search)
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

        // 총 금액 계산
        $data['orders_amount'] = OrderDetailListQueryBuilder::sumAmount($query);

        $query = OrderDataQueryBuilder::orderBy($query);

        $data['orders'] = OrderDetailListQueryBuilder::paginate($query);

        return $data;
    }
}