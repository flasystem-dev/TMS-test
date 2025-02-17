<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\CommonCode;
use App\Models\TalkTemplate;

use Linkhub\LinkhubException;
use Linkhub\Popbill\JoinForm;
use Linkhub\Popbill\CorpInfo;
use Linkhub\Popbill\ContactInfo;
use Linkhub\Popbill\ChargeInfo;
use Linkhub\Popbill\PopbillException;
use Linkhub\Popbill\PopbillMessaging;
use Linkhub\Popbill\ENumMessageType;
use Linkhub\Popbill\RefundForm;
use Linkhub\Popbill\PaymentForm;

class SMSController extends Controller
{
    public function __construct()
    {
        // 통신방식 설정
        if(!defined('LINKHUB_COMM_MODE')) {
            define('LINKHUB_COMM_MODE', config('popbill.LINKHUB_COMM_MODE'));
        }

        // 문자 서비스 클래스 초기화
        $this->PopbillMessaging = new PopbillMessaging(config('popbill.LinkID'), config('popbill.SecretKey'));

        // 연동환경 설정값, true-개발용, false-상업용
        $this->PopbillMessaging->IsTest(config('popbill.IsTest'));

        // 인증토큰의 IP제한기능 사용여부, true-사용, false-미사용, 기본값(true)
        $this->PopbillMessaging->IPRestrictOnOff(config('popbill.IPRestrictOnOff'));

        // 팝빌 API 서비스 고정 IP 사용여부, true-사용, false-미사용, 기본값(false)
        $this->PopbillMessaging->UseStaticIP(config('popbill.UseStaticIP'));

        // 로컬서버 시간 사용 여부, true-사용, false-미사용, 기본값(true)
        $this->PopbillMessaging->UseLocalTimeYN(config('popbill.UseLocalTimeYN'));
    }

    // HTTP Get Request URI -> 함수 라우팅 처리 함수
    public function RouteHandelerFunc(Request $request)
    {
        $APIName = $request->route('APIName');
        return $this->$APIName();
    }

    /**
     * 메시지 길이(90byte)에 따라 단문/장문(SMS/LMS)을 자동으로 인식하여 1건의 메시지를 전송을 팝빌에 접수합니다.
     * - https://developers.popbill.com/reference/sms/php/api/send#SendXMS
     */
    public function SendXMS()
    {

        // 팝빌 회원 사업자번호, "-"제외 10자리
        $CorpNum = '1234567890';

        // 예약전송일시(yyyyMMddHHmmss) ex)20151212230000, null인경우 즉시전송
        $ReserveDT = null;

        // 광고문자 전송여부
        $adsYN = false;

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        $Messages[] = array(
            'snd' => '',  // 발신번호, 팝빌에 등록되지 않은 발신번호 기재시 오류처리
            'sndnm' => '발신자명',   // 발신자명
            'rcv' => '',   // 수신번호
            'rcvnm' => '수신자성명',  // 수신자성명
            'msg' => '장문 메시지 내용 장문으로 보내는 기준은 메시지 길이을 기준으로 90byte이상입니다. 2000byte에서 길이가 조정됩니다.' // 메시지 내용
        );

        // 팝빌 회원 아이디
        $UserID = 'testkorea';

        try {
            $receiptNum = $this->PopbillMessaging->SendXMS($CorpNum, '', '', '', $Messages, $ReserveDT, $adsYN, $UserID, '', '', $RequestNum);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
            return view('PResponse', ['code' => $code, 'message' => $message]);
        }
        return view('ReturnValue', ['filedName' => 'XMS 단건전송 접수번호(receiptNum)', 'value' => $receiptNum]);
    }

    // 주문 실패 문자
    public function order_fail(Request $request) {
        $msg = '[에러 발생] 주문 확인 요망';

        $brand = 'BTCP';

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);


        // 팝빌 회원 사업자번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 예약전송일시(yyyyMMddHHmmss) ex)20151212230000, null인경우 즉시전송
        $ReserveDT = null;

        // 광고문자 전송여부
        $adsYN = false;

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        $Messages[] = array(
            'snd' => $bs_info -> shop_send_tel,  // 발신번호, 팝빌에 등록되지 않은 발신번호 기재시 오류처리
            'sndnm' => '개발팀',   // 발신자명
            'rcv' => '010-3655-9079',   // 수신번호
            'rcvnm' => '마케팅팀',  // 수신자성명
            'msg' => $msg // 메시지 내용
        );

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        try {
            $receiptNum = $this->PopbillMessaging->SendXMS($CorpNum, '', '', '', $Messages, $ReserveDT, $adsYN, $UserID, '', '', $RequestNum);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
            return response() -> json(['code' => $code, 'message' => $message]);
        }
    }


    // SMS 전송 ( 커스텀 )
    public function sendSMS(Request $request)
    {

        $sender = $request->sender;
        $receive = preg_replace('/[^0-9]/', '', $request->receive_phone);
        $message = $request->message;

        $sender_data = DB::table('popbill_sms_sender')->where('id', $sender)->first();

        // 팝빌 회원 사업자번호, "-"제외 10자리
        $CorpNum = $sender_data->business_number;

        // 예약전송일시(yyyyMMddHHmmss) ex)20151212230000, null인경우 즉시전송
        $ReserveDT = null;

        // 광고문자 전송여부
        $adsYN = false;

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        $Messages[] = array(
            'snd' => $sender_data->sender,  // 발신번호, 팝빌에 등록되지 않은 발신번호 기재시 오류처리
            'sndnm' => $sender_data->sender_name,   // 발신자명
            'rcv' => $receive,   // 수신번호
            'rcvnm' => '',  // 수신자성명
            'msg' => $message // 메시지 내용
        );

        // 팝빌 회원 아이디
        $UserID = $sender_data->popbill_id;

        try {
            $receiptNum = $this->PopbillMessaging->SendXMS($CorpNum, '', '', '', $Messages, $ReserveDT, $adsYN, $UserID, '', '', $RequestNum);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('sms_log') -> insert([
                'sender'=> $sender_data->sender,
                'phone' => $request->receive_phone,
                'status' => "실패",
                'template' => $request->template,
                'od_id' => $request->od_id,
                'contents' => $message,
                'handler' => $request->handler
            ]);
            
            return reponse() ->json(['status' => false, 'message' => $message]);
        }

        DB::table('sms_log') -> insert([
            'sender'=> $sender_data->sender,
            'phone' => $request->receive_phone,
            'status' => "성공",
            'template' => $request->template,
            'od_id' => $request->od_id,
            'contents' => $message,
            'handler' => $request->handler
        ]);

        return response() -> json(['status' => true, 'message' => "전송 완료"]);
    }

    // SMS 전송 ( 주문 관련 문자 )
    public function sendSMS_orderData(Request $request)
    {
        $order_idx = $request->order_idx;
        $template_type = $request->template_type;
        $payment_number = $request->payment_number;
        $handler = $request->handler ?? "자동 전송";

        $order = OrderData::find($order_idx);

        $brand_info = DB::table('code_of_company_info') -> where('brand_type_code', $order -> brand_type_code) -> first();
        $templateCode = DB::table('popbill_template_numberInUse') -> where('brand_type_code', '=', $order->brand_type_code) ->value($template_type);

        $templateName = DB::table('popbill_template_info') -> where('templateCode', '=', $templateCode) ->value("templateName");

        // 문자 내용
        $message = self::set_variable($order_idx, $templateCode, $payment_number, $template_type);

        // 팝빌 회원 사업자번호, "-"제외 10자리
        $CorpNum = preg_replace('/[^0-9]/', '', $brand_info -> shop_business_number);

        // 예약전송일시(yyyyMMddHHmmss) ex)20151212230000, null인경우 즉시전송
        $ReserveDT = null;

        // 광고문자 전송여부
        $adsYN = false;

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        $Messages[] = array(
            'snd' => $brand_info->shop_send_tel,  // 발신번호, 팝빌에 등록되지 않은 발신번호 기재시 오류처리
            'sndnm' => $brand_info->shop_name,   // 발신자명
            'rcv' => $order -> orderer_phone,   // 수신번호
            'rcvnm' => $order -> orderer_name,  // 수신자성명
            'msg' => $message // 메시지 내용
        );

        // 팝빌 회원 아이디
        $UserID = $brand_info->popbill_id;

        try {
            $receiptNum = $this->PopbillMessaging->SendXMS($CorpNum, '', '', '', $Messages, $ReserveDT, $adsYN, $UserID, '', '', $RequestNum);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('sms_log') -> insert([
                'sender'=> $brand_info->shop_send_tel,
                'phone' => $order -> orderer_phone,
                'status' => "실패",
                'template' => $templateName,
                'od_id' => $order->od_id,
                'contents' => $message,
                'handler' => $handler
            ]);

            return reponse() ->json(['status' => false, 'message' => $message]);
        }

        DB::table('sms_log') -> insert([
            'sender'=> $brand_info->shop_send_tel,
            'phone' => $order -> orderer_phone,
            'status' => "성공",
            'templateCode' => $templateCode,
            'template' => $templateName,
            'od_id' => $order->od_id,
            'contents' => $message,
            'handler' => $handler
        ]);

        return response() -> json(['status' => true, 'message' => "전송 완료"]);
    }

#######################################################################################################################

    // 컨텐츠에 변수 -> 내용 변환
    public static function set_variable($order_idx, $templateCode, $payment_number, $template_type) {
        $template = TalkTemplate::find($templateCode);

        $values = json_decode($template -> values, true);
        $content = $template -> template;
        $order = OrderData::find($order_idx);

        if(!empty($values)) {
            foreach ($values as $value) {
                $column = $value['column'];

                // 배송 날짜의 경우 + 시간 추가
                if($value['column'] === "delivery_date") {
                    $str = DB::table($value['table']) -> select($column) -> where('order_idx', $order_idx) -> first() -> $column;
                    $str .= " " . DB::table($value['table']) -> select('delivery_time') -> where('order_idx', $order_idx) -> first() -> delivery_time;
                }
                // 금액이 들어갈 경우 3자리 단위 콤마, 원 추가
                elseif($value['column'] === "payment_amount"){
                    $payment = OrderPayment::where('order_idx', $order->order_idx)->where('payment_number', $payment_number) -> first();
                    $str = number_format($payment-> payment_amount) . "원";
                }
                // 가상계좌 / 무통장안내
                elseif($value['column'] === "bank_num") {
                    if($template_type === "without_bank_account") {
                        $str = DB::table('code_of_company_info') -> select('bank_account_info') -> where('brand_type_code', $order->brand_type_code) -> value('bank_account_info');
                    }else {
                        $payment = OrderPayment::where('order_idx', $order->order_idx)->where('payment_number', $payment_number) -> first();
                        $num = str_split($payment -> bank_num, 4);
                        $bank_num = implode(" ",$num);
                        $str = $payment -> bank_name . " / " . $bank_num;
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
                    $str = DB::table($value['table']) -> select($column) -> where('order_idx', $order_idx) -> first() -> $column;
                }
                $content = str_replace($value['variable'], $str, $content);
            }
        }
        return $content;
    }
}
