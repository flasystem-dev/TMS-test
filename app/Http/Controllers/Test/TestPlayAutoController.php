<?php

namespace App\Http\Controllers\Test;

use Google\Cloud\Language\LanguageClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Utils\Common;

class TestPlayAutoController extends Controller
{
    // 브랜드 코드 ( 꽃총, 칙폭, 사팔, 오만, 바로 )
    protected static $brands = ['BTCP', 'BTCC', 'BTSP', 'BTOM', 'BTBR'];

########################################################################################################################
#################################################### 실행 함수 ##########################################################

    // 플레이오토 쇼핑몰 코드 업데이트 [실행 함수]
    public static function updateMallCode()
    {
        try {
            $brands = self::$brands;
            // api 모든 쇼핑몰 코드 가져오기
            $code_arr = self::getSiteCode();

            foreach ($brands as $brand) {
                // 필요한 쇼핑몰 이름 리스트 가져오기
                $malls_name_arr = self::getMallNameArr($brand);

                // 모든 코드에서 필요한 쇼핑몰 이름과 코드 추출하기
                $malls_list = self::findMalls($code_arr, $malls_name_arr);

                // DB에 코드 업데이트
                self::updateDB($malls_list, $brand);
            }
        } catch(\Exception $e) {
            Log::error("업데이트 API 동작 실패");
            Log::error($e);
        }
    }

    // 신규, 취소, 반품 주문 가져와 DB INSERT [실행 함수]
    public static function getData()
    {
        try {
            $brands = self::$brands;

            foreach ($brands as $brand)
            {
                // 주문 리스트 가져오기
                $orderList = self::getOrders($brand);

                // 테스트용 리스트 갯수 줄이는 함수
                //            $list = self::testMethod($orderLIst, 2);
                // DB INSERT
                if(isset($orderList)){
                    self::setOrderData($orderList, $brand);
                }
            }

        } catch (\Exception $e) {
            Log::error("신규주문 API 동작 실패");
            Log::error($e);
        }
    }

    // 취소마감, 반품마감 주문 가져와 DB UPDATE [실행 함수]
    public static function getCancelData()
    {
        try {
            $brands = self::$brands;

            foreach ($brands as $brand)
            {
                // 취소, 반품 마감 가져오기 (API)
                $orderList = self::getCancelOrders($brand);

                if(isset($orderList)){
                    foreach($orderList as $orderData) {

                        DB::table('test_test') -> insert([
                            'test1' => '취소마감-중복제거 전(마켓 주문번호)',
                            'test2' => $orderData['OrderCode']
                        ]);

                        // 중복 작업 제거
                        if(self::checkCancelOrder($orderData['OrderCode'])) continue;

                        // 취소마감, 반품마감 업데이트 (DB)
                        self::updateCancelData($orderData);

                    }
                }
            }
        } catch (\Exception $e){
            Log::error("취소마감 API 동작 실패");
            Log::error($e);
        }
    }

########################################################################################################################
######################################### 플레이오토 쇼핑몰 코드 업데이트 함수 ###############################################

    // 쇼핑몰 코드 모두 가져오기 API
    public static function getSiteCode() {
        $response = Http::withHeaders([
            'X-API-KEY' => '20b1d9fe52320bda8b704b5cf75e1ac9'
        ]) -> get('https://playapi.api.plto.com/restApi/empapi/getMarketList');
        $obj = $response-> json();
        return $obj['result'];
    }

    // DB 쇼핑몰 이름 리스트
    public static function getMallNameArr($brand): array
    {
        $result = DB::table('playauto_api') -> select('mall') -> where('brand_type_code',"=", $brand) -> get();
        $mallName_arr = [];
        foreach ($result as $obj) {
            $mallName_arr[] = $obj -> mall;
        }
        return $mallName_arr;
    }

    // 필요한 쇼핑몰 이름과 코드만 추출하기
    public static function findMalls($res, $malls): array
    {
        $collection = Collection::make($res);
        $obj_arr = [];
        foreach ($malls as $mall){
            $obj = $collection-> where('name', $mall) -> first();
            $obj_arr[] = $obj;
        }
        return $obj_arr;
    }

    // DB에 코드 업데이트
    public static function updateDB($list, $brand) : void
    {
        foreach($list as $mall) {
            DB::table('playauto_api')
                -> where('mall','=', $mall['name'])
                -> where('brand_type_code','=', $brand)
                -> update(['code' => $mall['code']]);
        }
    }

########################################################################################################################
############################################ 오픈마켓 신규주문 가져오기 함수 ################################################

    // API 주문 리스트 가져오기 (신규주문, 취소, 반품)
    public static function getOrders($brand)
    {
        // api 키 가져오기
        $api_key = self::getAPI_Key($brand);

        // malls 양식 만들기
        $mall_Arr = self::makeMallsObj($brand);

        // 오늘 날짜 포맷
        $today = Carbon::now();
        $todayFormat = $today -> format('Ymd');
        $pastFormay = $today -> subDays(3) -> format('Ymd');

        // 주문 API
        $response = Http::withHeaders([
            'X-API-KEY' => $api_key
        ]) -> get('http://playauto-api.playauto.co.kr/emp/v1/orders/', [
            'malls' => $mall_Arr,
            'states' => ['신규주문', '취소', '반품요청' , '교환요청'],
            'startDate' => $pastFormay,
            'endDate' => $todayFormat
        ]);
        $status = $response -> getStatusCode();
        $orders = $response -> json();

        if($status != 200 ) {
            if( $orders['message'] != "조회된 주문건이 없습니다.") {
                Log::error("[".$brand."] 플레이오토 주문 가져오기 실패 \n ".$response);
            }
            return NULL;
        }

        return $orders;
    }

    // DB INSERT
    public static function setOrderData($orderList, $brand) {
        $cnt = 1;

        foreach ($orderList as $orderData)
        {
            // 주문 번호
            $order_number = $orderData['OrderCode'];

            // 신규 주문, 취소주문 중복 확인
            $check = false;
            if($orderData['OrderState'] == '신규주문') {
                $check = self::checkOrderList($orderData);
            } else {
                $check = self::countOrderNum($order_number);
            }
            if($check) continue;

            // 취소 요청 시 기존 주문 [취소대기]로 변경
            if($orderData['OrderState'] != '신규주문')
                self::changeState_PSWC($order_number);

            // 제품 구매 수량
            $Count = (int)$orderData['Count'];

            // 상품 금액
            $total_amount = (int)$orderData['Price'];

            // 공급가
            $supply_amount = (int)$orderData['SupplyPrice'];

            // 제품 판매 URL 가져오기
            $API_info = self::getMallCode_ItemURL($orderData['SiteCode']);

            // 옵션 파싱 Array
            $optionList = self::parsingOption($orderData['Option'], $orderData['SiteCode'], $Count);

            // 상품 명
            $goods_name = $orderData['ProdName'];

            try {
                // 상품 명 추출
                if(strpos($optionList['etcOption'],'축하') !== false) {
                    if(strpos($optionList['etcOption'],'제품 선택:') !== false) {
                        $goods_name = trim(explode('제품 선택:', $optionList['etcOption'])[1]);
                    }elseif(strpos($optionList['etcOption'],'제품선택:') !== false) {
                        $goods_name = trim(explode('제품선택:', $optionList['etcOption'])[1]);
                    }elseif(strpos($optionList['etcOption'],'상품선택:') !== false) {
                        $goods_name = trim(explode('상품선택:', $optionList['etcOption'])[1]);
                    }else {
                        $item_arr = explode('축하', $optionList['etcOption']);
                        if(empty($item_arr[0])) {
                            Log::error("[상품명 파싱 실패] 주문번호 : ".$order_number." | 기타 옵션 : ".$optionList['etcOption']);
                        } else {
                            $item_str = explode($item_arr[0], $optionList['etcOption'])[1];
                            $goods_name = trim(explode("/", $item_str)[0]);
                        }
                    }
                } elseif(strpos($optionList['etcOption'],'근조') !== false && strpos($optionList['etcOption'],'오브제') === false) {
                    if(strpos($optionList['etcOption'],'제품 선택:') !== false) {
                        $goods_name = trim(explode('제품 선택:', $optionList['etcOption'])[1]);
                    }elseif(strpos($optionList['etcOption'],'제품선택:') !== false) {
                        $goods_name = trim(explode('제품선택:', $optionList['etcOption'])[1]);
                    }elseif(strpos($optionList['etcOption'],'상품선택:') !== false) {
                        $goods_name = trim(explode('상품선택:', $optionList['etcOption'])[1]);
                    }else {
                        $item_arr = explode('근조', $optionList['etcOption']);
                        if(empty($item_arr[0])) {
                            Log::error("[상품명 파싱 실패] 주문번호 : ".$order_number." | 기타 옵션 : ".$optionList['etcOption']);
                        }else {
                            $item_str = explode($item_arr[0], $optionList['etcOption'])[1];
                            $goods_name = trim(explode("/", $item_str)[0]);
                        }
                    }
                } elseif(strpos($optionList['etcOption'],'제품 선택') !== false) {
                    $goods_name = explode('제품 선택:', $optionList['etcOption'])[1];
                } elseif(strpos($optionList['etcOption'],'제품선택') !== false) {
                    $goods_name = explode('제품선택:', $optionList['etcOption'])[1];
                }
                if(strpos($goods_name,'-1개') !== false) {
                    $goods_name = explode('-1개', $goods_name)[0];
                }
                $goods_name = preg_replace('/[0-9]{2}\./', '', $goods_name);

            } catch(Exception $e){
                Log::error("[상품명 파싱 실패] 주문번호 : ".$order_number." | 기타 옵션 : ".$optionList['etcOption']);
                Log::error($e);
                $goods_name = $orderData['ProdName'];
            }



            // display용 옵션 파싱
            $option_display = self::makeOptionDisplay($optionList,$orderData['OrderName'],$orderData['OrderHtel'], $orderData['RecipientName'], $orderData['RecipientHtel'], $optionList['Card']  );

            if($Count > 1) {
                $option_display = self::makeOptionDisplay2($orderData['Option'], $orderData['SiteCode']);
                $total_amount = $total_amount / $Count;
                $supply_amount = $supply_amount / $Count;
            }

            if($option_display === true)
                $option_display = self::makeOptionDisplay($optionList,$orderData['OrderName'],$orderData['OrderHtel'], $orderData['RecipientName'], $orderData['RecipientHtel'], $optionList['Card'] );

            // 옵션 파싱 성공 여부 확인
            $option_parse_yn = $optionList['option_parse_yn'];

            // 배송일 데이터 수집 (Google API)
            $deliveryTime = self::parseDeliveryTime($optionList['deliveryTime'] );

            if($deliveryTime['deliveryTime']['check'] == 'Y')
                $option_parse_yn = 'N';

            // 파싱된 옵션 리스트
            $optionList = array_merge($optionList,$deliveryTime);

            // 옵션 주문자 파싱
            $ordererData = self::parse_NameAndPhone($optionList['ordererData']);

            // 옵션 받는자 파싱
            $receiverData = self::parse_NameAndPhone($optionList['receiverData']);

            // Order_data DB용 변수
            $site_code = $API_info->mall_code;
            $brand_type_code = $brand;
            $site_name = $orderData['SiteName'];
            $order_time = $orderData['OrderDate'];
            $orderer_mall_id = $orderData['OrderId'];
            $orderer_name = $orderData['OrderName'];
            $orderer_tel = $orderData['OrderHtel'];
            $orderer_phone = $ordererData['phone'];
            $orderer_email = $orderData['OrderEmail'];
            $options = $orderData['Option'];
            $open_market_goods_url = $orderData['ProdCode'];
            $payment_date = $orderData['CashDate'];
            $payment_state_code = self::getStatusCode($orderData['OrderState']);
            $playauto_date = $orderData['WriteDate'];
            $db_num = $orderData['Number'];
            $pay_amount = $total_amount;

            // Order_delivery DB용 변수
            $receiver_name = $orderData['RecipientName'];
            $receiver_tel = $orderData['RecipientHtel'];
            $receiver_phone = $receiverData['phone'];
            $delivery_date = $optionList['deliveryTime']['date'];
            $delivery_time = $optionList['deliveryTime']['time'];
            $delivery_post = $orderData['RecipientZip'];
            $delivery_address = $orderData['RecipientAddress'];
            $delivery_ribbon_left = $optionList['deliveryLeftMsg'];
            $delivery_ribbon_right = $optionList['deliveryRightMsg'];
            $delivery_message = $orderData['Msg'];
            $delivery_card = "";

            // 스마트스토어의 경우 주문자, 수령인 휴대전화 입력
            if($site_code == 'MLNV') {
                $orderer_phone = $orderer_tel;
                $receiver_phone = $receiver_tel;
            }

            // 톡 스토어 결제 금액 = 공급가
            if($site_code == 'MLKK') {
                $pay_amount = $supply_amount;
                $total_amount = $supply_amount;
            }

            // 카드 메시지일 경우
            if($optionList['Card'] == 'Y') {
                if($site_code == 'MLNV') {
                    $delivery_card = $delivery_ribbon_left."\n". $delivery_ribbon_right;
                } else {
                    $delivery_card = $delivery_ribbon_right."\n".$delivery_ribbon_left;
                }
                $delivery_ribbon_left = "";
                $delivery_ribbon_right = "";
            }



            // 핸드폰 빈칸 확인 후 변경
            if(empty($orderer_phone)) $orderer_phone = $orderer_tel;
            if(empty($receiver_phone)) $receiver_phone = $receiver_tel;

            for($j=0; $j<$Count; $j++) {
                $od_id = (int)(microtime(true)*10) . Str::padLeft($cnt, 2, '0');
                $idx = DB::table('order_data')->max('order_idx') + 1;

                DB::table('order_data')->insert([
                    'od_id' => $od_id,
                    'order_idx' => $idx,
                    'mall_code' => $site_code,
                    'brand_type_code' => $brand_type_code,
                    'order_number' => $order_number,
                    'order_time' => $order_time,
                    'orderer_mall_id' => $orderer_mall_id,
                    'orderer_name' => $ordererData['name'] != '' ? $orderer_name . "/" . $ordererData['name'] : $orderer_name,
                    'orderer_tel' => $orderer_tel,
                    'orderer_phone' => $orderer_phone,
                    'orderer_email' => $orderer_email,
                    'order_quantity' => $Count,
                    'payment_date' => $payment_date,
                    'payment_type_code' => 'PTOP',
                    'payment_state_code' => $payment_state_code,
                    'total_amount' => $total_amount,
                    'pay_amount' => $pay_amount,
                    'supply_amount' => $supply_amount,
                    'admin_regist' => 'API',
                    'playauto_date' => $playauto_date,
                    'create_ts' => NOW(),
                    'options_string' => $options,
                    'options_parse_yn' => $option_parse_yn,
                    'options_string_display' => $option_display,
                    'open_market_goods_url' => $open_market_goods_url,
                    'db_num' => $db_num,
                    'new_order_yn' => 'Y'
                ]);

                DB::table('order_delivery')->insert([
                    'order_idx' => $idx,
                    'order_delivery_number' => 1,
                    'receiver_name' => $receiverData['name'] != '' ? $receiver_name . "/" . $receiverData['name'] : $receiver_name,
                    'receiver_tel' => $receiver_tel,
                    'receiver_phone' => $receiver_phone,
                    'delivery_date' => $delivery_date,
                    'delivery_time' => $delivery_time,
                    'delivery_post' => $delivery_post,
                    'delivery_address' => $delivery_address,
                    'delivery_card' => $delivery_card,
                    'delivery_ribbon_left' => $delivery_ribbon_left,
                    'delivery_ribbon_right' => $delivery_ribbon_right,
                    'delivery_message' => $delivery_message,
                    'delivery_state_code' => 'DLUD',
                    'goods_name' => $goods_name,
                    'use_yn' => 'Y'
                ]);
                $cnt++;

                DB::table('test_test') -> insert([
                    'test1' => '신규주문',
                    'test2' => $idx
                ]);
            }
        };
    }

    // DB에서 malls 양식 만들기 (malls => '쇼핑몰코드:쇼핑몰ID')
    public static function makeMallsObj($brand) {
        $result = DB::table('playauto_api') -> select('code', 'id') -> where('brand_type_code', "=", $brand) -> get();
        $malls = [];
        foreach($result as $data) {
            $malls[] = $data -> code.":".$data ->id;
        }
        return $malls;
    }


    // 배송 옵션 파싱
    public static function parsingOption($op, $mall, $cnt)
    {
        try {
            switch($mall)
            {
                ################################### 옥션 ########################################
                case 'A001' :
                    $str_arr = str_replace('/'.$cnt.'개',"",$op);
                    $str_list = explode("／", $str_arr);

                    $str_arr = explode('1.주문자',$str_list[0]);
                    $etcOption = trim($str_arr[0]);

                    // 주문자명 & 전화번호
                    $ordererData = trim(explode(":",$str_arr[1])[1]);

                    // 받는분 & 전화번호
                    $receiverData = trim(explode(":",$str_list[1])[1]);

                    // 배송 날짜 & 시간
                    $deliveryTime = trim(explode(":",$str_list[2])[1]);

                    // 리본 우측 메세지 (경조사어)
                    $deliveryRightMsg = trim(explode(":",$str_list[3])[1]);

                    // 리본 좌측 메세지 (보내는분)
                    $deliveryLeftMsg = trim(explode(":",$str_list[4])[1]);

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'Card' => 'N',
                        'option_parse_yn' => 'Y'
                    ];

                    if(self::checkCardMsg($op))
                        $optionList['Card'] = 'Y';

                    return $optionList;

                ################################### G마켓 ########################################
                case 'A006' :
                    $str_arr = str_replace('/'.$cnt.'개',"",$op);
                    $str_list = explode(", ",$str_arr);
                    $etcOption = $str_list[0];

                    // 주문자명 & 전화번호
                    $ordererData = trim(explode(":",$str_list[1])[1]);

                    // 받는분 & 전화번호
                    $receiverData = trim(explode(":",$str_list[2])[1]);

                    // 배송 날짜 & 시간
                    $deliveryTime = trim(explode(":",$str_list[3])[1]);

                    // 리본 우측 메세지 (경조사어)
                    $deliveryRightMsg = trim(explode(":",$str_list[4])[1]);

                    // 리본 좌측 메세지 (보내는분)
                    $deliveryLeftMsg = trim(explode(":",$str_list[5])[1]);

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'Card' => 'N',
                        'option_parse_yn' => 'Y'
                    ];

                    if(self::checkCardMsg($op))
                        $optionList['Card'] = 'Y';

                    return $optionList;

                ################################### 스마트스토어 ########################################
                case 'A077' :
                    try {
                        $str_arr = explode(' / ',$op);
                        $cnt_sep = count($str_arr);
                        $deliveryTime = trim(explode(":",$str_arr[0])[1]);
                        if(strpos($op,'리본문구')!==false) {
                            $deliveryLeftMsg = trim(explode(":",$str_arr[1])[1]);
                            $deliveryRightMsg = trim(explode(":",$str_arr[2])[1]);
                        }elseif (strpos($op,'경조사어')!==false) {
                            $deliveryRightMsg = trim(explode(":",$str_arr[1])[1]);
                            $deliveryLeftMsg = trim(explode(":",$str_arr[2])[1]);
                        }else {
                            $deliveryRightMsg = trim(explode(":",$str_arr[1])[1]);
                            $deliveryLeftMsg = trim(explode(":",$str_arr[2])[1]);
                        }



                        $etcOption = '';
                        if($cnt_sep > 3) {
                            for($i=3; $i<$cnt_sep; $i++) {
                                if($i<$cnt_sep-1) {
                                    $etcOption .= $str_arr[$i]." | ";
                                } else {
                                    $etcOption .= $str_arr[$i];
                                }
                            }
                        }

                        $optionList = [
                            'ordererData' => "",
                            'receiverData' => "",
                            'deliveryTime' => $deliveryTime,
                            'deliveryLeftMsg' => $deliveryLeftMsg,
                            'deliveryRightMsg' => $deliveryRightMsg,
                            'etcOption' => $etcOption,
                            'Card' => 'N',
                            'option_parse_yn' => 'Y'
                        ];

                        if(self::checkCardMsg($op))
                            $optionList['Card'] = 'Y';

                        return $optionList;

                    } catch(\Exception $e) {
                        $str_arr = explode(' / 2.',$op);

                        $deliveryTime = trim(explode(":",$str_arr[0])[1]);
                        $str_arr = explode(' / 3.',$str_arr[1]);
                        $deliveryRightMsg = trim(explode(":",$str_arr[0])[1]);
                        $str_arr = explode(' / ',$str_arr[1]);
                        $deliveryLeftMsg = trim(explode(":",$str_arr[0])[1]);

                        $etcOption = '';

                        // 기타 옵션
                        $cnt_sep = count($str_arr);
                        for($i=1; $i<$cnt_sep; $i++) {
                            if($i<$cnt_sep-1) {
                                $etcOption .= $str_arr[$i]." | ";
                            } else {
                                $etcOption .= $str_arr[$i];
                            }
                        }

                        $optionList = [
                            'ordererData' => "",
                            'receiverData' => "",
                            'deliveryTime' => $deliveryTime,
                            'deliveryLeftMsg' => $deliveryLeftMsg,
                            'deliveryRightMsg' => $deliveryRightMsg,
                            'etcOption' => $etcOption,
                            'Card' => 'N',
                            'option_parse_yn' => 'Y'
                        ];

                        if(self::checkCardMsg($op))
                            $optionList['Card'] = 'Y';

                        return $optionList;
                    }


                ################################### 11번가 ########################################
                case 'A112' :

                    // 주문자명 & 전화번호
                    $str_arr = explode(',2.받는분',$op);
                    $ordererData = trim(explode(":",$str_arr[0])[1]);

                    // 받는분 & 전화번호
                    $str_arr = explode(',3.배송',$str_arr[1]);
                    $receiverData = trim(explode(":",$str_arr[0])[1]);

                    // 카드 메시지의 경우 파싱 변경
                    if(self::checkCardMsg($op)) {
                        $str_arr = explode(',4.메시지',$str_arr[1]);
                        $deliveryTime = trim(explode(":",$str_arr[0])[1]);

                        $str_arr = explode(',5.메시지',$str_arr[1]);
                        $deliveryRightMsg = trim(explode(":",$str_arr[0])[1]);

                    } else {
                        // 배송 날짜 & 시간
                        $str_arr = explode(',4.경조',$str_arr[1]);
                        $deliveryTime = trim(explode(":",$str_arr[0])[1]);

                        // 리본 우측 메세지 (경조사어)
                        $str_arr = explode(',5.보내',$str_arr[1]);
                        $deliveryRightMsg = trim(explode(":",$str_arr[0])[1]);
                    }

                    // 리본 좌측 메세지 (보내는분)
                    $str_arr = explode(',',$str_arr[1]);
                    $deliveryLeftMsg = trim(explode(":",$str_arr[0])[1]);

                    // 기타 옵션
                    $etcOption = $str_arr[1];

                    for($i=2; $i<count($str_arr); $i++){
                        $etcOption .= " | ".$str_arr[$i];
                    }

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'Card' => 'N',
                        'option_parse_yn' => 'Y'
                    ];

                    if(self::checkCardMsg($op))
                        $optionList['Card'] = 'Y';


                    return $optionList;

                ################################### 톡스토어 ########################################
                case 'B688' :
                    $str_arr = explode(",1.", $op);
                    $etcOption = $str_arr[0];

                    // 주문자명 & 전화번호
                    $str_arr = explode(",2.",$str_arr[1]);
                    $ordererData = trim(explode("핸드폰/",$str_arr[0])[1]);

                    // 받는분 & 전화번호
                    $str_arr = explode(",3.",$str_arr[1]);
                    $receiverData = trim(explode("핸드폰/",$str_arr[0])[1]);

                    // 배송 날짜 & 시간
                    $str_arr = explode(",4.",$str_arr[1]);
                    $deliveryTime = trim(explode("시간/",$str_arr[0])[1]);

                    // 카드 메시지 일 경우 파싱 방법 변경
                    if(self::checkCardMsg($op)) {
                        $str_arr = explode(",5.",$str_arr[1]);
                        $deliveryRightMsg = trim(explode("메시지1 (선택)/",$str_arr[0])[1]);
                        $deliveryLeftMsg = trim(explode("메시지2 (선택)/",$str_arr[1])[1]);

                    } else {
                        // 리본 우측 메세지 (경조사어)
                        $str_arr = explode(",5.",$str_arr[1]);
                        $deliveryRightMsg = trim(explode("(우측리본)/",$str_arr[0])[1]);

                        // 리본 좌측 메세지 (보내는분)
                        $deliveryLeftMsg = trim(explode("(좌측리본)/",$str_arr[1])[1]);
                    }

                    $optionList = [
                        'ordererData' => $ordererData,
                        'receiverData' => $receiverData,
                        'deliveryTime' => $deliveryTime,
                        'deliveryLeftMsg' => $deliveryLeftMsg,
                        'deliveryRightMsg' => $deliveryRightMsg,
                        'etcOption' => $etcOption,
                        'Card' => 'N',
                        'option_parse_yn' => 'Y'
                    ];

                    if(self::checkCardMsg($op))
                        $optionList['Card'] = 'Y';


                    return $optionList;
                ############################### 메이크샵, 쿠팡 ########################################
                case 'B118' :
                case 'B378' :
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

                ############################### 인터파크 ########################################
                case 'A027' :

                    $optionList = [
                        'ordererData' => "",
                        'receiverData' => "",
                        'deliveryTime' => "",
                        'deliveryLeftMsg' => "",
                        'deliveryRightMsg' => "",
                        'etcOption' => "",
                        'Card' => 'N',
                        'option_parse_yn' => 'Y'
                    ];

                    $str_arr = explode("|", $op);

                    // 기타 옵션
                    $optionList['etcOption'] = trim($str_arr[0]);

                    // 주문자 정보
                    $str_arr = explode("보내는분",$str_arr[1]);

                    $str_arr = explode("/", $str_arr[1]);

                    if(count($str_arr) == 6) {
                        $optionList['deliveryTime'] = trim($str_arr[1]);
                        $optionList['ordererData'] = trim($str_arr[2]);
                        $optionList['ordererData'] .= " / ".trim($str_arr[3]);
                        $optionList['deliveryRightMsg'] = trim($str_arr[4]);
                        $optionList['deliveryLeftMsg'] = trim($str_arr[5]);

                    } else {
                        foreach ($str_arr as $str){
                            $optionList['etcOption'] .= " | ".$str;
                        }
                    }

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
                'Card' => 'N',
                'option_parse_yn' => 'N'
            ];
            return $optionList;
        }
    }

    // 수량 2개 이상 옵션 디스플레이
    public static function makeOptionDisplay2($op, $mall){
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

    // 옵션 보여주기용 문자열 만들기
    public static function makeOptionDisplay($OptionList, $od_name, $od_phone, $re_name, $re_phone, $check_card)
    {
        $ordererData = $OptionList['ordererData'] ? "주문자명/전화번호 : " . $od_name . "/" . $od_phone . "(" . $OptionList['ordererData'] . ")" : "주문자명/전화번호 : " . "(" . $OptionList['ordererData'] . ")";
        $receiverData = $OptionList['receiverData'] ? "받는분/전화번호 : " . $re_name . "/" . $re_phone . "(" . $OptionList['receiverData'] . ")" : "받는분/전화번호 : " . "(" . $OptionList['receiverData'] . ")";
        $deliveryTime = $OptionList['deliveryTime'] ? "배송날짜/시간 : " . $OptionList['deliveryTime'] : "배송날짜/시간 : ";

        // 카드 메시지 일 경우 확인
        if($check_card == 'Y'){
            $deliveryLeftMsg = $OptionList['deliveryLeftMsg'] ? "카드메시지1 : " . $OptionList['deliveryLeftMsg'] : "카드메시지1 : ";
            $deliveryRightMsg = $OptionList['deliveryRightMsg'] ? "카드메시지2 : " . $OptionList['deliveryRightMsg'] : "카드메시지2 : ";
        } else {
            $deliveryLeftMsg = $OptionList['deliveryLeftMsg'] ? "좌측문구(보내는분) : " . $OptionList['deliveryLeftMsg'] : "좌측문구(보내는분) : ";
            $deliveryRightMsg = $OptionList['deliveryRightMsg'] ? "우측문구(경조사어) : " . $OptionList['deliveryRightMsg'] : "우측문구(경조사어) : ";
        }

        $etcOption = $OptionList['etcOption'] ? "기타 옵션 : " . $OptionList['etcOption'] : "기타 옵션 : ";
        return $ordererData . "\n" . $receiverData . "\n"
            . $deliveryTime . "\n" . $deliveryLeftMsg . "\n" . $deliveryRightMsg . "\n" . $etcOption;
    }

    // 쇼핑몰 코드 가져오기
    public static function getMallCode_ItemURL($siteCode)
    {
        $result = DB::table('playauto_api') -> select('mall_code') -> where('code','=',$siteCode) -> get();

        return $result[0];
    }

    // 플레이오토 브랜드별 api 키 가져오기
    public static function getAPI_Key($brand)
    {
        $result = DB::table('code_of_company_info') -> select('playauto_api_key') -> where('brand_type_code','=',$brand) -> get();
        return $result[0] -> playauto_api_key;
    }


    // 배송날짜 파싱
    public static function parseDeliveryTime()
    {
        $time = "2024/03/16 11:00";

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

        dd($deliveryTime);

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


        dd($deliveryTime);
        return $deliveryTime;
    }


    // 구글 Cloud Natural Language API 라이브러리 사용
    public static function useGoogleAPI($time, $deliveryTime)
    {
        $languageClient = new LanguageClient([
            'keyFilePath' => '/var/www/flasystem_net/app/Util/key.json',
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


        dd($result);
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

    // 오전 오후 명칭 제거 (Google API 에서 구분 후 시간 추출을 위한 메서드)
    public static function removeAM_PM($time){
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

        $day_arr = [
            'day' => $time,
            'day_str' => $day_str
        ];
        return $day_arr;
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

    // 결제 상태 코드 가져오기
    public static function getStatusCode($status) {

        switch($status){
            case '신규주문' :
                return "PSDN";
            case '취소' :
                return "PSCR";
            case '반품요청' :
                return "PSRR";
            case '교환요청' :
                return "PSER";
        }
    }

    // 주문번호 존재하는지 확인
    public static function checkOrderList($orderData)
    {
        $orders = DB::table('order_data') -> where('order_number', "=", $orderData['OrderCode']) -> get();
        $cnt = count($orders);
        $check = 0;
        if($cnt > 0) {
            $mall = $orderData['SiteCode'];
            if($mall == 'B688') {
                $new_price = (int)$orderData['SupplyPrice'] / (int)$orderData['Count'];
            } else {
                $new_price = (int)$orderData['Price'] / (int)$orderData['Count'];
            }
            foreach ($orders as $order) {
                $ex_price = $order -> total_amount;
                if($new_price != $ex_price) {
                    $check++;
                }
            }
            if($check == $cnt ) {
                return false;
            }else {
                return true;
            }
        } else {
            return false;
        }

    }

    // 주문번호로 취소 요청, 반품 요청 존재 확인
    public static function countOrderNum($order_num)
    {
        return DB::table('order_data') -> where('order_number', "=", $order_num) -> whereIn('payment_state_code', ['PSCR', 'PSRR', 'PSER','PSWC']) -> exists();
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

    // 카드 메시지인지 옵션 확인
    public static function checkCardMsg($op)
    {
        $result = false;
        if (str_contains($op, '메시지1')) return true;
        if (str_contains($op, '카드메세지')) return true;

        return $result;
    }

    public static function testMethod($orderList, $num)
    {
        $list = [];
        for($i=0; $i<$num; $i++){
            $list[] = $orderList[$i];
        }
        return $list;
    }

########################################################################################################################
############################################### 취소, 반품 마감 함수 ######################################################

    // API 주문 가져오기 (취소마감, 반품마감)
    public static function getCancelOrders($brand)
    {
        // api 키 가져오기
        $api_key = self::getAPI_Key($brand);

        // malls 양식 만들기
        $mall_Arr = self::makeMallsObj($brand);

        // 오늘 날짜 포맷
        $today = Carbon::now();
        $todayFormat = $today -> format('Ymd');
        $pastFormay = $today -> subDays(5) -> format('Ymd');

        // 주문 API
        $response = Http::withHeaders([
            'X-API-KEY' => $api_key
        ]) -> get('http://playauto-api.playauto.co.kr/emp/v1/orders/', [
            'malls' => $mall_Arr,
            'states' => ['취소마감', '반품마감', '교환마감'],
            'startDate' => $pastFormay,
            'endDate' => $todayFormat
        ]);
        $status = $response -> getStatusCode();

        if($status != 200) {
//            Log::error("[".$brand."] 플레이오토 취소 마감 주문 가져오기 실패 \n ".$response);
            return NULL;
        }

        return $response -> json();
    }

    // DB UPDATE
    public static function updateCancelData($orderData) {

        // 주문 번호
        $order_number = $orderData['OrderCode'];

        // 결제 상태 코드 가져오기
        $payment_state_code = self::getPSCode($orderData['OrderState']);

        // 주문 인덱스 모두 가져오기
        $list = DB::table('order_data')
            -> select('order_idx', 'payment_state_code')
            -> where('order_number',"=", $order_number)
            -> get();

        foreach ($list as $item) {
            DB::table('test_test') -> insert([
                'test1' => '취소마감-중복제거 후',
                'test2' => $item -> order_idx
            ]);

            // 취소 관련 요청 들어온 주문 삭제
            if($item->payment_state_code == 'PSRR' || $item->payment_state_code == 'PSCR' || $item->payment_state_code == 'PSER') {
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> delete();
                DB::table('test_test') -> insert([
                    'test1' => '취소마감-주문서 삭제',
                    'test2' => $item -> order_idx
                ]);
                // 신규 주문 취소 마감으로 업데이트
            } else {
                DB::table('order_data')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['payment_state_code' => $payment_state_code,
                        'refund_amount' => DB::raw('pay_amount'),
                        'pay_amount' => 0,
                        'new_order_yn' => 'N',
                        'update_ts' => NOW()
                    ]);

                DB::table('order_delivery')
                    -> where('order_idx', "=", $item -> order_idx)
                    -> update(['delivery_state_code_before' => DB::raw('delivery_state_code'),
                        'delivery_state_code' => "DLCC"
                    ]);
                DB::table('test_test') -> insert([
                    'test1' => '취소마감-취소마감 업데이트',
                    'test2' => $item -> order_idx
                ]);
            }
        }
    }

    // 주문 취소 관련 결제 코드 가져오기
    public static function getPSCode($status)
    {
        switch($status){
            case '취소마감' :
                return "PSCC";
            case '반품마감' :
                return "PSRC";
            case '교환마감':
                return "PSEC";
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

########################################################################################################################
####################################################  배송 API 함수 ######################################################

    // 송장 전송 API
    public static function setDeliveryState($order){

        // 택배사 코드 : 자체배송
        $sender_code = 'T064';

        if($order -> mall_code == 'MLAC')
            $sender_code = 'T018';          // 택배사 코드 : 기타

        // api 키 가져오기
        $api_key = self::getAPI_Key($order -> brand_type_code);

        $year = date('Y');
        $month = date('m');
        $day = date('d');

        $response = Http::withHeaders([
            'X-API-KEY' => $api_key
        ]) -> patch('http://playauto-api.playauto.co.kr/emp/v1/senders', [
            'changeState' => 'true',
            'overWrite' => 'true',
            'data' => [[
                'number' => $order -> db_num,                   // db 번호
                'sender' => $sender_code,                       // 택배사 코드 : 자체배송
                'senderno' => $year . $month . $day . '1234'    // 송장번호

            ]]
        ]);

    }

    // 플레이 오토 택배사 코드 가져오기
    public static function getDeliveryCode(){
        $api_key = '20b1d9fe52320bda8b704b5cf75e1ac9';

        $response = Http::withHeaders([
            'X-API-KEY' => $api_key
        ]) -> get('https://playapi.api.plto.com/restApi/empapi/getDelivCode');
        $res = $response -> json();

        return $res;
    }

}

