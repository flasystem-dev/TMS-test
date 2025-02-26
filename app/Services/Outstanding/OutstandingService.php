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

        $query = Orderdata::with(['delivery', 'payments', 'vendor'])
            -> whereHas('delivery', function($query) {
                $query->where('is_balju', 1);
            })
            -> where('brand_type_code', $search['brand'])
            -> where('misu_amount', '>', 0)
            -> where('is_view', 1)
            -> whereBetween($search['date_type'], [$search['start_date'], $search['end_date']." 23:59:59"]);

        return $query -> paginate(15) ->withQueryString();
    }
}