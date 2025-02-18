<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\DTOs\OrderProduct;

use App\Models\Order\OrderData;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;


class OrderService
{
    // 수기 주문을 위한 input 데이터 가공
    public static function makeInput($input, OrderProduct $orderProduct)
    {
        $server_num = 1;
        $timestamp = Carbon::now();
        $microTime = (int)((microtime(true) - floor(microtime(true))) * 100);
        $input['od_id'] = $timestamp->format('ymdHis') . $microTime . $server_num;
        $input['order_idx'] = OrderData::max('order_idx') + 1;

        // order_data
        if($input['brand_type_code']==='BTFC'||$input['brand_type_code']==='BTCS') {
            $input['group_code'] = "vendor";
        }else {
            $input['group_code'] = "pass";
        }
        $input['mall_code'] = $input['mall_code'] ?? '';
        $input['order_number'] = $input['od_id'];
        $input['misu_amount'] = $input['total_amount'];
        $input['balju_amount'] = $orderProduct -> balju_price;
        $input['vendor_amount'] = $orderProduct -> vendor_price;
        $input['goods_url'] = $orderProduct->product_img;
        $input['handler'] = Auth::user()->name;

        // order_delivery
        $input['delivery_time'] = self::makeDeliveryTime($input);
        $input['goods_name'] = $orderProduct->product_name;

        // order_payment
        $input['payment_pid'] = $input['brand_type_code'] . $input['order_number'] . "-1";
        $input['payment_item'] = $orderProduct->product_name;
        $input['payment_amount'] = $input['total_amount'];

        return $input;
    }

    // orderProduct DTO 만들기
    public static function makeOrderProduct($request)
    {
        $product_id = $request -> product_id ?? 0;
        $price_type = $request -> price_type ?? 0;
        $product_price = $request -> product_price ?? 0;
        $selected_options = $request -> select_options ?? [];
        $options_name = $request -> option_name ?? [];
        $options_price = $request -> option_price ?? [];

        $orderProduct = OrderProduct::makeProduct($product_id, $price_type, $product_price);

        if(!empty($selected_options)) {
            foreach ($selected_options as $option_id) {
                $orderProduct = OrderProduct::makeOption($orderProduct, $option_id);
            }
        }

        if(!empty($options_name)) {
            foreach ($options_name as $key => $option_name) {
                if(!empty($option_name)){
                    $orderProduct = OrderProduct::makeCustomOption($orderProduct, $option_name, $options_price[$key]);
                }
            }
        }

        return $orderProduct;
    }

    // 주문 상품 upsert
    public static function upsert_orderItem($order_idx, OrderProduct  $orderProduct)
    {
        $orderProduct_array = $orderProduct -> toArray();

        $item = OrderItem::updateOrCreate(['order_id' => $order_idx], $orderProduct_array);
        DB::table('order_item_options')->where('order_item_id', $item->id)->delete();
        if(!empty($orderProduct->options)) {
            foreach ($orderProduct->options as $option) {
                $option['order_item_id'] = $item->id;
                OrderItemOption::create($option);
            }
        }
    }

    // 금액 재확인 & 상태 변경
    public static function amountStateVerification($order_idx)
    {
        $order = OrderData::with('item','payments')->find($order_idx);

        if($order->group_code !== "openMarket") {
            $order->total_amount = $order->calculateTotalAmount();
            $order->updatePaymentState();

            self::auto_update_order_log($order);
            $order -> save();
        }

    }

########################################################################################################################

    // 배송시간 확인
    protected static function makeDeliveryTime($input) {
        switch ($input['delivery_time_sel']) {
            case "now":
            case "input":
                return $input['delivery_time'];
            case "event":
                return $input['event_hour']."시 ". $input['event_min']."분 ".$input['event_text'];
            case "time":
                return $input['event_time_start']."시~".$input['event_time_end']."시 사이";
        }
    }

    // 주문 자동 변경 로그
    public static function auto_update_order_log($order) {
        $content = "";

        $attr_arr1 = $order -> attributesToArray();

        foreach ($attr_arr1 as $key => $value) {
            if($order -> isDirty($key)){
                $value = trim($value);
                $content .= "<p class='column_container'>";
                $content .= "<span class='column_log_text'>[" . $key . "]</span>";
                $content .= "<span class='origin_value_text'>" ;
                $content .= empty(trim($order->getOriginal($key))) && $order->getOriginal($key) != 0 ? "(빈칸)" : trim($order->getOriginal($key));
                $content .= "</span>";
                $content .= "<span class='log_text'>=></span>";
                $content .= "<span class='log_value_text'>";
                $content .= empty($value) && $value != 0 ? "(빈칸)" : $value;
                $content .= "</span></p>";
            }
        }

        if(!empty($content)) {
            DB::table('order_log')->insert([
                'od_id' => $order->od_id,
                'log_by_name' => "자동 변경",
                'log_time' => NOW(),
                'log_status' => '주문 수정',
                'log_content' => $content
            ]);
        }
    }
}