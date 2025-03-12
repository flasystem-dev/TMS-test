<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\CommonCode;
use App\Models\CodeOfCompanyInfo;
use App\Transaction\OrderDataTran;

use App\Services\Order\OrderService;
use App\Services\Document\OrderTranIndexService;



class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('auth-check');
    }

    public function get_orders(Request $request){
        $page = $request->query('page');

        if($page>1){
            $open="N";
        }


        $search = $request -> all();

        $query = OrderTranIndexService::getTranQuery($search);
        $data = $query->get();
        $data['sum_amount']= $query->sum('total_amount');
        $data['commonDate'] = CommonCode::commonDate();

        $data['orders_count'] = $data->count();
        $data['orders'] = OrderTranIndexService::pagenate($query);
        if(self::rateLimit()) {
            return redirect() -> away('https://flabiz.kr/sub/max_traffic_tms.php');
        }

        $col_style='collapsed';
        $show_box='';



        return view('Document.document-orders', $data);
    }

    public static function bank_code(Request $request) {
        $pg = $request -> pg;

        $options = "<option value=''>-은행선택-</option>";

        if($pg == "toss") {
            $banks = DB::table('code_of_toss_card_bank') ->  select('code_no', 'code_name') -> where('type', 'BANK') -> get();
            foreach ($banks as $bank) {
                $options .= "<option value='" . $bank -> code_no . "'>" . $bank -> code_name . "</option>";
            }
        } else if($pg == "nice") {
            $banks = DB::table('code_of_nicepay_card_bank') -> select('code_no', 'code_name') -> where('type', 'BANK') -> get();
            foreach ($banks as $bank) {
                $options .= "<option value='" . $bank -> code_no . "'>" . $bank -> code_name . "</option>";
            }
        }

        return $options;
    }
    public static function rateLimit()
    {
        $hit = DB::table('connection_log')
            ->select('hit')
            ->where('user_id', Auth::user()->user_id)
            ->where('log_date', date('Y-m-d'))
            ->where('uri', 'order/ecommerce_orders')
            ->first() -> hit;

        if($hit > 2000) {
            return true;
        }
        return false;
    }
    public function refund_table(Request $request) {
        $data['order'] = OrderData::find($request -> order_idx);
        return view('Document.include.select-refund', $data);
    }
}
