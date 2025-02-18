<?php
namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\CommonCode;
use App\Models\TalkTemplate;
use App\Models\AlimLog;


use Linkhub\LinkhubException;
use Linkhub\Popbill\JoinForm;
use Linkhub\Popbill\CorpInfo;
use Linkhub\Popbill\ContactInfo;
use Linkhub\Popbill\ChargeInfo;
use Linkhub\Popbill\PopbillException;
use Linkhub\Popbill\PopbillKakao;
use Linkhub\Popbill\ENumKakaoType;
use Linkhub\Popbill\KakaoButton;
use Linkhub\Popbill\RefundForm;
use Linkhub\Popbill\PaymentForm;

class KakaoTalkController extends Controller
{

    public function __construct()
    {

        // 통신방식 설정
        if(!defined('LINKHUB_COMM_MODE')) {
            define('LINKHUB_COMM_MODE', config('popbill.LINKHUB_COMM_MODE'));
        }

        // 카카오톡 서비스 클래스 초기화
        $this->PopbillKakao = new PopbillKakao(config('popbill.LinkID'), config('popbill.SecretKey'));

        // 연동환경 설정값, true-개발용, false-상업용
        $this->PopbillKakao->IsTest(config('popbill.IsTest'));

        // 인증토큰의 IP제한기능 사용여부, true-사용, false-미사용, 기본값(true)
        $this->PopbillKakao->IPRestrictOnOff(config('popbill.IPRestrictOnOff'));

        // 팝빌 API 서비스 고정 IP 사용여부, true-사용, false-미사용, 기본값(false)
        $this->PopbillKakao->UseStaticIP(config('popbill.UseStaticIP'));

        // 로컬서버 시간 사용 여부, true-사용, false-미사용, 기본값(true)
        $this->PopbillKakao->UseLocalTimeYN(config('popbill.UseLocalTimeYN'));
    }

    // HTTP Get Request URI -> 함수 라우팅 처리 함수
    public function RouteHandelerFunc(Request $request)
    {
        $APIName = $request->route('APIName');
        return $this->$APIName($request);
    }

    /**
     * 카카오톡 채널을 등록하고 내역을 확인하는 카카오톡 채널 관리 페이지 팝업 URL을 반환합니다.
     * - 반환되는 URL은 보안 정책상 30초 동안 유효하며, 시간을 초과한 후에는 해당 URL을 통한 페이지 접근이 불가합니다.
     * - https://developers.popbill.com/reference/kakaotalk/php/api/channel#GetPlusFriendMgtURL
     */
    public function GetPlusFriendMgtURL(Request $request)
    {
        $brand = $request -> brand;
        
        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        try {
            $url = $this->PopbillKakao->GetPlusFriendMgtURL($CorpNum, $UserID);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
//            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
            return response() -> json(['code' => 'F', 'message' => $message]);
        }
//        return view('PopBill.ReturnValue', ['filedName' => "카카오톡 발신번호 관리 팝업 URL", 'value' => $url]);
        return response() -> json(['code' => 'S', 'url' => $url]);
    }


    /**
     * 알림톡 템플릿을 신청하고 승인심사 결과를 확인하며 등록 내역을 확인하는 알림톡 템플릿 관리 페이지 팝업 URL을 반환합니다.
     * - 반환되는 URL은 보안 정책상 30초 동안 유효하며, 시간을 초과한 후에는 해당 URL을 통한 페이지 접근이 불가합니다.
     * - https://developers.popbill.com/reference/kakaotalk/php/api/template#GetATSTemplateMgtURL
     */
    public function GetATSTemplateMgtURL(Request $request)
    {
        $brand = $request -> brand;

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;


        try {
            $url = $this->PopbillKakao->GetATSTemplateMgtURL($CorpNum, $UserID);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
        }

//        return view('PopBill.ReturnValue', ['filedName' => "알림톡 템플릿 관리 팝업 URL", 'value' => $url]);
        return response() -> json(['code' => 'S', 'url' => $url]);
    }
    
    /**
     * 알림톡 템플릿 리스트 업데이트
     * 승인된 알림톡 템플릿 목록을 확인합니다.
     * - https://developers.popbill.com/reference/kakaotalk/php/api/template#ListATSTemplate
     */
    public function ListATSTemplate()
    {

        $brands = ['BTCP', 'BTCC', 'BTSP', 'BTBR', 'BTOM'];

        foreach ($brands as $brand) {
            // 사업자 번호 가져오기
            $bs_info = DB::table('code_of_company_info') -> select('shop_business_number') -> where('brand_type_code', '=', $brand) -> first();
            // 숫자 제거 (정규식)
            $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

            // 팝빌 회원 사업자 번호, "-"제외 10자리
            $CorpNum = $bs_number;

            try {
                $result = $this->PopbillKakao->ListATSTemplate($CorpNum);
            } catch (PopbillException $pe) {
                $code = $pe->getCode();
                $message = $pe->getMessage();
                return view('PopBill.PResponse', ['code' => $code, 'message' => $message."(".$brand.")"]);
            }

            foreach($result as $template) {
                if($template -> plusFriendID == '@디얼디어플라워' || $template -> plusFriendID == '@화환24' || $template -> plusFriendID == '@와이더블유플라워') {
                    continue;
                }
                $brand_type_code = self::find_brand_type_code($template -> plusFriendID);
                if(DB::table('popbill_template_info') -> where('templateCode',$template -> templateCode) -> doesntExist()) {
                    DB::table('popbill_template_info') -> insert([
                        'brand_type_code' => $brand_type_code,
                        'plusFriendID' => $template -> plusFriendID,
                        'templateName' => $template -> templateName,
                        'template' => $template -> template,
                        'templateCode' => $template -> templateCode,
                        'variables' => self::template_variable($template -> template)
                    ]);
                }
                // 이미 존재하는 템플릿 업데이트
                else {
                    DB::table('popbill_template_info')
                        -> where('templateCode',$template -> templateCode)
                        -> update([
                            'brand_type_code' => $brand_type_code,
                            'plusFriendID' => $template -> plusFriendID,
                            'templateName' => $template -> templateName,
                            'template' => $template -> template,
                            'variables' => self::template_variable($template -> template)
                        ]);
                }
            }
        }
        session()->flash('update', 1);
        return redirect('KakaoTalk/Page/Templates');
    }

    public function SendTalk(Request $request){
        $hp = $request -> send_talk_hp;
        $templateCode = $request -> templateCode;
        $template_content = $request -> content;
        $od_id = $request -> od_id;

        // 템플릿 코드로 템플릿 정보 가져오기
        $template_info = DB::table('popbill_template_info') -> where('templateCode', '=', $templateCode) -> first();

        // 브랜드 코드 가져오기
        $brand = $template_info -> brand_type_code;

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        // 팝빌에 사전 등록된 발신번호
        $Sender = $bs_info -> shop_send_tel;

        // 알림톡 내용, 최대 1000자
        $content = $template_content;

        // 대체문자 제목
        // - 메시지 길이(90byte)에 따라 장문(LMS)인 경우에만 적용.
        $AltSubject = '';

        // 대체문자 유형(altSendType)이 "A"일 경우, 대체문자로 전송할 내용 (최대 2000byte)
        // └ 팝빌이 메시지 길이에 따라 단문(90byte 이하) 또는 장문(90byte 초과)으로 전송처리
        $altContent = '';

        // 대체문자 유형 (null , "C" , "A" 중 택 1)
        // null = 미전송, C = 알림톡과 동일 내용 전송 , A = 대체문자 내용(altContent)에 입력한 내용 전송
        $AltSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        // - 분단위 전송, 미입력 시 즉시 전송
        $ReserveDT = '';

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => $hp,
            // 수신자명
            'rcvnm' => ''
        );

        // 알림톡 버튼정보를 템플릿 신청시 기재한 버튼정보와 동일하게 전송하는 경우 null 처리.
        $buttons = null;

        try {
            $receiptNum = $this->PopbillKakao->SendATS(
                $CorpNum,
                $templateCode,
                $Sender,
                $content,
                $altContent,
                $AltSendType,
                $receivers,
                $ReserveDT,
                $UserID,
                $RequestNum,
                $buttons,
                $AltSubject
            );
            DB::table('alim_log') -> insert([
                'od_id' => $od_id,
                'templateName' => $template_info -> templateName,
                'templateCode' => $template_info -> templateCode,
                'phone' => $hp,
                'log_time' => NOW(),
                'log_status' => "성공",
                'template' => $content
            ]);

        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('alim_log') -> insert([
                'od_id' => $od_id,
                'templateName' => $template_info -> templateName,
                'templateCode' => $template_info -> templateCode,
                'phone' => $hp,
                'log_time' => NOW(),
                'log_status' => "실패",
                'template' => $content
            ]);

            return response() -> json(['code' => $code, 'message' => "[전송 실패]".$message]);
        }
        return response() -> json(['code' => 200, 'message' => "[전송 성공] 접수번호 : ".$receiptNum]);
    }

    /**
     * 승인된 템플릿의 내용을 작성하여 1건의 알림톡 전송을 팝빌에 접수합니다.
     * - 사전에 승인된 템플릿의 내용과 알림톡 전송내용(content)이 다를 경우 전송실패 처리됩니다.
     * - 전송실패 시 사전에 지정한 변수 'altSendType' 값으로 대체문자를 전송할 수 있고 이 경우 문자(SMS/LMS) 요금이 과금됩니다.
     * - https://developers.popbill.com/reference/kakaotalk/php/api/send#SendATS
     */

    /* 알림톡 단건 ( 주문 정보 전달 ) */
    public function SendATS_one(Request $request)
    {
        //템플릿 타입
        $template_type=$request->template_type;
        $order_idx = $request->order_idx;
        $payment_number = $request->payment_number;

        $order = OrderData::find($order_idx);

        // 승인된 알림톡 템플릿코드
        // └ 알림톡 템플릿 관리 팝업 URL(GetATSTemplateMgtURL API) 함수, 알림톡 템플릿 목록 확인(ListATStemplate API) 함수를 호출하거나
        //   팝빌사이트에서 승인된 알림톡 템플릿 코드를  확인 가능.
        $templateCode = DB::table('popbill_template_numberInUse') -> where('brand_type_code', '=', $order->brand_type_code) ->value($template_type);

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $order->brand_type_code) -> first();
        // 숫자 이외 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        // 팝빌에 사전 등록된 발신번호
        // altSendType = 'C' / 'A' 일 경우, 대체문자를 전송할 발신번호
        // altSendType = '' 일 경우, null 또는 공백 처리
        // ※ 대체문자를 전송하는 경우에는 사전에 등록된 발신번호 입력 필수
        $Sender = $bs_info -> shop_send_tel;

        // 알림톡 내용, 최대 1000자
        $content = self::set_variable($order_idx, $templateCode, $payment_number, $template_type);

        // 대체문자 제목
        // - 메시지 길이(90byte)에 따라 장문(LMS)인 경우에만 적용.
        $AltSubject = '';

        // 대체문자 유형(altSendType)이 "A"일 경우, 대체문자로 전송할 내용 (최대 2000byte)
        // └ 팝빌이 메시지 길이에 따라 단문(90byte 이하) 또는 장문(90byte 초과)으로 전송처리
        $altContent = '';

        // 대체문자 유형 (null , "C" , "A" 중 택 1)
        // null = 미전송, C = 알림톡과 동일 내용 전송 , A = 대체문자 내용(altContent)에 입력한 내용 전송
        $AltSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        // - 분단위 전송, 미입력 시 즉시 전송
        $ReserveDT = '';

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => $order -> orderer_phone,
            // 수신자명
            'rcvnm' => $order -> orderer_name
        );

        // 알림톡 버튼정보를 템플릿 신청시 기재한 버튼정보와 동일하게 전송하는 경우 null 처리.
        $buttons = null;

        $templateName= DB::table('popbill_template_info') -> where('templateCode', '=', $templateCode) ->value("templateName");
        try {
            $receiptNum = $this->PopbillKakao->SendATS(
                $CorpNum,
                $templateCode,
                $Sender,
                $content,
                $altContent,
                $AltSendType,
                $receivers,
                $ReserveDT,
                $UserID,
                $RequestNum,
                $buttons,
                $AltSubject
            );
            DB::table('alim_log') -> insert([
                'od_id' => $order -> od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => $order -> orderer_phone,
                'log_time' => NOW(),
                'log_status' => "성공",
                'template' => $content
            ]);
            return response() -> json(["status"=>true, "msg"=>"카톡 전송 완료"]);

        } catch (PopbillException $pe) {
            \Log::error("[알림톡 전송 실패]");
            \Log::error($pe->getMessage());
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('alim_log') -> insert([
                'od_id' => $order -> od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => $order -> orderer_phone,
                'log_time' => NOW(),
                'log_status' => "실패",
                'template' => $content
            ]);

            return response() -> json(["status"=>false, "msg"=>"카톡 전송 실패"]);
//            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
        }
//        return view('PopBill.ReturnValue', ['filedName' => '알림톡 단건전송 접수번호(receiptNum)', 'value' => $receiptNum]);
    }

    // 상세페이지 - 알림톡 전송 v2
    public function SendTalk_custom(Request $request)
    {
        //템플릿 타입
        $template_type = $request->talk_template_type;
        $order_idx = $request->order_idx;

        $variables = $request->variables;
        $values = $request->values;

        $order = OrderData::find($order_idx);

        // 승인된 알림톡 템플릿코드
        // └ 알림톡 템플릿 관리 팝업 URL(GetATSTemplateMgtURL API) 함수, 알림톡 템플릿 목록 확인(ListATStemplate API) 함수를 호출하거나
        //   팝빌사이트에서 승인된 알림톡 템플릿 코드를  확인 가능.
        $templateCode = DB::table('popbill_template_numberInUse')->where('brand_type_code', '=', $order->brand_type_code)->value($template_type);

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info')->where('brand_type_code', '=', $order->brand_type_code)->first();
        // 숫자 이외 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info->shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info->popbill_id;

        // 팝빌에 사전 등록된 발신번호
        // altSendType = 'C' / 'A' 일 경우, 대체문자를 전송할 발신번호
        // altSendType = '' 일 경우, null 또는 공백 처리
        // ※ 대체문자를 전송하는 경우에는 사전에 등록된 발신번호 입력 필수
        $Sender = $bs_info->shop_send_tel;

        // 템플릿 정보
        $template_info = TalkTemplate::find($templateCode);

        // 알림톡 내용, 최대 1000자
        $content = $template_info -> template;

        if(is_array($variables)){
            foreach ($variables as $key => $value) {
                $content = str_replace($value, $values[$key] , $content);
            }
        }

        // 대체문자 제목
        // - 메시지 길이(90byte)에 따라 장문(LMS)인 경우에만 적용.
        $AltSubject = '';

        // 대체문자 유형(altSendType)이 "A"일 경우, 대체문자로 전송할 내용 (최대 2000byte)
        // └ 팝빌이 메시지 길이에 따라 단문(90byte 이하) 또는 장문(90byte 초과)으로 전송처리
        $altContent = '';

        // 대체문자 유형 (null , "C" , "A" 중 택 1)
        // null = 미전송, C = 알림톡과 동일 내용 전송 , A = 대체문자 내용(altContent)에 입력한 내용 전송
        $AltSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        // - 분단위 전송, 미입력 시 즉시 전송
        $ReserveDT = '';

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => $request->receive_number,
            // 수신자명
            'rcvnm' => $order->orderer_name
        );

        // 알림톡 버튼정보를 템플릿 신청시 기재한 버튼정보와 동일하게 전송하는 경우 null 처리.
        $buttons = null;

        $templateName = DB::table('popbill_template_info')->where('templateCode', '=', $templateCode)->value("templateName");
        try {
            $receiptNum = $this->PopbillKakao->SendATS(
                $CorpNum,
                $templateCode,
                $Sender,
                $content,
                $altContent,
                $AltSendType,
                $receivers,
                $ReserveDT,
                $UserID,
                $RequestNum,
                $buttons,
                $AltSubject
            );
            DB::table('alim_log')->insert([
                'od_id' => $order->od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => $order->orderer_phone,
                'log_time' => NOW(),
                'log_status' => "성공",
                'template' => $content
            ]);
            return response()->json(["status" => true, "msg" => "카톡 전송 완료"]);

        } catch (PopbillException $pe) {
            \Log::error("[알림톡 전송 실패] 커스텀 톡");
            \Log::error($pe->getMessage());            
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('alim_log')->insert([
                'od_id' => $order->od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => $order->orderer_phone,
                'log_time' => NOW(),
                'log_status' => "실패",
                'template' => $content
            ]);

            return response()->json(["status" => false, "msg" => "카톡 전송 실패"]);
//            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
        }
    }

    // 간편주문 앱 상품 링크 보내기
    public function SendLink(Request $request){
        $goods_code = $request -> goods_id;
        $brand = $request -> brand_type_code;
        $hp = $request -> od_hp;
        $tmp_id = $request -> tmp_id;

        // 사용 중인 템플릿 코드 목록
        $template = DB::table('popbill_template_numberInUse') -> where('brand_type_code', '=', $brand) -> first();

        // 템플릿 코드
        $templateCode = $template -> send_link;

        // 템플릿 정보
        $template_info = TalkTemplate::find($templateCode);

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        // ※ 대체문자를 전송하는 경우에는 사전에 등록된 발신번호 입력 필수
        $Sender = $bs_info -> shop_send_tel;

        // 템플릿 내용
        $content = $template_info -> template;

        // 링크 내용
        $link = $bs_info -> mobile_api_url;

        $link .= "shop/".$goods_code;

        $link .= "?tmp_id=".$tmp_id;

        $content = str_replace('#{링크}', $link, $content) ?? $content;

        // 대체문자 제목
        // - 메시지 길이(90byte)에 따라 장문(LMS)인 경우에만 적용.
        $AltSubject = '';

        // 대체문자 유형(altSendType)이 "A"일 경우, 대체문자로 전송할 내용 (최대 2000byte)
        // └ 팝빌이 메시지 길이에 따라 단문(90byte 이하) 또는 장문(90byte 초과)으로 전송처리
        $altContent = '';

        // 대체문자 유형 (null , "C" , "A" 중 택 1)
        // null = 미전송, C = 알림톡과 동일 내용 전송 , A = 대체문자 내용(altContent)에 입력한 내용 전송
        $AltSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        // - 분단위 전송, 미입력 시 즉시 전송
        $ReserveDT = '';

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => $hp,
            // 수신자명
            'rcvnm' => ''
        );
        // 알림톡 버튼정보를 템플릿 신청시 기재한 버튼정보와 동일하게 전송하는 경우 null 처리.
        $buttons = null;

        try {
            $receiptNum = $this->PopbillKakao->SendATS(
                $CorpNum,
                $templateCode,
                $Sender,
                $content,
                $altContent,
                $AltSendType,
                $receivers,
                $ReserveDT,
                $UserID,
                $RequestNum,
                $buttons,
                $AltSubject
            );
            DB::table('alim_log') -> insert([
                'od_id' => $tmp_id,
                'templateName' => $template_info -> templateName,
                'templateCode' => $template_info -> templateCode,
                'phone' => $hp,
                'log_time' => NOW(),
                'log_status' => "성공",
                'template' => $content
            ]);
            
            return response() -> json(['code' => 200, 'message' => "[전송 완료]"]);

        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('alim_log') -> insert([
                'od_id' => $tmp_id,
                'templateName' => $template_info -> templateName,
                'templateCode' => $template_info -> templateCode,
                'phone' => $hp,
                'log_time' => NOW(),
                'log_status' => "실패",
                'template' => $content
            ]);
//            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
            return response() -> json(['code' => $code, 'message' => "[전송 실패]".$message]);
        }
//        return view('PopBill.ReturnValue', ['filedName' => '알림톡 단건전송 접수번호(receiptNum)', 'value' => $receiptNum]);

    }

    /**
     * 팝빌 사이트에 로그인 상태로 접근할 수 있는 페이지의 팝업 URL을 반환합니다.
     * - 반환되는 URL은 보안 정책상 30초 동안 유효하며, 시간을 초과한 후에는 해당 URL을 통한 페이지 접근이 불가합니다.
     * - https://developers.popbill.com/reference/kakaotalk/php/api/member#GetAccessURL
     */
    public function GetAccessURL()
    {
        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = '1234567890';

        // 팝빌 회원 아이디
        $UserID = 'testkorea';

        try {
            $url = $this->PopbillKakao->GetAccessURL($CorpNum, $UserID);
        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
        }
        return view('PopBill.ReturnValue', ['filedName' => "팝빌 로그인 URL", 'value' => $url]);
    }

    // 간편주문 앱 에러 발생 알림톡
    public function send_error_log($od_id)
    {

        $brand = "BTCP";

        // 승인된 알림톡 템플릿코드
        // └ 알림톡 템플릿 관리 팝업 URL(GetATSTemplateMgtURL API) 함수, 알림톡 템플릿 목록 확인(ListATStemplate API) 함수를 호출하거나
        //   팝빌사이트에서 승인된 알림톡 템플릿 코드를  확인 가능.
        $templateCode = "024010000286";

        // 사업자 번호 가져오기
        $bs_info = DB::table('code_of_company_info') -> where('brand_type_code', '=', $brand) -> first();
        // 숫자 제거 (정규식)
        $bs_number = preg_replace('/[^0-9]/', '', $bs_info -> shop_business_number);

        // 팝빌 회원 사업자 번호, "-"제외 10자리
        $CorpNum = $bs_number;

        // 팝빌 회원 아이디
        $UserID = $bs_info -> popbill_id;

        // 팝빌에 사전 등록된 발신번호
        // altSendType = 'C' / 'A' 일 경우, 대체문자를 전송할 발신번호
        // altSendType = '' 일 경우, null 또는 공백 처리
        // ※ 대체문자를 전송하는 경우에는 사전에 등록된 발신번호 입력 필수
        $Sender = $bs_info -> shop_send_tel;

        $template = DB::table('popbill_template_info') -> where('templateCode', '=', $templateCode) -> first();

        $variables_str = $template -> variables;
        $variable_arr = json_decode($variables_str,true);


        // 알림톡 내용, 최대 1000자
        $content = $template -> template;

        $content = str_replace($variable_arr[0], $od_id, $content);

        $content = str_replace($variable_arr[1], '간편주문 API 전송 실패', $content);
        

        // 대체문자 제목
        // - 메시지 길이(90byte)에 따라 장문(LMS)인 경우에만 적용.
        $AltSubject = '';

        // 대체문자 유형(altSendType)이 "A"일 경우, 대체문자로 전송할 내용 (최대 2000byte)
        // └ 팝빌이 메시지 길이에 따라 단문(90byte 이하) 또는 장문(90byte 초과)으로 전송처리
        $altContent = '';

        // 대체문자 유형 (null , "C" , "A" 중 택 1)
        // null = 미전송, C = 알림톡과 동일 내용 전송 , A = 대체문자 내용(altContent)에 입력한 내용 전송
        $AltSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        // - 분단위 전송, 미입력 시 즉시 전송
        $ReserveDT = '';

        // 전송요청번호
        // 팝빌이 접수 단위를 식별할 수 있도록 파트너가 부여하는 식별번호.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $RequestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => "010-3655-9079",
            // 수신자명
            'rcvnm' => "도수현"
        );

        // 알림톡 버튼정보를 템플릿 신청시 기재한 버튼정보와 동일하게 전송하는 경우 null 처리.
        $buttons = null;

        $templateName= DB::table('popbill_template_info') -> where('templateCode', '=', $templateCode) ->value("templateName");
        try {
            $receiptNum = $this->PopbillKakao->SendATS(
                $CorpNum,
                $templateCode,
                $Sender,
                $content,
                $altContent,
                $AltSendType,
                $receivers,
                $ReserveDT,
                $UserID,
                $RequestNum,
                $buttons,
                $AltSubject
            );
            DB::table('alim_log') -> insert([
                'od_id' => $od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => "010-3655-9079",
                'log_time' => NOW(),
                'log_status' => "성공",
                'template' => $content
            ]);

        } catch (PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();

            DB::table('alim_log') -> insert([
                'od_id' => $od_id,
                'templateName' => $templateName,
                'templateCode' => $templateCode,
                'phone' => "010-3655-9079",
                'log_time' => NOW(),
                'log_status' => "실패",
                'template' => $content
            ]);

//            return view('PopBill.PResponse', ['code' => $code, 'message' => $message]);
            echo "전송에 실패했습니다. 개발팀에 문의하세요.";
        }
//        return view('PopBill.ReturnValue', ['filedName' => '알림톡 단건전송 접수번호(receiptNum)', 'value' => $receiptNum]);
        echo "success";
    }

#############################################################################################################################

    // 템플릿에서 변수명 추출
    public static function template_variable($template) {

        $template_arr = explode('#{', $template);
        $variables = array();
        foreach ($template_arr as $value) {
            if(!strpos($value, '}')) {
                continue;
            }

            $variable = '#{';
            $variable .= Str::before($value, '}');
            $variable .= '}';

            $variables[] = $variable;
        }
        return json_encode($variables, JSON_UNESCAPED_UNICODE);
    }
    
    // 채널명에서 브랜드 코드 추출
    public static function find_brand_type_code($plusFriendID) {

        switch($plusFriendID) {
            case '@디얼디어플라워':
                return 'BTDD';
            case '@꽃파는사람들주문':
                return 'BTCS';
            case '@플라체인주문':
                return 'BTFC';
            case '@77887788':
                return 'BTCC';
            case '@48flower':
                return 'BTSP';
            case '@무조건오만플라워':
                return 'BTOM';
            case '@baro-flower':
                return 'BTBR';
            default :
                return 'BTCP';
        }
    }

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

                    \Log::error($order->order_idx);
                    \Log::error($payment_number);
                    $payment = OrderPayment::where('order_idx', $order_idx)->where('payment_number', $payment_number) -> first();
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
