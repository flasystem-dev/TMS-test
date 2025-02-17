<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Message\KakaoTalkController;

class FlaAppController extends Controller
{
    public function RouteHandelerFunc(Request $request)
    {
        $APIName = $request->route('APIName');
        return $this->$APIName($request);
    }

    public function GoodsList(Request $request) {
        $goods_list = $request -> json();

        foreach ($goods_list as $goods){
            if(DB::table('app_goods_list') -> where('goods_id', $goods['goods_id']) -> exists()) {
                $result = DB::table('app_goods_list') -> where('goods_id', $goods['goods_id']) -> update([
                    'goods_name' => $goods['goods_name'],
                    'goods_ctgy' => $goods['goods_ctgy'],
                    'goods_amount' => $goods['goods_amount'],
                    'brand_type_code' => $goods['brand_type_code']
                ]);
            }else {
                $result = DB::table('app_goods_list') -> insert([
                    'goods_id' => $goods['goods_id'],
                    'goods_name' => $goods['goods_name'],
                    'goods_ctgy' => $goods['goods_ctgy'],
                    'goods_amount' => $goods['goods_amount'],
                    'brand_type_code' => $goods['brand_type_code']
                ]);
            }
        }

        return $result;
    }

    //  간편주문앱 링크 정보 미리 보내기
    public function send_data(Request $request) {
        $tmp_id = (int)(microtime(true)*10);

        $response = Http::withOptions(['verify' => false]) -> post('https://fla-app.flabiz.kr/sub/get_temp_data.php',[
            'tmp_id'         => $tmp_id,
            'od_name'        => $request -> od_name,
            'od_hp'          => $request -> od_hp,
            'od_b_name'      => $request -> od_b_name,
            'od_b_hp'        => $request -> od_b_hp,
            'od_deli_date'   => $request -> od_deli_date,
            'od_deli_time'   => $request -> od_deli_time,
            'od_addr'        => $request -> od_addr,
            'od_msg_left'    => $request -> od_msg_left,
            'od_msg_right'   => $request -> od_msg_right,
            'od_register'    => $request -> od_register,
            'brand_type_code'=> $request -> brand_type_code
        ]);

        if(!$response -> ok()){
            return "앱 정보 전달 실패";
        }
        $request -> tmp_id = $tmp_id;

        $kakao = new KakaoTalkController();
        $res = $kakao -> SendLink($request);
        $result = json_decode($res -> getContent());
        if($result -> code != 200) {
            return "[알림톡 전송 실패]".$result -> message;
        } else {
            return $result -> message;
        }
    }
}
