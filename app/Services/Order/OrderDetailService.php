<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\DTOs\OrderProduct;
use App\Services\Order\OrderService;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemOption;

use App\Utils\Common;

class OrderDetailService
{
    ##############################################  주문 - 미확인 체크해제  ################################################
    public static function isNotNew(OrderData $order){
        if(Auth::user() -> auth < 8) {
            $order -> is_now = 0;
            $order -> save();
        }
    }

    ##############################################  주문서 수정 업데이트  ##################################################
    public static function putOrderData($input)
    {
        $order = OrderData::find($input['order_idx']);
        $order -> fill($input);

        $delivery = OrderDelivery::find($input['order_idx']);
        $delivery -> fill($input);

        Common::order_log($order, $delivery, $input);

        $order -> save();
        $delivery -> save();
    }

    #######################################################  상품 변경   ################################################

    public static function change_product($order, $orderProduct)
    {
        $order-> delivery -> goods_name = $orderProduct -> product_name;

        OrderService::upsert_orderItem($order->order_idx, $orderProduct);

        $order -> delivery -> save();
    }

    ##############################################  상품 변경 로그 컨텐츠 만들기  ###########################################
    public static function makeOrderLog_fetchItem($item, $orderProduct) {
        // 이전 상품
        $content = "<p class='column_container'>";
        $content .= "<span class='product_log_text'>";
        $content .= $item->product_name;
        if($item->options->isNotEmpty()) {
            foreach($item->options as $option) {
                $content .= "<br> " . $option->option_name . " (+" . number_format($option->option_price) . ")";
            }
        }
        $content .= "</span>";
        
        $content .= "<span class='log_text'>=></span>";

        // 변경 될 상품
        $content .= "<span class='product_log_text'>";
        $content .= $orderProduct->product_name;
        if(!empty($orderProduct->options)) {
            foreach($orderProduct->options as $option) {
                $content .= "<br> " . $option['option_name'] . " (+" . number_format($option['option_price']) . ")";
            }
        }
        $content .= "</span></p>";
        
        return $content;
    }

    ##################################################  상품 발주 정보 변경  ##############################################

    public static function update_vendorAmount($input)
    {
        $order = OrderData::with('item') -> find($input['order_idx']);

        $content = Common::log_contents_frame("사업자 발주", $order -> vendor_amount , $input ['vendor_amount']);

        $order -> vendor_amount = $input ['vendor_amount'];
        $order -> save();

        if(isset($input['option_id'])) {
            $vendor_options_amount = 0;

            foreach ($input['option_id'] as $key => $option_id) {
                $option = OrderItemOption::find($option_id);

                $content .= Common::log_contents_frame("옵션 발주", $option -> vendor_option_price , $input ['vendor_option_price'][$key]);

                $option -> vendor_option_price = $input ['vendor_option_price'][$key];

                $vendor_options_amount += $option -> vendor_option_price;

                $option -> save();
            }
            $order -> item -> vendor_options_amount = $vendor_options_amount;
            $order -> item -> save();
        }

        DB::table('order_log') -> insert([
            'od_id' => $order->od_id,
            'log_by_name' => Auth::user()->name,
            'log_time' => NOW(),
            'log_status' => '발주 정보 변경',
            'log_content' => $content
        ]);
    }
}