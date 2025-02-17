<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Specification extends Model
{
    use HasFactory;

    protected $table = "specification";

    protected $fillable = ['mall_code','year','month',
        'sp_order_cnt','sp_pay_amount','sp_order_amount', 'sp_vendor_amount',
        'sp_total_option_price','sp_vendor_options_amount','sp_service_percent','sp_service_price_dc','sp_service_fee',
        'sp_card_charge','sp_card_amount','sp_card_charge_fee','sp_card_fee', 'sp_tax_amount', 'sp_settlement_amount',
        'sp_deposit_price',
        'sp_etc1','sp_etc2','sp_etc3',
        'deposit_date','checkout_date','user_name','send_email','send_talk'];

//    protected $appends = ['profit_amount', 'difference_amount', 'revenue_amount' ];

    // 발주수익 = 사업자 발주 * 서비스 퍼센트
    public function getProfitAmountAttribute() {
        return $this->sp_vendor_amount / 100 * $this -> sp_service_percent;
    }

    // 차액수익 = 원청금액 - 사업자발주 - 사업자옵션
    public function getDifferenceAmountAttribute(){
        if($this -> sp_service_percent===0) { return 0; }
        return $this->sp_order_amount - $this->sp_vendor_amount - $this->sp_vendor_options_amount;
    }

    // 수익 총액 = 발주수익 + 차액수익 + 기타2 + 기타3 - 카드수수료
    public function getRevenueAmountAttribute(){
        return $this->profit_amount + $this->difference_amount + $this->sp_etc2 + $this->sp_etc3 - $this->sp_card_fee;
    }

    // 공제 총액 = 원천징수 + 서비스금액 - 기타1
    public function getDeductionAmountAttribute(){
        return $this->sp_tax_amount + $this->sp_service_fee - $this->sp_etc1;
    }
    
########################################################################################################################

    public function getBankName($code){
        return  DB::table('code_of_nicepay_card_bank')
            -> where('code_no', $code)
            -> first() -> code_name ?? "";
    }
}
