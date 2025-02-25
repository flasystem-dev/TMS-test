<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderPaymentImport;
use App\Imports\UserImport;
use App\Imports\VendorImport;
use App\Models\TMS_User;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;

use App\Models\TMS_Product;
use App\Models\TMS_ProductOption;

use App\Models\Product\Product;
use App\Models\Product\ProductOptionPrice;
use App\Models\Product\ProductPrice;

class DevController extends Controller
{
    public function index(Request $request) {

        return view('util.dev-part' );
    }

    public function updateCommonCode(Request $request) {
        Cache::forget('common_codes');
        Cache::put('common_codes_updated_at', now());

        Cache::rememberForever('common_codes', function () {
            return CommonCode::all()->keyBy('code');
        });

        return response() -> json(true);
    }

    public function user_require_reload() {
        TMS_User::whereNot('dep', "임원") -> update(['require_reload' => 1]);
    }

    public function orderPayment(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new OrderPaymentImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function user(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new UserImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function vendor(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new VendorImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function statistics_url() {
        $url_data = DB::table('order_data_url')-> select('url') -> where('url', 'like', '%http%') -> get();

        $statistics_data = [];

        foreach ($url_data as $value) {
            $url_data = $value -> url;

            $data = self::extractUrls($url_data);

            // http 제거
            $data1 = str_replace("https://", "", $data);
            $data2 = str_replace("http://", "", $data1);

            // 뒤의 uri 제거
            if(Str::contains($data2, "/")) {
                $data3 = explode("/", $data2)[0];
            }else {
                $data3 = $data2;
            }
            
            // www. 제거
            $data4 = str_replace("www.", "", $data3);

            // .com 제거
            $data5 = str_replace(".com", "", $data4);

            // .co.kr 제거
            $data6 = str_replace(".co.kr", "", $data5);

            // .kr 제거
            $data7 = str_replace(".kr", "", $data6);


            $statistics_data[] = $data7;
        }

        $result = array_count_values($statistics_data);

        foreach ($result as $key => $value) {
            DB::table('statistics_url') -> upsert(
                [ 'url' => $key, 'count' => $value ],
                ['url'],
                ['count']
            );
        }

        return true;
    }

    public function upsert_orderDataItem(Request $request)
    {
        $order_index = $request->order_idx;

        OrderData::with('delivery:order_idx,goods_name,pr_idx,send_name', 'payments:order_idx,payment_item', 'carts', 'vendor')
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
        return response() -> json(true);
    }

    public static function upsert_orderDataItem_auto($order_idx)
    {
        OrderData::with('delivery:order_idx,goods_name,pr_idx,send_name', 'payments:order_idx,payment_item', 'carts', 'vendor')
            ->where('order_idx', $order_idx)
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
    }

#######################################################################################################################

    public static function extractUrls($text) {
        // URL을 찾기 위한 정규식 패턴
        // http 또는 https 로 시작하는 곳 찾아서
        // :// 가 뒤따라오는 형태
        // 공백이 아닌 문자로 끝나는 곳 매칭
        $pattern = '/\bhttps?:\/\/[^\s]+/i';

        // 정규식으로 URL 추출
        preg_match_all($pattern, $text, $matches);

        return $matches[0][0]; // 추출된 URL들만 반환
    }
}
