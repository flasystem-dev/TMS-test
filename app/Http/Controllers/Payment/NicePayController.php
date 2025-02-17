<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Controllers\Message\KakaoTalkController;
use App\Http\Controllers\Message\SMSController;
use App\Utils\Common;

use App\Services\Message\MessageService;
use App\Services\Order\PaymentService;
use App\Services\Order\OrderService;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\CodeOfNicePay;
use App\Models\CodeOfCompanyInfo;

class NicePayController extends Controller
{
    // 메서드 라우트 연결 함수
    public function RouteHandelerFunc(Request $request)
    {
        $method = $request->route('method');
        return $this->$method($request);
    }

########################################################################################################################
#################################################### 일반 결제 ###########################################################

    // 일반 결제 ( 주문 )
    public static function payRequest($request, $order_number, $payment_number) {

        $orders = OrderData::where('order_number', $order_number) -> get() -> reverse();

        $brand_name = array(
            'BTCP' => "(꽃총)",
            'BTCS' => "(꽃사)",
            'BTFC' => "(플체)"
        );

        if(count($orders) > 1) {
            $data['goodsName'] = $brand_name[$orders[0] ->brand_type_code].$orders[0] -> delivery -> goods_name . "외 " . count($orders) -1 . "개";
        }else {
            $data['goodsName'] = $brand_name[$orders[0] ->brand_type_code].$orders[0] -> delivery -> goods_name;
        }

        $total_sum_amount = 0;
        foreach ($orders as $order) {
            $total_sum_amount += $order->misu_amount;
        }

        $brand_info = DB::table('code_of_company_info') -> where('brand_type_code', $orders[0] -> brand_type_code) -> first();

        $data['merchantKey']    = $brand_info -> nicepay_key;
        $data['MID']            = $brand_info -> nicepay_mid;
        $data['price']          = $total_sum_amount;
        $data['buyerName']      = $request -> paymentName ?? $orders[0]->orderer_name;
        $data['buyerTel']       = str_replace("-" ,"", $orders[0]->orderer_phone);
        $data['buyerEmail']     = $orders[0]->orderer_email;
        $data['moid']           = $orders[0]->brand_type_code.$orders[0]->order_number."-".$payment_number;                 // 상품주문번호
        $data['returnURL']      = '';                       // 결과페이지(절대경로) - 모바일 결제창 전용
        $data['ReqReserved']    = '';

        $data['ediDate']    = date("YmdHis");
        $data['hashString'] = bin2hex(hash('sha256', $data['ediDate'].$data['MID'].$data['price'].$data['merchantKey'], true));

        $data['targetURL'] = url('Payment/Nice/Pay/payResult');
        return view('payment.NicePay', $data);
    }

    public function payResult(Request $request) {
        $authResultCode     = $request -> AuthResultCode;       // 인증결과 : 0000(성공)
        $authResultMsg      = $request -> AuthResultMsg;        // 인증결과 메시지
        $nextAppURL         = $request -> NextAppURL;           // 승인 요청 URL
        $txTid              = $request -> TxTid;                // 거래 ID
        $authToken          = $request -> AuthToken;            // 인증 TOKEN
        $payMethod          = $request -> PayMethod;            // 결제수단
        $mid                = $request -> MID;                  // 상점 아이디
        $moid               = $request -> Moid;                 // 상점 주문번호
        $amt                = $request -> Amt;                  // 결제 금액
        $reqReserved        = $request -> ReqReserved;          // 상점 예약필드
        $netCancelURL       = $request -> NetCancelURL;         // 망취소 요청 URL
        $authSignature      = $request -> Signature;            // Nicepay에서 내려준 응답값의 무결성 검증 Data

        $brand = substr($moid, 0, 4);

        $key = DB::table('code_of_company_info') -> where('brand_type_code', $brand) -> first() -> nicepay_key;

        $merchantKey = $key;

        // 인증 응답 Signature = hex(sha256(AuthToken + MID + Amt + MerchantKey)
        $authComparisonSignature = bin2hex(hash('sha256', $authToken. $mid. $amt. $merchantKey, true));

        // 인증 응답으로 받은 Signature 검증을 통해 무결성 검증을 진행하여야 합니다.
        if($authResultCode === "0000" && $authSignature == $authComparisonSignature /* 무결성 검사 */){
            /*
            ****************************************************************************************
            * <해쉬암호화> (수정하지 마세요)
            * SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
            ****************************************************************************************
            */
            $ediDate = date("YmdHis");
            $signData = bin2hex(hash('sha256', $authToken . $mid . $amt . $ediDate . $merchantKey, true));

            try{
                $data = Array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'CharSet' => 'utf-8'
                );

                $response = self::reqPost($data, $nextAppURL);

                $result = json_decode($response, true);

                $is_done = PaymentService::updateOrderData_nicePay($result);

                if($is_done) {

                    [$brand_orderNumber, $payment_number] = explode("-", $result['Moid']);
                    $order_number = substr($brand_orderNumber,4);

                    $order = OrderData::where('order_number', $order_number) -> first();
                    
                    // 알림톡 or SMS 전송
                    if($result['ResultCode'] === '3001' || $result['ResultCode'] === '4000') {
                        MessageService::sendMessage($order->order_idx, $payment_number, "pay_complete", $order -> is_alim);

                    }elseif($result['ResultCode'] === '4100') {
                        MessageService::sendMessage($order->order_idx, $payment_number, "VA_guide", $order -> is_alim);
                    }

                    return view('util.window-close', ['msg'=>"주문 완료"]);
                }else {
                    Log::error("[나이스페이] 결제 실패");
                    Log::error("[상점 주문 번호] : " . $moid);
                    Log::error("결과코드 : " . $result['ResultCode']);
                    Log::error("결과메세지 : " . $result['ResultMsg']);
                    return back() -> with('alert', "[에러발생]\n고객센터에 문의해주세요.") -> withInput();
                }

            }catch(\Exception $e) {
                $data = Array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'NetCancel' => '1',
                    'CharSet' => 'utf-8'
                );

                $response = self::reqPost($data, $netCancelURL);

                $result = json_decode($response, true);
                Log::error("[나이스페이] 결제 승인 실패(망 취소)");
                Log::error("에러코드 : " . $result['ErrorCD']);
                Log::error("에러메시지 : " . $result['ErrorMsg']);
                Log::error($e);

                return back()->with('alert', "[에러 발생]\n에러코드 : ".$result['ErrorCD']."\n에러메세지 : ".$result['ErrorMsg']);
            }
        }else if($authComparisonSignature == $authSignature){
            Log::error("[나이스페이] 결제 승인 실패(인증 실패)");
            Log::error("인증코드 : " . $authResultCode);
            Log::error("인증메세지 : " . $authResultMsg);

            return back() -> with('alert', "[인증 실패]\n" . "코드 : " . $authResultCode. "\n" . "실패메세지 : " . $authResultMsg) -> withInput();


        }else {
            Log::error("[나이스페이] 결제 승인 실패(무결성 검사 실패)");
            return back() -> with('alert', "[에러 발생]\n개발팀에 문의해주세요.\n(무결성 검사 실패)") -> withInput();
        }
    }

########################################################################################################################
##################################################### 추가 결제 ##########################################################

    // 추가 결제
    public static function addPayRequest($request, $pid) {

        $payment = OrderPayment::where('payment_pid', $pid)->first();
        $order = OrderData::find($payment->order_idx);
        $brand = DB::table('code_of_company_info') -> where('brand_type_code', $order -> brand_type_code) -> first();

        $brand_name = array(
            'BTCP' => "(꽃총)",
            'BTCS' => "(꽃사)",
            'BTFC' => "(플체)"
        );

        $price = $payment -> payment_amount;
        $moid = $pid;

        $data['goodsName']      = $brand_name[$order ->brand_type_code].$payment->payment_item;
        $data['merchantKey']    = $brand -> nicepay_key;
        $data['MID']            = $brand -> nicepay_mid;
        $data['price']          = $price;
        $data['buyerName']      = $request -> paymentName ?? $order->orderer_name;
        $data['buyerTel']       = str_replace("-" ,"", $order -> orderer_phone);
        $data['buyerEmail']     = $order->orderer_email;
        $data['moid']           = $moid;                    // 상품주문번호
        $data['returnURL']      = '';                       // 결과페이지(절대경로) - 모바일 결제창 전용
        $data['ReqReserved']    = '';

        $data['ediDate']    = date("YmdHis");
        $data['hashString'] = bin2hex(hash('sha256', $data['ediDate'].$data['MID'].$data['price'].$data['merchantKey'], true));

        $data['targetURL'] = url('Payment/Nice/Pay/addPayResult');
        return view('payment.NicePay', $data);
    }

    public function addPayResult(Request $request) {
        $authResultCode     = $request -> AuthResultCode;       // 인증결과 : 0000(성공)
        $authResultMsg      = $request -> AuthResultMsg;        // 인증결과 메시지
        $nextAppURL         = $request -> NextAppURL;           // 승인 요청 URL
        $txTid              = $request -> TxTid;                // 거래 ID
        $authToken          = $request -> AuthToken;            // 인증 TOKEN
        $payMethod          = $request -> PayMethod;            // 결제수단
        $mid                = $request -> MID;                  // 상점 아이디
        $moid               = $request -> Moid;                 // 상점 주문번호
        $amt                = $request -> Amt;                  // 결제 금액
        $reqReserved        = $request -> ReqReserved;          // 상점 예약필드
        $netCancelURL       = $request -> NetCancelURL;         // 망취소 요청 URL
        $authSignature      = $request -> Signature;            // Nicepay에서 내려준 응답값의 무결성 검증 Data

        $payment = OrderPayment::where('payment_pid', $moid)->first();
        $order = OrderData::find($payment->order_idx);

        $key = DB::table('code_of_company_info') -> where('brand_type_code', $order->brand_type_code) -> first() -> nicepay_key;

        $merchantKey = $key;

        // 인증 응답 Signature = hex(sha256(AuthToken + MID + Amt + MerchantKey)
        $authComparisonSignature = bin2hex(hash('sha256', $authToken. $mid. $amt. $merchantKey, true));

        // 인증 응답으로 받은 Signature 검증을 통해 무결성 검증을 진행하여야 합니다.
        if($authResultCode === "0000" && $authSignature === $authComparisonSignature /* 무결성 검사 */){
            /*
            ****************************************************************************************
            * <해쉬암호화> (수정하지 마세요)
            * SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
            ****************************************************************************************
            */
            $ediDate = date("YmdHis");
            $signData = bin2hex(hash('sha256', $authToken . $mid . $amt . $ediDate . $merchantKey, true));

            try{
                $data = Array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'CharSet' => 'utf-8'
                );

                $response = self::reqPost($data, $nextAppURL);

                $result = json_decode($response, true);

                self::insertPaymentJSON($order -> order_idx, $response);

                // 결제 후 주문 정보 업데이트
                $is_done = PaymentService::updateOrderData_nicePay($result);

                if($is_done) {
                    // 알림톡 or SMS 전송
                    if($result['ResultCode'] === '3001' || $result['ResultCode'] === '4000') {
                        MessageService::sendMessage($order->order_idx, $payment->payment_number, "pay_complete", $order -> is_alim);

                    }elseif($result['ResultCode'] === '4100') {
                        MessageService::sendMessage($order->order_idx, $payment->payment_number, "VA_guide", $order -> is_alim);
                    }

                    Session::flash('alert', 1);
                    $url = 'order/order-detail/' . $order->order_idx;
                    return redirect($url);
                }else {
                    Log::error("[나이스페이] 결제 실패");
                    Log::error("[상점 주문 번호] : " . $moid);
                    Log::error("결과코드 : " . $result['ResultCode']);
                    Log::error("결과메세지 : " . $result['ResultMsg']);
                    return back() -> with('alert', "[에러발생]\n고객센터에 문의해주세요.") -> withInput();
                }

            }catch(\Exception $e) {
                $data = Array(
                    'TID' => $txTid,
                    'AuthToken' => $authToken,
                    'MID' => $mid,
                    'Amt' => $amt,
                    'EdiDate' => $ediDate,
                    'SignData' => $signData,
                    'NetCancel' => '1',
                    'CharSet' => 'utf-8'
                );

                $response = self::reqPost($data, $netCancelURL);

                $result = json_decode($response, true);
                Log::error("[나이스페이] 결제 승인 실패(망 취소)");
                Log::error("에러코드 : " . $result['ErrorCD']);
                Log::error("에러메시지 : " . $result['ErrorMsg']);
                Log::error($e);

                return back()->with('alert', "[에러 발생]\n개발팀에 문의해주세요\n에러코드 : ".$result['ErrorCD']."\n에러메세지 : ".$result['ErrorMsg']);
            }
        }else if($authComparisonSignature == $authSignature){
            Log::error("[나이스페이] 결제 승인 실패(인증 실패)");
            Log::error("인증코드 : " . $authResultCode);
            Log::error("인증메세지 : " . $authResultMsg);

            return back() -> with('alert', "[인증 실패]\n" . "코드 : " . $authResultCode. "\n" . "실패메세지 : " . $authResultMsg) -> withInput();


        }else {
            Log::error("[나이스페이] 결제 승인 실패(무결성 검사 실패)");
            return back() -> with('alert', "[에러 발생]\n개발팀에 문의해주세요.\n(무결성 검사 실패)") -> withInput();
        }
    }

########################################################################################################################
################################################# 카드 키인 ##############################################################

    // 수기 주문 - 키인 결제 API
    public static function reqKeyIn(Request $request, $pid) {

        $payment = OrderPayment::where('payment_pid', $pid) ->first();
        $order = OrderData::find($payment->order_idx);

        $comInfo = CodeOfCompanyInfo::firstWhere('brand_type_code', $order -> brand_type_code);

        $payment_number = $payment -> payment_number;
        $mid = $comInfo -> nicepay_mid2;
        $merchantKey = $comInfo -> nicepay_key2;

        $ediDate = date("YmdHis");
        $tidDate = date('ymdHis');
        $rdnNum = random_int(1000,9999);

        $tid = $mid."01"."01".$tidDate.$rdnNum;
        $goodsName = $order -> delivery -> goods_name;
        $moid = $pid;
        $amt = $payment -> payment_amount;
        $cardInterest = 0;                  // 가맹점 분담 무이자 여부 ( 0: 이자 / 1: 무이자 )
        $cardQuota = $request -> cardQuota; // 할부개월 ( 00 : 일시불 / 02 : 2개월 / 03 : 3개월 ... )
        $cardNo = $request -> cardNum1.$request -> cardNum2.$request -> cardNum3.$request -> cardNum4;       // 카드번호
        $cardExpire = $request->exYear.$request->exMonth;               // 유효기간 YYMM

        $plainText = "CardNo={$cardNo}&CardExpire={$cardExpire}";

        $encData = bin2hex(self::aesEncryptSSL($plainText, substr($merchantKey, 0, 16)));

        $signData = bin2hex(hash('sha256', $mid.$amt.$ediDate.$moid.$merchantKey, true));

        try {
            $data = Array(
                'TID' => $tid,
                'MID' => $mid,
                'Moid' => $moid,
                'Amt' => $amt,
                'BuyerName' => $request -> paymentName ?? $order -> orderer_name,
                'BuyerTel' => str_replace("-" ,"", $order -> orderer_phone),
                'GoodsName' => $goodsName,
                'EncData' => $encData,
                'EdiDate' => $ediDate,
                'SignData' => $signData,
                'CardInterest' => $cardInterest,
                'CardQuota' => $cardQuota,
                'CharSet' => 'utf-8'
            );

            $encodedData = array_map(function($value) {
                return iconv('UTF-8', 'EUC-KR', $value);
            }, $data);

            $url = 'https://webapi.nicepay.co.kr/webapi/card_keyin.jsp';

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'
            ])->asForm() -> post($url, $encodedData);

            $result = json_decode($response, true);
            $result['PayMethod'] = "KEYIN";
            $result['MID'] = $tid;

            self::insertPaymentJSON($order -> order_idx, $response);

            // 결제 후 주문 정보 업데이트
            $is_done = PaymentService::updateOrderData_nicePay($result);

            if($is_done) {
                MessageService::sendMessage($order->order_idx, $payment_number, "pay_complete", $order -> is_alim);

                return response() -> json(['status' => "1", 'msg' => "주문 완료"]);
            }else {
                Log::error("[나이스페이] 키인 결제 실패");
                Log::error("[상점 주문 번호] : " . $moid);
                Log::error("결과코드 : " . $result['ResultCode']);
                Log::error("결과메세지 : " . $result['ResultMsg']);
                return response() -> json(['status' => "0", 'msg' => "[에러발생]\n개발팀에 문의하세요.\n(".$result['ResultMsg'].")"]);
            }

        }catch (Exception $e) {
            Log::error("[나이스페이] 키인 결제 에러 발생");
            Log::error($e);

            return response() -> json(['status' => "0", 'msg' => "[에러발생]\n개발팀에 문의하세요.\n(키인 에러 발생)"]);
        }
    }

    // 추가 결제 - 키인 결제 API
    public static function reqKeyIn_add(Request $request, $pid) {

        $payment = OrderPayment::where('payment_pid', $pid)->first();

        $order = OrderData::find($payment->order_idx);

        $comInfo = CodeOfCompanyInfo::firstWhere('brand_type_code', $order -> brand_type_code);

        $mid = $comInfo -> nicepay_mid2;
        $merchantKey = $comInfo -> nicepay_key2;

        $ediDate = date("YmdHis");
        $tidDate = date('ymdHis');
        $rdnNum = random_int(1000,9999);
        $tid = $mid."01"."01".$tidDate.$rdnNum;
        $goodsName = $request -> payment_item;

        $payment_number = $payment -> payment_number;
        $moid = $pid;
        $amt = $request->payment_amount;

        $cardInterest = 0;                  // 가맹점 분담 무이자 여부 ( 0: 이자 / 1: 무이자 )
        $cardQuota = $request -> cardQuota; // 할부개월 ( 00 : 일시불 / 02 : 2개월 / 03 : 3개월 ... )
        $cardNo = $request -> cardNum1.$request -> cardNum2.$request -> cardNum3.$request -> cardNum4;       // 카드번호
        $cardExpire = $request->exYear.$request->exMonth;               // 유효기간 YYMM

        $plainText = "CardNo={$cardNo}&CardExpire={$cardExpire}";

        $encData = bin2hex(self::aesEncryptSSL($plainText, substr($merchantKey, 0, 16)));

        $signData = bin2hex(hash('sha256', $mid.$amt.$ediDate.$moid.$merchantKey, true));

        try {
            $data = Array(
                'TID' => $tid,
                'MID' => $mid,
                'Moid' => $moid,
                'Amt' => $amt,
                'BuyerName' => $request -> paymentName ?? $order -> orderer_name,
                'BuyerTel' => str_replace("-" ,"", $order -> orderer_phone),
                'GoodsName' => $goodsName,
                'EncData' => $encData,
                'EdiDate' => $ediDate,
                'SignData' => $signData,
                'CardInterest' => $cardInterest,
                'CardQuota' => $cardQuota,
                'CharSet' => 'utf-8'
            );

            $encodedData = array_map(function($value) {
                return iconv('UTF-8', 'EUC-KR', $value);
            }, $data);

            $url = 'https://webapi.nicepay.co.kr/webapi/card_keyin.jsp';

            $response = Http::withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'
            ])->asForm() -> post($url, $encodedData);

            self::insertPaymentJSON($order -> order_idx, $response);

            $result = json_decode($response, true);
            $result['PayMethod'] = "KEYIN";
            $result['MID'] = $tid;

            // 결제 후 주문 정보 업데이트
            $is_done = PaymentService::updateOrderData_nicePay($result);

            if($is_done) {
                MessageService::sendMessage($order->order_idx, $payment_number, "pay_complete", $order->is_alim);
                return response()->json(['status' => "1", 'msg' => "주문 완료"]);

            }else {
                Log::error("[나이스페이] 키인 결제 실패");
                Log::error("[상점 주문 번호] : " . $moid);
                Log::error("결과코드 : " . $result['ResultCode']);
                Log::error("결과메세지 : " . $result['ResultMsg']);
                return response() -> json(['status' => false, 'msg' => "[에러발생]\n개발팀에 문의하세요.\n(".$result['ResultMsg'].")"]);
            }

        }catch (Exception $e) {
            Log::error("[나이스페이] 키인 결제 에러 발생");
            Log::error($e);

            return response() -> json(['status' => false, 'msg' => "[에러발생]\n개발팀에 문의하세요.\n(키인 에러 발생)"]);
        }
    }

########################################################################################################################
##################################################### 환불 ##############################################################

    // 환불 API
    public static function payRefund(Request $request) {
        $ip = $_SERVER["REMOTE_ADDR"];

        DB::table('refund_connection_log') -> insert([
            'title' => "TMS-nice",
            'ip' => $ip
        ]);

        $order_idx = $request -> order_idx;
        $refund_handler = $request -> refund_handler;
        $reason = $request -> reason;
        $bank_code = $request -> bank_code;
        $account_number = $request -> account_number;
        $account_number = preg_replace('/[^0-9]/', '', $account_number);
        $account_holder = $request -> account_holder;
        $payment_number = $request -> payment_number;
        $refund_amount = $request -> refund_amount;
        $partial_cancel = $request -> partial_cancel ?? 0;

        $order = OrderData::find($order_idx);
        $payment = OrderPayment::where('order_idx', $order_idx) -> where('payment_number', $payment_number) -> first();
        $brand_info = DB::table('code_of_company_info') -> where('brand_type_code', $order -> brand_type_code) -> first();

        // 다수건 주문 일괄 취소
        if($request->all_cancel==="Y") {
            $partial_cancel = 0;

            $totalPaymentAmount = DB::table('order_data')
                ->where('order_data.order_number', $order->order_number)
                ->leftJoin('order_payment', function ($join) {
                    $join->on('order_data.order_idx', '=', 'order_payment.order_idx')
                        ->where('order_payment.payment_number', 1);
                })
                ->sum('order_payment.payment_amount');

            $refund_amount = $totalPaymentAmount;
        }

        $merchantKey = $brand_info -> nicepay_key;
        $mid = $brand_info -> nicepay_mid;

        // 수기주문 키 변경
        if($payment -> payment_type_code === "PTMN"){
            $merchantKey = $brand_info -> nicepay_key2;
            $mid = $brand_info -> nicepay_mid2;
        }

        $moid = $payment -> payment_pid;
        $cancelMsg = $reason;
        $tid = $payment -> payment_key;
        $cancelAmt = $refund_amount;
        $partialCancelCode = $partial_cancel;

        $ediDate = date("YmdHis");
        $signData = bin2hex(hash('sha256', $mid . $cancelAmt . $ediDate . $merchantKey, true));

        try{
            $data = Array(
                'TID' => $tid,
                'MID' => $mid,
                'Moid' => $moid,
                'CancelAmt' => $cancelAmt,
                'CancelMsg' => iconv("UTF-8", "EUC-KR", $cancelMsg),
                'PartialCancelCode' => $partialCancelCode,
                'EdiDate' => $ediDate,
                'SignData' => $signData,
                'CharSet' => 'utf-8'
            );

            // 가상계좌 환불 계좌 정보 전달
            if($payment->payment_type_code==="PTVA") {
                $data['RefundAcctNo'] = $account_number;
                $data['RefundBankCd'] = $bank_code;
                $data['RefundAcctNm'] = iconv('UTF-8', 'EUC-KR', $account_holder);
            }

            $response = self::reqPost($data, "https://pg-api.nicepay.co.kr/webapi/cancel_process.jsp"); //취소 API 호출
            $result = json_decode($response, true);
            
            // 환불 성공
            if($result['ResultCode']==='2211' || $result['ResultCode']==='2001') {
                try {
                    // 다수건 주문 일괄 취소 처리
                    if($request->all_cancel==="Y") {
                        $refund_orders = OrderData::where('order_number', $order->order_number) -> get();

                        foreach ($refund_orders as $refund_order) {
                            $refund_payment = OrderPayment::where('order_idx', $refund_order -> order_idx) -> where('payment_number', 1) -> first();

                            $refund_amount = $refund_payment -> payment_amount;

                            $refund_order -> refund_amount = (int)$refund_order -> refund_amount + $refund_amount;
                            $refund_order -> pay_amount = (int)$refund_order -> pay_amount - $refund_amount;
                            $refund_order -> admin_memo .= "\n".$reason;
                            $refund_order -> update_ts = NOW();

                            $refund_payment -> refund_amount += $refund_amount;
                            $refund_payment -> payment_amount = 0;
                            $refund_payment -> cancel_amount += $refund_amount;
                            $refund_payment -> refund_handler = $refund_handler;
                            $refund_payment -> payment_state_code = "PSCC";

                            $refund_order -> save();
                            $refund_payment -> save();

                            DB::table('order_log') -> insert([
                                'od_id' => $refund_order -> od_id,
                                'log_by_name' => Auth::user()->name,
                                'log_time' => NOW(),
                                'log_status' => '환불',
                                'log_content' => "환불 처리"
                            ]);

                            DB::table('order_refund') -> insert([
                                'order_idx' => $refund_order -> order_idx,
                                'order_number' => $refund_order -> order_number,
                                'refund_amount' => $refund_amount,
                                'refund_reason' => $reason,
                                'refund_time' => NOW(),
                                'refund_bank' => !empty($bank_code) ? DB::table('code_of_nicepay_card_bank') -> where('code_no',$bank_code) -> where('type', 'BANK') -> first() -> code_name : '',
                                'refund_acount' => $account_number,
                                'refund_acount_name' => $account_holder,
                                'handler' => Auth::user()->name
                            ]);

                            OrderService::amountStateVerification($order->order_idx);
                        }

                    // 일반 주문 환불 처리
                    }else {

                        $order -> refund_amount = (int)$order -> refund_amount + (int)$cancelAmt;
                        $order -> pay_amount = (int)$order -> pay_amount - (int)$cancelAmt;
                        $order -> admin_memo .= "\n".$reason;
                        $order -> update_ts = NOW();

                        $payment -> payment_amount -= (int)$cancelAmt;
                        $payment -> refund_amount += $cancelAmt;
                        $payment -> cancel_amount += $cancelAmt;
                        $payment -> refund_handler = $refund_handler;

                        if($payment -> payment_amount===0) {
                            $payment -> payment_state_code = "PSCC";
                        }

                        $order -> save();
                        $payment -> save();

                        DB::table('order_log') -> insert([
                            'od_id' => $order -> od_id,
                            'log_by_name' => Auth::user()->name,
                            'log_time' => NOW(),
                            'log_status' => '환불',
                            'log_content' => "환불 처리"
                        ]);

                        DB::table('order_refund') -> insert([
                            'order_idx' => $order -> order_idx,
                            'order_number' => $order -> order_number,
                            'refund_amount' => $cancelAmt,
                            'refund_reason' => $reason,
                            'refund_time' => NOW(),
                            'refund_bank' => !empty($bank_code) ? DB::table('code_of_nicepay_card_bank') -> where('code_no',$bank_code) -> where('type', 'BANK') -> first() -> code_name : '',
                            'refund_acount' => $account_number,
                            'refund_acount_name' => $account_holder,
                            'handler' => Auth::user()->name
                        ]);
                        OrderService::amountStateVerification($order->order_idx);
                    }

                    return response()->json(['state'=>true,'message'=>"환불 완료"]);

                }catch (\Exception $e){
                    Log::error("[나이스페이] 환불 후 업데이트 실패");
                    Log::error($e);
                    return "문제 발생";
                }
            }else {
                Log::error("[나이스페이] 환불 실패");
                Log::error("에러코드 : " . $result['ResultCode']);
                Log::error("에러메시지 : " . $result['ResultMsg']);

                return response()->json(['state'=>false,'message'=>$result['ResultMsg']]);
            }
        }catch(Exception $e){
            Log::error("[나이스페이] 환불 에러 발생");
            Log::error($e);
            return response()->json(['state'=>false,'message'=>"환불실패 에러발생"]);
        }
    }

########################################################################################################################
####################################################### 웹훅 ############################################################

    // 웹훅 API ( 가상계좌 입금 완료 )
    public static function webhook(Request $request) {
        $data = $request -> getContent();

        DB::table('test_table') -> insert([
            'test1' => '나이스페이 웹훅',
            'test2' => $data
        ]);
        
        parse_str($data, $response);

        $result = array();

        foreach ($response as $key => $value) {
            $result[$key] = iconv('EUC-KR', 'UTF-8', $value);
        }


        try{
            $payment = OrderPayment::where('payment_pid', $result['MOID'])->first();
            $orders = OrderData::with('payments')->where('order_number', $payment->order_idx) -> get();

            $json = json_encode($result, JSON_UNESCAPED_UNICODE);

            self::insertPaymentJSON($payment -> order_idx, $json);

            PaymentService::updateOrderData_webhook($orders, $payment, $result);

            MessageService::sendMessage_webhook($orders, $payment->payment_number);

            echo "OK";

        }catch (\Exception $e) {
            Log::error("[나이스페이] 웹훅 실패");
            Log::error($e);

            echo "FAIL";
        }

    }


########################################################################################################################
################################################# 보조 함수 ##############################################################

    // API 요청 curl
    protected static function reqPost(Array $data, $url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);					//connection timeout 15
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));	//POST data
        curl_setopt($ch, CURLOPT_POST, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    // AES 암호화 (opnessl)
    protected static function aesEncryptSSL($data, $key){
        $iv = openssl_random_pseudo_bytes(16);
        return @openssl_encrypt($data, "AES-128-ECB", $key, true, $iv);
    }

    // 결제 JSON 저장
    protected static function insertPaymentJSON($order_idx, $response)
    {
        DB::table('payment_result_json') -> insert([
            'order_idx' => $order_idx,
            'result_json' => $response,
            'log_time' => NOW()
        ]);
    }
}
