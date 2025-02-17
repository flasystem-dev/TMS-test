<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OrderPaymentImport;
use App\Imports\UserImport;
use App\Imports\VendorImport;
use App\Models\TMS_User;
use Illuminate\Support\Str;

class DevController extends Controller
{
    public function index(Request $request) {

        return view('util.dev-part' );
    }

    public function updateCommonCode(Request $request) {
        Cache::forget('common_codes');
        Cache::put('common_codes_updated_at', now());

        Cache::rememberForever('common_codes', function () {
            return CommonCode::all()->keyBy('code');
        });

        return response() -> json(true);
    }

    public function user_require_reload() {
        TMS_User::whereNot('dep', "임원") -> update(['require_reload' => 1]);
    }

    public function orderPayment(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new OrderPaymentImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function user(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new UserImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function vendor(Request $request) {
        if($request->ip()!="14.42.241.54"){
            return "접근 불가";
        }

        set_time_limit(600);

        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new VendorImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }

    }

    public function statistics_url() {
        $url_data = DB::table('order_data_url')-> select('url') -> where('url', 'like', '%http%') -> get();

        $statistics_data = [];

        foreach ($url_data as $value) {
            $url_data = $value -> url;

            $data = self::extractUrls($url_data);

            // http 제거
            $data1 = str_replace("https://", "", $data);
            $data2 = str_replace("http://", "", $data1);

            // 뒤의 uri 제거
            if(Str::contains($data2, "/")) {
                $data3 = explode("/", $data2)[0];
            }else {
                $data3 = $data2;
            }
            
            // www. 제거
            $data4 = str_replace("www.", "", $data3);

            // .com 제거
            $data5 = str_replace(".com", "", $data4);

            // .co.kr 제거
            $data6 = str_replace(".co.kr", "", $data5);

            // .kr 제거
            $data7 = str_replace(".kr", "", $data6);


            $statistics_data[] = $data7;
        }

        $result = array_count_values($statistics_data);

        foreach ($result as $key => $value) {
            DB::table('statistics_url') -> upsert(
                [ 'url' => $key, 'count' => $value ],
                ['url'],
                ['count']
            );
        }

        return true;
    }

#######################################################################################################################

    public static function extractUrls($text) {
        // URL을 찾기 위한 정규식 패턴
        // http 또는 https 로 시작하는 곳 찾아서
        // :// 가 뒤따라오는 형태
        // 공백이 아닌 문자로 끝나는 곳 매칭
        $pattern = '/\bhttps?:\/\/[^\s]+/i';

        // 정규식으로 URL 추출
        preg_match_all($pattern, $text, $matches);

        return $matches[0][0]; // 추출된 URL들만 반환
    }
}
