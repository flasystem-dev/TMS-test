<?php
namespace App\Services\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Services\Order\OrderService;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;

class PaymentService
{
    #####################################  나이스페이 결제 후 OrderData 업데이트  ###########################################
    public static function updateOrderData_nicePay($result)
    {
        $payment = OrderPayment::where('payment_pid', $result['Moid']) -> first();
        $order = OrderData::find($payment->order_idx);

        $payment_type = self::PayMethod_code($result['PayMethod']);

        $order -> payment_time = NOW();

        $payment -> payment_pg = "nice";
        $payment -> payment_type_code = $payment_type;
        $payment -> payment_key = $result['TID'];
        $payment -> payment_mid = $result['MID'];
        $payment -> auth_code = $result['AuthCode'] ?? '';
        $payment -> auth_date = $result['AuthDate'] ?? '';

        // 신용카드 결제완료
        if($result['ResultCode'] === '3001') {
            self::payment_completed($order, $result);
            self::payMethod_card($payment, $result);

        // 계좌이체 결제완료
        }else if($result['ResultCode'] === '4000'){
            self::payment_completed($order, $result);
            self::payMethod_bank($payment, $result);

        // 가상계좌 발급
        }else if($result['ResultCode'] === '4100'){
            self::payMethod_vbank($payment, $result);

        }else {
            return false;
        }

        $order -> save();
        $payment -> save();

        OrderService::amountStateVerification($order->order_idx);
        return true;
    }

    #####################################  나이스페이 웹훅 후 OrderData 업데이트  ###########################################
    public static function updateOrderData_webhook($orders, $payment, $result)
    {
        foreach ($orders as $order) {
            if($payment->payment_number===1){
                $newPayment = $order -> payments[0];
            }else{
                $newPayment = $payment;
            }

            $order -> pay_amount = (int)$order -> pay_amount + (int)$result['Amt'];
            $order -> misu_amount = (int)$order -> misu_amount - (int)$result['Amt'];
            $order -> create_ts = NOW();
            $order -> update_ts = NOW();
            $order -> save();

            $newPayment -> payment_type_code = "PTVA";
            $newPayment -> payment_state_code = 'PSDN';
            $newPayment -> payment_time = NOW();
            $newPayment -> payment_key = $result['TID'];
            $newPayment -> cashReceipt_type = $result['RcptType'];
            $newPayment -> cashReceipt_key = $result['RcptTID'];
            $newPayment -> cashReceipt_num = $result['RcptAuthCode'];
            $newPayment -> save();

            self::auto_webhook_order_log($order, $newPayment);

            OrderService::amountStateVerification($order->order_idx);
        }
    }

########################################################################################################################
########################################################################################################################

    #############################################  결제 완료  ########################################################
    protected static function payment_completed($order, $result)
    {
        $order -> pay_amount = (int)$order -> pay_amount + (int)$result['Amt'];
        $order -> misu_amount = (int)$order -> misu_amount - (int)$result['Amt'];
        $order -> payment_state_code = 'PSDN';
        $order -> payment_time = NOW();
    }

    ############################################  신용카드 결제  ########################################################
    protected static function payMethod_card($payment, $result) {
        $payment -> payment_state_code = 'PSDN';
        $payment -> payment_time = NOW();
        $payment -> card_name = $result['CardName'];
        $payment -> card_num = $result['CardNo'];
        if(!empty($result['ClickpayCl'])) {
            $payment -> card_name .= "|" . $result['ClickpayCl'];
        }
        $payment -> cashReceipt_type = $result['RcptType'] ?? '';
        $payment -> cashReceipt_key = $result['RcptTID'] ?? '';
        $payment -> cashReceipt_num = $result['RcptAuthCode'] ?? '';
    }

    #############################################  계좌이체 결제  ########################################################
    protected static function payMethod_bank($payment, $result) {
        $payment -> payment_state_code = 'PSDN';
        $payment -> payment_time = NOW();
        $payment -> bank_name = $result['BankName'];
        $payment -> cashReceipt_type = $result['RcptType'] ?? '';
        $payment -> cashReceipt_key = $result['RcptTID'] ?? '';
        $payment -> cashReceipt_num = $result['RcptAuthCode'] ?? '';
    }

    ##############################################  가상계좌 결제  ########################################################
    protected static function payMethod_vbank($payment, $result) {
        $payment -> bank_name = $result['VbankBankName'] ?? '';
        $payment -> bank_num = $result['VbankNum'] ?? '';
    }

    ##############################################  결제 수단 코드  ######################################################
    protected static function PayMethod_code($method) {
        switch ($method) {
            case 'CARD':
                return 'PTCD';
            case 'BANK':
                return 'PTBT';
            case 'VBANK':
                return 'PTVA';
            case 'KEYIN':
                return 'PTMN';
        }
    }

    protected static function auto_webhook_order_log($order, $payment) {

        $content = "<p class='column_container'>";
        $content .= "<p>";
        $content .= "[결제 번호 : " . $payment->payment_number . "] ";
        $content .= " 입금 완료";
        $content .= "</p></p>";

        if(!empty($content)) {
            DB::table('order_log')->insert([
                'od_id' => $order->od_id,
                'log_by_name' => "웹훅",
                'log_time' => NOW(),
                'log_status' => '결제',
                'log_content' => $content
            ]);
        }
    }
}