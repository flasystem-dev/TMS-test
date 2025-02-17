<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
