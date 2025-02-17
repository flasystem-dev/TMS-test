<?php
namespace App\Services\Message;

use Illuminate\Http\Request;
use App\Http\Controllers\Message\KakaoTalkController;
use App\Http\Controllers\Message\SMSController;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public static function sendMessage($order_idx, $payment_number, $template_type, $message_type) {
        try {
            $request = new Request();
            $request -> order_idx = $order_idx;
            $request -> payment_number = $payment_number;
            $request -> template_type = $template_type;

            if($message_type === "talk") {
                $kakao = new KakaoTalkController();
                $kakao -> SendATS_one($request);
                unset($kakao);

            }elseif($message_type === "sms") {
                $sms = new SMSController();
                $sms -> sendSMS_orderData($request);
                unset($sms);
            }
        }catch (\Exception $e) {
            \Log::error("[메시지 전송 실패]");
            \Log::error("message_type : " . $message_type);
            \Log::error("order_idx : " . $order_idx);
            \Log::error("payment_number : " . $payment_number);
            \Log::error("template_type : " . $template_type);
            \Log::error($e->getMessage());
        }

    }

    // 웹훅 -> 메시지 전송
    public static function sendMessage_webhook($orders, $payment_number)
    {
        $templateCode = DB::table('popbill_template_numberInUse')->where('brand_type_code', $orders[0]->brand_type_code)->value('order_check');

        self::sendMessage($orders[0]->order_idx, $payment_number, "pay_complete", $orders[0]->is_alim);

        if(DB::table('alim_log')->where('od_id', $orders[0]->od_id)->where('templateCode', $templateCode)->doesntExist()) {
            foreach ($orders as $order) {
                self::sendMessage($order->order_idx, $payment_number, "order_check", $orders[0]->is_alim);
            }
        }

        if(DB::table('sms_log')->where('od_id', $orders[0]->od_id)->where('templateCode', $templateCode)->doesntExist()) {
            foreach ($orders as $order) {
                self::sendMessage($order->order_idx, $payment_number, "order_check", $orders[0]->is_alim);
            }
        }
    }
}