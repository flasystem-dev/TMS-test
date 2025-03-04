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

        $query = Vendor::with(['orders'])
            -> whereHas('orders', function($query) use($search) {
                $query->whereHas('delivery', function($query2) {
                    $query2->where('is_balju', 1);
                    $query2->whereNot('delivery_state_code', 'DLCC');
                });
                $query-> whereIn('payment_state_code', ['PSUD', 'PSOC'])
                    -> where('brand_type_code', $search['brand'])
                    -> where('misu_amount', '>', 0)
                    -> where('is_view', 1);
            });

        $query = Orderdata::with(['delivery', 'payments', 'vendor'])
            -> whereHas('delivery', function($query) {
                $query->where('is_balju', 1);
                $query->whereNot('delivery_state_code', 'DLCC');
            })
            -> whereIn('payment_state_code', ['PSUD', 'PSOC'])
            -> where('brand_type_code', $search['brand'])
            -> where('misu_amount', '>', 0)
            -> where('is_view', 1)
            -> whereBetween($search['date_type'], [$search['start_date'], $search['end_date']." 23:59:59"]);

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
}