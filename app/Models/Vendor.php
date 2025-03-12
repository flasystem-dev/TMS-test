<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\CommonCode;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';
    protected $primaryKey = 'idx';
    public $incrementing = false;
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    protected $fillable=[
        'idx', 'brand_type' , 'vendor_id','mall_name','service_type','gen_number','did_number','domain','rep_name',
        'rep_tel','rep_email','partner_name','partner_tel','partner_email','recommend_person','registered_date','created_date', 'updated_date',
        'vendor_memo','support_memo', 'iden_name','iden_file','rr_number1','rr_number2','company_name','company_addr','business_number','business_file','bank_code',
        'bank_number','name_of_deposit','bank_file','membership_pay_done','membership','membership_pay_type','membership_pay_date','membership_pay_name','membership_memo',
        'is_valid','assurance','assurance_amount','assurance_contractor','assurance_number','assurance_ex_date','deposit_form','settlement_memo','price_type',
        'meta_tag', 'logo_file','service_price_type','service_price','service_percent','service_ex_date','rep_type','is_credit','is_jungsan'
    ];

    public function orders()
    {
        return $this->hasMany(OrderData::class, 'mall_code', 'idx');
    }

    public function getChannelNameAttribute()
    {
        return $this->rep_name ?? "없음";
    }

    public function brand_code() {
        if(strlen($this->brand_type)===4){
            return $this->brand_type;
        }else{
            return substr($this->brand_type,0,-1);
        }
    }

    public function recommend_count() {
        return DB::table('vendor')
            ->where('recommend_person', $this->idx)
            -> count();
    }
    public static function vendorMaxNum(){
       $max_num = Vendor::query()->max('idx');
       return $max_num+1;
    }

    // 미수거래건수
    public function misu_count() {
        return DB::table('order_data')->where('payment_state_code', "PSUD")->where('payment_type_code','PTDP')-> where('mall_code', $this->idx) ->count() ;
    }

    // 미수거래금액
    public function misu_total() {
        return DB::table('order_data')->where('payment_state_code', "PSUD")->where('payment_type_code','PTDP')-> where('mall_code', $this->idx) ->sum('misu_amount') ;
    }

    // 남은 미수 거래 금액
    public function possible_misu() {
        $max_misu = $this->assurance_amount;
        $misu_total = $this->misu_total();
        return $max_misu-$misu_total;
    }

    public static function vendorSaveFirst($idx){
        Vendor::insert([
                'idx'=>$idx,
                'brand_type'=>'BTCS',
                'is_valid'=>'N',
                'regist_date'=>NOW()
            ]);
    }
    public static function get_mall_name($idx) {
        return Vendor::find($idx) -> mall_name ?? "없음";
    }
########################################################################################################################
    ###################################################  정산 용 함수  ###################################################

    public function order_count() {
        return $this -> sp_order_cnt ?? $this -> order_cnt ?? 0;
    }

    public function order_amount() {
        return $this -> sp_order_amount ?? $this -> total_amount ?? 0;
    }

    public function vendor_amount() {
        return $this -> sp_vendor_amount ?? $this -> vendor_amount ?? 0;
    }

    public function option_amount() {
        return $this -> sp_vendor_options_amount ?? $this -> vendor_options_amount ?? 0;
    }

    public function profit_amount() {
        return $this->vendor_amount()/100*$this->service_percent();
    }

    // 차액 = 원청금액 - 화훼금액 - 옵션금액
    public function difference_amount() {
        if($this->service_percent===0) { return 0; }

        return $this->order_amount() - $this->vendor_amount() - $this->option_amount();
    }

    // 카드 수수료 = 카드 금액 3.3%
    public function card_fee() {
        if(!is_null($this->sp_card_fee)){
            return $this->sp_card_fee;
        }
        if($this->service_percent===0 || $this->brand_type==="BTFCC" || $this->brand_type==="BTFCB") {
            return 0;
        }
        return floor(($this->card_amount/100*3.3)/10)*10;
    }

    public function card_amount() {
        if(!is_null($this->sp_card_amount)){
            return $this->sp_card_amount;
        }
        if($this->service_percent===0) { return 0; }
        return $this->card_amount ?? 0;
    }

    // 수익총액 = 발주수익 + 차액수익 + 기타2 + 기타3 - 카드수수료
    public function revenue_amount() {
        if($this->service_percent===0) { return 0; }

        return $this->profit_amount()
            +$this->difference_amount()
            +$this->etc2_amount()
            +$this->etc3_amount()
            -$this->card_fee();
    }

    public function service_percent() {
        return $this->sp_service_percent ?? $this->service_percent ?? 0;
    }

    public function outstanding_amount() {
        if($this->service_percent===0) { return 0; }
        return $this->misu_amount ?? 0;
    }

    public function service_fee($year, $month) {
        if(!is_null($this->sp_service_fee)){
            return $this->sp_service_fee;
        }
        if($this->service_percent===0) { return 0; }

        $month_check = str_pad($month, 2, '0', STR_PAD_LEFT);
        $yearMonth = $year."-".$month_check;

        if (Str::startsWith($this->registered_date, $yearMonth)){
            return 0;
        }
        if($this->service_price===999999){
            $registered_date = Carbon::parse($this->registered_date);

            if($registered_date->lessThan('2016-10-01') && $this->vendor_amount() > 1000000 ) {
                return 55000;
            }

            return 33000;
        }
        return $this->service_price;
    }

    // 원청징수 합계
    public function tax_amount() {
        if(!is_null($this->sp_tax_amount)){
            return $this->sp_tax_amount;
        }
        if($this->service_percent===0) { return 0; }

        if($this->rep_type==='개인'){
            $tax_amount = floor(($this->revenue_amount()/100*3)/10)*10;
            return $tax_amount + floor(($tax_amount/10)/10)*10;
        }

        return 0;
    }

    public function etc1_amount() {
        return $this->sp_etc1 ?? $this -> etc1 ?? 0;
    }

    public function etc2_amount() {
        return $this->sp_etc2 ?? $this -> etc2 ?? 0;
    }

    public function etc3_amount() {
        return $this->sp_etc3 ?? $this -> etc3 ?? 0;
    }

    // 공제 총액 = 원천징수 + 서비스금액 - 기타1
    public function deduction_amount($year, $month) {
        return $this->tax_amount() + $this->service_fee($year, $month) - $this->etc1_amount();
    }

    // 실제 지급액
    public function settlement_amount($year, $month) {
        if(!is_null($this->sp_settlement_amount)){
            return $this->sp_settlement_amount;
        }

        return round($this->revenue_amount() - $this->deduction_amount($year, $month) , -1);
    }

########################################################################################################################


    // 당월 원청금액
    public function orders_for_this_month_amount($month) {
        return DB::table('order_data')
            -> selectRaw('sum(total_amount) as total_amount')
            -> where('mall_code', $this->idx)
            -> where('payment_date','LIKE',"$month%")
            -> first() -> total_amount;
    }
    //당월 화훼금액
    public function orders_for_this_month_balju_amount($month) {
        return DB::table('order_data')
            -> selectRaw('sum(vendor_amount) as vendor_amount')
            -> where('mall_code', $this->idx)
            -> where('payment_date','LIKE',"$month%")
            -> first() -> vendor_amount;
    }

    public function recommend_name($mall_code){
        return Vendor::find($mall_code)->rep_name ?? "";
    }

    public static function vendorList($search) {

        $query = Vendor::query();

        if($search){
            $search_column = [
                'rep_name',
                'rep_tel',
                'mall_name',
                'gen_number',
                'did_number',
                'recommend_person',
            ];

            // 1차 검색어
            if(!empty($search['word1'])){
                if($search['sw_1']!= "all") {
                    $query->whereRaw("REPLACE(" . $search['sw_1'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['word1']) . "%"]);
                }else {
                    $query -> where(function($query) use ($search_column,$search){
                        foreach ($search_column as $column) {
                            $query->orWhereRaw(
                                "REPLACE(" . $column . ", '-', '') LIKE ?",
                                ["%" . str_replace('-', '', $search['word1']) . "%"]
                            );
                        };
                    });
                }
            }

            // 2차 검색어
            if(!empty($search['word2'])){
                if($search['sw_2']!= "all") {
                    $query->whereRaw("REPLACE(" . $search['sw_2'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['word2']) . "%"]);
                }else {
                    $query -> where(function($query) use ($search_column,$search){
                        foreach ($search_column as $column) {
                            $query->orWhereRaw(
                                "REPLACE(" . $column . ", '-', '') LIKE ?",
                                ["%" . str_replace('-', '', $search['word2']) . "%"]
                            );
                        };
                    });
                }
            }

//            if(isset($search_arr['type'])) {
//                $query -> where('brand_type', "=", $search_arr['type']);
//            }

            // 나머지 검색
            foreach ($search as $key => $value) {
                if(!empty($value)) {
                    switch ($key) {
                        case "brand_type":
                            $query -> where($key, $value);
                            break;
                    }
                }
            }
        }
        $query -> orderBy('idx', 'desc');
        return $query;
    }

    public static function flaBusinessSave($vendor){
       Vendor::where('idx',$vendor['idx'])->update([
            'brand_type' =>$vendor['brand_type'],
            'vendor_id'=>$vendor['vendor_id'],
            'mall_name'=>$vendor['mall_name'],
            'service_type'=>$vendor['service_type'],
            'gen_number'=>$vendor['gen_number'],
            'did_number'=>$vendor['did_number'],
            'domain'=>$vendor['domain'],
            'rep_name'=>$vendor['rep_name'],
            'rep_tel'=>$vendor['rep_tel'],
            'rep_email'=>$vendor['rep_email'],
            'partner_name'=>$vendor['partner_name'],
            'partner_tel'=>$vendor['partner_tel'],
            'recommend_person'=>$vendor['recommend_person'],
            'registered_date'=>$vendor['registered_date'],
            'updated_date'=> NOW(),
            'vendor_memo'=>$vendor['vendor_memo'],
            'iden_name'=>$vendor['iden_name'],
            'iden_file'=>$vendor['iden_file'],
            'rr_number1'=>$vendor['rr_number1'],
            'rr_number2'=>$vendor['rr_number2'],
            'company_name'=>$vendor['company_name'],
            'business_number'=>$vendor['business_number'],
            'business_file'=>$vendor['business_file'],
            'bank_code'=>$vendor['bank_code'],
            'bank_number'=>$vendor['bank_number'],
            'name_of_deposit'=>$vendor['name_of_deposit'],
            'bank_file'=>$vendor['bank_file'],
            'membership_pay_done'=>$vendor['membership_pay_done'],
            'membership'=>$vendor['membership'],
            'membership_pay_type'=>$vendor['membership_pay_type'],
            'membership_pay_date'=>$vendor['membership_pay_date'],
            'membership_pay_name'=>$vendor['membership_pay_name'],
            'membership_memo'=>$vendor['membership_memo'],
            'is_valid'=>$vendor['is_valid'],
            'assurance'=>$vendor['assurance'],
            'assurance_amount'=>$vendor['assurance_amount'],
            //'assurance_contractor'=>$vendor['assurance_contractor'],
            'assurance_ex_date'=>$vendor['assurance_ex_date'],
            'deposit_form'=>$vendor['deposit_form'],
            //'assurance_memo'=>$vendor['assurance_memo']
            'price_type' => $vendor['price_type'],
            'service_price' => $vendor['service_price'],
            'service_percent' => $vendor['service_percent']
        ]);
   }

   // 사업자 검색
   public static function search_vendor($search) {
       $vendors = Vendor::query();

       if($search) {
           // 검색어
           if(!empty($search['search_word'])){
               if($search['search_ctgy'] != "all") {
                   $vendors->whereRaw("REPLACE(" . $search['search_ctgy'] . ", '-', '') LIKE ?", ["%". str_replace('-', '', $search['search_word'])."%"]);
               }else {
                   $vendors -> where(function($query) use ($search){
                       $ctgys = [
                           'mall_name',
                           'rep_name',
                           'rep_tel',
                           'partner_name',
                           'partner_tel',
                           'gen_number',
                           'did_number'
                       ];
                       foreach ($ctgys as $ctgy) {
                           $query -> orWhereRaw("REPLACE(". $ctgy. ", '-', '') LIKE ?",["%". str_replace('-','', $search['search_word'])."%"]);
                       }
                   });
               }
           }
       }

       // 비활성 포함
       if(!(isset($search['is_valid']) && $search['is_valid']=='N')){
           $vendors->where('is_valid', 'Y');
       }

       return $vendors -> paginate(3) -> withQueryString();
   }
   
}
