<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\API\PlayAuto2APIController;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

use Google\Cloud\Language\LanguageClient;

use App\QueryBuilders\Statistics\CalculateSalesQueryBuilder;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;
use App\Models\Order\OrderPaymentDeleted;

use App\Models\OrderCart;
use App\Models\CodeOfCompanyInfo;
use App\Models\OrderView;
use App\Models\CommonCode;
use App\Http\Controllers\Message\KakaoTalkController;
use App\Http\Controllers\Message\SMSController;
use App\Models\TalkTemplate;
use App\Models\TMS_Notification;
use App\Models\TMS_User_Noti;
use App\Models\TMS_Product;
use App\Models\TMS_ProductOption;
use App\Models\Vendor;
use App\Models\ContractCompany;
use App\Models\User;
use App\Models\BoardValue;
use App\Models\BoardNotice;
use App\Models\BoardEvent;
use App\Models\TotalInformations;
use App\Models\Product\Product;
use App\Models\Product\ProductOptionPrice;
use App\Models\Product\ProductPrice;

use App\Services\Vendor\SchedulerService;

use Illuminate\Support\Facades\Mail;

use App\Services\GoogleAPIService;

class TestController extends Controller
{
    public static function test(Request $request) {
        $time = "25년 3월 20일";


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

        $result = GoogleAPIService::dateText($time);

        dd($result);
    }

    public static function get_api2(Request $request) {
        DB::table('test_table') -> insert([
            'test1' => "BMSv2 배송결과 재전송",
            'test2' => $request->getContent()
        ]);
        return response() -> json(["완료"]);
    }

    public static function get_api(Request $request) {
        $orders = $request->input('orders');

        foreach ($orders as $order) {
                $order_idx = OrderData::max('order_idx') + 1;
            try {
                $delivery = $order['delivery'];
                $item = $order['item'];

                $order['order_idx'] = $order_idx;
                $delivery['order_idx'] = $order_idx;
                $item['order_id'] = $order_idx;

                $newOrder = new OrderData();
                $newOrder->fill($order);
                $newOrder->save();

                $newDelivery = new OrderDelivery();
                $newDelivery->fill($delivery);
                $newDelivery->save();

                $newItem = new OrderItem();
                $newItem->fill($item);
                $newItem->save();
            }catch (\Exception $e) {
                \Log::error("데이터 받기 실패");
                \Log::error("order-idx : ". $order_idx);
                \Log::error($e->getMessage());
            }
        }
        
        return response()->json(["완료"]);
    }

    public static function test2(Request $request)
    {
        set_time_limit(600);

        $year_month = "2024-11";
//
        $start = $year_month . "-01 00:00:00";
//
//        $endOfMonth = Carbon::parse($start)->day(15)->format('Y-m-d');
//        $endOfMonth = Carbon::parse($start)->endOfMonth()->format('Y-m-d');
//        $end = $endOfMonth. " 23:59:59";
//        $brand = "BTFC";

        $order_index = 3147004;

        OrderData::with('delivery:order_idx,goods_name,pr_idx,send_name', 'payments:order_idx,payment_item', 'carts', 'vendor')
//            ->whereBetween('create_ts',[$start, $end])
            ->where('order_idx', $order_index)
            ->chunk(1000, function ($orders) {
                DB::transaction(function () use ($orders) {
                    foreach ($orders as $order) {
                        if($order->payments && $order->payments->isNotEmpty()) {
                            $order->payments[0] -> payment_item = $order->delivery->goods_name;
                        }

                        $price_type = 1;
                        $product_id = 0;
                        $product_name = $order->delivery->goods_name ?? '';
                        $product_price = 0;

                        if($order->vendor) {
                            $price_type = $order->vendor->price_type;
                        }

                        if(!empty($order->delivery->pr_idx)) {
                            $tms_product = TMS_Product::find($order->delivery->pr_idx);
                            if($tms_product) {
                                $product = Product::where('code', $tms_product->pr_id)->first();

                                $product_id = $product->id;
                                $product_name = $product->name;
                                $price_data = ProductPrice::where('product_id', $product_id)->where('price_type_id', $price_type)->first();
                                if($price_data) {
                                    $product_price = $price_data->product_price;
                                }
                            }
                        }

                        $flag = false;
                        if($product_id === 0) {
                            if(DB::table('order_items')->where('order_id', $order->order_idx)->exists()) {
                                $flag = true;
                            }
                        }

                        if($flag) {
                            continue;
                        }

                        $options_amount = 0;
                        $balju_options_amount = 0;
                        $vendor_options_amount = 0;

                        if($order->carts && $order->carts->isNotEmpty()) {
                            foreach ($order->carts as $cart) {
                                $options_amount += $cart->option_price ?? 0;
                                $balju_options_amount += $cart->balju_option_price ?? 0;
                                $vendor_options_amount += $cart->vendor_option_price ?? 0;

                            }
                        }

                        $product_price = $product_price !== 0 ? $product_price : $order -> total_amount - $options_amount;

                        $item = OrderItem::updateOrCreate(
                            ['order_id' => $order->order_idx], // 찾을 조건
                            [
                                'product_id' => $product_id,
                                'price_type_id' => $price_type,
                                'product_name' => $product_name,
                                'quantity' => 1,
                                'item_total_amount' => $product_price + $options_amount,
                                'product_price' => $product_price,
                                'options_amount' => $options_amount,
                                'balju_options_amount' => $balju_options_amount,
                                'vendor_options_amount' => $vendor_options_amount
                            ]
                        );
//                $item -> product_id = $product_id;
//                $item -> price_type_id = $price_type;
//                $item -> product_name = $product_name;
//                $item -> quantity = 1;
//
//                $item -> item_total_amount = $order -> total_amount;
//                $item -> product_price = $product_price !== 0 ? $product_price : $order -> total_amount - $options_amount;
//
//                $item -> options_amount = $options_amount;
//                $item -> balju_options_amount = $balju_options_amount;
//                $item -> vendor_options_amount = $vendor_options_amount;
//                $item -> save();

                        if($order->carts && $order->carts->isNotEmpty()) {
                            DB::table('order_item_options')->where('order_item_id', $item -> id) -> delete();

                            foreach ($order->carts as $cart) {
                                $option_type = 8;
                                $option_name = $cart->option_name;

                                if(Str::contains($cart->option_name, '상품구분:')) {
                                    $option_type = 1;
                                    $option_type_name = "상품구분";
                                    $option_name = explode("상품구분:", $cart->option_name)[1];
                                }elseif(Str::contains($cart->option_name, '지역선택:')) {
                                    $option_type = 9;
                                    $option_type_name = "지역추가금";
                                    $option_name = explode("지역선택:", $cart->option_name)[1];
                                }elseif(Str::contains($cart->option_name, '지역추가금:')) {
                                    $option_type = 9;
                                    $option_type_name = "지역추가금";
                                    $option_name = explode("지역추가금:", $cart->option_name)[1];
                                }elseif(Str::contains($cart->option_name, '쌀 선택:')) {
                                    $option_type = 3;
                                    $option_type_name = "쌀선택";
                                    $option_name = explode("쌀 선택:", $cart->option_name)[1];
                                }elseif(Str::contains($cart->option_name, '받침대:')) {
                                    $option_type = 2;
                                    $option_type_name = "옵션";
                                    $option_name = explode("받침대:", $cart->option_name)[1];
                                }elseif(Str::contains($cart->option_name, '옵션 선택:')){
                                    $option_type = 1;
                                    $option_type_name = "상품구분";
                                    $option_name = explode("옵션 선택:", $cart->option_name)[1];
                                }else {
                                    if($cart->is_userView === 0) {
                                        $option_type = 10;
                                        $option_type_name = "기타";
                                    }else {
                                        $option_type_name = "기타옵션";
                                    }
                                }

                                OrderItemOption::insert([
                                    'order_item_id'     => $item -> id,
                                    'option_type_id'       => $option_type,
                                    'option_type_name'  => $option_type_name,
                                    'option_price_id'   => 0,
                                    'option_name'       => $option_name,
                                    'option_price'      => $cart->option_price,
                                    'balju_option_price'    => $cart->balju_option_price ?? 0,
                                    'vendor_option_price'   => $cart->vendor_option_price ?? 0,
                                    'is_view'           => $cart->is_userView ?? 1
                                ]);

//                        $option = new OrderItemOption();
//                        $option -> order_item_id = $item -> id;
//                        $option -> option_type = $option_type;
//                        $option -> option_type_name = $option_type_name;
//                        $option -> option_price_id = 0;
//                        $option -> option_name = $option_name;
//                        $option -> option_price = $cart->option_price;
//                        $option -> balju_option_price = $cart->balju_option_price ?? 0;
//                        $option -> vendor_option_price = $cart->vendor_option_price ?? 0;
//                        $option -> is_view = $cart->is_userView ?? 1;
//                        $option -> save();
                            }
                        }

                        $order -> payment_time = $order -> payment_date;

                        if($order -> admin_discount === 0) {
                            $order -> admin_discount = $order -> discount_amount;
                            $order -> total_amount -= $order -> discount_amount;
                            $order -> discount_amount = 0;
                        }

                        $order -> goods_url = $order -> open_market_goods_url;
                        $order -> is_new = $order -> new_order_yn === 'Y' ? 1 : 0;
                        $order -> handler = $order -> delivery -> send_name ?? "";
                        $order -> save();
                    }
                });
            });
//            ->where('brand_type_code', $brand)
//            ->get();

        return "success".$year_month;
    }


    public static function upsert_product_test() {
        $pr_id = [
            "p_e_25_2_1", "p_e_25_2_2", "p_e_25_2_3", "p_e_25_2_4",
            "c_e_25_2_1", "c_e_25_2_2", "c_e_25_2_3", "c_e_25_2_4",
        ];

        $tms_products = TMS_Product::whereIn('pr_id', $pr_id)->get();


        foreach ($tms_products as $tms_product) {
            Product::upsert([
                'code'  => $tms_product->pr_id,
                'ctgyA' => $tms_product->pr_ctgy1,
                'ctgyB' => $tms_product->pr_ctgy2,
                'ctgyC' => $tms_product->pr_ctgy3,
                'type'  => $tms_product->pr_type,
                'name'  => $tms_product->pr_name,
                'brand'  => $tms_product->pr_brand,
                'img'       =>  $tms_product->pr_img,
                'thumbnail' =>  $tms_product->pr_thumb,
                'description' => $tms_product->pr_description,
                'is_popular' => $tms_product->pr_popular === 'Y' ? 1 : 0,
                'is_discount' => $tms_product->pr_discount === 'Y' ? 1 : 0,
                'delivery_type' => $tms_product->delivery_type,
                'is_used'   => $tms_product->is_used === 'Y' ? 1 : 0,
                'memo'      => $tms_product->pr_memo,
                'search_words'  => $tms_product->search_words
            ],['code'], ['ctgyA', 'ctgyB', 'ctgyC', 'type', 'name', 'brand', 'img', 'thumbnail', 'description', 'is_popular', 'is_discount', 'is_used', 'memo', 'search_words']);

            $product_id = Product::where('code', $tms_product->pr_id) -> value('id');

            ProductPrice::insert([
                'product_id' => $product_id,
                'price_type_id' => 1,
                'product_price'=> $tms_product->pr_amount_type1,
                'balju_price' => $tms_product->pr_amount_type1,
                'vendor_price' => $tms_product->pr_amount_type1
            ]);
        }
        return "product_success2";
    }

    public static function upsert_orderData(Request $request)
    {
        $year_month = "2024-09";

        $start = $year_month . "-01 00:00:00";
        $endOfMonth = Carbon::parse($start)->endOfMonth()->format('Y-m-d');
        $end = $endOfMonth. " 23:59:59";
//        $brand = "BTFC";

        $orders = OrderData::with('delivery', 'payments', 'carts', 'vendor')
            ->whereBetween('create_ts',[$start, $end])
//            ->where('brand_type_code', $brand)
            ->get();

        DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                if($order->payments && $order->payments->isNotEmpty()) {
                    $order->payments[0] -> payment_item = $order->delivery->goods_name;
                }

                if($order->delivery === NULL) {
                    \Log::error("주문 인덱스 : " . $order->order_idx);
                }

                $price_type = 1;
                $product_id = 0;
                $product_name = $order->delivery->goods_name;
                $product_price = 0;

                if($order->vendor) {
                    $price_type = $order->vendor->price_type;
                }

                if(!empty($order->delivery->pr_idx)) {
                    $tms_product = TMS_Product::find($order->delivery->pr_idx);
                    if($tms_product) {
                        $product = Product::where('code', $tms_product->pr_id)->first();

                        $product_id = $product->id;
                        $product_name = $product->name;
                        $price_data = ProductPrice::where('product_id', $product_id)->where('price_type_id', $price_type)->first();
                        if($price_data) {
                            $product_price = $price_data->product_price;
                        }
                    }

                }

                $item = new OrderItem();
                $item -> id = OrderItem::max('id') + 1;
                $item -> order_id = $order->order_idx;
                $item -> product_id = $product_id;
                $item -> price_type = $price_type;
                $item -> product_name = $product_name;
                $item -> quantity = 1;

                $options_amount = 0;
                $balju_options_amount = 0;
                $vendor_options_amount = 0;

                if($order->carts && $order->carts->isNotEmpty()) {
                    foreach ($order->carts as $cart) {
                        $options_amount += $cart->option_price ?? 0;
                        $balju_options_amount += $cart->balju_option_price ?? 0;
                        $vendor_options_amount += $cart->vendor_option_price ?? 0;

                    }
                }

                $item -> item_total_amount = $order -> total_amount;
                $item -> product_price = $product_price !== 0 ? $product_price : $order -> total_amount - $options_amount;

                $item -> options_amount = $options_amount;
                $item -> balju_options_amount = $balju_options_amount;
                $item -> vendor_options_amount = $vendor_options_amount;
                $item -> save();

                if($order->carts && $order->carts->isNotEmpty()) {
                    foreach ($order->carts as $cart) {
                        $option_type = 2;
                        $option_name = $cart->option_name;

                        if(Str::contains($cart->option_name, '상품구분:')) {
                            $option_type = 1;
                            $option_type_name = "상품구분";
                            $option_name = explode("구분:", $cart->option_name)[1];
                        }elseif(Str::contains($cart->option_name, '지역선택:')) {
                            $option_type = 3;
                            $option_type_name = "지역추가금";
                            $option_name = explode("선택:", $cart->option_name)[1];
                        }elseif(Str::contains($cart->option_name, '지역추가금:')) {
                            $option_type = 3;
                            $option_type_name = "지역추가금";
                            $option_name = explode("추가금:", $cart->option_name)[1];
                        }elseif(Str::contains($cart->option_name, '받침대:')) {
                            $option_type_name = "옵션";
                            $option_name = explode("침대:", $cart->option_name)[1];
                        }elseif(Str::contains($cart->option_name, '옵션 선택:')){
                            $option_type = 1;
                            $option_type_name = "상품구분";
                            $option_name = explode("선택:", $cart->option_name)[1];
                        }else {
                            if($cart->is_userView === 0) {
                                $option_type = 4;
                                $option_type_name = "기타";
                            }else {
                                $option_type_name = "옵션";
                            }
                        }

                        $option = new OrderItemOption();
                        $option -> order_item_id = $item -> id;
                        $option -> option_type = $option_type;
                        $option -> option_type_name = $option_type_name;
                        $option -> option_price_id = 0;
                        $option -> option_name = $option_name;
                        $option -> option_price = $cart->option_price;
                        $option -> balju_option_price = $cart->balju_option_price ?? 0;
                        $option -> vendor_option_price = $cart->vendor_option_price ?? 0;
                        $option -> is_view = $cart->is_userView ?? 1;
                        $option -> save();
                    }
                }

                $order -> payment_time = $order -> payment_date;
                $order -> admin_discount = $order -> discount_amount;
                $order -> total_amount -= $order -> discount_amount;
                $order -> discount_amount = 0;

                $order -> goods_url = $order -> open_market_goods_url;
                $order -> is_new = $order -> new_order_yn === 'Y' ? 1 : 0;
                $order -> handler = $order -> delivery -> send_name ?? "";
                $order -> save();
            }
        });

        return "success".$year_month;
    }

    public static function option_type() {

    }

    public static function samga_api_test() {
        $data = [
            'od_id'   => '999999_9999999',
            'od_deli_date'  => "2025-04-01",
            'od_deli_time'  => "지금즉시배송",
            'od_name'       => "테스트3",
            'od_b_name'     => "테스트3",
            'od_b_addr1'    => "테스트 주소3",
            'od_b_addr2'    => "테스트3",
            'od_msg_left'   => "그녀들의 모임'S",
            'od_msg_right'  => "테스트3",
            'od_receipt_price'  => 48000,
            'od_memo'       => "♥♥새꽃으로-제작해주세요(재생x)♥♥
                                빈소별 개별 배송사진 촬영 꼭 해주세요",
            'key'           => '04cb1ddcc25fd932ef101d3e88d39a451f9dd4c7b461d55a3389c24bc3ccbafe',
            'od_it_name'    => "근조장구 1호",
            'product_code'  => 103
        ];

        $url = "https://flabiz.kr/api/partner/samga/test";

        $response = Http::post($url, $data);

        dd($response->json());
    }

    public static function bms_api_test() {
        $data = [
            'key'   => 'testtest1',
            'od_id'  => Carbon::now()->format('YmdHis'),
            'event_URL'  => "https://tms.flabiz.kr",
            'groom_info'       => [
                [
                    'title' => "신랑",
                    'name' => "이준서",
                    'phone' => '010-1111-1111'

                ],
                [
                    'title' => "신랑측 아버지",
                    'name' => "이인수",
                    'phone' => '010-2222-2222'

                ],
                [
                    'title' => "신랑측 어머니",
                    'name' => "이영희",
                    'phone' => '010-3333-3333'

                ]
            ],
            'bride_info'     => [
                [
                    'title' => "신부",
                    'name' => "김은재",
                    'phone' => '010-4444-4444'

                ],
                [
                    'title' => "신부측 아버지",
                    'name' => "김희승",
                    'phone' => '010-5555-5555'

                ]
            ],
            'delivery_address'    => "테스트 주소 123 김해",
            'delivery_date'    => "2024-10-24",
            'delivery_time'   => "13:30:00",
        ];

        $url = "https://partner.flabiz.kr/api/bms/oi";

        return view('test.bms_api_test', compact('data'));
    }

    public static function bms2_api_test() {
        $data = [
            'order_number'  => "1234-1234",
            'order_time'  => Carbon::now()->format('Y-m-d H:i:s'),
            'orderer_name' => "테스트",
            'orderer_phone' => "010-1111-1111",
            'receiver_name' => "받는분 테스트",
            'receiver_phone' => "010-2222-2222",
            'total_amount' => 110000,
            'pay_amount' => 100000,
            'supply_amount' => 90000,
            'delivery_address'    => "테스트 주소 123 김해",
            'delivery_date'    => "2025-03-28",
            'delivery_time'   => "13:30:00",
            'delivery_ribbon_right' => '경조사어 없음',
            'delivery_ribbon_left' => '보내는분 <== 테스트 문구 추가',
            'order_memo' => '주문 메모',
            'goods_name' => '상품명',
            'goods_code' => 'hjch001',
            'goods_url' => '',
            'goods_options' => [
                [
                    'option_name' => '받침대',
                    'option_price' => 10000
                ]
            ]
        ];

        $url = "https://partner.flabiz.kr:3000/api/bms/partner/t2";

        $response = Http::withHeaders([
            'Authorization' => '848389a500206c9012a6ad949749902e'
        ])->post($url, $data);

        dd($response->json());

    }

    public static function BMSv2_response_api(Request $request) {
        $key = $request -> flatest;
        if($key !== "12345") {
            return response()->json(['code'=>400, 'message'=>"조회 실패"]);
        }

        $data = [
            'order_number'  => '123-456',
            'event_URL'  => "https://tms.flabiz.kr",
            'groom_info'       => [
                [
                    'title' => "신랑",
                    'name' => "이준서",
                    'phone' => '010-1111-1111'
                ],
                [
                    'title' => "신랑측 아버지",
                    'name' => "이인수",
                    'phone' => '010-2222-2222'
                ],
                [
                    'title' => "신랑측 어머니",
                    'name' => "이영희",
                    'phone' => '010-3333-3333'
                ]
            ],
            'bride_info'     => [
                [
                    'title' => "신부",
                    'name' => "김은재",
                    'phone' => '010-4444-4444'
                ],
                [
                    'title' => "신부측 아버지",
                    'name' => "김희승",
                    'phone' => '010-5555-5555'
                ]
            ],
            'delivery_address'    => "테스트 주소 123",
            'delivery_date'    => "2024-10-30",
            'delivery_time'   => "14:30:00",
        ];

        return response() -> json($data);
    }

    public function BMSv2_retrieve_api(Request $request) {
        $data['start_date'] = "2024-10-01";
        $data['end_date'] = Carbon::now()->format('Y-m-d');

        $response = Http::withHeaders([
            'Authorization' => 'd2492ab671ec3081700dbcfbbd2ab90e'
        ])
        ->get("https://partner.flabiz.kr:3000/api/bms/retrieve/orders", $data);
        dd($response->json());
    }

    public static function update_product_url() {
        $products = TMS_Product::all();

        foreach ($products as $key => $product) {

            $img_url = self::update_img_url($product);
            $thumb_url = self::update_thumb_url($product);
            $des_url = self::update_des_url($product);
//
            $product -> pr_img = $img_url;
            $product -> pr_thumb = $thumb_url;
            $product -> pr_description = $des_url;
//
            $product -> save();
        }


        return "success";
    }

    public static function update_des_url($product) {

        $des_url = $product -> pr_description;

//        return str_replace("flatalk.co.kr", "flachain.net", $des_url);
        return str_replace("http", "https", $des_url);

    }

    public static function update_img_url($product) {

        $img_url = $product -> pr_img;
//
//        $arr_1 = explode("s://", $img_url);
//
//        $https = $arr_1[0]."://";
//
//        $url_arr = explode("/", $arr_1[1]);
//
//        $new_domain = "flasystem.flachain.net/";
//
//        foreach ($url_arr as $key => $value) {
//            if($key==0) {
//                continue;
//            }else if($key == count($url_arr)-1){
//                $new_domain .= $value;
//            }else{
//                $new_domain .= $value . "/";
//            }
//        }
//
//        return $https.$new_domain;

        return str_replace("http", "https", $img_url);
    }

    public static function update_thumb_url($product) {

        $img_url = $product -> pr_thumb;

//        $arr_1 = explode("s://", $img_url);
//
//        $https = $arr_1[0]."://";
//
//        $url_arr = explode("/", $arr_1[1]);
//
//        $new_domain = "flasystem.flachain.net/";
//
//        foreach ($url_arr as $key => $value) {
//            if($key==0) {
//                continue;
//            }else if($key == count($url_arr)-1){
//                $new_domain .= $value;
//            }else{
//                $new_domain .= $value . "/";
//            }
//        }
//
//        return $https.$new_domain;

        return str_replace("http", "https", $img_url);
    }

    public static function sub_brand_code($brand) {
        if(strlen($brand)==4) {
            return $brand;
        }else {
            return substr($brand, 0, 4);
        }
    }


    public static function date_check($date) {
        if(Str::contains($date, ['0000'])) {
            return null;
        }else {
            return $date;
        }

    }
}
