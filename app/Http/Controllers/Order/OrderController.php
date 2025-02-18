<?php


namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\CommonCode;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CodeOfCompanyInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

use App\Services\Order\OrderService;
use App\Services\Order\OrderIndexService;
use App\Services\Order\OrderDetailListService;
use App\QueryBuilders\OrderDetailList;
use App\Utils\Common;

class OrderController extends Controller
{
########################################################################################################################
##################################################### View #############################################################

    ############################################### 주문 리스트 - 인덱스 ##################################################
    public function ecommerce_orders(Request $request){

        $search = $request -> except('page');

        $data = OrderIndexService::getOrderList($search);

        $data['commonDate'] = CommonCode::commonDate();

        $data = OrderIndexService::countOrderData($data);

        if(self::rateLimit()) {
            return redirect() -> away('https://flabiz.kr/sub/max_traffic_tms.php');
        }

//        dd($data['orders']->items());
//        dd($data['isNotBalju']);
        return view('order.ecommerce-orders', $data);
    }

    ################################################### 주문 로그 #######################################################
    public static function log_view($od_id) {
        $logs = DB::table('order_log') -> where('od_id', '=', $od_id) -> orderBy('log_time', 'desc') -> get();

        return view('order.popup.order_log', ['logs' => $logs]);
    }


    ################################################# 주문 상세 검색 리스트 ###############################################
    public function order_list_detail(Request $request){
        $search = $request -> except('page');

        if(session('order_idx')) {
            $data = OrderDetailListService::getSelectedOrders();
        }else {
            $data = OrderDetailListService::getOrderList($search);
        }

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('order.order-list-detail', $data);
    }

########################################################################################################################
######################################################### 수정 ##########################################################

    ################################################# 주문 - 취소 진행중 ################################################
    // 오픈마켓 취소 요청 처리 중 담당자 이름 입력
    public function cancel_progress(Request $request){
        $idx = $request -> order_idx;
        $name = $request -> send_name;

        $order = OrderDelivery::find($idx);

        $order -> send_name = $name;
        $order -> save();

        return "SUCCESS";
    }

    ################################################# 주문 - 취소 요청 거절 ################################################
    // 오픈마켓 취소요청 -> 정상 진행 변경
    public function cancel_refuse(Request $request) {
        $order_number = $request -> order_number;
        $memo_content = $request -> memo_content;
        $register = $request -> register;

        $order_list = DB::table('order_data') -> where('order_number', '=', $order_number) -> get();

        foreach ($order_list as $item){
            // 취소 관련 요청 들어온 주문 삭제
            if($item->payment_state_code == 'PSRR' || $item->payment_state_code == 'PSCR' || $item->payment_state_code == 'PSER') {
                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                // 관리자 메모 업데이트
            } else {
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['admin_memo' => $item -> admin_memo."\n".$memo_content,
                        'payment_state_code' => 'PSDN',
                        'new_order_yn' => 'N',
                        'update_ts' => NOW()
                    ]);
            }
            DB::table('order_log')
                -> insert([
                    'od_id' => $item -> od_id,
                    'log_by_name' => $register,
                    'log_time' => NOW(),
                    'log_status' => '취소 요청 거절',
                    'log_content' => '[취소 거절]\n'.$memo_content
                ]);
        }
        return "SUCCESS";
    }

    ################################################# 주문 - 결제 상태 -> 취소 완료 수정 ####################################
    public function cancel_complete(Request $request) {
        $order_number = $request -> order_number;
        $memo_content = $request -> memo_content;
        $state_code   = $request -> state_code;
        $register = $request -> register;

        $order_list = DB::table('order_data') -> where('order_number', '=', $order_number) -> get();


        foreach ($order_list as $item){
            // 취소 관련 요청 들어온 주문 삭제
            if($item->payment_state_code == 'PSRR' || $item->payment_state_code == 'PSCR' || $item->payment_state_code == 'PSER') {
                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                // 관리자 메모 업데이트
            } else {

                $payment_state_code_ToComplete = [
                    'PSRR' => 'PSRC',
                    'PSCR' => 'PSCC',
                    'PSER' => 'PSEC'
                ];

                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['admin_memo' => $item -> admin_memo."\n".$memo_content,

                        'payment_state_code' => $payment_state_code_ToComplete[$state_code],
                        'refund_amount' => DB::raw('pay_amount'),
                        'pay_amount' => 0,
                        'new_order_yn' => 'N',
                        'update_ts' => NOW()
                    ]);
            }
            DB::table('order_log')
                -> insert([
                    'od_id' => $item -> od_id,
                    'log_by_name' => $register,
                    'log_time' => NOW(),
                    'log_status' => '결제 상태 변경',
                    'log_content' => '[취소 완료 상태 변경]\n'.$memo_content
                ]);
        }

        return "SUCCESS";
    }

    ############################################## 주문 - 배송 상태 변경 ##################################################
    public function update_deli_state(Request $request)
    {
        $order = OrderData::find($request->order_idx);
        $delivery = OrderDelivery::find($request->order_idx);
        $delivery->delivery_state_code_before = $delivery->delivery_state_code;
        $contents = Common::log_contents_frame("상태 변경", $delivery->delivery_state_code, $request->state);
        $delivery->delivery_state_code = $request->state;
        $delivery->save();

        DB::table('order_log')->insert([
            "od_id" => $order->od_id,
            "log_by_name" => $request->handler,
            "log_time" => NOW(),
            "log_status" => "배송 상태 변경",
            "log_content" => $contents
        ]);

        Session::flash('alert', 1);
        return response()->json(true);
    }

    ############################################# 주문 하이라이트 수정 ####################################################
    public function highlight_orders($state, Request $request) {
        $orders = $request->order_idx;

        foreach ($orders as $idx) {
            $order = OrderData::find($idx);
            if($state=='on'){
                $order -> is_highlight = 1;
            }elseif($state=='off'){
                $order -> is_highlight = 0;
            }
            $order -> save();
        }
        Session::flash('alert', 1);
        return response()->json(true);
    }

    ############################################# 결제 - 일괄 입금 처리 ####################################################
    // 결제상태
    // 입금자명
    // 결제메모
    // 결제일
    public static function deposit_completed(Request $request) {
        $orders = $request->order_idx;
        $deposit_name = $request -> deposit_name;
        $state = $request -> payment_state_code;
        $payment_time = $request -> payment_time;
        $payment_memo = $request -> payment_memo;
        $handler = $request -> handler;

        foreach ($orders as $idx) {
            $order = OrderData::find($idx);
            $payment = OrderPayment::where('order_idx', $idx)->orderBy('payment_number')->first();

            $contents = Common::log_contents_frame("입금자명", $payment->deposit_name , $deposit_name);
            $contents .= Common::log_contents_frame("결제상태", $payment->payment_state_code , $state);
            $contents .= Common::log_contents_frame("결제일", $payment->payment_time , $payment_time);
            $contents .= Common::log_contents_frame("결제메모", $payment->payment_memo , $payment_memo);

            switch ($payment -> payment_state_code) {
                case 'PSDN':
                case 'PSUD':
                    if ($state === "PSCC") {
                        $payment->cancel_amount = $payment->payment_amount;
                        $payment->payment_amount = 0;
                    }
                    break;
                case 'PSCC':
                    switch ($state) {
                        case "PSDN":
                        case "PSUD":
                            $payment -> payment_amount = $payment -> cancel_amount;
                            $payment -> cancel_amount = 0;
                    }
                    break;
            }

            $payment -> payment_state_code = $state;
            $payment -> deposit_name = $deposit_name;
            $payment -> payment_time = $payment_time;
            if(empty($payment -> payment_memo)) {
                $payment -> payment_memo = $payment_memo;
            }else {
                $payment -> payment_memo .= "\n".$payment_memo;
            }
            $payment -> save();

            DB::table('order_log') -> insert([
                "od_id" => $order->od_id,
                "log_by_name" => $handler,
                "log_time" => NOW(),
                "log_status" => "일괄 처리",
                "log_content" => $contents
            ]);

            OrderService::amountStateVerification($idx);
        }

        Session::flash('alert', 1);
        return response()->json(true);
    }

    ############################################# 주문 - 일괄 입력 #######################################################

    // 관리자메모
    public static function batch_input(Request $request) {
        $orders = $request->order_idx;
        $admin_memo = $request -> admin_memo;
        $handler = $request -> handler;

        foreach ($orders as $idx) {
            $order = OrderData::find($idx);

            $contents = Common::log_contents_frame("관리자메모", $order->admin_memo , $admin_memo);

            DB::table('order_log') -> insert([
                "od_id" => $order->od_id,
                "log_by_name" => $handler,
                "log_time" => NOW(),
                "log_status" => "일괄 입력",
                "log_content" => $contents
            ]);

            if(empty($order -> admin_memo)) {
                $order -> admin_memo = $admin_memo;
            }else {
                $order -> admin_memo .= "\n".$admin_memo;
            }

            $order -> save();
//            Common::check_OrderAmount_state($idx);
        }

        Session::flash('alert', 1);
        return response()->json(true);
    }

########################################################################################################################
###################################################### 제거, 삭제 ########################################################

    ################################################# 주문 제거 ##########################################################
    // 일반사용자 주문 조회 안되게
    public function remove_orders(Request $request) {
        $orders = $request->order_idx;

        foreach ($orders as $idx) {
            $order = OrderData::find($idx);
            $order -> is_view = 0;
            $order -> save();

            $contents = Common::log_contents_frame("주문제거", 1 , 0);

            DB::table('order_log') -> insert([
                "od_id" => $order->od_id,
                "log_by_name" => $request->handler,
                "log_time" => NOW(),
                "log_status" => "주문 숨김",
                "log_content" => $contents
            ]);
        }
        Session::flash('alert', 1);
        return response()->json(true);
    }

########################################################################################################################
######################################################### 기능 ##########################################################

    ################################################ 접속 횟수 제한 ######################################################
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

    ################################################## 발주 확인 ########################################################
    // 발주 전 발주 이력이 있는지 빠른 확인
    public static function order_log_check(Request $request) {
        $delivery = OrderDelivery::select('send_id') -> where('order_idx', $request -> order_idx) -> first();

        if(empty($delivery->send_id)) {
            return response() -> json(true);
        }else {
            return response() -> json(false);
        }
    }

    ############################################### 주문리스트 perPage 설정 ###############################################
    public function set_perPage(Request $request) {
        session(['perPage' => $request -> perPage]);
        return response() -> json(true);
    }

    ############################################### 선택 된 주문만 보기 ###################################################
    public function select_orders_view(Request $request) {
        $order_idx = $request->order_idx;

        Session::flash('order_idx', $order_idx);
        return response()->json(true);
    }

    ################################################## 채널명 가져오기 ###################################################
    public static function get_channel($order) {
        $commonCodes = Cache::get('common_codes', collect());
        $openMarkets = $commonCodes->filter(function ($item) {
            return $item->parent_code === 'ML';
        })->pluck('code')->toArray();

        switch($order->brand_type_code) {
            case 'BTCS':
                $url = "https://" . $order -> domain . ".flapeople.com";

                return [
                    'name' => $order->rep_name ?? "없음",
                    'mall_name' => $order->mall_name ?? "없음",
                    'URL' => $url
                ];

            case 'BTFC':
                $url = "https://" . $order -> domain . ".flachain.net";

                return [
                    'name' => $order->rep_name ?? "없음",
                    'mall_name' => $order->mall_name ?? "없음",
                    'URL' => $url
                ];
            default:
                if(Str::contains($order->mall_code, $openMarkets)) {
                    $url = Common::get_admin_url($order->mall_code);
                    return [
                        'name' => CommonCodeName($order->mall_code),
                        'mall_name' => CommonCodeName($order->mall_code),
                        'URL' => $url
                    ];
                }
                $url = "https://" . $order -> domain . ".flabiz.kr";
                return [

                    'name' => $order -> name ?? "없음",
                    'mall_name' => $order -> mall_name ?? "없음",
                    'URL' => $url
                ];
        }
    }

    #################################################  미수 가능 여부 가져오기  ############################################
    public static function get_isCredit($order) {
        $commonCodes = Cache::get('common_codes', collect());
        $openMarkets = $commonCodes->filter(function ($item) {
            return $item->parent_code === 'ML';
        })->pluck('code')->toArray();

        switch($order->brand_type_code) {
            case 'BTCS';
            case 'BTFC';
                return $order -> is_credit ?? false;
            default:
                if(Str::contains($order->mall_code, $openMarkets)) {
                    return false;
                }
                return $order -> is_credit ?? false;
        }
    }
}
