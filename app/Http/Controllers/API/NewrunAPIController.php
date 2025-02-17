<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\CodeOfCompanyInfo;
use App\Models\CommonCode;

use App\Http\Controllers\API\PlayAuto2APIController;

class NewrunAPIController extends Controller
{

    ####################################################################################################################
    ####################################################  실행 함수  ####################################################

    #뉴런 발주 전송
    public static function sendOrderToNewrun(Request $request) {
        try {
            $order_idx = $request -> order_idx;
            // 등록자 이름
            $register = $request -> register;

            $order = OrderData::find($order_idx);
            $delivery = OrderDelivery::find($order_idx);

            if($request -> check != 1){
                // 이미 처리된 주문인지 체크
                if (!empty($delivery -> send_id)) return '이미 전송된 주문입니다.';
            }

            // 유효성 검사
            $validator = self::validatorData($order, $delivery);
            if ($validator->fails()) return self::makeFailMsg($validator);
            return self::sendNewrunAPI($order, $delivery, $register);

        }catch (\Exception $e){
            Log::error("업데이트 API 동작 실패");
            Log::error($e);
        }
    }

    # 배송 완료, 사진 API
    public static function getDeliveryAPI(Request $request){
        try {
            $tid = $request -> tid;

            OrderDelivery::where('send_id', $tid)
                -> update([
                    'delivery_com_time' => $request -> delivery_date,
                    'delivery_photo' => $request -> delivery_photo ?? "",
                    'delivery_state_code_before' => DB::raw('delivery_state_code'),
                    'delivery_state_code' => "DLDN"
                ]);

        }catch (\Exception $e) {
            Log::error("배송완료/사진 API 동작 실패");
            Log::error($e);
        }


    }

    ####################################################################################################################
    ####################################################################################################################
    
    # 뉴런 API 전송
    public static function sendNewrunAPI($order, $delivery, $register){

        $com_info = CodeOfCompanyInfo::where('brand_type_code', $order -> brand_type_code) -> first();
        $nr_url = $com_info -> newrun_api_url;
        $nr_mid = $com_info -> newrun_api_mid;
        $nr_key = $com_info -> newrun_api_key;

        $mall_name = CommonCodeName($order -> mall_code) ?? $order -> mall_code;

        $memo = $order -> options_string_display. "\n". $mall_name . ' / ' . $order -> order_number;

        if($order -> item -> options -> isNotEmpty()){
            foreach ($order -> item -> options as $option) {
                $memo .= "\n" . $option->option_name . " : " . number_format($option->option_price);
            }
        }

        // 메모에 URL 추가
        if($order -> options_type === 'U') {
            $url = DB::table('order_data_url')->where('order_idx', $order->order_idx)->first() -> url;
            $memo .= "\n" . $url;
        }

        $response = Http::asForm() -> post('http://'.$nr_url.'/api/order', [
            'mid' => $nr_mid,
            'mertkey' => $nr_key,
            'buyer_name' => $order -> orderer_name,
            'buyer_phone' => $order -> orderer_phone,
            'buyer_tel' => $order -> orderer_tel,
            'buyer_email' => $order -> orderer_email,
            'delivery_name1' => $delivery -> receiver_name,
            'delivery_phone1' => $delivery -> receiver_phone,
            'delivery_tel1' => $delivery -> receiver_tel,
            'delivery_date1' => $delivery -> delivery_date,
            'delivery_time1' => $delivery -> delivery_time,
            'delivery_post1' => $delivery -> delivery_post,
            'delivery_address1' => $delivery -> delivery_address,
            'delivery_card1' => $delivery -> delivery_card,
            'delivery_ribbon_left1' => $delivery -> delivery_ribbon_left,
            'delivery_ribbon_right1' => $delivery -> delivery_ribbon_right,
            'delivery_message1' => $delivery -> delivery_message,
            'goods_name1' => $delivery -> goods_name,
            'goods_price1' => $order -> total_amount,
            'payment_type' => '20',
            'payment_state' => '20',
            'admin_regist' => $register,
            'admin_memo' => $memo,
            'ord_device' => 'api_TMS'
            ]);

        $result = $response -> json();

        if($result['resultCode'] === '0000'){
            $nr_od_id = $result['resultData']['tid'];
            
            # 뉴런 배송 API 보내기
            $result2 = Http::asForm() -> post('http://'.$nr_url.'/api/delivery', [
                'mid' => $nr_mid,
                'mertkey' => $nr_key,
                'tid' => $nr_od_id,
                'return_url' => route('NewrunDeliveryAPI')
                ]);

            $res = $result2 -> json();
            
            if ($res['resultCode'] == '0000') {

                $order -> is_new = 0;
                $order -> handler = $register;
                $order -> save();

                $delivery -> delivery_state_code_before = DB::raw('delivery_state_code');
                $delivery -> delivery_state_code = 'DLSP';
                $delivery -> send_id = $nr_od_id;
                $delivery -> send_time = NOW();
                $delivery -> is_balju = 1;
                $delivery -> save();

                DB::table('order_log')
                    -> insert([
                        'od_id' => $order -> od_id,
                        'log_by_name' => $register,
                        'log_time' => NOW(),
                        'log_status' => '주문 발주 성공',
                        'log_content' => '뉴런 주문 전송 성공'
                    ]);

                if($order -> group === "openMarket") {
                    PlayAuto2APIController::setDeliveryState($order);
//                    if($result != "SUCCESS") {
//                        return "[플레이오토 에러 발생]".$result;
//                    }
                }

                return "SUCCESS";
            } else {
                DB::table('order_log') -> where('od_id', $order -> od_id) -> where('log_status', '주문 발주') -> delete();


                DB::table('order_log')
                    -> insert([
                        'od_id' => $order -> od_id,
                        'log_by_name' => $register,
                        'log_time' => NOW(),
                        'log_status' => '주문 발주 실패',
                        'log_content' => '뉴런 주문 전송 실패'
                    ]);

                \Log::error("[뉴런] 배송 API 실패");
                \Log::error($result2);

                return "에러코드 : ".$res['resultCode']."\n메시지 : ".$res['resultMsg'];
            }
        } else {

            DB::table('order_log') -> where('od_id', $order -> od_id) -> where('log_status', '주문 발주') -> delete();

            DB::table('order_log')
                -> insert([
                    'od_id' => $order -> od_id,
                    'log_by_name' => $register,
                    'log_time' => NOW(),
                    'log_status' => '주문 발주 실패',
                    'log_content' => '뉴런 주문 전송 실패'
                ]);

            \Log::error("[뉴런] 전송 API 실패");
            \Log::error($response);

            $msg = DB::table('newrun_error_type') -> select('msg') -> where ('code', '=', $result['resultCode']) -> first();
            if($msg == '')
                return "에러코드 : ".$result['resultCode']."\n메시지 : ".$result['resultMsg']."\n개발팀에 문의하세요.";

            return "에러코드 : ".$result['resultCode']."\n메시지 : ".$msg->msg;
        }
    }


    # 주문 정보 유효성 검사
    public static function validatorData($order, $delivery)
    {
        $vali_data = [
            'orderer_name' => $order -> orderer_name,
            'orderer_phone' => $order -> orderer_phone,
            'receiver_name' => $delivery -> receiver_name,
            'receiver_phone' =>  $delivery -> receiver_phone,
            'delivery_date' => $delivery -> delivery_date,
            'delivery_address' => $delivery -> delivery_address,
            'goods_name' => $delivery -> goods_name,
            'pay_amount' => $order -> pay_amount
        ];

        $messages = [
            'orderer_name.required' => '주문자 이름을 확인하세요.',
            'orderer_phone.required' => '주문자 휴대전화를 확인하세요.',
            'receiver_phone.required' =>  '받는분 휴대전화를 확인하세요.',
            'receiver_name.required' =>  '받는분 이름을 확인하세요.',
            'delivery_date.required' => '배송일을 확인하세요.',
            'delivery_address.required' => '배송 주소를 확인하세요.',
            'goods_name.required' => '상품명을 확인하세요.',
            'pay_amount.required' => '상품 가격을 확인하세요.'
        ];

        $validator = Validator::make($vali_data, [
            'orderer_name' => ['required'],
            'orderer_phone' => ['required'],
            'receiver_name' => ['required'],
            'receiver_phone' => ['required'],
            'delivery_date' => ['required'],
            'delivery_address' => ['required'],
            'goods_name' => ['required'],
            'pay_amount' => ['required']
        ], $messages);

        return $validator;
    }

    // 유효성 검사 실패 메시지
    public static function makeFailMsg($validator) {
        $errors = $validator->errors();
        $msg = '';
        foreach ($errors -> all() as $err) {
            $msg.=$err."\n";
        }
        return $msg;
    }

    ####################################################################################################################
    ####################################################################################################################

}
