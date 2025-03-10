<?php
namespace App\Services\Outstanding;

use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Vendor;

class OutstandingOrderService
{
    public static function getOrders($search)
    {
        if(empty($search)){
            return null;
        }

        $query = Orderdata::with(['delivery', 'payments', 'vendor', 'pass', 'client'])
//            -> join('order_delivery as delivery', 'order_data.order_idx', '=', 'delivery.order_idx')

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

        if(isset($search['is_client'])) {
            switch ($search['is_client']) {
                case '0':
                    $query -> where('client_id', 0);
                    break;
                case '1':
                    $query -> whereNot('client_id', 0);
                    break;
            }
        }

        for ($i=1; $i<=2; $i++) {
            if(!empty($search['search_word1'])){
                if($search['search1'] === 'all'){

                }else {
                    switch ($search['search1']) {
                        case 'od_id':
                            $query -> where('od_id', $search['search_word1']);
                            break;
                        case 'rep_name':
                            if($search['brand'] === "BTCS" || $search['brand'] === "BTFC") {
                                $query -> whereHas('vendor', function($query) use ($search) {
                                    $query->where('rep_name', 'like', '%'.$search['search_word1'].'%');
                                });
                            } else {
                                $query -> whereHas('pass', function($query) use ($search) {
                                    $query->where('name', 'like', '%'.$search['search_word1'].'%');
                                });
                            }
                            break;
                        case 'client_name':
                            $query -> whereHas('client', function($query) use ($search) {
                                $query->where('name', 'like', '%'.$search['search_word1'].'%');
                            });
                    }
                }
            }
        }

        if(!empty($search['search_word2'])){
            if($search['search2'] === 'all'){

            }else {
                switch ($search['search2']) {
                    case 'od_id':
                        $query -> where('od_id', $search['search_word2']);
                        break;
                    case 'rep_name':
                        if($search['brand'] === "BTCS" || $search['brand'] === "BTFC") {
                            $query -> whereHas('vendor', function($query) use ($search) {
                                $query->where('rep_name', 'like', '%'.$search['search_word2'].'%');
                            });
                        } else {
                            $query -> whereHas('pass', function($query) use ($search) {
                                $query->where('name', 'like', '%'.$search['search_word2'].'%');
                            });
                        }
                        break;
                    case 'client_name':
                        $query -> whereHas('client', function($query) use ($search) {
                            $query->where('name', 'like', '%'.$search['search_word2'].'%');
                        });
                }
            }
        }

        $query -> orderBy('create_ts', 'desc');
        return $query -> paginate(10) ->withQueryString();
    }
}