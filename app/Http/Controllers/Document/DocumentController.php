<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\CommonCode;
use App\Models\CodeOfCompanyInfo;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('auth-check');
    }

    public function get_orders(Request $request){
        $search_arr = $request -> except('page');

        $open = $request->query('open');
        $page = $request->query('page');

        if($page>1){
            $open="N";
        }

        $result = OrderData::orderList($search_arr);
        $orders = $result['orders'];

        $commonDate =CommonCode::commonDate();
        $count = $orders->count();
        $col_style='collapsed';
        $show_box='';
        foreach ($search_arr as $value) {
            if (!empty($value)) {
                $show_box='show';
                $col_style='';
            }
        }
        foreach ($orders as $order) {

            if($order->order_quantity>1){
                $order->sub_idx = OrderData::duplicationOrderNumber($order->order_idx);
            }
        }

        return view('Document.document-orders', ['orders' => $orders, 'count' => $count,'commonDate'=>$commonDate,'show_box'=>$show_box,'col_style'=>$col_style, 'open' => $open]);
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

    public function refund_table(Request $request) {
        $data['order'] = OrderData::find($request -> order_idx);
        return view('Document.include.select-refund', $data);
    }
}
