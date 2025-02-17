<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Linkhub\LinkhubException;
use Linkhub\Popbill\JoinForm;
use Linkhub\Popbill\CorpInfo;
use Linkhub\Popbill\ContactInfo;
use Linkhub\Popbill\ChargeInfo;
use Linkhub\Popbill\PopbillException;

use Linkhub\Popbill\PopbillKakao;
use Linkhub\Popbill\ENumKakaoType;

class PopBillController extends Controller
{
    public function __construct() {

        // 통신방식 설정
        define('LINKHUB_COMM_MODE', config('popbill.LINKHUB_COMM_MODE'));

        // 카카오톡 서비스 클래스 초기화
        $this->PopbillKakao = new PopbillKakao(config('popbill.LinkID'), config('popbill.SecretKey'));

        // 연동환경 설정값, 개발용(true), 상업용(false)
        $this->PopbillKakao->IsTest(config('popbill.IsTest'));

        // 인증토큰의 IP제한기능 사용여부, 권장(true)
        $this->PopbillKakao->IPRestrictOnOff(config('popbill.IPRestrictOnOff'));

        // 팝빌 API 서비스 고정 IP 사용여부, true-사용, false-미사용, 기본값(false)
        $this->PopbillKakao->UseStaticIP(config('popbill.UseStaticIP'));

        // 로컬시스템 시간 사용 여부 true(기본값) - 사용, false(미사용)
        $this->PopbillKakao->UseLocalTimeYN(config('popbill.UseLocalTimeYN'));
    }

    // 브랜드에 따라 다른 메서드 실행해야 함
    public function SendATS(){

        // 팝빌 회원 사업자번호, "-"제외 10자리
        $testCorpNum = '6058623194';    // 변수 : 사업자 등록번호

        // 팝빌회원 아이디
        $testUserID = 'fla001'; //   변수 : 팝빌 아이디

        // 템플릿 코드 - 템플릿 목록 조회 (ListATSTemplate) 함수의 반환항목 확인
        $templateCode = '019070000133'; // 변수 : 템플릿 코드

        // 팝빌에 사전 등록된 발신번호
        $sender = '16000103';   // 변수 : 발신 번호

        // 알림톡 내용, 최대 1000자
        /*
        $content = '안녕하세요, 전국 꽃배달 서비스 꽃파는총각입니다. 주문내용 확인드립니다.'.PHP_EOL;  // 변수 : 브랜드명
        $content .= " [받는분] ${od_b_name} (주문번호:{$od_id})".PHP_EOL;  // 변수 : 받는분 이름, 주문번호
        $content .= " [배달일시] ${od_deli_txt}".PHP_EOL;
        $content .= " [배달장소] ${od_b_addr1} ${od_b_addr2} ${od_b_addr3}".PHP_EOL;
        $content .= " [경조사어] ${od_msg_right}".PHP_EOL;
        $content .= " [보내는분] ${od_msg_left}".PHP_EOL.PHP_EOL;
        $content .= '수정사항 있으시면 반드시 회신 부탁 드립니다. 수정사항이 없으시면 하단의 주문확인 완료 버튼을 클릭해주세요! 주문확인완료 이후의 주문내용관련 수정은 반드시 고객센터로 문의바랍니다.'.PHP_EOL.PHP_EOL;
        $content .= '고객센터 : 1600-0103'.PHP_EOL;         // 변수 : 고객센터 번호
        $content .= '휴대폰 : 010-3142-0678'.PHP_EOL.PHP_EOL;  // 변수 : 휴대폰 번호
        $content .= '감사합니다.'.PHP_EOL;
        */
        // 대체문자 내용
        $altContent = '';

        // 대체문자 전송유형 공백-미전송, A-대체문자내용 전송, C-알림톡내용 전송
        $altSendType = 'C';

        // 예약전송일시, yyyyMMddHHmmss
        $reserveDT = '';

        // 전송요청번호
        // 파트너가 전송 건에 대해 관리번호를 구성하여 관리하는 경우 사용.
        // 1~36자리로 구성. 영문, 숫자, 하이픈(-), 언더바(_)를 조합하여 팝빌 회원별로 중복되지 않도록 할당.
        $requestNum = '';

        // 수신자 정보
        $receivers[] = array(
            // 수신번호
            'rcv' => '01096602505',
            // 수신자명
            'rcvnm' => '도수현'
        );

        // 버튼정보를 수정하지 않고 템플릿 신청시 기재한 버튼내용을 전송하는 경우, null처리.
        $buttons = null;

        // 버튼배열, 버튼링크URL에 #{템플릿변수}를 기재하여 승인받은 경우 URL 수정가능.
        // $buttons[] = array(
        //     // 버튼 표시명
        //     'n' => '템플릿 안내',
        //     // 버튼 유형, WL-웹링크, AL-앱링크, MD-메시지 전달, BK-봇키워드
        //     't' => 'WL',
        //     // 링크1, [앱링크] Android, [웹링크] Mobile
        //     'u1' => 'https://www.popbill.com',
        //     // 링크2, [앱링크] IOS, [웹링크] PC URL
        //     'u2' => 'http://www.popbill.com',
        // );

        try {
            $receiptNum = $this->PopbillKakao->SendATS($testCorpNum, $templateCode, $sender, $content,
                $altContent, $altSendType, $receivers, $reserveDT, $testUserID, $requestNum, $buttons);
        } catch(PopbillException $pe) {
            $code = $pe->getCode();
            $message = $pe->getMessage();
            return view('zz-test', ['code' => $code, 'message' => $message, 'value' => 'error']);
        }
        return view('zz-test', ['value' => $receiptNum]);
    }
}
