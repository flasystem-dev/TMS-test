<?php

namespace App\Http\Controllers\API;

use Google\Cloud\Language\LanguageClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderItem;
use App\Models\CommonCode;
use App\Models\TMS_Notification;

use App\Utils\Common;

class PlayAuto2APIController extends Controller
{
    protected static $api_key = "TxWJjPcLZ7501L6VVxqxv3Zx4vIkfFsw20BJuJph";
    protected static $auth_key = "808eb9db38393c8527651a1232d54d720e4fd7959013513618c984bf";

    ###################################################################################################################################
    ############################################### 스케줄러 ###########################################################################

    # 토큰 받아와 DB 저장 ( 플레이오토 2.0 API 요청 시 필요 [토큰 생성 후 24시간 유효] : 현재 4시간 단위 업데이트)
    # set_token();

    # 쇼핑몰 -> 플레이오토 2.0 신규 주문 가져오기
    # get_order();

    # 쇼핑몰 -> 플레이오토 2.0 주문 동기화 ( 기존 주문 -> 취소요청/교환요청/반품요청/배송중 동기화 )
    # Synchronize_order();

    # 플레이오토 2.0 -> TMS 주문 전송 ( 신규 / 취소 ) -> 출고 대기 변경
    # send_data_TMS();

    # 플레이오토 2.0 -> TMS 주문 전송 ( 취소마감 )
    # send_cancelData_TMS();

    ###################################################################################################################################
    ###################################################################################################################################

    # 출고대기 주문 -> 배송중 변경
    # setDeliveryState($order);

    # 작업 성공 여부 콜백 함수 ( 신규 주문 가져오기, 동기화 , 배송중(송장 보내기) 변경 )
    # set_api_result(Request $request);

    # 쇼핑몰 계정 관리
    # update_shop_info();

    # 현재 플레이오토 2.0에서 사용중 인 쇼핑몰 정보 가져오기 ( 쇼핑몰 이름, 쇼핑몰 코드, 쇼핑몰 아이디 )
    # get_shopCode();

    ##################################################################################################################################
    ###################################################### 실행 #######################################################################


    // 쇼핑몰에서 플레이오토 2.0 으로 주문 가져오기
    public static function get_order()
    {
        $site_code_list = DB::table('playauto2_api') -> select('site_code') ->where('is_api_used', 'Y') -> groupBy('mall_code') -> get();

        
        foreach ($site_code_list as $site_code) {

            $code = $site_code -> site_code;

            $site_id_list = DB::table('playauto2_api') -> select('site_id') ->where('is_api_used', 'Y') -> where('site_code', '=', $code) -> get();

            $site_id = [];

            foreach ($site_id_list as $id) {
                $site_id[] = $id -> site_id;
            }

            $response = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> post('https://openapi.playauto.io/api/work/addWork/v1.2',[
                'act' => 'ScrapOrderAndConfirmDoit',
                'params' => [
                    'site_code' => $code,
                    'site_id' => $site_id,
                    'show_shop_info_yn' => true
                ],
                'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
            ]);

            sleep(2);

//            DB::table('playauto2_log') -> insert([
//                'order_idx' => 001,
//                'work_name' => '쇼핑몰 주문 가져오기 API',
//                'content' => $response
//            ]);
        }
        
        $obj = $response-> json();

        return 'success';
    }

    // 주문 동기화 API ( 취소, 교환, 반품 )
    public static function Synchronize_order()
    {
        $site_code_list = DB::table('playauto2_api') -> select('site_code') ->where('is_api_used', 'Y') -> groupBy('site_code') -> get();

        foreach ($site_code_list as $site_code) {
            $code = $site_code -> site_code;
            $site_id_list = DB::table('playauto2_api') -> select('site_id') ->where('is_api_used', 'Y') -> where('site_code', '=', $code) -> get();

            $site_id = [];

            foreach ($site_id_list as $id) {
                $site_id[] = $id -> site_id;
            }

            $response = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> post('https://openapi.playauto.io/api/work/addWork/v1.2',[
                'act' => 'SyncOrderState',
                'params' => [
                    'site_code' => $code,
                    'site_id' => $site_id,
                    'show_shop_info_yn' => true
                ],
                'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
            ]);

            sleep(2);
        }
        $obj = $response-> json();

        return 'SUCCESS';
    }


    // 토큰 API로 받아서 저장
    public static function set_token()
    {
        $response = Http::withHeaders([
            'x-api-key' => self::$api_key
        ]) -> post('https://openapi.playauto.io/api/auth',[
            'authentication_key' => self::$auth_key
        ]);
        $obj = $response-> json();
        DB::table('playauto2_token') -> where('idx', 1)
            ->update([
                'token' => $obj[0]['token'],
                'sol_no' => $obj[0]['sol_no']
            ]);

        DB::table('playauto2_log') -> insert([
            'order_idx' => 000,
            'work_name' => '토큰 저장 API',
            'content' => $response
        ]);
    }

    // TMS 받은 주문 보내기
    public static function send_data_TMS() {

        try {
            // 플토 2.0 주문 가져오기 ( 신규, 취소, 교환, 반품 )
            $response = self::get_orderToTMS();

            $arr = json_decode($response,true);
            $data_arr = $arr["results"];

            if(!empty($data_arr)) {

                foreach ($data_arr as $result_data) {

                    $check = false;

                    $order_number = $result_data['shop_ord_no'];
                    if(empty($order_number)){
                        $order_number = $result_data['shop_ord_no_real'];
                    }

                    if($result_data['ord_status'] === '신규주문' || $result_data['ord_status'] === '주문재확인') {
                        // 신규 주문 중복 확인
                        $check = self::checkOrderList($result_data);
                    } else {
                        // 취소 주문 중복 확인
                        $check = self::countOrderNum($order_number);
                    }

                    if($check) continue;
                    // 취소 요청 시 기존 주문 [취소대기]로 변경
                    if($result_data['ord_status'] === '취소요청' || $result_data['ord_status'] === '반품요청' || $result_data['ord_status'] === '교환요청') {
                        self::changeState_PSWC($order_number);
                    }

                    // 주문 DB 에 저장
                    self::set_order_ToDB($result_data, $response);
                }
            }

        } catch (\Exception $e) {
            Log::error("[플레이오토 2.0] 신규주문 API 동작 실패");
            Log::error($e);
        }

        return "SUCCESS";

    }

    // TMS 받은 주문 보내기 ( 취소 완료 )
    public static function send_cancelData_TMS() {

        try {
            // 플토 2.0 주문 가져오기 ( 취소 완료 )
            $data_arr = self::get_CancelOrder_ToTMS();

            if(isset($data_arr)) {

                foreach ($data_arr as $result_data) {
                    $order_number = $result_data['shop_ord_no'];
                    if(empty($order_number)){
                        $order_number = $result_data['shop_ord_no_real'];
                    }
                    // 중복 작업 제거
                    if(self::checkCancelOrder($order_number)) continue;
                    // 취소마감, 반품마감 업데이트 (DB)
                    self::updateCancelData($result_data);
                }

            }

        } catch (\Exception $e) {
            Log::error("[플레이오토 2.0] 취소 완료 주문 API 동작 실패");
            Log::error($e);
        }

        return "SUCCESS";

    }

    // 신규 주문 -> 배송중 API
    public static function setDeliveryState($order){

        try{
            // 배송 정보 업데이트 API ( 송장 입력 )
            $result = self::delivery_update_data($order);

            if($result['result'] != "성공") {
                \Log::error("[플레이오토] 송장 입력 실패");
                \Log::error("order_idx : ".$order->order_idx);
                \Log::error($result);

                return "[에러 발생]".$result['messages'][0];
            }

            sleep(1);

            // 운송장 전송 (배송중 상태 변경)
            self::send_delivery_toPT($order);

            return "SUCCESS";

        }catch (\Exception $e) {
            \Log::error("[플레이오토]배송중 변경 실패");
            \Log::error($e);
        }
    }

    // 쇼핑몰에서 플레이오토 2.0 으로 문의 가져오기
    public static function get_CS()
    {
        $site_code_list = DB::table('playauto2_api') -> select('site_code') ->where('is_api_used', 'Y') -> groupBy('mall_code') -> get();

        foreach ($site_code_list as $site_code) {
            $code = $site_code -> site_code;
            $site_id_list = DB::table('playauto2_api') -> select('site_id') ->where('is_api_used', 'Y') -> where('site_code', '=', $code) -> get();

            $site_id = [];

            foreach ($site_id_list as $id) {
                $site_id[] = $id -> site_id;
            }

            $response1 = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> post('https://openapi.playauto.io/api/work/addWork/v1.2',[
                'act' => 'ScrapCS',
                'params' => [
                    'site_code' => $code,
                    'site_id' => $site_id,
                    'show_shop_info_yn' => true
                ],
                'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
            ]);

            sleep(2);

            $response2 = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> post('https://openapi.playauto.io/api/work/addWork/v1.2',[
                'act' => 'ScrapEmergencyCS',
                'params' => [
                    'site_code' => $code,
                    'site_id' => $site_id,
                    'show_shop_info_yn' => true
                ],
                'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
            ]);

            sleep(2);

            $response3 = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> post('https://openapi.playauto.io/api/work/addWork/v1.2',[
                'act' => 'ScrapReview',
                'params' => [
                    'site_code' => $code,
                    'site_id' => $site_id,
                    'show_shop_info_yn' => true
                ],
                'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
            ]);
        }
        $obj = $response1-> json();

//        dd($obj);

        return 'success';
    }

    // 배송중 재전송
    public function resend_delivery(Request $request) {
        $order_idx = $request -> order_idx;
        $order = OrderData::find($order_idx);
        $uniq = DB::table('playauto_info')-> where('order_idx', $order->order_idx) -> first() -> uniq;

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> get('https://openapi.playauto.io/api/order/'.$uniq);

        $result = $response -> json();
        $order_data = $result['order_data'][0];

        $order -> db_num = $order_data['bundle_no'];
        $order -> save();

        if($order_data['ord_status'] === "신규주문") {
            // 출고 대기
            $response = self::set_delivery($order);
            if($response['results'] !=="성공") {
                return response() -> json(['status'=>false, 'msg'=> "[출고 대기 실패]" . $response['messages']]);
            }
            sleep(1);

            // 송장 입력
            $response2 = self::delivery_update_data($order);
            if($response2['result'] !== "성공") {
                return response() -> json(['status'=>false, 'msg'=> "[송장 입력 실패]" . $response2['messages']]);
            }
            sleep(1);
            
            // 송장 전송
            $response3 = self::send_delivery_toPT($order);
            if(isset($response3['error'])){
                return response() -> json(['status'=>false, 'msg'=> "[송장 전송 실패]" . $response3['error'][0]['messages'][0]]);
            }

        }elseif ($order_data['ord_status'] === "출고대기") {
            // 송장 입력
            $response2 = self::delivery_update_data($order);
            if($response2['result'] !== "성공") {
                return response() -> json(['status'=>false, 'msg'=> "[송장 입력 실패]" . $response2['messages']]);
            }
            sleep(1);

            // 송장 전송
            $response3 = self::send_delivery_toPT($order);
            if(isset($response3['error'])){
                return response() -> json(['status'=>false, 'msg'=> "[송장 전송 실패]" . $response3['error'][0]['messages'][0]]);
            }
        }elseif ($order_data['ord_status'] === "배송중") {
            return response() -> json(['status'=>true, 'msg'=> "이미 배송중 처리 된 주문입니다."]);
        }
        
        return response() -> json(['status'=>true, 'msg'=> "재전송 완료"]);
    }
    
    // 주문 묶음번호 다시 가져오기
    public function reconnect_order(Request $request) {
        $order_idx = $request -> order_idx;
        $order = OrderData::find($order_idx);
        $uniq = DB::table('playauto_info')-> where('order_idx', $order->order_idx) -> first() -> uniq;

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> get('https://openapi.playauto.io/api/order/'.$uniq);

        $result = $response -> json();
        $order_data = $result['order_data'][0];

        $order -> db_num = $order_data['bundle_no'];

        if(empty($order_data['bundle_no'])) {
            return response() -> json(['status'=>true, 'msg'=> "플레이오토에 문제가 있습니다."]);
        }

        $order -> save();

        return response() -> json(['status'=>true, 'msg'=> "연결 완료"]);
    }

    ##################################################################################################################################
    ####################################################### 함수 ######################################################################
    
    // 새로 받은 주문 DB 저장
    public static function set_order_ToDB($result_data, $response) {


        // 옵션 파싱 ( 주문자, 수령자, 배송일, 리본문구 )
        $optionList = self::parsingOption($result_data);

        // 상품명 파싱
        $goods_name = self::parse_goods_name($optionList['etcOption']);
        
        // 상품명 빈칸이면 기존 값
        if(empty($goods_name))
            $goods_name = $result_data['shop_sale_name'];

        // 옵션 디스플레이
        $option_display = self::makeOptionDisplay($result_data, $optionList);

        // 주문 2개 이상 디스플레이
        if($result_data['sale_cnt'] > 1)
            $option_display = self::makeOptionDisplay2($result_data);

        // 2개이상 주문 -> 옵션 값은 1개
        if($option_display === true)
            $option_display = self::makeOptionDisplay($result_data, $optionList);

        // 추가구매옵션명
        if(!empty($result_data['shop_add_opt_name'])) {
            if($result_data['pa_shop_cd'] == 'A001' || $result_data['pa_shop_cd'] == 'A006') {
                $opt_sub = explode(":", $result_data['shop_add_opt_name'])[1];
                $opt_arr = explode("/", $opt_sub);
                $goods_name .= "[" . $opt_arr[0] . "-" . $opt_arr[1] . "]";
                $option_display .= "\n" . "\n" . $result_data['shop_add_opt_name'];

            }else {
                $option_display .= "\n" . "\n" . $result_data['shop_add_opt_name'];
            }
        }

        $option_string = $result_data['shop_opt_name'];

        // 쿠팡의 경우 추가메시지 수집
        if(!empty($result_data['order_msg'])) {
            if($result_data['pa_shop_cd'] == 'B378') {
                $option_display .= "\n" . $result_data['order_msg'];
                $option_string = $result_data['shop_opt_name'] . "\n" . $result_data['order_msg'];
                $option_display .= "\n" . "\n" . $result_data['order_msg'];
            }
        }

        // 옵션 파싱 성공 여부 확인
        $option_parse_yn = $optionList['option_parse_yn'];

        // 배송일 데이터 수집 (Google API)
        $deliveryTime = self::parseDeliveryTime($optionList['deliveryTime'] );
        
        // 배송일 옵션 파싱 성공 여부 확인
        if($deliveryTime['deliveryTime']['check'] == 'Y')
            $option_parse_yn = 'N';

        // 파싱된 옵션 리스트
        $optionList = array_merge($optionList,$deliveryTime);

        // 옵션 주문자 파싱
        $ordererData = self::parse_NameAndPhone($optionList['ordererData']);

        // 옵션 받는자 파싱
        $receiverData = self::parse_NameAndPhone($optionList['receiverData']);

        if(empty($result_data['bundle_no'])){
            \Log::error("[플레이오토] 묶음 번호 가져오기 실패");

            DB::table('test_table') -> insert([
                'test1' => "[플레이오토] 묶음 번호 가져오기 실패",
                "test2" => $response
            ]);
        }

        $product_price = $result_data['sales']/$result_data['sale_cnt'];

        if($result_data['pa_shop_cd'] === "A077") {
            $product_price = $result_data['pay_amt']/$result_data['sale_cnt'];
        }

        // 주문 수량 만큼 반복해서 주문
        for($i=0; $i<$result_data['sale_cnt']; $i++) {
            $server_num = 1;
            $timestamp = Carbon::now();
            $microTime = (int)((microtime(true) - floor(microtime(true))) * 10);

            $od_id = $timestamp->format('ymdHis') . $microTime . $i . $server_num;
            $order_idx = DB::table('order_data')->max('order_idx') + 1;
            $order = new OrderData();
            $delivery = new OrderDelivery();
            $item = new OrderItem();

            $order_number = $result_data['shop_ord_no'];
            if(empty($order_number)){
                $order_number = $result_data['shop_ord_no_real'];
            }
            
            $order -> order_idx                 = $order_idx;
            $order -> od_id                     = $od_id;
            $order -> brand_type_code           = self::check_brand($result_data);
            $order -> mall_code                 = self::check_mall($result_data);
            $order -> group_name                = "openMarket";
            $order -> order_number              = $order_number;
            $order -> order_time                = $result_data['ord_time'];
            $order -> orderer_mall_id           = $result_data['order_id'];
            $order -> orderer_name              = $ordererData['name'] != '' ? $result_data['order_name'] ."/".$ordererData['name'] : $result_data['order_name'];
            $order -> orderer_tel               = $result_data['order_htel'];
            $order -> orderer_phone             = $ordererData['phone'] != '' ? $ordererData['phone'] : $result_data['order_htel'];
            $order -> orderer_email             = $result_data['order_email'];
            $order -> order_quantity            = $result_data['sale_cnt'];
            $order -> payment_type_code         = 'PTOP';
            $order -> payment_state_code        = self::check_pay_state($result_data);
            $order -> payment_time              = $result_data['pay_time'];
            $order -> total_amount              = $product_price;
            $order -> pay_amount                = $product_price;
            $order -> supply_amount             = $result_data['shop_supply_price']/$result_data['sale_cnt'];
            $order -> create_ts                 = $result_data['wdate'];
            $order -> options_string            = $option_string;
            $order -> options_parse_yn          = $option_parse_yn;
            $order -> options_string_display    = $option_display;
            $order -> goods_url                 = $result_data['shop_sale_no'];
            $order -> db_num                    = $result_data['bundle_no'];

            $delivery -> order_idx              = $order_idx;
            $delivery -> receiver_name          = !empty($receiverData['name']) ? $result_data['to_name'] ."/".$receiverData['name'] : $result_data['to_name'];
            $delivery -> receiver_tel           = $result_data['to_htel'];
            $delivery -> receiver_phone         = !empty($receiverData['phone']) ? $receiverData['phone'] : $result_data['to_htel'];
            $delivery -> delivery_date          = $optionList['deliveryTime']['date'];
            $delivery -> delivery_time          = $optionList['deliveryTime']['time'];
            $delivery -> delivery_post          = $result_data['to_zipcd'];
            $delivery -> delivery_address       = trim($result_data['to_addr1']." ".$result_data['to_addr2']);
            $delivery -> delivery_ribbon_left   = $optionList['deliveryLeftMsg'];
            $delivery -> delivery_ribbon_right  = $optionList['deliveryRightMsg'];
            $delivery -> delivery_message       = $result_data['ship_msg'];
            $delivery -> goods_name             = $goods_name;

            $item -> order_id                   = $order_idx;
            $item -> product_name               = $goods_name;
            $item -> item_total_amount          = $product_price;
            $item -> product_price              = $product_price;

            $order -> save();
            $delivery -> save();
            $item -> save();

            DB::table('order_log') -> insert([
                'od_id' => $od_id,
                'log_by_name' => 'API',
                'log_time' => NOW(),
                'log_status' => '주문 가져오기',
                'log_content' => '플레이오토 => TMS 주문 정보 가져오기'
            ]);

            $str_log = json_encode($result_data, JSON_UNESCAPED_UNICODE);

            DB::table('test_data') -> insert([
                'test1' => $od_id,
                'test2' => $str_log
            ]);

            DB::table('playauto_info') -> insert([
                'order_idx' => $order_idx,
                'uniq'  => $result_data['uniq']
            ]);

            // 출고 대기 변경
            self::set_delivery($order);

        }
        return "SUCCESS";
    }
    
    // 토큰 값
    protected static function get_token() {
        $playauto = DB::table('playauto2_token') -> select('token') -> first();
        return $playauto -> token;
    }

    // 사용중인 쇼핑몰 코드 / ID 가져오기
    public static function get_shopCode()
    {
        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> get('https://openapi.playauto.io/api/shops',[
            'used' => 'true',
            'etc_detail' => 'true'
        ]);
        $shop_list = $response-> json();

        foreach ($shop_list as $shop) {
            $mall_code = DB::table('common_code') -> select('code') -> where('description', '=', $shop['shop_name']) -> first() -> code;
            $brand_type_code = DB::table('common_code') -> select('code') -> where('code_name', '=', $shop['seller_nick']) -> first() -> code;
            $mall = $shop['shop_name'];
            $site_code = $shop['shop_cd'];
            $site_id = $shop['shop_id'];

            DB::table('playauto2_api') -> insert([
                'mall_code'         => $mall_code,
                'brand_type_code'   => $brand_type_code,
                'mall'              => $mall,
                'site_code'         => $site_code,
                'site_id'           => $site_id
            ]);
        }
    }

    // 작업 결과 값 콜백
    public static function set_api_result(Request $request) {
        $test = file_get_contents("php://input");

        $data = $request -> all();
        $result = $data['results'];
        try {
            if($result['status'] === '실패'){

                if(!Str::contains($result['message'][0], "일시적") && !Str::contains($result['job_name'], ['긴급메세지 수집', '상품문의 수집', '상품평 수집'])) {
                    $request -> title = $result['job_name'];
                    $request -> type = "error";
                    $request -> text = "[{$result['site_nick']}][{$result['site_name']}]".$result['message'][0];

                    TMS_Notification::create_noti($request);
                }

                DB::table('playauto2_callback') -> insert([
                    'work_no' => $result['work_no'],
                    'job_name' => $result['job_name'],
                    'site_name' => $result['site_name'] ?? NULL,
                    'site_nick' => $result['site_nick'] ?? NULL,
                    'shop_id' => $result['shop_id'] ?? NULL,
                    'job_status' => $result['status'],
                    'message' => $result['message'][0]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('플레이오토 콜백 받기 실패');
            \Log::error('[work_no] : '.$result['work_no']);
            \Log::error('[job_name] : '.$result['job_name']);
            \Log::error('[site_name] : '.$result['site_name']);
            \Log::error('[site_nick] : '.$result['site_nick']);
            \Log::error('[shop_id] : '.$result['shop_id']);
            \Log::error('[job_status] : '.$result['job_status']);
            \Log::error('[job_status] : '.$result['message'][0]);
            \Log::error($result);
            \Log::error($e);
        }

//         DB::table('playauto2_callback') -> insert([
//            'work_no' => $result['work_no'],
//            'job_name' => $result['job_name'],
//            'site_name' => $result['site_name'] ?? NULL,
//            'site_nick' => $result['site_nick'] ?? NULL,
//            'shop_id' => $result['shop_id'] ?? NULL,
//            'job_status' => $result['status'],
//            'message' => $result['message'][0]
//        ]);

    }

    // 운송장 전송
    public static function send_delivery_toPT($order) {

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> post('https://openapi.playauto.io/api/work/addWorkSelect',[
            'work_type' => 'SEND_INVOICE',
            'list' => [$order -> db_num.""],
            'show_shop_info_yn' => true,
            'api_callback_url' => 'https://tms.flabiz.kr/api/PlayAuto/test'
        ]);

        $result = $response -> json();

        DB::table('playauto2_work_log') -> insert([
            'order_idx' => $order -> order_idx,
            'work_name' => "송장 전송 API",
            'work_num' => $result['results'][0]['work_no']?? '',
            'content' => $response
        ]);

        return $result;
    }

    // 플레이오토 2.0 에서 TMS 로 주문 가져오기
    public static function get_orderToTMS(){
        // 날짜 포맷
        $today = Carbon::now();
        $todayFormat = $today -> format('Y-m-d');
        $pastFormay = $today -> subDays(3) -> format('Y-m-d');


        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> post('https://openapi.playauto.io/api/orders',[
            'date_type'         => 'wdate',
            'sdate'             => $pastFormay,
            'edate'             => $todayFormat,
            'status'            => ['신규주문', '취소요청', '반품요청', '교환요청', '주문재확인'],
            'bundle_yn'         => true
        ]);

        return $response;
    }

    // 플레이오토 2.0 에서 TMS 로 취소 완료 주문 가져오기
    public static function get_CancelOrder_ToTMS(){
        // 날짜 포맷
        $today = Carbon::now();
        $todayFormat = $today -> format('Y-m-d');
        $pastFormay = $today -> subDays(3) -> format('Y-m-d');


        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> post('https://openapi.playauto.io/api/orders',[
            'date_type'         => 'wdate',
            'sdate'             => $pastFormay,
            'edate'             => $todayFormat,
            'status'            => ['취소완료', '교환완료', '반품완료']
        ]);

        $result = $response -> json();

        return $result['results'];
    }
    

    // 브랜드 코드 가져오기
    public static function check_brand($result_data) {
        return DB::table('playauto2_api') -> select('brand_type_code') -> where('site_id', '=', $result_data['shop_id']) -> where('site_code', '=', $result_data['pa_shop_cd']) -> first() -> brand_type_code;
    }
    
    // 몰 코드 가져오기
    public static function check_mall($result_data) {
        return DB::table('playauto2_api') -> select('mall_code') -> where('site_id', '=', $result_data['shop_id']) -> where('site_code', '=', $result_data['pa_shop_cd']) -> first() -> mall_code;
    }
    
    // 결제 코드 가져오기
    public static function check_pay_state($result_data) {
        $type_code = [
            '신규주문' => 'PSDN',
            '취소요청' => 'PSCR',
            '반품요청' => 'PSRR',
            '교환요청' => 'PSER',
            '취소완료' => 'PSCC',
            '반품완료' => 'PSRC',
            '교환완료' => 'PSEC',
            '주문재확인'=> 'PSOC'
        ];

        return $type_code[$result_data['ord_status']];
    }

    // 상품 옵션 파싱
    public static function parsingOption($result_data)
    {
        $op = $result_data['shop_opt_name'];
        $mall = $result_data['shop_cd'];
        $cnt = $result_data['sale_cnt'];

        if(Str::contains($op,'/'.$cnt.'개')) {
            $op = str_replace('/'.$cnt.'개',"",$op);
        }

        try {
            switch($mall)
            {
                ################################### 옥션 ########################################
                case 'A001' :

                    $str_arr = explode('1.주문자성함/핸드폰 :',$op);
                    $etcOption = trim($str_arr[0]);

                    // 주문자명 & 전화번호
                    $str_arr = explode("2.받는분성함/핸드폰 :",$str_arr[1]);
                    $ordererData = trim($str_arr[0]);

                    // 받는분 & 전화번호
                    $str_arr = explode("3.배송날짜/시간 :",$str_arr[1]);
                    $receiverData = trim($str_arr[0]);

                    // 배송 날짜 & 시간
                    $str_arr = explode("4.경조사어(우측리본) :",$str_arr[1]);
                    $deliveryTime = trim($str_arr[0]);

                    // 리본 우측 메세지 (경조사어)
                    $str_arr = explode("5.보내는분(좌측리본) :",$str_arr[1]);
                    $deliveryRightMsg = trim($str_arr[0]);

                    // 리본 좌측 메세지 (보내는분)
                    $deliveryLeftMsg = trim($str_arr[1]);

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'option_parse_yn' => 'Y'
                    ];

                    return $optionList;

                ################################### G마켓 ########################################
                case 'A006' :

                    $str_arr = explode(', 1.주문자성함/핸드폰:',$op);
                    $etcOption = trim($str_arr[0]);

                    // 주문자명 & 전화번호
                    $str_arr = explode(", 2.받는분성함/핸드폰:",$str_arr[1]);
                    $ordererData = trim($str_arr[0]);

                    // 받는분 & 전화번호
                    $str_arr = explode(", 3.배송날짜/시간:",$str_arr[1]);
                    $receiverData = trim($str_arr[0]);

                    // 배송 날짜 & 시간
                    $str_arr = explode(", 4.경조사어(우측리본):",$str_arr[1]);
                    $deliveryTime = trim($str_arr[0]);

                    // 리본 우측 메세지 (경조사어)
                    $str_arr = explode(", 5.보내는분(좌측리본):",$str_arr[1]);
                    $deliveryRightMsg = trim($str_arr[0]);

                    // 리본 좌측 메세지 (보내는분)
                    $deliveryLeftMsg = trim($str_arr[1]);

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'option_parse_yn' => 'Y'
                    ];

                    return $optionList;

                ################################### 스마트스토어 ########################################
                case 'A077' :

                    // 배송 날짜 & 시간
                    $str_arr = explode("/ 2. 경조사어(우측리본):", $op);
                    $deliveryTime = trim(str_replace("1. 배송날짜/시간:","",$str_arr[0]));

                    // 리본 우측 메세지 (경조사어)
                    $str_arr = explode("/ 3. 보내는분(좌측리본):", $str_arr[1]);
                    $deliveryRightMsg = trim($str_arr[0]);

                    // 리본 좌측 메세지 (보내는분)
                    $etcOption = "";
                    if(Str::contains($str_arr[1], " / ")){

                        $str_arr = explode(" / ", $str_arr[1]);
                        $deliveryLeftMsg = trim($str_arr[0]);

                        foreach ($str_arr as $i => $str) {
                            if($i == 0) continue;
                            $etcOption .= $str." | ";
                        }

                    } else {
                        $deliveryLeftMsg = trim($str_arr[1]);
                    }

                    $optionList = [
                        'ordererData' => "",
                        'receiverData' => "",
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'option_parse_yn' => 'Y'
                    ];

                    return $optionList;

                ################################### 11번가 ########################################
                case 'A112' :

                    // 주문자명 & 전화번호
                    $str_arr = explode(',2.받는분성함/핸드폰:',$op);
                    $ordererData = trim(str_replace("1.주문자성함/핸드폰:", "", $str_arr[0]));

                    // 받는분 & 전화번호
                    $str_arr = explode(',3.배송날짜/시간:',$str_arr[1]);
                    $receiverData = trim($str_arr[0]);

                    // 배송 날짜 & 시간
                    $str_arr = explode(',4.경조사어(우측리본):',$str_arr[1]);
                    $deliveryTime = trim($str_arr[0]);

                    // 리본 우측 메세지 (경조사어)
                    $str_arr = explode(',5.보내는분(좌측리본):',$str_arr[1]);
                    $deliveryRightMsg = trim($str_arr[0]);

                    $str_arr = explode(',',$str_arr[1]);

                    // 리본 좌측 메세지 (보내는분)
                    if(Str::contains($str_arr[1], ["근조오브제", "지역 선택", "제품 선택"])) {
                        $deliveryLeftMsg = trim($str_arr[0]);

                        $etcOption = "";

                        foreach ($str_arr as $i => $str) {
                            if($i == 0) continue;

                            $etcOption .= trim($str)." | ";
                        }
                    } else {

                        $deliveryLeftMsg = '';

                        $etcOption = '';
                        foreach ($str_arr as $str) {

                            if(Str::contains($str, ["근조오브제", "지역 선택", "제품 선택"])) {
                                $etcOption .= trim($str)." | ";
                            } else {
                                $deliveryLeftMsg .= $str.", ";
                            }
                        }
                    }

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'option_parse_yn' => 'Y'
                    ];

                    return $optionList;

                ################################### 톡스토어 ########################################
                case 'B688' :

                    $str_arr = explode("1.주문자성함/핸드폰/", $op);
                    $etcOption = $str_arr[0];

                    // 주문자명 & 전화번호
                    $str_arr = explode(",2.받는분성함/핸드폰/",$str_arr[1]);
                    $ordererData = trim($str_arr[0]);

                    // 받는분 & 전화번호
                    $str_arr = explode(",3.배송날짜/시간/",$str_arr[1]);
                    $receiverData = trim($str_arr[0]);

                    // 배송 날짜 & 시간
                    $str_arr = explode(",4.경조사어(우측리본)/",$str_arr[1]);
                    $deliveryTime = trim($str_arr[0]);

                    //
                    $str_arr = explode(",5.보내는분(좌측리본)/",$str_arr[1]);
                    $deliveryRightMsg = trim($str_arr[0]);

                    $deliveryLeftMsg = trim($str_arr[1]);

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'option_parse_yn' => 'Y'
                    ];

                    return $optionList;

                ############################### 메이크샵, 쿠팡, 인터파크 ########################################
                case 'B118' :
                case 'B378' :
                case 'A027' :
                    $optionList = [
                        'ordererData' => "",
                        'receiverData' => "",
                        'deliveryTime' => "",
                        'deliveryLeftMsg' => "",
                        'deliveryRightMsg' => "",
                        'etcOption' => trim($op),
                        'Card' => 'N',
                        'option_parse_yn' => 'N'
                    ];
                    return $optionList;
            }
        } catch (\Exception $e){
            $optionList = [
                'ordererData' => '',
                'receiverData' => '',
                'deliveryTime' => '',
                'deliveryLeftMsg' => '',
                'deliveryRightMsg' => '',
                'etcOption' => $op,
                'option_parse_yn' => 'N'
            ];
            return $optionList;
        }
    }
    
    
    

    // 상품 명 추출
    public static function parse_goods_name($etc) {

        if(empty($etc)){ return ""; }

        $goods_name = "";

        if(strpos($etc,'축하') !== false) {
            if(strpos($etc,'제품 선택:') !== false) {
                $goods_name = trim(explode('제품 선택:', $etc)[1]);

            }elseif(strpos($etc,'제품선택:') !== false) {
                $goods_name = trim(explode('제품선택:', $etc)[1]);
            }elseif(strpos($etc,'상품선택:') !== false) {
                $goods_name = trim(explode('상품선택:', $etc)[1]);
            }else {
                if(!Str::contains($etc, ['축하합', '축하해', '축하드'])) {
                    $item_arr = explode('축하', $etc);
                    if(empty($item_arr[0])) {
                        Log::error("[상품명 파싱 실패] 기타 옵션 : ".$etc);
                    } else {
                        $item_str = explode($item_arr[0], $etc)[1];
                        $goods_name = trim(explode("/", $item_str)[0]);
                    }
                }
            }
        } elseif(strpos($etc,'근조') !== false && strpos($etc,'오브제') === false) {
            if(strpos($etc,'제품 선택:') !== false) {
                $goods_name = trim(explode('제품 선택:', $etc)[1]);
            }elseif(strpos($etc,'제품선택:') !== false) {
                $goods_name = trim(explode('제품선택:', $etc)[1]);
            }elseif(strpos($etc,'상품선택:') !== false) {
                $goods_name = trim(explode('상품선택:', $etc)[1]);
            }else {
                $item_arr = explode('근조', $etc);
                if(empty($item_arr[0])) {
                    Log::error("[상품명 파싱 실패] 기타 옵션 : ".$etc);
                }else {
                    $item_str = explode($item_arr[0], $etc)[1];
                    $goods_name = trim(explode("/", $item_str)[0]);
                }
            }
        } elseif(strpos($etc,'제품 선택:') !== false) {
            $goods_name = explode('제품 선택:', $etc)[1];
        } elseif(strpos($etc,'제품선택:') !== false) {
            $goods_name = explode('제품선택:', $etc)[1];
        } elseif(Str::contains($etc, '제품 선택/')) {
            $goods_name = explode('제품 선택/', $etc)[1];
        }

        if(strpos($goods_name,'-1개') !== false) {
            $goods_name = explode('-1개', $goods_name)[0];
        }

        $goods_name = preg_replace('/[0-9]{2}\./', '', $goods_name);

        if(Str::contains($goods_name, '|')) {
            $goods_name = trim(str_replace('|', '', $goods_name));
        }

        return $goods_name;
    }

    // 주문번호 존재하는지 확인
    public static function checkOrderList($result_data)
    {

        $order_number = $result_data['shop_ord_no'];
        if(empty($order_number)){
            $order_number = $result_data['shop_ord_no_real'];
        }

        $orders = DB::table('order_data') -> where('order_number', "=", $order_number) -> get();
        $cnt = count($orders);

        $flag = false;

        if($cnt > 0) {

            $new_op = $result_data['shop_opt_name'];

            // 기존 주문 옵션과 새로 들어온 주문 옵션 비교
            foreach ($orders as $order) {

                $ex_op = $order -> options_string;

                if($new_op == $ex_op) {
                    return true;
                }
            }

        }

        return $flag;
    }

    // 주문번호로 취소 요청, 반품 요청 존재 확인
    public static function countOrderNum($order_num)
    {
        return DB::table('order_data') -> where('order_number', "=", $order_num) -> whereIn('payment_state_code', ['PSCR', 'PSCC', 'PSRR', 'PSRC', 'PSER', 'PSEC','PSWC']) -> exists();
    }

    // 취소 요청 시 기존 주문 취소 대기로 변경
    public static function changeState_PSWC($order_num)
    {
        $orders = OrderData::where('order_number', $order_num) -> get();
        foreach($orders as $order) {
            if ($order -> payment_state_code == 'PSCR' || $order -> payment_state_code == 'PSRR' || $order -> payment_state_code == 'PSER') {
                continue;
            } else {
                $order -> payment_state_code = 'PSWC';
                $order -> save();
            }
        }
    }

    // 옵션 보여주기용 문자열 만들기
    public static function makeOptionDisplay($result_data , $OptionList)
    {
        $ordererData = $OptionList['ordererData'] ? "주문자명/전화번호 : " . $result_data['order_name'] . "/" . $result_data['order_htel'] . "(" . $OptionList['ordererData'] . ")" : "주문자명/전화번호 : " . "(" . $OptionList['ordererData'] . ")";
        $receiverData = $OptionList['receiverData'] ? "받는분/전화번호 : " . $result_data['to_name'] . "/" . $result_data['to_htel'] . "(" . $OptionList['receiverData'] . ")" : "받는분/전화번호 : " . "(" . $OptionList['receiverData'] . ")";
        $deliveryTime = $OptionList['deliveryTime'] ? "배송날짜/시간 : " . $OptionList['deliveryTime'] : "배송날짜/시간 : ";

        $deliveryLeftMsg = $OptionList['deliveryLeftMsg'] ? "좌측문구(보내는분) : " . $OptionList['deliveryLeftMsg'] : "좌측문구(보내는분) : ";
        $deliveryRightMsg = $OptionList['deliveryRightMsg'] ? "우측문구(경조사어) : " . $OptionList['deliveryRightMsg'] : "우측문구(경조사어) : ";

        $etcOption = $OptionList['etcOption'] ? "기타 옵션 : " . $OptionList['etcOption'] : "기타 옵션 : ";
        return $ordererData . "\n" . $receiverData . "\n"
            . $deliveryTime . "\n" . $deliveryLeftMsg . "\n" . $deliveryRightMsg . "\n" . $etcOption;
    }

    // 수량 2개 이상 옵션 디스플레이
    public static function makeOptionDisplay2($result_data){

        $op = $result_data['shop_opt_name'];
        $mall = $result_data['shop_cd'];

        $op = str_replace("01.", "", $op);
        $str_arr = explode("1.", $op);
        $display = "";

        if(count($str_arr) == 2) return true;

        ########## 옥션, G마켓, 톡스토어, 11번가 ###########
        if($mall == 'A001' || $mall == 'A006' || $mall == 'B688' || $mall == 'A112') {

            $str_arr = explode("1.주문자", $op);
            if(count($str_arr) > 2) {
                $display = "1.주문자" . $str_arr[1] . $str_arr[0];

                for ($i = 2; $i < count($str_arr); $i++) {
                    $display .= " || 1.주문자" . $str_arr[$i];
                }
            }

            return $display;
            ########## 스마트스토어 ###########
        } elseif($mall == 'A077') {
            $str_arr = explode("1.배송날짜", $op);
            if(count($str_arr) > 2){
                $display .= "1.배송날짜" . $str_arr[1];

                for ($i = 2; $i < count($str_arr); $i++) {
                    $display .= " || 1.배송날짜" . $str_arr[$i];
                }
            }

            return $display;

        } else {
            return $op;
        }
    }

    // 배송날짜 파싱
    public static function parseDeliveryTime($time)
    {

        $today = Date::now();
        $todayFormat = $today -> format('Y-m-d');
        $time = trim($time);
        $deliveryTime = [
            'deliveryTime' => [
                'date' => NULL,
                'time' => $time,
                'check' => 'N'
            ]
        ];

        if ($time == '') return $deliveryTime;
        
        // 해당 글자가 존재하는지 확인
        if (Str::contains($time, ['오늘', '바로', '즉시', '금일'])) {
            $deliveryTime['deliveryTime']['date'] = $todayFormat;
            $deliveryTime['deliveryTime']['time'] = $time;
            return $deliveryTime;
        }

        // 문자열에 숫자가 1개라도 존재하는지 확인
        if (!preg_match("/[0-9]/",$time)) {
            $deliveryTime['deliveryTime']['date'] = $todayFormat;
            $deliveryTime['deliveryTime']['time'] = $time;
            return $deliveryTime;
        }

        // 구글 API
        $deliveryTime = self::useGoogleAPI($time, $deliveryTime);

        // MM*DD 의 경우 체크
        if (empty($deliveryTime['deliveryTime']['date'])) {
            $month = intval(substr($time,0,2));
            $date = intval(substr($time,3,2));

            if($month >= 1 && $month <= 12 && $date >= 1 && $date <= 31){
                $year = date('Y');
                $deliveryTime['deliveryTime']['date'] = Carbon::create($year,$month,$date,'0','0','0','Asia/Seoul') -> toDateString();
                $parse_time = substr($time, 5);
                $deliveryTime['deliveryTime']['time'] = $parse_time;
            }
        }

        // YYYYMMDD 로 시작하는 경우 체크
        if (empty($deliveryTime['deliveryTime']['date'])) {
            $this_year = intval(date('Y'));
            $year = intval(substr($time,0, 4));
            $month = intval(substr($time,4,2));
            $date = intval(substr($time,6,2));

            if($month >= 1 && $month <= 12 && $date >= 1 && $date <= 31) {
                if($year == $this_year || $year == $this_year+1) {
                    $deliveryTime['deliveryTime']['date'] = Carbon::create($year,$month,$date,'0','0','0','Asia/Seoul') -> toDateString();
                    $parse_time = substr($time, 8);
                    $deliveryTime['deliveryTime']['time'] = $parse_time;
                }
            }
        }

        // YYMMDD 로 시작하는 경우 체크
        if (empty($deliveryTime['deliveryTime']['date'])) {
            $this_year = intval(date('y'));
            $year = intval(substr($time,0, 2));
            $month = intval(substr($time,2,2));
            $date = intval(substr($time,4,2));

            if($month >= 1 && $month <= 12 && $date >= 1 && $date <= 31) {
                if($year == $this_year || $year == $this_year+1){
                    $year = date('Y');
                    $deliveryTime['deliveryTime']['date'] = Carbon::create($year,$month,$date,'0','0','0','Asia/Seoul') -> toDateString();
                    $parse_time = substr($time, 6);
                    $deliveryTime['deliveryTime']['time'] = $parse_time;
                }
            }
        }

        // YYYY*MM*DD 로 시작하는 경우 체크
        if (empty($deliveryTime['deliveryTime']['date'])) {
            $this_year = intval(date('Y'));
            $year = intval(substr($time,0, 4));
            $month = intval(substr($time,5,2));
            $date = intval(substr($time,8,2));

            if($month >= 1 && $month <= 12 && $date >= 1 && $date <= 31) {
                if($year == $this_year || $year == $this_year+1){
                    $deliveryTime['deliveryTime']['date'] = Carbon::create($year,$month,$date,'0','0','0','Asia/Seoul') -> toDateString();
                    $parse_time = substr($time, 10);
                    $deliveryTime['deliveryTime']['time'] = $parse_time;
                }
            }
        }

        // 파싱된 날짜 추출
        $parse_Date = Date::parse($deliveryTime['deliveryTime']['date']);
        $parse_today = Date::parse($todayFormat);

        // 파싱된 날짜가 과거이면 오늘로 변경 ( 체크 Y )
        if($parse_Date < $parse_today) {
            $deliveryTime['deliveryTime']['date'] = NULL;
            $deliveryTime['deliveryTime']['time'] = $time;
            $deliveryTime['deliveryTime']['check'] = 'Y';
        }

        // 비어 있다면 오늘 날짜로 입력 ( 체크 Y )
        if(empty($deliveryTime['deliveryTime']['date'])) {
//            $deliveryTime['deliveryTime']['date'] = $todayFormat;
            $deliveryTime['deliveryTime']['time'] = $time;
            $deliveryTime['deliveryTime']['check'] = 'Y';
        }

        return $deliveryTime;
    }

    // 구글 Cloud Natural Language API 라이브러리 사용
    public static function useGoogleAPI($time, $deliveryTime)
    {
        $languageClient = new LanguageClient([
            'keyFilePath' => base_path()."/google_keyFile.json",
            "projectId" => 'My First Project'
        ]);
        $day_str = "";

        if(strpos($time,'오전')!==false){
            $time = str_replace('오전', "", $time);
            $day_str = "오전";

        }else if(strpos($time,'오후')!==false){
            $time =  str_replace('오후', "", $time);
            $day_str = "오후";

        }else if(strpos($time,'am')!==false) {
            $time = str_replace('am', "", $time);
            $day_str = "오전";

        }else if(strpos($time,'pm')!==false) {
            $time = str_replace('pm', "", $time);
            $day_str = "오후";
        }else if(strpos($time,'AM')!==false) {
            $time = str_replace('AM', "", $time);
            $day_str = "오전";

        }else if(strpos($time,'PM')!==false) {
            $time = str_replace('PM', "", $time);
            $day_str = "오후";
        }

        $result = $languageClient->analyzeEntities($time) ->entitiesByType('DATE');

        if($result != [])
        {
            for($i=0; $i<10; $i++){
                if(isset($result[$i])){

                    $year = $result[$i]['metadata']['year'] ?? date('Y');
                    $month = $result[$i]['metadata']['month'] ?? date('m');
                    $date = $result[$i]['metadata']['day'] ?? date('d');
                    $deliveryTime['deliveryTime']['date'] = Carbon::create($year,$month,$date,'0','0','0','Asia/Seoul') -> toDateString();
                    $day = $result[$i]['name'];
//                    $day_arr = self::removeAM_PM($day);
                    $parse_time = str_replace($day, "", $time);
                    $parse_time = $day_str.$parse_time;
                    if($parse_time == '') {
                        $deliveryTime['deliveryTime']['time'] = $time;
                    }else {
                        $deliveryTime['deliveryTime']['time'] = $parse_time;
                    }
                }
            }
        }

        return $deliveryTime;
    }

    // 이름 & 전화번호 파싱
    public static function parse_NameAndPhone($str)
    {

        $data_arr = [
            "name" => "",
            "phone" => ""
        ];

        if($str == '') return $data_arr;

        $idx = array_search(0, str_split($str));

        if($idx) {
            $parse_name = substr($str, 0, $idx);
            $data_arr['name'] = preg_replace("/[^a-zA-Z0-9가-힣()]/", "", $parse_name);
            $data_arr['phone'] = Common::addHyphen(explode($parse_name, $str)[1]);
            return $data_arr;
        }
        return $data_arr;
    }

    // 출고 지시
    public static function set_delivery($order) {

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> put('https://openapi.playauto.io/api/order/instruction', [
            'bundle_codes' => [$order -> db_num.""],
            'auto_bundle' => false
        ]);

        $result = $response -> json();

        DB::table('playauto2_log') -> insert([
            'order_idx' => $order -> order_idx,
            'work_name' => '출고 대기 API',
            'content' => $response
        ]);
        if(isset($result['error_code'])) {
            \Log::error("출고 지시(출고대기 이동) 실패");
            \Log::error("주문 인덱스 : ". $order -> order_idx);

            \Log::error($result['messages']);
        }
        return $result;
    }

    // 송장 입력 API
    public static function delivery_update_data($order) {
        try {
            // 택배사 코드 : 자체배송
            $sender_code = "68";

            if($order -> mall_code === 'MLAC')
                $sender_code = '25';          // 택배사 코드 : 기타

            $year = date('Y');
            $month = date('m');
            $day = date('d');

            // 송장 전송 API (배송 정보 업데이트)
            $response = Http::withHeaders([
                'x-api-key' => self::$api_key,
                'Authorization' => 'Token '.self::get_token()
            ]) -> put('https://openapi.playauto.io/api/order/setInvoice', [
                'change_complete' => true,
                'overwrite' => true,
                'orders' => [[
                    'bundle_no' => $order -> db_num."",                   // db 번호
                    'carr_no' => $sender_code,                       // 택배사 코드 : 자체배송
                    'invoice_no' => $year . $month . $day . '1234'    // 송장번호

                ]]
            ]);

            DB::table('playauto2_log') -> insert([
                'order_idx' => $order -> order_idx,
                'work_name' => '송장 입력 API',
                'content' => $response
            ]);

            if(!Str::contains($response, ['성공'])){
                sleep(2);
                $response = Http::withHeaders([
                    'x-api-key' => self::$api_key,
                    'Authorization' => 'Token '.self::get_token()
                ]) -> put('https://openapi.playauto.io/api/order/setInvoice', [
                    'change_complete' => true,
                    'overwrite' => true,
                    'orders' => [[
                        'bundle_no' => $order -> db_num."",                   // db 번호
                        'carr_no' => $sender_code,                       // 택배사 코드 : 자체배송
                        'invoice_no' => $year . $month . $day . '1234'    // 송장번호

                    ]]
                ]);

                DB::table('playauto2_log') -> insert([
                    'order_idx' => $order -> order_idx,
                    'work_name' => '송장 입력 API 재시도',
                    'content' => $response . "\n By " . $order -> handler
                ]);
            }

            $result = $response -> json();
            return $result[0];

        }catch(Exception $e) {
            Log::error("송장 전송 API 동작 실패");
            Log::error("order_idx : " . $order -> order_idx);

            Log::error($e);
        }

    }

    // 택배사 코드 가져오기
    public static function getDeliveryCode() {
        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> get('https://openapi.playauto.io/api/carriers');

        $result = $response -> json();

        dd($result);

    }
    
    // 쇼핑몰 계정 관리
    public static function update_shop_info($shop) {

        $etc = [];

        $etc['etc1'] = $shop -> etc1;

        if(!empty($shop -> etc2)) $etc['etc2'] = $shop -> etc2;
        if(!empty($shop -> etc3)) $etc['etc3'] = $shop -> etc3;
        if(!empty($shop -> etc4)) $etc['etc4'] = $shop -> etc4;
        if(!empty($shop -> etc5)) $etc['etc5'] = $shop -> etc5;
        if(!empty($shop -> etc6)) $etc['etc6'] = $shop -> etc6;
        if(!empty($shop -> etc7)) $etc['etc7'] = $shop -> etc7;

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> patch('https://openapi.playauto.io/api/shop/edit',[
            'id' => $shop -> site_id,
            'pwd' => $shop -> site_pw,
            'shop_cd' => $shop -> site_code,
            'seller_nick' => $shop -> site_nick,
            'etc' => $etc
        ]);

        $result = $response -> json();

        if(isset($result['success'])) {
            DB::table('playauto2_api') -> where('mall_code', '=',  $shop -> mall_code) -> where('brand_type_code', '=', $shop -> brand_type_code)
                -> update([
                    'account_status' => '성공',
                    'account_memo' => '',
                ]);

            return "성공";
        } else {
            DB::table('playauto2_api') -> where('mall_code', '=',  $shop -> mall_code) -> where('brand_type_code', '=', $shop -> brand_type_code)
                -> update([
                    'account_status' => '실패',
                    'account_memo' => $result['messages'][0]
                ]);
            
            return $result['messages'][0];
        }

    }

    // 주문 취소 마감 관련 중복 확인 체크
    public static function checkCancelOrder($orderCode)
    {
        $return = true;

        // 주문 번호로 조회한 모든 결제 코드 리스트 가져오기
        $result = DB::table('order_data')
            ->select('payment_state_code')
            ->where('order_number', "=", $orderCode)
            ->get();

        // 주문 리스트에 [ 반품 요청, 취소 요청, 교환 요청, 신규 주문 ] 중 하나라도 있을 시 함수 시작
        foreach ($result as $data) {

            if ($data->payment_state_code == 'PSRR' || $data->payment_state_code == 'PSCR' || $data->payment_state_code == 'PSER' || $data->payment_state_code == 'PSWC' || $data->payment_state_code == 'PSDN' ) {
                $return = false;
            }
        }
        return $return;
    }

    // DB UPDATE ( 취소완료 )
    public static function updateCancelData($orderData) {

        // 주문 번호
        $order_number = $orderData['shop_ord_no'];
        if(empty($order_number)){
            $order_number = $orderData['shop_ord_no_real'];
        }

        // 결제 상태 코드 가져오기
        $payment_state_code = self::check_pay_state($orderData);

        // 주문 인덱스 모두 가져오기
        $list = DB::table('order_data')
            -> select('order_idx', 'payment_state_code')
            -> where('order_number',"=", $order_number)
            -> get();

        foreach ($list as $item) {

            // 취소 관련 요청 들어온 주문 삭제
            if($item->payment_state_code == 'PSRR' || $item->payment_state_code == 'PSCR' || $item->payment_state_code == 'PSER') {
                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                // 신규 주문 취소 마감으로 업데이트
            } else {
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['payment_state_code' => $payment_state_code,
                        'refund_amount' => DB::raw('pay_amount'),
                        'pay_amount' => 0,
                        'is_new' => 0,
                        'update_ts' => NOW()
                    ]);

                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['delivery_state_code_before' => DB::raw('delivery_state_code'),
                        'delivery_state_code' => "DLCC"
                    ]);
            }
        }
    }

    public static function delete_work_list():void
    {
        Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token ' . self::get_token()
        ])->delete('https://openapi.playauto.io/api/work/delete/v1.1', [
            'work_nos' => [],
            'is_all' => "Y"
        ]);
    }

    // 출고 완료 주문 조회
    public static function check_delivery_status() {
        // 날짜 포맷
        $today = Carbon::now();
        $todayFormat = $today -> format('Y-m-d');
        $pastFormay = $today -> subDays(3) -> format('Y-m-d');


        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> post('https://openapi.playauto.io/api/orders',[
            'date_type'         => 'wdate',
            'sdate'             => $pastFormay,
            'edate'             => $todayFormat,
            'status'            => ['출고완료']
        ]);

        $result = $response -> json();

//        dd($result['results']);
        return $result['results'];

    }

    // 출고완료 주문 배송중 변경 ( 운송장 전송 )
    public static function send_delivery_order() {

        $data_arr = self::check_delivery_status();

        foreach ($data_arr as $data) {

            $od_id = $data['shop_ord_no'];
            if(empty($od_id)){
                $od_id = $data['shop_ord_no_real'];
            }


            $order_list = OrderData::where('order_number', $od_id) -> get();

            foreach ($order_list as $order) {
                self::send_delivery_toPT($order);
                sleep(2);
            }
        }

        return "SUCCESS";
    }


    public static function test_update() {
        $today = Carbon::now();
        $todayFormat = $today -> subDays(31) -> format('Y-m-d');
        $pastFormay = $today -> subDays(3) -> format('Y-m-d');

        $response = Http::withHeaders([
            'x-api-key' => self::$api_key,
            'Authorization' => 'Token '.self::get_token()
        ]) -> post('https://openapi.playauto.io/api/orders',[
            'date_type'         => 'wdate',
//            'start'             => 1600,
            'length'            => 2500,
            'sdate'             => $pastFormay,
            'edate'             => $todayFormat,
            'status'            => ['ALL'],
            'shop_cd'           => "A077"
        ]);

        $result = $response ->json();

//        dd($result);

//        dd($result['results']);

        if(count($result['results']) == 0) {
            return "finish";
        }

        foreach ($result['results'] as $data) {
            $order = OrderData::where('db_num', $data['bundle_no']) -> get();

//            dd($order);

            if(count($order)>1) {
                continue;
            }
            if($order->isEmpty()) continue;

            $order_number = !empty($data['shop_ord_no']) ? $data['shop_ord_no'] : $data['shop_ord_no_real'];

            if($order[0] -> order_number == $order_number) continue;

            $order[0] -> order_number = $order_number;
            $order[0] -> save();

        }


        return "success";
    }
}
