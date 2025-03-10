<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Controllers\Message\KakaoTalkController;

use App\Models\CodeOfCompanyInfo;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderURL;
use App\Models\OrderCart;

use App\Models\Product\Product;

use App\Models\Vendor;
use App\Models\VendorPass;
use App\Models\Client;
use App\Models\TMS_Product;
use App\Models\Payment\CodeOfNicePay;
use App\Utils\Common;

class OrderData extends Model
{
    protected $table = 'order_data';
    protected $primaryKey = 'order_idx';
    public $incrementing = false;
    const CREATED_AT = 'create_ts';
    const UPDATED_AT = 'update_ts';
    protected $fillable=[
        'order_idx','od_id','brand_type_code','mall_code', 'client_id', 'group_code','order_number',
        'order_time','orderer_mall_id','orderer_name','orderer_tel','orderer_phone','orderer_email',
        'order_quantity','payment_type_code','payment_state_code', 'payment_time',
        'total_amount','pay_amount','discount_amount','admin_discount','point_amount','refund_amount','misu_amount','supply_amount', 'balju_amount','vendor_amount',
        'admin_regist','admin_memo',
        'create_ts','update_ts',
        'options_string','options_parse_yn','options_string_display','options_type',
        'order_claim','order_claim_memo','goods_url','db_num',
        'is_new', 'is_view', 'is_highlight', 'is_alim', 'is_tran', 'inflow', 'handler'
    ];

    public function delivery()
    {
        return $this -> hasOne(OrderDelivery::class, 'order_idx', 'order_idx');
    }

    public function payments()
    {
        return $this -> hasMany(OrderPayment::class, 'order_idx', 'order_idx') -> orderBy('payment_number', 'asc');
    }

    public function item()
    {
        return $this -> hasOne(OrderItem::class, 'order_id', 'order_idx');
    }

    public function carts()
    {
        return $this -> hasMany(OrderCart::class, 'order_idx', 'order_idx') -> orderBy('id','asc');
    }

    public function vendor() {
        return $this->hasOne(Vendor::class, 'idx', 'mall_code');
    }

    public function pass() {
        return $this->hasOne(VendorPass::class, 'id', 'mall_code');
    }

    public function client() {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function event_url() {
        return $this->hasOne(OrderURL::class, 'order_idx', 'order_idx');
    }

########################################################################################################################
################################################  편의 기능 함수  ########################################################

    public function setOrdererPhoneAttribute($value)
    {
        $this->attributes['orderer_phone'] = preg_replace('/\D/', '', $value);
    }

    public function setOrdererTelAttribute($value)
    {
        $this->attributes['orderer_tel'] = preg_replace('/\D/', '', $value);
    }

    // 조회 시 하이픈이 포함된 한국 전화번호 형식으로 변환
    public function getOrdererPhoneAttribute($value)
    {
        return formatPhoneNumber($value);
    }
    public function getOrdererTelAttribute($value)
    {
        return formatPhoneNumber($value);
    }

    public function getChannelNameAttribute()
    {
        switch ($this->group_code) {
            case 'openMarket':
                return CommonCodeName($this->mall_code);
            case 'vendor':
                return $this -> vendor -> rep_name ?? "없음";
            case 'pass':
                return $this -> pass -> name ?? "없음";
            default:
                return CommonCodeName($this->mall_code) ?? "";
        }
    }
    
    // 채널명
    public function channel() {
        $commonCodes = Cache::get('common_codes', collect());
        $openMarkets = $commonCodes->filter(function ($item) {
            return $item->parent_code === 'ML';
        })->pluck('code')->toArray();

        switch($this->brand_type_code) {
            case 'BTCS':
                $url = "https://" . $this -> vendor -> domain . ".flapeople.com";

                return [
                    'name' => $this -> vendor -> rep_name ?? "없음",
                    'mall_name' => $this -> vendor -> mall_name ?? "없음",
                    'URL' => $url
                ];

            case 'BTFC':
                $url = "https://" . $this -> vendor -> domain . ".flachain.net";

                return [
                    'name' => $this -> vendor -> rep_name ?? "없음",
                    'mall_name' => $this -> vendor -> mall_name ?? "없음",
                    'URL' => $url
                ];
            default:
                if(Str::contains($this->mall_code, $openMarkets)) {
                    $url = Common::get_admin_url($this->mall_code);
                    return [
                        'name' => CommonCodeName($this->mall_code),
                        'mall_name' => CommonCodeName($this->mall_code),
                        'URL' => $url
                    ];
                }
                $url = "https://" . $this -> pass -> domain . ".flabiz.kr";
                return [
                    'name' => $this -> pass -> name ?? "없음",
                    'mall_name' => $this -> pass -> mall_name ?? "없음",
                    'URL' => $url
                ];
        }
    }

    public function channel_name()
    {
        switch ($this->group_code) {
            case 'openMarket':
                return CommonCodeName($this->mall_code);
            case 'vendor':
                return $this -> vendor_name ?? "없음";
            case 'pass':
                return $this -> pass_name ?? "없음";
            default:
                return CommonCodeName($this->mall_code) ?? "";
        }
    }

    public function channel_mall()
    {
        switch ($this->group_code) {
            case 'openMarket':
                return CommonCodeName($this->mall_code);
            case 'vendor':
                return $this -> vendor_mallName ?? "없음";
            case 'pass':
                return $this -> pass_mallName ?? "없음";
        }
    }

    public function channel_url() {
        switch($this->brand_type_code) {
            case 'BTCS':
                return "https://" . $this -> vendor_domain . ".flapeople.com";

            case 'BTFC':
                return "https://" . $this -> vendor_domain . ".flachain.net";
            default:
                if($this->group_code === 'openMarket') {
                    return Common::get_admin_url($this->mall_code);
                }

                return "https://" . $this -> pass_domain . ".flabiz.kr";

        }
    }

    // 미수 가능 여부 ( 발주용 )
    public function is_credit() {
        switch ($this->group_code) {
            case 'openMarket':
                return false;
            case 'vendor':
                return $this -> vendor_isCredit;
            case 'pass':
                return $this -> pass_isCredit;
        }
    }

    // 플레이오토 연결 여부
    public function playauto_connect() {
        $commonCodes = Cache::get('common_codes', collect());
        $openMarkets = $commonCodes->filter(function ($item) {
            return $item->parent_code === 'ML';
        })->pluck('code')->toArray();

        if(Str::contains($this->mall_code, $openMarkets)) {
            if(empty($this->db_num)) {
                return false;
            }
            return true;
        }
        return true;
    }

    // 가격 타입
    public function price_type() {
        $commonCodes = Cache::get('common_codes', collect());
        $openMarkets = $commonCodes->filter(function ($item) {
            return $item->parent_code === 'ML';
        })->pluck('code')->toArray();

        switch($this->brand_type_code) {
            case 'BTCS':
            case 'BTFC':
                return $this -> vendor -> price_type ?? 1;
            default:
                if(Str::contains($this->mall_code, $openMarkets)) {
                    return 1;
                }
                return $this -> pass -> price_type ?? 1;
        }
    }
    
    // 중복 주문을 위한 인덱스
    public function sub_index(){
        $orderCollection  = OrderData::whereBetween('order_idx',[$this->order_idx-25, $this->order_idx+25])
            ->where('order_number', $this->order_number)
            ->orderBy('order_idx', 'asc')
            ->pluck('order_idx');

        return $orderCollection->search($this->order_idx) + 1;
    }

    // total_amount 계산
    public function calculateTotalAmount() {
        return $this->item->item_total_amount - $this->admin_discount - $this->discount_amount - $this->point_amount;
    }
    
    // order_payment 재검증
    public function calculatePayments() {
        $pay_amount = 0;
        $misu_amount = 0;
        $refund_amount = 0;

        $payment_number = $this->payments->min('payment_number');
        $count = 0;

        foreach ($this->payments as $payment) {
            if ($payment->payment_number === $payment_number) {
                $this->payment_type_code = $payment->payment_type_code;
            }

            switch ($payment->payment_state_code) {
                case "PSUD":
                    $misu_amount += (int)$payment->payment_amount;
                    break;
                case "PSDN":
                    $pay_amount += (int)$payment->payment_amount;
                    $refund_amount += (int)$payment->refund_amount;
                    break;
                case "PSCC":
                    if (!empty($payment->payment_pg)) {
                        $refund_amount += (int)$payment->refund_amount;
                    }
                    $count++;
                    break;
            }
        }

        return [
            'pay_amount' => $pay_amount,
            'misu_amount' => $misu_amount,
            'refund_amount' => $refund_amount,
            'canceled_count' => $count,
        ];
    }

    // 결제 상태 변경
    public function updatePaymentState() {
        $calc_data = $this->calculatePayments();
        $payment_cnt = $this->payments->count();

        $this->pay_amount = $calc_data['pay_amount'];
        $this->misu_amount = $calc_data['misu_amount'];
        $this->refund_amount = $calc_data['refund_amount'];

        if ($payment_cnt !== 0 && $payment_cnt === $calc_data['canceled_count']) {
            $this->payment_state_code = "PSCC";
        } elseif ($this->total_amount !== $this->pay_amount + $this->misu_amount) {
            $this->payment_state_code = "PSOC";
        } elseif ($this->total_amount === $this->pay_amount) {
            $this->payment_state_code = "PSDN";
        } else {
            $this->payment_state_code = "PSUD";
        }
    }

########################################################################################################################
################################################# 조회 ##################################################################

    ############################################### 주문 - 최근 사업자 주문 3개 조회 ########################################
    public static function recent_vendor_order($vendor_idx) {
        return OrderData::where('mall_code', $vendor_idx )
            -> where(function($query){
                $query-> where('payment_state_code', "PSDN")
                    -> orWhere(function($query){
                        $query->where('payment_state_code', "PSUD")
                            ->where('payment_type_code', 'PTDP');
                    });
            })
            -> orderBy('order_idx', 'desc')
            -> limit(3)
            -> get();
    }
}
