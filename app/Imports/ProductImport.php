<?php

namespace App\Imports;

use App\Models\TMS_Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

use App\Models\CommonCode;
use mysql_xdevapi\Exception;

class ProductImport implements ToModel, WithStartRow, WithUpserts, SkipsEmptyRows
{

    protected String $handler;
    protected static $pr_img = "https://flasystem.flabiz.kr/assets/images/product/img/";
    protected static $pr_thumb = "https://flasystem.flabiz.kr/assets/images/product/img/thumb/thumb_";

    public function __construct($name)
    {
        $this->handler = $name;
    }

    /**
    * @param array $row
    *
    * @return TMS_Product|null
    */
    public function model(array $row)
    {
        try {
            return new TMS_Product([
                'pr_id'             => $row[0],
                'pr_ctgy1'          => self::ctgy_code($row[1]),
                'pr_ctgy2'          => self::make_ctgy_arr($row[2], $row[3], $row[4]),
                'pr_ctgy3'          => self::make_ctgy_arr($row[5], $row[6], $row[7]),
                'pr_type'           => $row[8],
                'pr_name'           => $row[9],
                'pr_brand'          => self::pr_brand($row[10]),
                'pr_img'            => self::url_change($row[11]),
//                'pr_thumb'          => self::save_thumbnail($row[11]),
                'pr_description'    => self::url_change($row[12]),
                'pr_order_amount'   => $row[13],
                'order_amount_type' => self::order_amount_type($row[14]),
                'pr_amount_type1'   => $row[15],
                'pr_amount_type2'   => $row[16],
                'pr_amount_type3'   => $row[17],
                'pr_amount_type4'   => $row[18],
                'pr_amount_type5'   => $row[19],
                'pr_popular'        => $row[20],
                'pr_discount'       => $row[21],
                'delivery_type'     => self::delivery_type($row[22]),
                'is_used'           => $row[23],
                'pr_memo'           => $row[24],
                'search_words'      => '',
                'pr_handler'        => $this->handler
            ]);
        }catch (\Exception $e) {
            dd($row);
            Log::error($e);
        }
    }

    public function startRow(): int
    {
        return 2; // 데이터 읽기가 시작될 첫 번째 행을 지정합니다. (여기서는 2번째 행부터 읽습니다.)
    }

    // 발주 금액 타입
    protected static function order_amount_type($type) {
        return match ($type) {
            '금액' => 'A',
            '비율' => 'R',
            default => NULL,
        };
    }
    
    // 상품 지역 추가금 코드
    protected static function address_add_amount($text) {
        return match ($text) {
            '축하화환'      => 'CH',
            '근조화환'      => 'GH',
            '축하오브제'     => 'CO',
            '근조오브제'     => 'GO',
            '근조바구니'     => 'BG',
            '축하 쌀화환'    => 'CS',
            '근조 쌀화환'    => 'GS',
            '기타'          => 'ET',
            default => NULL,
        };
    }

    // 배송 타입 코드
    protected static function delivery_type($type) {
        $code = CommonCode::firstWhere('code_name', $type);

        return $code -> code;
    }

    // 브랜드 타입 코드
    protected static function pr_brand($brand) {
        $code = CommonCode::firstWhere('code_name', $brand);

        return $code -> code;
    }

    // 한글로 받은 카테고리 코드로 변환하기
    protected static function ctgy_code($text) {
        
        $code = DB::table('tms_ctgy') -> where('ct_name', '=', trim($text)) -> first();
        $ctgy = '';
        if(!empty($code)) {
            $ctgy = $code -> ct1 . $code -> ct2;
        }

        return $ctgy;
    }

    // 카테고리 '/' 로 구분하는 문자열 만들기
    protected static function make_ctgy_arr($text1, $text2, $text3) {
        $str = '';

        if(!empty($text1)) {
            $str .= self::ctgy_code($text1);
        }

        if(!empty($text2)) {
            $str .= '/' . self::ctgy_code($text2);
        }

        if(!empty($text3)) {
            $str .= '/' . self::ctgy_code($text3);
        }

        return $str;
    }

    // 업셋용 유니크 키
    public function uniqueBy() {
        return "pr_id";
    }

    public static function url_change($url) {
        $url = str_replace(":3000", "", $url);
        return str_replace('style="width: 25%;"', "", $url);
    }

    // 대표 사진 썸네일 만들기
    protected static function make_thumbnail($img_name, $url){
        $img_path = public_path('assets/images/products/img/fla');
        $manager = ImageManager::withDriver(new Driver());

        $decode = file_get_contents($url);
        $image = $manager -> read($decode);
        $image -> resize(500, 500) -> save($img_path."/thumb_".$img_name);
    }
     public static function save_thumbnail($img) {
         $file_arr = explode('/', $img);
         $last_index = count($file_arr) - 1;
         $file_fullName = $file_arr[$last_index];
         self::make_thumbnail($file_fullName, $img);
         $thumb_url = asset("assets/images/products/img/fla/thumb_".$file_fullName);
         return str_replace(":3000", "", $thumb_url);
    }
}
