<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Order\OrderPayment;

class OrderPaymentDeleted extends Model
{
    use HasFactory;

    protected $table = 'order_payment_deleted';

    protected $fillable=[
        'id', 'order_idx', 'order_number', 'payment_pid', 'payment_item', 'payment_number','payment_pg',
        'payment_state_code', 'payment_type_code', 'payment_amount', 'payment_key', 'payment_mid', 'payment_receipt_url', 'payment_result_json',
        'payment_time', 'auth_code', 'auth_date', 'card_name', 'card_num', 'bank_name', 'bank_num', 'cashReceipt_type', 'cashReceipt_key', 'cashReceipt_num',
        'refund_handler', 'payment_memo',
        'deposit_name', 'document_type', 'is_publish', 'advance_payment', 'created_at', 'updated_at'
    ];

    public function restore_payment()
    {
        try {
            $payment = new OrderPayment();
            $payment -> fill($this->toArray());
            if($payment -> save()) {
                $this -> delete();

            } else {
                \Log::error("OrderPayment 복구 실패");
                \Log::error("payment_id : " . $this->id);
            }
        }catch (\Exception $e){
            \Log::error("OrderPayment 복구 실패");
            \Log::error("payment_id : " . $this->id);
            \Log::error($e);
        }

    }
}
