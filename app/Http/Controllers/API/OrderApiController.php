<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Log;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\CommonCode;
use App\Http\Controllers\Message\KakaoTalkController;

class OrderApiController extends Controller
{
    public function OrderSaveApi(Request $request){
        try{
            $data = $request -> all();
            $order = $data['order'];
            $delivery = $data['delivery'];
            $etc = $data['etc'];

            $order_idx = DB::table('order_data')->max('order_idx') + 1;
            if(DB::table('order_data') -> where('od_id', $order['od_id']) -> exists()) {

                $order_idx = self::updateOrder($order);

            } else {

                if($order['options_type'] == 'O') {
                    DB::table('order_data_image') -> insert([
                        'order_idx' => $order_idx,
                        'filename' => $etc['od_letter_url']
                    ]);
                    $order['options_parse_yn'] = "N";
                }
                // URL
                if($order['options_type'] == 'U') {
                    DB::table('order_data_url') -> insert([
                        'order_idx' => $order_idx,
                        'url' => $etc['od_letter_url']
                    ]);
                    $order['options_parse_yn'] = "N";
                }
//                for($i=0; $i< (int)$order['order_quantity']; $i++) {
                self::orderInsert($order_idx, $order, $delivery);
//                }
            }

            $returnData=array(
                "code" => "200",
                "message" => "성공"
            );

            if($order['payment_state_code'] == 'PSDN') {
                // 결제완료 알림톡 전송

                $response = self::sendATS($order_idx);

                return $response;
//                $result = $response -> json();
//
//                if($result['code'] != 200) {
//                    $returnData=array(
//                        "code" => "200",
//                        "message" => "[주문성공] !! 알림톡 전송 실패 !!"
//                    );
//                }
            }

        }catch(\Exception $e) {
            $data = $request -> all();
            $od_id = $data['order']['od_id'];
            Log::error("[간편주문앱] 주문 가져오기 실패");
            Log::error($e);
            try{
                $kakao = new KakaoTalkController();
                $kakao -> send_error_log($od_id);

            }catch (\Exception $e) {
                Log::error("[간편주문앱] 실패 카톡 전송 실패");
                Log::error($e);
            }


        }

        return response()->json(['code' => '444', 'message' => '실패' ]);
    }

    public static function sendATS($order_idx){
        try {
            $request = new Request();
            $request -> order_idx = $order_idx;
            $request -> template_type = 'pay_complete';
            $request -> payment_number = 1;

            $kakao = new KakaoTalkController();
            return $kakao -> SendATS_one($request);

        }catch(\Exception $e) {
            \Log::error("[간편주문앱] 카톡 전송 실패");
            \Log::error("order_idx : ".$order_idx);
            \Log::error($e);
        }
    }

    protected static function orderInsert($order_idx, $order,$order_delivery){
        $bank_name = !empty($order['bank_info']) ? DB::table('code_of_toss_bank_stock') -> where('code',$order['bank_info']) -> first() -> bank : '';
        $bank_num = '';
        if(!empty($order['bank_num'])) {
            $num = str_split($order['bank_num'], 4);
            $bank_num = $bank_name . " " . implode(" ",$num);
        }

        OrderData::insert([
            'od_id' => $order['od_id'],
            'order_idx' => $order_idx,
            'mall_code' => $order['mall_code'],
            'brand_type_code' => $order['brand_type_code'],
            'order_number' => $order['order_number'],
            'order_time' => $order['order_time'],
            'orderer_mall_id' => $order['orderer_mall_id']?? '',
            'orderer_name' => $order['orderer_name'],
            'orderer_tel' => $order['orderer_tel'],
            'orderer_phone' => $order['orderer_phone'],
            'orderer_email' => $order['orderer_email'],
            'order_quantity' => $order['order_quantity'],
            'payment_type_code' => $order['payment_type_code'],
            'payment_state_code' => $order['payment_state_code'],
            'payment_date' => $order['payment_date'] == '0000-00-00 00:00:00' ? NULL : $order['payment_date'],
            'total_amount' => $order['total_amount'] / $order['order_quantity'],
            'pay_amount' => $order['pay_amount'] / $order['order_quantity'],
            'supply_amount' => $order['supply_amount'] / $order['order_quantity'],
            'admin_regist' => $order['admin_regist'],
            'create_ts' => NOW(),
            'options_string' => $order['options_string']?? '',
            'options_parse_yn' => $order['options_parse_yn']?? '',
            'options_string_display' => $order['options_string_display']?? '',
            'options_type' => $order['options_type'],
            'open_market_goods_url' => $order['open_market_goods_url']?? '',
            'new_order_yn' => 'Y'
        ]);

        OrderPayment::insert([
            'order_idx' => $order_idx,
            'order_number' => $order['order_number'],
            'payment_pg' => 'toss',
            'payment_type_code' => $order['payment_type_code'],
            'payment_state_code' => $order['payment_state_code'],
            'payment_amount' => $order['pay_amount'],
            'payment_key' => $order['payment_key'],
            'payment_mid' => $order['payment_mid'],
            'payment_receipt_url' => $order['payment_receipt_url'],
            'payment_result_json' => $order['payment_result_json'],
            'card_name' => !empty($order['card_info']) ? DB::table('code_of_toss_card') -> where('code',$order['card_info']) -> first() -> card  : '',
            'card_num' => $order['card_num']?? '',
            'bank_name' => !empty($order['bank_info']) ? DB::table('code_of_toss_bank_stock') -> where('code',$order['bank_info']) -> first() -> bank : '',
            'bank_num' => $order['bank_num']?? ''
        ]);

        OrderDelivery::insert([
            'order_idx' => $order_idx,
            'receiver_name' => $order_delivery['receiver_name'],
            'receiver_tel' => $order_delivery['receiver_tel'],
            'receiver_phone' => $order_delivery['receiver_phone'],
            'delivery_date' => $order_delivery['delivery_date'],
            'delivery_time' => $order_delivery['delivery_time'],
            'delivery_post' => $order_delivery['delivery_post'],
            'delivery_address' => $order_delivery['delivery_address'],
            'delivery_card' => $order_delivery['delivery_card'],
            'delivery_ribbon_left' => $order_delivery['delivery_ribbon_left'],
            'delivery_ribbon_right' => $order_delivery['delivery_ribbon_right'],
            'delivery_message' => $order_delivery['delivery_message']?? '',
            'delivery_state_code' => 'DLUD',
            'goods_name' => $order_delivery['goods_name'],
        ]);

        $item_id = OrderItem::max('id') + 1;

        OrderItem::insert([
            'id' => $item_id,
            'order_id' => $order_idx,
            'product_name' => $order['goods_name'],
            'item_total_amount' => $order['total_amount'],
            'product_price' => $order['total_amount'],
        ]);

        if($order['option_price'] != 0) {
            $option_type_id = 2;
            $option_name = $order['option_name'];

            if(Str::contains($order['option_name'], "지역선택:")) {
                $option_type_id = 9;
                $option_name = explode(":", $order['option_name'])[1];

            }elseif (Str::contains($order['option_name'], "받침대선택:")) {
                $option_name = explode(":", $order['option_name'])[1];

            }elseif (Str::contains($order['option_name'], "옵션 선택:")) {
                $option_type_id = 1;
                $option_name = explode(":", $order['option_name'])[1];

            }elseif (Str::contains($order['option_name'], "쌀 선택:")) {
                $option_type_id = 3;
                $option_name = explode(":", $order['option_name'])[1];
            }

            OrderItemOption::insert([
                'order_item_id' => $item_id,
                'option_type_id' => $option_type_id,
                'option_type_name' => optionTypeName($option_type_id),
                'option_name' => $option_name,
                'option_price' => $order['option_price'],
                'balju_option_price' => $order['option_price'],
                'vendor_option_price' => $order['option_price'],
            ]);
        }

        return "success";
    }

    public static function updateOrder($order) {
        $before_order = OrderData::where('od_id', $order['od_id']) -> first();
        $payment_tbl = OrderPayment::where('order_idx', $before_order->order_idx) -> where('payment_number', '1') -> first();

        $card_info = $order['card_info'];
        $band_info = $order['bank_info'];

        if(DB::table('code_of_toss_card_bank') -> where('code_no',$order['card_info']) -> where('type', 'CARD') -> exists()) {
            $card_info = DB::table('code_of_toss_card_bank') -> where('code_no',$order['card_info']) -> where('type', 'CARD') -> first() -> code_name;
        }

        if(DB::table('code_of_toss_card_bank') -> where('code_no',$order['bank_info']) -> where('type', 'BANK') -> exists()) {
            $band_info = DB::table('code_of_toss_card_bank') -> where('code_no',$order['bank_info']) -> where('type', 'BANK') -> first() -> code_name;
        }

        $before_order -> mall_code                 = $order['mall_code'];
        $before_order -> brand_type_code           = $order['brand_type_code'];
        $before_order -> order_number              = $order['order_number'];
        $before_order -> order_time                = $order['order_time'];
        $before_order -> orderer_mall_id           = $order['orderer_mall_id']?? '';
        $before_order -> orderer_name              = $order['orderer_name'];
        $before_order -> orderer_tel               = $order['orderer_tel'];
        $before_order -> orderer_phone             = $order['orderer_phone'];
        $before_order -> orderer_email             = $order['orderer_email'];
        $before_order -> order_quantity            = $order['order_quantity'];
        $before_order -> payment_type_code         = $order['payment_type_code'];
        $before_order -> payment_state_code        = $order['payment_state_code'];
        $before_order -> payment_date              = $order['payment_date'];
        $before_order -> total_amount              = $order['total_amount'];
        $before_order -> pay_amount                = $order['pay_amount'];
        $before_order -> supply_amount             = $order['supply_amount'];
        $before_order -> admin_regist              = $order['admin_regist'];
        $before_order -> create_ts                 = NOW();
        $before_order -> update_ts                 = NOW();
        $before_order -> options_string            = $order['options_string']?? '';
        $before_order -> options_parse_yn          = $order['options_parse_yn']?? '';
        $before_order -> options_type              = $order['options_type'];
        $before_order -> open_market_goods_url     = $order['open_market_goods_url']?? '';
        $before_order -> new_order_yn              = 'Y';
        $before_order -> save();

        $payment_tbl -> order_number               = $order['order_number'];
        $payment_tbl -> payment_pg                 = $order['payment_pg'];
        $payment_tbl -> payment_type_code          = $order['payment_type_code'];
        $payment_tbl -> payment_state_code         = $order['payment_state_code'];
        $payment_tbl -> payment_amount             = $order['pay_amount'];
        $payment_tbl -> payment_key                = $order['payment_key'];
        $payment_tbl -> payment_mid                = $order['payment_mid'];
        $payment_tbl -> payment_receipt_url        = $order['payment_receipt_url'];
        $payment_tbl -> payment_result_json        = $order['payment_result_json'];
        $payment_tbl -> payment_time               = $order['payment_date'];
        $payment_tbl -> card_name                  = $card_info?? '';
        $payment_tbl -> card_num                   = $order['card_num']?? '';
        $payment_tbl -> bank_name                  = $band_info?? '';
        $payment_tbl -> bank_num                   = $order['bank_num']?? '';
        $payment_tbl -> save();

        return $before_order -> order_idx;
    }
}
