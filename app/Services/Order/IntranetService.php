<?php
namespace App\Services\Order;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Services\Order\OrderDetailService;

use App\DTOs\OrderProduct;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderItemOption;
use App\Models\Product\Product;

class IntranetService
{

    public static function makeOrderProduct($input)
    {
        return OrderProduct::makeProductFromInput($input);
    }

    public static function makeBaljuData($order,OrderProduct $orderProduct,  $input)
    {
        $brand = DB::table('code_of_company_info')->where('brand_type_code', $order->brand_type_code)->first();
        $product = Product::find($order->item->product_id);

        $send_id = $order -> od_id;
        if(!empty($order->delivery->send_id)) {
            $send_id .= rand(10,99);
        }

        $data = [
            'rw_sender'         => 100,
            'rw_style'          => 0,
            'rw_method'         => "html",
            'rw_sno'            => $send_id,                 // 쇼핑몰 주문번호
            'rw_returnurl'      => '',
            'rw_rosewebid'      => $brand->roseweb_id,
            'rw_rosewebpw'      => $brand->roseweb_pw,
            'rw_sendsms'        => 'Y',
            'rw_sendfax'        => 'Y',
            'rw_assoc'          => 'flowercenter',
            'rw_associd'        => $brand->intranet_id,           // 발송협회 아이디
            'rw_sujuid'         => $input['receive_shop_id'],       // 발송협회 수주아이디
            'rw_bdate'          => $input['delivery_date'],         // 배송일
            'rw_btime'          => $input['delivery_time'],         // 배송시간
            'rw_menucode'       => self::set_menuCode($product),    // 상품코드
            'rw_menu_etc'       => $input['intranet_product_name'],               // 상품 추가 설명
            'rw_qty'            => 1,                // 수량
            'rw_price'          => $orderProduct->balju_price + $orderProduct->balju_options_amount,          // 결제금액, 발주금액
            'rw_origin_price'   => $input['price_view_none'] ? 1 : $order->total_amount,  // 원청금액
            'rw_jname'          => $input['orderer_name'],          // 주문자
            'rw_jtel'           => $input['orderer_tel'],           // 주문전화
            'rw_jhandtel'       => $input['orderer_phone'],         // 주문핸드폰
            'rw_aname'          => $input['receiver_name'],         // 받는분
            'rw_atel'           => $input['receiver_tel'],          // 받는분전화
            'rw_ahandtel'       => $input['receiver_phone'],        // 받는분핸드폰
            'rw_arrive_place1'  => $input['delivery_address'],      // 배송지1
            'rw_kyungjo'        => $input['delivery_ribbon_right'], // 경조사어
            'rw_sendpeople'     => $input['delivery_ribbon_left'],  // 보내는분
            'rw_card'           => $input['delivery_card'],         // 카드메세지
//            'add2'              => '',                     // 메모
            'rw_custreq'        => $input['rw_custreq'],
//                'rw_shopreq1'       => "",                              // 화원요구사항
            'rw_photourl'       => $input['goods_url'],                // 이미지경로
            'rw_type'           => 'hosting',
            'rw_paymethod'      => 'card',
            'rw_writer'         => $input['handler'],
            'rw_dica'           => $input['rw_dica']? "Y" : "N",
            'rw_happycall'      => $input['rw_happycall']? "Y" : "N",
            //            'rw_item_name'      =>
        ];

        $data['rw_item_name'] = "";
        $data['rw_item_price'] = "";

        $balju_options_amount = 0;
        $vendor_options_amount = 0;

        if(!empty($orderProduct -> options)){
            foreach($orderProduct -> options as $key => $option){

                if($option['option_id']=== "0") {
                    $model_option = new OrderItemOption();
                    $model_option -> order_item_id = $order->item->id;
                    $model_option -> option_type_id = 10;
                    $model_option -> option_type_name = "기타";
                    $model_option -> option_price_id = 0;
                    $model_option -> option_name = $option['option_name'];
                    $model_option -> option_price = 0;
                    $model_option -> is_view = 0;

                }else {
                    $model_option = OrderItemOption::find($option['option_id']);
                }

                if($key!==count($orderProduct -> options)-1) {
                    $data['rw_item_name'] .= $option['option_name'].",";
                    $data['rw_item_price'] .= $option['balju_option_price'].",";
                }else {
                    $data['rw_item_name'] .= $option['option_name'];
                    $data['rw_item_price'] .= $option['balju_option_price'];
                }

                $model_option -> balju_option_price = (int)$option['balju_option_price'];
                $model_option -> vendor_option_price = (int)$option['vendor_option_price'];

                $balju_options_amount += (int)$option['balju_option_price'];
                $vendor_options_amount += (int)$option['vendor_option_price'];

                $model_option -> save();
            }
        }
        $order -> item -> balju_options_amount = $balju_options_amount;
        $order -> item -> vendor_options_amount = $vendor_options_amount;
        $order -> item -> save();

        $order -> delivery -> send_id = $send_id;
        $order -> delivery -> save();

        return $data;
    }

    public static function balju_order($data)
    {
        $url = "http://ext2intra.roseweb.co.kr/intranet_post.html";

        $response = Http::asForm()->post($url, $data);

        $result = $response->body();

        DB::table('test_table') -> insert([
            'test1' => "인트라넷 발주 결과",
            'test2' => $result,
            'test3' => $response,
        ]);

        return $result;
    }

    public static function updateOrderData($order , OrderProduct $orderProduct)
    {
        $order -> balju_amount = $orderProduct -> balju_price;
        $order -> vendor_amount = $orderProduct -> vendor_price;
        $order -> is_new = 0;
        $order -> handler = Auth::user() -> name;

        $order -> delivery -> delivery_state_code = "DLSP";
        $order -> delivery -> is_balju = 1;

        if(!empty($orderProduct->options)) {
            foreach($orderProduct -> options as $option){
                $is_view = 1;
                if($option['option_type_id'] === 10) {
                    $is_view = 0;
                }

                OrderItemOption::updateOrCreate(
                    [
                        'order_item_id' => $order->item->id,
                        'option_type_id' => $option['option_type_id'],
                        'option_name' => $option['option_name']
                    ],
                    [
                        'option_type_name' => $option['option_type_name'],
                        'option_price_id' => $option['option_price_id'],
                        'option_price' => $option['option_price'],
                        'balju_option_price' => $option['balju_option_price'],
                        'vendor_option_price' => $option['vendor_option_price'],
                        'is_view' => $is_view,
                    ]
                );
            }
        }

        $order -> save();
        $order -> delivery -> save();

        DB::table('order_log') -> insert([
            'od_id' => $order -> od_id,
            'log_by_name' => Auth::user() -> name,
            'log_status' => "발주",
            'log_content' => "인트라넷 발주"
        ]);
    }


########################################################################################################################
########################################################################################################################

    // 상품명으로 인트라넷 상품코드 변경
    public static function set_menuCode($product) {
        switch ($product->ctgyA) {
            case 'A1':
                if(Str::contains($product->name, '3단')) {
                    return "35";    // 축하 3단
                }elseif(Str::contains($product->name, '쌀')) {
                    return "44";    // 쌀화환
                }elseif(Str::contains($product->name, '4단')) {
                    return "37";    // 축하 4단
                }else {
                    return "08";    // 축하
                }
            case 'A2':
                if(Str::contains($product->name, '3단')) {
                    return "39";    // 근조 3단
                }elseif(Str::contains($product->name, '4단')) {
                    return "42";    // 근조 4단
                }elseif(Str::contains($product->name, '오브제')) {
                    return "45";    // 오브제
                }elseif(Str::contains($product->name, '바구니')) {
                    return "41";    // 근조바구니
                }else {
                    return "09";    // 근조
                }
            case 'A3':
                return "04";    // 동양란
            case 'A4':
                return "12";    // 서양란
            case 'A5':
            case 'A6':
                return "05";    // 관엽식물
            case 'A7':
                return "01";    // 꽃다발
            case 'A8':
                return "02";    // 꽃바구니
            default :
                return "11";    // 기타
        }
    }

    // UTF-8 => euc-kr 인코딩
    public static function incodingToEucKr(array $array) {
        $hasError = false;
        $errorMsg = '';

        $array = array_map(function ($value) use (&$hasError, &$errorMsg) {
            if (is_array($value)) {
                $result = self::incodingToEucKr($value); // 재귀적으로 배열의 값을 변환
                if ($result['state'] === false) {
                    // 재귀 호출에서 오류가 발생한 경우
                    $hasError = true;
                    $errorMsg = $result['msg'];
                    return $value; // 에러가 있는 경우 원래 값을 반환
                }
                return $result['data'];
            } else if (is_string($value)) {
                try {
                    return iconv( "UTF-8","EUC-KR", $value);
                }catch (\Exception $e) {
                    \Log::error("[인트라넷 인코딩 오류]");
                    \Log::error($value);
                    \Log::error($e->getMessage());

                    // 개별 문자 확인
                    $invalidCharacters = [];

                    for ($i = 0, $iMax = mb_strlen($value, 'UTF-8'); $i < $iMax; $i++) {
                        $char = mb_substr($value, $i, 1, 'UTF-8');
                        try {
                            // 개별 문자를 인코딩 시도
                            iconv("UTF-8", "EUC-KR", $char);
                        } catch (\Exception $e) {
                            // 인코딩할 수 없는 문자일 경우 배열에 추가
                            $invalidCharacters[] = $char;
                        }
                    }

                    $hasError = true;
                    $invalidCharsStr = implode(', ', $invalidCharacters);

                    $errorMsg = "문제 지점 : ".$value."\n"."문제 발생 문자 : ".$invalidCharsStr."\n\n인트라넷에서 인식 할수 없는 내용이 포함되어 있습니다.\n문제 발생 문자를 확인해주세요.";

                    return $value;
                }
            }
            return $value; // 문자열이 아닌 경우 변환하지 않음
        }, $array);

        if($hasError) {
            return ['state'=>true, 'msg'=> $errorMsg];
        }

        return ['state' => false, 'data'=>$array];
    }
}