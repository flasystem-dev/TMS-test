<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;

class TosspaymentsController extends Controller
{

    // 주문 위젯
    public static function get_widget($od_id) {
        $order = OrderData::where('od_id', $od_id) -> first();
        $delivery = OrderDelivery::where('order_idx', $order -> order_idx) -> first();

        // 토스페이 위젯으로 연결
        return view('payment.Tosspayments', ['order' => $order, 'delivery' => $delivery]);
    }
    
    // 토스페이 성공
    public static function success(Request $request)
    {
        $paymentKey = $request->paymentKey;
        $orderId = $request->orderId;
        $amount = $request->amount;
        $handler = $request -> handler;
//        $paymentType = $request->paymentType;
        if(Str::contains($orderId, 'A' )) {
            return self::add_price_success($paymentKey, $orderId, $amount, $handler);
        }else {
            return self::order_success($paymentKey, $orderId, $amount, $handler);
        }
    }

    // 가상계좌 웹훅
    public static function webhook(Request $request) {
        $data = $request -> getContent();
        $response = json_decode($data, true);
        OrderData::update_PTVA($response);
    }

    // 취소 요청
    public static function complain(Request $request) {

        $ip = $_SERVER["REMOTE_ADDR"];

        DB::table('refund_connection_log') -> insert([
            'title' => "TMS-toss",
            'ip' => $ip
        ]);

        $order_idx = $request -> order_idx;
        $register = $request -> register_name;
        $reason = $request -> reason;
        $bank_code = $request -> bank_code;
        $account_number = $request -> account_number;
        $account_number = preg_replace('/[^0-9]/', '', $account_number);
        $account_holder = $request -> account_holder;
        $payment_number = $request -> payment_number;


        $order = OrderData::find($order_idx);
        $delivery = OrderDelivery::find($order->order_idx);
        $payment = OrderPayment::where('order_idx', $order->order_idx) -> where('payment_number', $payment_number) -> first();
        $pay_amount = $order -> pay_amount;
        $brand_secretKey = DB::table('code_of_toss_key') -> where('toss_mid', '=', $payment -> payment_mid) -> first();

        $cancelAmount = 0; // 취소할 금액 ( $data 에 없으면 전액 )

        $secret_key = base64_encode($brand_secretKey -> toss_secret_key.":");

        // 가상계좌의 경우
        if($order -> payment_type_code == 'PTVA') {
            $data = [
                'cancelReason' => $reason,
                // 환불 받을 계좌 정보
                'refundReceiveAccount' => [
                    'bank' => $bank_code,
                    'accountNumber' => $account_number,
                    'holderName' => $account_holder
                ]
            ];
        } else {
            $data = [
                'cancelReason' => $reason
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'basic '.$secret_key
        ])->post('https://api.tosspayments.com/v1/payments/'.$payment->payment_key.'/cancel', $data);

        $res = $response-> json();
        if($response->ok()) {
            $cancelAmount = $res['cancels'][0]['cancelAmount'];

            $order -> payment_state_code = "PSCC";
            $order -> pay_amount -= $cancelAmount;
            $order -> refund_amount = $cancelAmount;
            $order -> admin_memo = $reason;
            $order -> update_ts = NOW();

            $delivery -> delivery_state_code_before = $delivery -> delivery_state_code;
            $delivery -> delivery_state_code = "DLCC";

            $payment -> payment_amount -= $cancelAmount;
            $payment -> refund_amount += $cancelAmount;
            $payment -> cancel_amount += $cancelAmount;
            $payment -> refund_handler = $register;
            $payment -> payment_state_code = "PSCC";

            $order -> save();
            $delivery -> save();
            $payment -> save();

            DB::table('order_log') -> insert([
                'od_id' => $order -> od_id,
                'log_by_name' => $register,
                'log_time' => NOW(),
                'log_status' => '환불',
                'log_content' => "환불 처리"
            ]);

            DB::table('order_refund') -> insert([
                'order_idx' => $order -> order_idx,
                'order_number' => $order -> order_number,
                'refund_amount' => $pay_amount,
                'refund_reason' => $reason,
                'refund_time' => NOW(),
                'refund_bank' => !empty($bank_code) ? DB::table('code_of_toss_card_bank') -> where('code',$bank_code) -> first() -> bank : '',
                'refund_acount' => $account_number,
                'refund_acount_name' => $account_holder,
                'handler' => $register
            ]);


            return response()->json(['state'=>true,'message'=>"환불 완료"]);
        } else {
            return response()->json(['state'=>false,'message'=>$res['message']]);
        }
    }

    // 현금영수증
    function cashReceipt(Request $request) {
        $orderId = $request -> od_id;
        $customerIdentityNumber = $request -> number;
        $type = $request -> type;

        $order = OrderData::where('od_id', $orderId) -> first();
        $delivery = OrderDelivery::find($order->order_idx);
        $amount = $order -> pay_amount;
        $orderName = $delivery -> goods_name;

        $brand_secretKey = DB::table('code_of_company_info') -> select('toss_secret_api_key') -> where('brand_type_code', '=', $order -> brand_type_code) -> first();
        $secret_key = base64_encode($brand_secretKey -> toss_secret_api_key.":");

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$secret_key
        ])->post('https://api.tosspayments.com/v1/cash-receipts', [
            'amount' => $amount,
            'orderId' => $orderId,
            'orderName' => $orderName,
            'customerIdentityNumber' => $customerIdentityNumber,
            'type' => $type
        ]);

        $res = $response -> json();
        if($response -> ok()){
            $order -> payment_receipt_url = $res['receiptUrl'];
            $order -> save();

            return "[발급 완료]";
        }

        return "[발급 실패]".$res['message'];
    }

    // 추가금 위젯
    public static function add_pay_widget($idx) {
        $order = OrderData::find($idx);
        $delivery = OrderDelivery::firstWhere('order_idx', $idx);

        // 구매 금액 수정
        $order -> total_amount = $order -> dataAdd -> misu_amount;
        
        // 추가금 구분
        $order -> od_id = 'A' . $order -> od_id;

        $delivery -> goods_name = $order -> dataAdd -> add_price_name;

        // 토스페이 위젯으로 연결
        return view('payment.Tosspayments', ['order' => $order, 'delivery' => $delivery]);
    }

    // 일반 주문
    public static function order_success($paymentKey, $orderId, $amount, $handler) {

        $order = OrderData::where('od_id', $orderId) -> first();
        $brand_secretKey = DB::table('code_of_company_info') -> select('toss_secret_key') -> where('brand_type_code', '=', $order -> brand_type_code) -> first();

        $secret_key = base64_encode($brand_secretKey -> toss_secret_key.":");

        $response = Http::withHeaders([
            'Authorization' => 'basic '.$secret_key
        ])->post('https://api.tosspayments.com/v1/payments/confirm', [
            'paymentKey' => $paymentKey,
            'orderId' => $orderId,
            'amount' => $amount
        ]);

        if($response -> ok()){
            $res_json = $response -> json();
            OrderData::update_data_after_pay($response);

            if($res_json['status'] == 'DONE'){

                DB::table('order_log')
                    -> insert([
                        'od_id' => $order -> od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '일반 결제',
                        'log_content' => '일반 결제 완료'
                    ]);

                return view('util.Window-Close', ['msg' => '[결제 완료]']);
            } elseif($res_json['method'] == '가상계좌') {

                DB::table('order_log')
                    -> insert([
                        'od_id' => $order -> od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '일반 결제',
                        'log_content' => '일반 결제 가상계좌 등록'
                    ]);
                
                return view('util.Window-Close', ['msg' => '[주문 완료] 가상 계좌 입금 요망']);
            } else {
                DB::table('order_log')
                    -> insert([
                        'od_id' => $order -> od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '일반 결제',
                        'log_content' => '일반 결제 실패'
                    ]);
                return view('util.Window-Close', ['msg' => '[결제 실패]']);
            }
        } else {
            return view('util.Window-Close', ['msg' => '[결제 실패]']);
        }
    }

    // 추가금 주문
    public static function add_price_success($paymentKey, $orderId, $amount, $handler) {
        $od_id = str_replace('A', '', $orderId);

        $order = OrderData::firstWhere('od_id', $od_id);
        $brand_secretKey = DB::table('code_of_company_info') -> select('toss_secret_key') -> where('brand_type_code', '=', $order -> brand_type_code) -> first();

        $secret_key = base64_encode($brand_secretKey -> toss_secret_key.":");

        $response = Http::withHeaders([
            'Authorization' => 'basic '.$secret_key
        ])->post('https://api.tosspayments.com/v1/payments/confirm', [
            'paymentKey' => $paymentKey,
            'orderId' => $orderId,
            'amount' => $amount
        ]);

        if($response -> ok()){
            $res_json = $response -> json();
            OrderDataAdd::update_orderDataAdd($response);

            if($res_json['status'] == 'DONE'){

                DB::table('order_log')
                    -> insert([
                        'od_id' => $od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '추가 결제',
                        'log_content' => '추가 결제 완료'
                    ]);
                
                return view('util.Window-Close', ['msg' => '[결제 완료]']);
            } elseif($res_json['method'] == '가상계좌') {

                DB::table('order_log')
                    -> insert([
                        'od_id' => $od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '추가 결제',
                        'log_content' => '추가 결제 가상계좌 등록'
                    ]);
                
                return view('util.Window-Close', ['msg' => '[주문 완료] 가상 계좌 입금 요망']);
            } else {
                DB::table('order_log')
                    -> insert([
                        'od_id' => $od_id,
                        'log_by_name' => $handler,
                        'log_time' => NOW(),
                        'log_status' => '추가 결제',
                        'log_content' => '추가 결제 실패'
                    ]);
                return view('util.Window-Close', ['msg' => '[결제 실패]']);
            }
        } else {
            return view('util.Window-Close', ['msg' => '[결제 실패]']);
        }
    }
}
