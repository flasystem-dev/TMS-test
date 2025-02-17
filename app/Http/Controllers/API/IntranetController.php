<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Message\KakaoTalkController;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\OrderCart;
use App\Models\TMS_Product;
use App\Models\TMS_ProductOption;

use Carbon\Carbon;

class IntranetController extends Controller
{

########################################################################################################################
################################################# 배송결과 웹훅 ###########################################################

    public function delivery_return(Request $request) {
        $data = $request -> getContent();

        DB::table('test_table')->insert([
            'test1' => '인트라넷 배송 결과',
            'test2' => $data,
            'test3' => '',
            'test4' => '',
        ]);

        try {

            // 인코딩
            try {
                // euc-kr -> utf-8 변환
                $convertedValue = iconv('EUC-KR', 'UTF-8', $data);
                parse_str($convertedValue, $data_array);

            } catch (\Exception $e) {
                parse_str($data, $decode_data);
                $data_array = self::incodingToUTF8($decode_data);
                $data_array['insuname'] = "@@";
                $data_array['insurel'] = "@@";
            }

            if(DB::table('order_delivery')->where('send_id', $data_array['oid'])->exists()) {
                $delivery = OrderDelivery::where('send_id', $data_array['oid']) ->first();
                $order = OrderData::find($delivery->order_idx);
            }elseif(DB::table('order_data')->where('od_id', substr($data_array['oid'], 0, -2))->exists()) {
                $order = OrderData::where('od_id', substr($data_array['oid'], 0, -2))->first();
                $delivery = OrderDelivery::find($order->order_idx);
            }else {
                $order = OrderData::where('od_id', $data_array['oid'])->first();
                $delivery = OrderDelivery::find($order->order_idx);
            }

            $oid = $data_array['oid'];
            $state = $data_array['state'] ?? 0;
            $insuName = $data_array['insuname'] ?? "";
            $insuRel = $data_array['insurel'] ?? "";
            $insuDate0 = $data_array['insudate0'] ?? "";
            $insuDate1 = $data_array['insudate1'] ?? "";
            $insuDate2 = $data_array['insudate2'] ?? "";
            $insuTime = !empty($insuDate0) ? $insuDate0 . " " . $insuDate1 . ":" . $insuDate2 . ":00" : null;
            $photo1 = $data_array['dica'] ?? "";
            $photo2 = $data_array['img2'] ?? "";
            $photo3 = $data_array['img3'] ?? "";
            $photo4 = $data_array['img4'] ?? "";
            $img_type = $data_array['img_type'] ?? "";
            $img_type2 = $data_array['img_type2'] ?? "";
            $img_type3 = $data_array['img_type3'] ?? "";
            $img_type4 = $data_array['img_type4'] ?? "";

            DB::table('intranet_delivery_log') -> insert([
                'order_idx'  => $order->order_idx ?? 0,
                'brand'      => $order->brand_type_code ?? "",
                'state'      => $state,
                'insu_name'  => $insuName,
                'insu_time'  => $insuTime,
                'insu_rel'   => $insuRel,
                'deli_img'   => $photo1,
                'type'       => $img_type,
                'deli_img2'  => $photo2,
                'type2'      => $img_type2,
                'deli_img3'  => $photo3,
                'type3'      => $img_type3,
                'deli_img4'  => $photo4,
                'type4'      => $img_type4
            ]);

            if ($data_array['state'] === "4") {
                $delivery->delivery_com_time            = $insuTime;
                $delivery->delivery_photo               = $photo1;
                $delivery->photo_type                   = $img_type;
                $delivery->delivery_photo2              = $photo2;
                $delivery->photo_type2                  = $img_type2;
                $delivery->delivery_photo3              = $photo3;
                $delivery->photo_type3                  = $img_type3;
                $delivery->delivery_photo4              = $photo4;
                $delivery->photo_type4                  = $img_type4;
                $delivery->delivery_insuName            = $insuName . " | " . $insuRel;
                $delivery->delivery_state_code_before   = $delivery->delivery_state_code;
                $delivery->delivery_state_code          = "DLDN";
                $delivery->send_id                      = $oid;
                $delivery->save();

                if (!empty($photo1)) {
                    try {
                        $codes = DB::table('popbill_template_numberInUse')->select('deli_done', 'deli_photo')->where('brand_type_code', $order->brand_type_code)->first();
                        $code_array = array_values((array) $codes);

                        $today = Carbon::parse(date("Y-m-d"));
                        $threeDaysAgo = $today->subDays(3);
                        $delivery_date = Carbon::parse($delivery->delivery_date);

                        // 배송 날짜 3일 지난 주문 알림톡 X
                        if($threeDaysAgo<=$delivery_date) {
                            if($order -> is_alim === "talk") {
                                if(DB::table('alim_log')->where('od_id', $order->od_id)->whereIn('templateCode', $code_array)->doesntExist()) {
                                    $kakao = new KakaoTalkController();
                                    $request = new Request();
                                    $request->order_idx = $order->order_idx;
                                    $request->template_type = 'deli_done';
                                    $request->payment_number = 1;

                                    $kakao->SendATS_one($request);

                                    $request->template_type = 'deli_photo';
                                    $kakao->SendATS_one($request);
                                }
                            }elseif ($order -> is_alim === "sms") {
                                if(DB::table('sms_log')->where('od_id', $order->od_id)->whereIn('templateCode', $code_array)->doesntExist()) {
                                    $sms = new SMSController();
                                    $request = new Request();
                                    $request->order_idx = $order->order_idx;
                                    $request->template_type = 'deli_done';
                                    $request->payment_number = 1;

                                    $sms -> sendSMS_orderData($request);

                                    $request->template_type = 'deli_photo';
                                    $sms -> sendSMS_orderData($request);
                                }
                            }
                        }

                    } catch (\Exception $e) {
                        \Log::error("[배송완료] 카톡 전송 실패");
                        \Log::error("order_idx : " . $order->order_idx);
                        \Log::error($e);
                    }
                }

            } elseif ($state === "2") {
                $delivery->delivery_state_code_before = $delivery->delivery_state_code;
                $delivery->delivery_state_code = "DLSP";

                $delivery->delivery_photo   = $photo1;
                $delivery->photo_type       = $img_type;
                $delivery->delivery_photo2  = $photo2;
                $delivery->photo_type2      = $img_type2;
                $delivery->delivery_photo3  = $photo3;
                $delivery->photo_type3      = $img_type3;
                $delivery->delivery_photo4  = $photo4;
                $delivery->photo_type4      = $img_type4;

                $delivery->save();
            }

            return response()->json("success");
        }catch (\Exception $e) {
            \Log::error("[인트라넷 배송 API 실패]");

            $utf8Data = mb_convert_encoding($data, 'UTF-8', 'EUC-KR');

            parse_str($utf8Data, $arr);

            \Log::error("[주문번호] : " . $arr['oid']);
            \Log::error($e);
            DB::table('test_table')->insert([
                'test1' => '인트라넷 배송 결좌 실패',
                'test2' => $data,
                'test3' => $arr['oid'] ?? "",
            ]);
        }
    }

########################################################################################################################
########################################################### View ######################################################    
    
    // 수주화원 검색
    public static function receive_shop_data(Request $request) {
        $data['var_sid']    = iconv("EUC-KR","UTF-8",$request->var_sid);
        $data['var_jiyok']  = iconv("EUC-KR","UTF-8",$request->var_jiyok);
        $data['var_corp']   = iconv("EUC-KR","UTF-8",$request->var_corp);
        $data['var_name']   = iconv("EUC-KR","UTF-8",$request->var_name);
        $data['var_tel']    = iconv("EUC-KR","UTF-8",$request->var_tel);
        $data['var_fax']    = iconv("EUC-KR","UTF-8",$request->var_fax);

        return view('order.popup.suju_shop_data', $data);
    }

########################################################################################################################
######################################################## 보조 함수 ######################################################

    public static function incodingToUTF8($data) {
        $fields = explode('&', $data);

        $data_array = [];

        foreach ($fields as $field) {
            list($key, $value) = explode('=', $field, 2);

            try {
                // euc-kr -> utf-8 변환
                $convertedValue = iconv('EUC-KR', 'UTF-8', $value);
            } catch (\Exception $e) {
                $convertedValue = "@@"; // 실패 시 대체 값
            }
            $data_array[$key] = $convertedValue;
        }
        return $data_array;
    }
}
