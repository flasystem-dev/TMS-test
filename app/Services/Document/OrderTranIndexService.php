<?php
namespace App\Services\Document;

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
use App\Models\Transaction\OrderDataTran;


class OrderTranIndexService
{
    public static function getTranQuery($search)
    {
        $query = OrderDataTran::orderby('order_idx', 'desc');
        if($search) {
            $query = self::sessionBrandFilter($query);
            // 나머지 검색
        }
        return $query;
    }


    public static function getSumAmount($query){

    }
    public static function sessionBrandFilter($query)
    {
        $brands = ["BTCP", "BTCC", "BTSP", "BTBR", "BTOM", "BTCS", "BTFC"];
        $filtered_brand = array_filter($brands, fn($brand) => session($brand) === 'Y');
        if(!empty($filtered_brand)) {
            $query->whereIn('brand_type_code',$filtered_brand);
        }
        return $query;
    }



}