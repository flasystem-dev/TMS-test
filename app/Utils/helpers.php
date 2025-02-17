<?php
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