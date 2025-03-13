<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (!function_exists('CommonCodeName')) {
    function CommonCodeName($code) {
        $commonCodes = Cache::get('common_codes', collect());

        return optional($commonCodes->get($code))->code_name;
    }
}

if (!function_exists('BrandAbbr')) {
    function BrandAbbr($code) {
        $map = [
            'BTCP' => '꽃총',
            'BTCC' => '칙폭',
            'BTSP' => '사팔',
            'BTBR' => '바로',
            'BTOM' => '오만',
            'BTCS' => '꽃사',
            'BTFC' => '플체',
            'BTNS' => '내시'
        ];
        return $map[$code] ?? '';
    }
}

if (!function_exists('write_table_log')) {
    function write_table_log($model) {

        $columns = $model -> getDirty() ?? [];
        $tbl_name = $model -> getTable();
        $id = $model -> getKey();

        $log_arr = [];
        if($columns) {
            foreach ($columns as $column => $value) {
                $log_arr[$column] = [
                    'prev' => trim($model->getOriginal($column)),
                    'curr' => trim($value)
                ];
            }

            $log_json= json_encode($log_arr, JSON_UNESCAPED_UNICODE);

            DB::table('table_log')->insert([
                'tbl_name' => $tbl_name,
                'key' => $id ?? "",
                'contents' => $log_json,
                'handler' => Auth::user()->name
            ]);
        }
    }
}

if (!function_exists('priceTypeName')) {
    function priceTypeName($id) {
        $priceType = Cache::get('product_price_type', collect());

        return optional($priceType->get($id))->name;
    }
}

if (!function_exists('optionTypeName')) {
    function optionTypeName($id) {
        $optionType = Cache::get('product_option_type', collect());

        return optional($optionType->get($id))->name;
    }
}

// 전화번호 형식 변환 함수 (한국식 자동 변환)
if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone); // 숫자만 남김

        // 02 지역번호 또는 3자리 지역번호 (서울: 02-123-4567, 지방: 031-123-4567)
        if (preg_match('/^(02)(\d{3,4})(\d{4})$/', $phone, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }

        // 일반 핸드폰 번호 (010-1234-5678)
        if (preg_match('/^(01[016789])(\d{3,4})(\d{4})$/', $phone, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }

        // 일반 지역번호 (031-123-4567, 042-123-4567)
        if (preg_match('/^(\d{3})(\d{3,4})(\d{4})$/', $phone, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }

        return $phone; // 형식에 맞지 않으면 원본 반환
    }
}

if (!function_exists('commonDate')) {
    function commonDate()
    {
        return [
            'today' => Carbon::today()->toDateString(),
            'yesterday' => Carbon::yesterday()->toDateString(),
            'tomorrow' => Carbon::tomorrow()->toDateString(),
            'this_week' => Carbon::now()->startOfWeek()->toDateString(),
            'this_month_s' => Carbon::now()->startOfMonth()->toDateString(),
            'this_month_e' => Carbon::now()->endOfMonth()->toDateString(),
            'prev_week_s' => Carbon::now()->subWeek()->startOfWeek()->toDateString(),
            'prev_week_e' => Carbon::now()->subWeek()->endOfWeek()->toDateString(),
            'prev_month_s' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
            'prev_month_e' => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
            '2month_ago_s' => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
            '2month_ago_e' => Carbon::now()->subMonths(2)->endOfMonth()->toDateString(),
            '3monthAgo' => Carbon::now()->subMonths(3)->toDateString(),
            '6monthAgo' => Carbon::now()->subMonths(6)->toDateString(),
            'this_year_s' => Carbon::now()->startOfYear()->toDateString(),
            'this_year_e' => Carbon::now()->endOfYear()->toDateString(),
            'prev_year_s' => Carbon::now()->subYear()->startOfYear()->toDateString(),
            'prev_year_e' => Carbon::now()->subYear()->endOfYear()->toDateString(),
        ];
    }
}