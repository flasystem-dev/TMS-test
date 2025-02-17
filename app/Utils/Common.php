<?php
namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Vendor;
use App\Models\OrderData;
use App\Models\OrderPayment;

class Common {
    public static function order_log($order, $delivery ,  $input ){
        $content = '';
        if(!empty($order)){
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
                };
            }
        }

        if(!empty($delivery)) {
            $attr_arr2 = $delivery->attributesToArray();
            foreach ($attr_arr2 as $key => $value) {
                if ($delivery->isDirty($key)) {
                    $value = trim($value);
                    $content .= "<p class='column_container'>";
                    $content .= "<span class='column_log_text'>[" . $key . "]</span>";
                    $content .= "<span class='origin_value_text'>" ;
                    $content .= empty(trim($delivery->getOriginal($key))) && $delivery->getOriginal($key) != 0 ? "(빈칸)" : trim($delivery->getOriginal($key));
                    $content .= "</span>";
                    $content .= "<span class='log_text'>=></span>";
                    $content .= "<span class='log_value_text'>";
                    $content .= empty($value) && $value != 0 ? "(빈칸)" : $value;
                    $content .= "</span></p>";
                };
            }
        }

        if(!empty($content)) {
            DB::table('order_log')->insert([
                'od_id' => $order->od_id,
                'log_by_name' => Auth::user()->name,
                'log_time' => NOW(),
                'log_status' => '주문 수정',
                'log_content' => $content
            ]);
        }
    }

    // 결제 테이블 로그
    public static function payment_log_content($payment){
        $attr_arr = $payment -> attributesToArray();
        $content = '';

        foreach ($attr_arr as $key => $value) {
            if($payment -> isDirty($key)){
                $value = trim($value);
                $content .= "<p class='column_container'>";
                $content .= "<span class='column_log_text'>[" . $key . "]</span>";
                $content .= "<span class='origin_value_text'>" ;
                $content .= empty(trim($payment->getOriginal($key))) && $payment->getOriginal($key) != 0 ? "(빈칸)" : CommonCodeName(trim($payment->getOriginal($key)))?? trim($payment->getOriginal($key));
                $content .= "</span>";
                $content .= "<span class='log_text'>=></span>";
                $content .= "<span class='log_value_text'>";
                $content .= empty($value) && $value != 0 ? "(빈칸)" : CommonCodeName($value)?? $value;
                $content .= "</span></p>";
            };
        }

        if(!empty($content)) {
            $title  = "<p class='column_container'>";
            $title .= "<span class='column_log_text'>[결제 번호]</span>";
            $title .= "<span class='origin_value_text'>{$payment -> payment_number}</span></p>";
            $content = $title . $content;
        }
        return $content;
    }
    
    // 전화번호, 휴대전화 번호 하이픈 입력
    public static function addHyphen($phone) {
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch($length){
            case 11 :
                return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
                break;
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
                break;
            default :
                return $phone;
                break;
        }
    }
    
    // 알림 시간 표시
    public static function diff_now($time) {
        $now = Carbon::now();

        $noti_time = Carbon::createFromFormat('Y-m-d H:i:s', $time);

        $diff = $now -> diffInMinutes($noti_time);

        if($diff < 60) {
            return $diff . "분 전";
        }else {
            return (int)($diff / 60) . "시간 전";
        }

    }

    // 주문 로그 프레임
    public static function log_contents_frame($column, $value1, $value2) {
        $contents = "";
        $contents .= "<p class='column_container'>";
        $contents .= "<span class='column_log_text'>[" . $column . "]</span>";
        $contents .= "<span class='origin_value_text'>" ;
        $contents .= empty($value1) && $value1 != 0 ? "(빈칸)" : trim($value1);
        $contents .= "</span>";
        $contents .= "<span class='log_text'>=></span>";
        $contents .= "<span class='log_value_text'>";
        $contents .= empty($value2) && $value2 != 0 ? "(빈칸)" : trim($value2);
        $contents .= "</span></p>";

        return $contents;
    }

    // 오픈마켓 상품 주소
    public static function get_item_url($mall_code, $brand) {
        switch ($mall_code) {
            case 'MLAC':
                return "http://itempage3.auction.co.kr/DetailView.aspx?itemno=";
            case 'MLGM':
                return "http://item.gmarket.co.kr/Item?goodscode=";
            case 'ML11':
                return "https://www.11st.co.kr/products/";
            case 'MLCP':
                return "https://www.coupang.com/vp/products/6935914304?itemId=";
            case 'MLKK':
                return match ($brand) {
                    "BTCP" => "https://store.kakao.com/ktjflower/products/",
                    "BTCC" => "https://store.kakao.com/7788flower/products/",
                    "BTBR" => "https://store.kakao.com/baroflower/products/",
                    default => "",
                };
            case 'MLNV':
                return match ($brand) {
                    'BTCP' => "https://smartstore.naver.com/ktjflower/products/",
                    'BTCC' => "https://smartstore.naver.com/20000flower/products/",
                    'BTSP' => "https://smartstore.naver.com/48000/products/",
                    'BTBR' => "https://smartstore.naver.com/baro-flower/products/",
                    'BTOM' => "https://smartstore.naver.com/50000flower/products/",
                    default => "",
                };
            default :
                return "";
        }
    }
    
    // 오픈마켓 관리자 주소
    public static function get_admin_url($mall_code) {
        return match ($mall_code) {
            'MLAC', 'MLGM' => "https://www.esmplus.com/Member/SignIn/LogOn",
            'MLNV' => "https://sell.smartstore.naver.com/",
            "ML11" => "https://login.11st.co.kr/auth/front/selleroffice/login.tmall",
            'MLCP' => "https://wing.coupang.com/",
            'MLKK' => "https://store-sell.kakao.com/",
            default => "",
        };
    }

    // 주문 금액 확인 후 상태 변경
    public static function check_OrderAmount_state($order_idx) {
        $order = OrderData::find($order_idx);

        if($order->payments->isNotEmpty()) {
            $pay_amount = 0;
            $misu_amount = 0;
            $refund_amount = 0;

            $payment_number = OrderPayment::where('order_idx', $order->order_idx)->min('payment_number');

            $count = 0;
            $payment_cnt = count($order->payments);
            foreach ($order->payments as $payment) {
                if($payment->payment_number === $payment_number){
                    $order->payment_type_code = $payment->payment_type_code;
                }

                if($payment->payment_state_code === "PSUD") {
                    $misu_amount += (int)$payment -> payment_amount;
                }elseif ($payment->payment_state_code === "PSDN") {
                    $pay_amount += (int)$payment -> payment_amount;
                    $refund_amount += (int)$payment -> refund_amount;
                }elseif ($payment->payment_state_code === "PSCC") {
                    if(!empty($payment->payment_pg)) {
                        $refund_amount += (int)$payment -> refund_amount;
                    }
                    $count++;
                }
            }

            $order -> pay_amount = $pay_amount;
            $order -> misu_amount = $misu_amount;
            $order -> refund_amount = $refund_amount;

            if($payment_cnt === $count){
                $order -> payment_state_code = "PSCC";
            }else if($order->total_amount - $order->discount_amount !== $order->pay_amount + $order->misu_amount) {
                $order -> payment_state_code = "PSOC";
            }else if($order->total_amount - $order->discount_amount === $order->pay_amount) {
                $order -> payment_state_code = "PSDN";
            }else {
                $order -> payment_state_code = "PSUD";
            }

            self::auto_update_order_log($order);

            $order -> save();
        }
    }
}


