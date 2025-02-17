<?php
namespace App\QueryBuilders\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;

class OrderDataQueryBuilder
{
    public static function createQuery()
    {
        return OrderData::Query();
    }

    public static function createDBQuery()
    {
        return DB::table('order_data');
    }

    public static function findBrand($query, $brand)
    {
        $query -> where('order_data.brand_type_code', $brand);
        return $query;
    }

    public static function isView($query)
    {
        if(Auth::user()->auth < 10) {
            $query -> where('order_data.is_view', 1);
        }
        return $query;
    }

    public static function canView($query)
    {
        $query -> where('order_data.is_view', 1);
        return $query;
    }

    public static function includeCancelOrder($query , $search)
    {
        $cancelCheck = $search['cancel_check'] ?? false;
        $query->when(!$cancelCheck, function ($query) {
            $query->whereNot('order_delivery.delivery_state_code', 'DLCC');
        });
        return $query;
    }

    public static function orderBy($query)
    {
        $query -> orderBy('order_data.create_ts','DESC');
        return $query;
    }
}