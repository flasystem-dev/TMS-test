<?php
namespace App\Services\Outstanding;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use Illuminate\Support\Facades\Schema;

class OutstandingService
{
    public static function getOrders($search)
    {
        if(empty($search)){
            return null;
        }

        $query = Orderdata::query()
            -> where('brand_type_code', $search['brand'])
            -> whereBetween($search['date_type'], [$search['start_date'], $search['end_date']." 23:59:59"]);

        return $query -> paginate(15);
    }
}