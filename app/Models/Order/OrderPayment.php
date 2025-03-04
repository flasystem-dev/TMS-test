<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order\OrderPaymentDeleted;

class OrderPayment extends Model
{
    use HasFactory;

    protected $table = 'order_payment';
    public $timestamps = false;

    protected $fillable=[
        'id', 'order_idx', 'order_number', 'payment_pid', 'payment_item', 'payment_number','payment_pg',
        'payment_state_code', 'payment_type_code', 'payment_amount', 'payment_key', 'payment_mid', 'payment_receipt_url', 'payment_result_json',
        'payment_time', 'auth_code', 'auth_date', 'card_name', 'card_num', 'bank_name', 'bank_num', 'cashReceipt_type', 'cashReceipt_key', 'cashReceipt_num',
        'refund_handler', 'payment_memo',
        'deposit_name', 'document_type', 'is_publish', 'advance_payment'
    ];

    public static function get_min_payment_number($order_idx)
    {
        // 사용된 모든 payment_number 가져오기
        $usedNumbers = OrderPayment::where('order_idx', $order_idx)
            ->pluck('payment_number')
            ->merge(OrderPaymentDeleted::where('order_idx', $order_idx)
                ->pluck('payment_number'))
            ->unique()
            ->sort()
            ->values();

        // 가장 작은 빈 payment_number 찾기
        $payment_number = 1;
        foreach ($usedNumbers as $used) {
            if ($used != $payment_number) {
                break; // 비어있는 가장 작은 번호 찾으면 종료
            }
            $payment_number++;
        }

        return $payment_number;
    }

    public function get_VA_info() {
        if($this->payment_type_code === "PTVA" && !empty($this->payment_pg)){
            return $this->bank_name . " " . $this->bank_num . " (주)플라시스템";
        }
        return "";
    }

    public function receipt_url() {
        switch ($this -> payment_pg){
            case 'toss':
                return $this -> payment_receipt_url;
            case 'nice':
                $url = "https://npg.nicepay.co.kr/issue/IssueLoader.do";
                $tid = $this -> payment_key;
                $type = 0;
                if(!empty($this->cashReceipt_key)) {
                    $tid = $this -> cashReceipt_key;
                    $type = 1;
                }
                return $url."?type={$type}&TID={$tid}";
            default :
                return "";
        }
    }
}
