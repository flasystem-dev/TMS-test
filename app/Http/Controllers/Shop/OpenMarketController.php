<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\PlayAuto2APIController;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailToPlan;

use App\Models\CommonCode;

class OpenMarketController extends Controller
{
    public static function Account_info(Request $request) {

        $data_list = DB::table('playauto2_api') -> orderBy('update_at', 'DESC') -> get();

        return view('shop.openMarket.openMarket-account', ['mall_list' => $data_list]);
    }

    public static function check_account_info(Request $request) {
        $data_list = DB::table('playauto2_api') -> where('is_api_used', 'Y') -> orderBy('brand_type_code') -> get();

        $playauto2 = new PlayAuto2APIController();

        foreach ($data_list as $data){
            $playauto2 -> update_shop_info($data);
        }

        return "update";
    }

    public static function update_account_info(Request $request) {
        $mall = $request -> mall;
        $brand = $request -> brand;
        $id = $request -> id;
        $pw = $request -> pw;
        $nick = $request -> nick;
        $name = $request -> handler;

        DB::table('playauto2_api') -> where('mall_code', "=", $mall) -> where('brand_type_code', "=", $brand) -> update([
            'site_id' => $id,
            'site_pw' => $pw,
            'site_nick' => $nick
        ]);

        $data = DB::table('playauto2_api') -> where('mall_code', "=", $mall) -> where('brand_type_code', "=", $brand) -> first();

        DB::table('account_log') -> insert([
            'log_handler' => $name,
            'log_content' => '['.$brand.']'.'['.$mall.']'.'비밀번호 : '.$pw. " 변경"
        ]);

        if($data->is_api_used == 'N') {
            DB::table('playauto2_api') -> where('mall_code', '=',  $data -> mall_code) -> where('brand_type_code', '=', $data -> brand_type_code)
                -> update([
                    'update_at' => NOW()
                ]);

            $arr = [
                'brand' => CommonCode::CodeName($brand),
                'mall'  => self::mall_name($mall)
            ];

            Mail::to('plan@flasystem.com') -> send(new SendMailToPlan($arr));

            return '성공';
        }

        $playauto2 = new PlayAuto2APIController();
        $result = $playauto2 -> update_shop_info($data);

        if($result == '성공') {
            DB::table('playauto2_api') -> where('mall_code', '=',  $data -> mall_code) -> where('brand_type_code', '=', $data -> brand_type_code)
                -> update([
                    'update_at' => NOW()
                ]);

            $arr = [
                'brand' => CommonCode::CodeName($brand),
                'mall'  => self::mall_name($mall)
            ];

            Mail::to('plan@flasystem.com') -> send(new SendMailToPlan($arr));

            return '성공';
        } else {
            return $result;
        }
    }

    // 관리자 메모 등록
    public static function set_admin_memo(Request $request) {
        DB::table('playauto2_api') -> where('mall_code', '=', $request -> mall_code ) -> where('brand_type_code', '=', $request -> brand_type_code)
            -> update([
                'admin_memo' => $request -> memo
            ]);

        return "수정 완료";
    }

    public static function mall_name($mall) {
        return match ($mall) {
            'BM' => "배민셀러",
            'BMB' => "배민비즈",
            default => CommonCodeName($mall),
        };
    }
}
