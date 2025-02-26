<?php
namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use App\Http\Controllers\Message\KakaoTalkController;
use App\Http\Controllers\Message\SMSController;
use App\Http\Controllers\Payment\NicePayController;
use App\Utils\Common;

use App\Services\Order\OrderService;
use App\Services\Order\OrderDetailService;

use App\Models\CodeOfCompanyInfo;
use App\Models\CommonCode;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderDataDeleted;

use App\Models\Vendor;

use App\Http\Controllers\Dev\DevController;

class OrderDetailController extends Controller
{

########################################################################################################################
##################################################### View #############################################################

    ############################################## 주문 상세 뷰 ##########################################################
    public function order_detail($order_idx){
        $order = OrderData::with('delivery', 'payments', 'item', 'vendor', 'pass') -> where('order_idx', $order_idx) -> first();
        OrderDetailService::isNotNew($order);

        $order -> channel = $order->channel();


        $order->item_url = CodeOfCompanyInfo::goodsUrl($order->mall_code,$order->brand_type_code);

        if(strpos($order->options_string_display," || ")!==false){
            $order->option_str_arr  =explode(" || ",$order->options_string_display);
        }

        $data['order'] = $order;

        if(!$order -> item) {
            DevController::upsert_orderDataItem_auto($order_idx);
            $order = OrderData::with('delivery', 'payments', 'item', 'vendor', 'pass') -> where('order_idx', $order_idx) -> first();
            if($order -> item){
                DB::table('test_log2')->insert([
                    'test1' => '과거 주문 자동 변환',
                    'test2' => $order_idx
                ]);

                return redirect("order/order-detail/{$order_idx}");
            }
        }
        return view('order.order-detail', $data);
    }

    ########################################### 벤더 정보 가져오기 ########################################################

    // 사업자 변경을 위한 사업자 정보 전달 뷰
    public function get_vendors(Request $request) {
        $vendors = Vendor::where('brand_type', "like", "%".$request->brand."%") -> get();
        $data['vendors'] = $vendors;
        $data['vendor_idx'] = $request -> vendor_idx;
        return view('order.include.vendor-option', $data);
    }

    #########################################  알림톡 템플릿 뷰 보내기 #####################################################
    public function get_template_data(Request $request) {
        $data = $request -> all();

        $order_idx = $data['order_idx'];
        $template_type = $data['template_type'] ?? "order_check";
        $payment_number = $data['payment_number'] ?? "total";

        $order = OrderData::find($order_idx);
        $templateCode = DB::table('popbill_template_numberInUse') -> where('brand_type_code', $order->brand_type_code) -> value($template_type);

        $data['template'] = DB::table('popbill_template_info') -> where('templateCode', $templateCode) -> first();
        $values = json_decode($data['template'] -> values, true);
        $data['variables'] = self::set_variable($order, $values, $payment_number, $template_type);

        return view('order.include.alim-talk', $data);
    }

    ######################################### 환불 모달 테이블 뷰 보내기 ###################################################
    public function refund_table(Request $request) {
        $data['order'] = OrderData::find($request -> order_idx);
        $data['payment'] = OrderPayment::where('order_idx', $request -> order_idx)->where('payment_number', $request->payment_number) -> first();
        $pg = $data['payment'] -> payment_pg;

        if($pg === "toss") {
            $data['banks'] = DB::table('code_of_toss_card_bank')->select('code_no', 'code_name')->where('type', 'BANK')->get();
        } elseif($pg === "nice") {
            $data['banks'] = DB::table('code_of_nicepay_card_bank') -> select('code_no', 'code_name') -> where('type', 'BANK') -> get();
        }

        return view('order.include.select-refund', $data);
    }

########################################################################################################################
######################################################## 등록 ###########################################################

    ############################################## 추가결제 등록 #########################################################
    public function add_payment(Request $request) {
        $input  = $request -> all();

        $payment_number = OrderPayment::where('order_idx', $request -> order_idx) -> max('payment_number') + 1;
        $order = OrderData::find($request -> order_idx);

        $pid = $order->brand_type_code.$order->order_number."-".$payment_number;
        if($order->order_quantity > 1) {
            $sub_idx = substr((string) $order->order_idx, -2);
            $pid .= "-".$sub_idx;
        }

        $input['payment_pid'] = $pid;
        $input['payment_number'] = $payment_number;

        OrderPayment::create($input);

        $order = OrderData::find($input['order_idx']);

        $contents = "<p class='column_container'>";
        $contents .= "<span class='column_log_text'>[결제 번호]</span>";
        $contents .= "<span class='origin_value_text'>{$payment_number}</span></p>";

        DB::table('order_log') -> insert([
            "od_id" => $order->od_id,
            "log_by_name" => Auth::user()->name,
            "log_time" => NOW(),
            "log_status" => "결제 추가",
            "log_content" => $contents
        ]);

        OrderService::amountStateVerification($input['order_idx']);

        switch ($input['payment_type_code']) {
            case 'PTMN':
                return NicePayController::reqKeyIn_add($request, $pid);
            case 'PTVA':
                return NicePayController::addPayRequest($request, $pid);
            case 'PTCD':
            case 'PTDP':
            case 'PTOP':
            case 'PTDB':
                Session::flash('alert', 1);
                return response() -> json(['status'=>1, 'msg'=>"추가결제 등록"]);
        }
    }

########################################################################################################################
######################################################## 수정 ###########################################################

    ############################################# 주문 정보 수정 ##########################################################
    public function order_update(Request $request){

        $input = $request->all();
        OrderDetailService::putOrderData($input);

        OrderService::amountStateVerification($input['order_idx']);

        Session::flash('update', '1');
        return response() -> json(1);
    }

    ########################################### 주문 상품 변경 ###########################################################
    public static function change_order_product(Request $request) {

        $order = OrderData::with('delivery', 'item') -> where('order_idx', $request -> order_idx) -> first();

        // orderProduct DTO
        $orderProduct = OrderService::makeOrderProduct($request);

        // 상품 변경 로그
        $content = OrderDetailService::makeOrderLog_fetchItem($order->item, $orderProduct);
        // 상품 변경
        OrderDetailService::change_product($order, $orderProduct);

        DB::table('order_log') -> insert([
            "od_id" => $order->od_id,
            "log_by_name" => Auth::user()->name,
            "log_time" => NOW(),
            "log_status" => "상품 변경",
            "log_content" => $content
        ]);

        OrderService::amountStateVerification($order->order_idx);

        Session::flash('update', '1');
        return response()->json(true);
    }

    ############################################## 사업자 변경 ###########################################################
    public function change_vendor(Request $request) {
        $input = $request -> all();
        $input['mall_code'] = $request -> change_vendor;
        $input['admin_memo'] = $request -> add_admin_memo;
        $input['orderer_mall_id'] = $request -> orderer_mall_id ?? "";

        $order = OrderData::find($request->order_idx);
        $order -> fill($input);

        Common::order_log($order, '', $input);

        if($order -> save()) {
            Session::flash('update', '1');
            return response() -> json(true);
        }else {
            return response() -> json(false);
        }
    }

    ########################################## 주문 - 결제 상태 변경 ######################################################
    public function change_payment_state(Request $request) {
        $state = $request -> state;
        $order_idx = $request -> order_idx;
        $handler = Auth::user()->name;

        $order = OrderData::find($order_idx);
        $before_state = $order -> payment_state_code;

        $order -> payment_state_code = $state;

        $order -> save();

        $contents = Common::log_contents_frame('결제상태', CommonCodeName($before_state) , CommonCodeName($state));

        DB::table('order_log')
            -> insert([
                'od_id' => $order -> od_id,
                'log_by_name' => $handler,
                'log_time' => NOW(),
                'log_status' => '주문 상태 변경',
                'log_content' => $contents
            ]);

        Session::flash('update', '1');
        return response()->json(['state'=>true, 'url'=> url('/order/order-detail')."/".$order->order_idx]);
    }

    ################################################### 결제 - 결제 상태 변경 #############################################
    public function change_payment_state_code(Request $request) {
        $contents = "<p class='column_container'>";
        $contents .= "<span class='column_log_text'>[결제 번호]</span>";
        $contents .= "<span class='origin_value_text'>{$request -> payment_number}</span></p>";

        $payment = OrderPayment::where('order_idx', $request -> order_idx) -> where('payment_number', $request -> payment_number) -> first();

        $contents .= Common::log_contents_frame("결제상태", CommonCodeName($payment->payment_state_code), CommonCodeName($request -> payment_state_code));

        switch ($payment -> payment_state_code) {
            case 'PSDN':
            case 'PSUD':
                if ($request->payment_state_code === "PSCC") {
                    $payment->cancel_amount = $payment->payment_amount;
                    $payment->payment_amount = 0;
                }
                break;
            case 'PSCC':
                switch ($request -> payment_state_code) {
                    case "PSDN":
                    case "PSUD":
                        $payment -> payment_amount = $payment -> cancel_amount;
                        $payment -> cancel_amount = 0;
                }
                break;
        }

        if(($request->payment_state_code === "PSDN") && empty($payment->payment_time)) {
            $payment -> payment_time = NOW();
        }

        $payment -> payment_state_code = $request -> payment_state_code;
        $payment -> save();

        $order = OrderData::find($request->order_idx);

        DB::table('order_log') -> insert([
            "od_id" => $order->od_id,
            "log_by_name" => Auth::user()->name,
            "log_time" => NOW(),
            "log_status" => "결제 변경",
            "log_content" => $contents
        ]);

        OrderService::amountStateVerification($order->order_idx);

        Session::flash('update', 1);
        return true;
    }

    ############################################### 결제 - 결제 수단 변경 #################################################
    public function change_payment_type(Request $request) {
        $contents = "<p class='column_container'>";
        $contents .= "<span class='column_log_text'>[결제 번호]</span>";
        $contents .= "<span class='origin_value_text'>{$request -> payment_number}</span></p>";

        $payment = OrderPayment::where('order_idx', $request -> order_idx) -> where('payment_number', $request -> payment_number) -> first();
        $order = OrderData::find($request->order_idx);

        $contents .= Common::log_contents_frame("결제수단", CommonCodeName($payment->payment_type_code), CommonCodeName($request -> payment_type_code));

        $min_payment = OrderPayment::min('payment_number');

        if($request -> payment_number == $min_payment) {
            $order -> payment_type_code = $request -> payment_type_code;
        }
        $payment -> payment_type_code = $request -> payment_type_code;
        $payment -> save();


        DB::table('order_log') -> insert([
            "od_id" => $order->od_id,
            "log_by_name" => Auth::user()->name,
            "log_time" => NOW(),
            "log_status" => "결제 변경",
            "log_content" => $contents
        ]);

        $order -> save();

        Session::flash('update', 1);
        return true;
    }

    #################################################### 결제 정보 수정 ##################################################
    public function update_payments(Request $request) {
        $data = $request -> all();

        $payment = OrderPayment::where('order_idx', $data['order_idx']) -> where('payment_number', $data['payment_number']) -> first();
        $payment -> fill($data);

        $content = Common::payment_log_content($payment);
        $payment -> save();

        $order = OrderData::find($data['order_idx']);

        DB::table('order_log') -> insert([
            'od_id' => $order->od_id,
            'log_by_name' => Auth::user()->name,
            'log_time' => NOW(),
            'log_status' => '결제 수정',
            'log_content' => $content
        ]);

        OrderService::amountStateVerification($request->order_idx);

        Session::flash('update', 1);
        return response() -> json(true);
    }

    ############################################### 발주 정보 수정 #######################################################
    public function update_baljuAmount(Request $request) {
        OrderDetailService::update_vendorAmount($request -> all());

        Session::flash('update', 1);
        return response() -> json(true);
    }

########################################################################################################################
################################################### 제거, 삭제 ###########################################################

    ################################################# 주문 삭제 ##########################################################
    public static function delete_order($order_idx){
        $order = OrderData::find($order_idx);
        $delivery = OrderDelivery::find($order_idx);

        $deleted = new OrderDataDeleted();
        $deleted -> fill($order->toArray());
        $deleted -> fill($delivery->toArray());
        $deleted -> log_name = Auth::user()->name;

        if(DB::table('order_payment') -> where('order_idx', $order_idx) -> exists()) {
            $payments = OrderPayment::where('order_idx', $order_idx) -> get();
            $deleted -> order_cart = $payments -> toJson();

            DB::table('order_payment') -> where('order_idx', $order_idx) -> delete();
        }

        $deleted -> save();
        $order -> delete();
        $delivery -> delete();

        return "주문 삭제 완료";
    }

    ################################################ 결제 삭제 ##########################################################
    public function delete_payment(Request $request) {
        $data = $request -> all();
        $payment = OrderPayment::where('order_idx', $data['order_idx']) -> where('payment_number', $data['payment_number']) -> first();

        $attr_arr = $payment -> attributesToArray();
        $content = '';

        foreach ($attr_arr as $key => $value) {
            $value = trim($value);
            $content .= "<p class='column_container'>";
            $content .= "<span class='column_log_text'>[" . $key . "]</span>";
            $content .= "<span class='origin_value_text'>" ;
            $content .= $value;
            $content .= "</span>";
            $content .= "</p>";
        }

        if(!empty($content)) {
            $title  = "<p class='column_container'>";
            $title .= "<span class='column_log_text'>[결제 번호]</span>";
            $title .= "<span class='origin_value_text'>{$payment -> payment_number}</span></p>";
            $content = $title . $content;
        }

        $payment -> delete();

        $order = OrderData::find($data['order_idx']);
        $min_payment_state = OrderPayment::where('order_idx', $data['order_idx'])->orderBy('payment_number', 'asc')->value('payment_state_code');

        $order -> payment_state_code = $min_payment_state;
        $order -> save();

        DB::table('order_log') -> insert([
            'od_id' => $order->od_id,
            'log_by_name' => Auth::user()->name,
            'log_time' => NOW(),
            'log_status' => '결제 삭제',
            'log_content' => $content
        ]);

        OrderService::amountStateVerification($order->order_idx);

        Session::flash('update', 1);
        return response() -> json(true);
    }

########################################################################################################################
#################################################### 보조 함수 ###########################################################

    ######################################### 알림톡 v2 커스텀 변수 입력 ###################################################
    public static function set_variable($order, $values, $payment_number, $template_type) {
        $variable = array();
        if($values){
            foreach ($values as $value) {
                $column = $value['column'];

                // 배송 날짜의 경우 + 시간 추가
                if($value['column'] === "delivery_date") {
                    $str = DB::table($value['table']) -> select($column) -> where('order_idx', $order->order_idx) -> first() -> $column;
                    $str .= " " . DB::table($value['table']) -> select('delivery_time') -> where('order_idx', $order->order_idx) -> first() -> delivery_time;
                }
                // 금액이 들어갈 경우 3자리 단위 콤마, 원 추가
                elseif($value['column'] === "payment_amount"){
                    if($payment_number==="total") {
                        $str = number_format($order-> total_amount) . "원";
                    }else {
                        $payment = OrderPayment::where('order_idx', $order->order_idx)->where('payment_number', $payment_number) -> first();
                        $str = number_format($payment-> payment_amount) . "원";
                    }
                }
                // 가상계좌 / 무통장안내
                elseif($value['column'] === "bank_num") {
                    if($template_type === "without_bank_account") {
                        $str = DB::table('code_of_company_info') -> select('bank_account_info') -> where('brand_type_code', $order->brand_type_code) -> value('bank_account_info');
                    }else {
                        $payment = OrderPayment::where('order_idx', $order->order_idx)->where('payment_number', $payment_number) -> first();
                        if(!empty($payment -> bank_num)) {
                            $num = str_split($payment -> bank_num, 4);
                            $bank_num = implode(" ",$num);
                            $str = $payment -> bank_name . " / " . $bank_num;
                        }else {
                            $str = "";
                        }
                    }
                }
                // 회사 정보 테이블
                elseif($value['table']=="code_of_company_info") {
                    $str = DB::table($value['table']) -> select($column) -> where('brand_type_code', $order->brand_type_code) -> first() -> $column;
                }
                // 배송 사진 url 변경
                elseif($value['column'] === "delivery_photo") {
                    $str = "https://flasystem.flabiz.kr/delivery/photo/" . $order->od_id;
                }
                else {
                    $str = DB::table($value['table']) -> select($column) -> where('order_idx', $order->order_idx) -> first() -> $column;
                }
                $variable[$value['variable']] = $str;
            }
        }
        return $variable;
    }
}
