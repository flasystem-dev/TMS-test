<?php
namespace App\QueryBuilders\Vendor;

use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VendorQueryBuilder
{
    public static function createQuery()
    {
        return Vendor::Query();
    }

    public static function createDBQuery()
    {
        return DB::table('vendor');
    }

    public static function vendorSubSelect($query)
    {
        $query -> select('idx', 'brand_type', 'mall_name', 'rep_name', 'partner_name', 'rep_type', 'registered_date', 'service_price', 'service_percent',
            'bank_code', 'vendor_id', 'gen_number', 'bank_number', 'name_of_deposit');
        return $query;
    }

    public static function findBrand($query, $brand)
    {
        $query->where('vendor.brand_type', 'like' ,$brand);
        return $query;
    }

    public static function isJungsan($query)
    {
        $query->where('vendor.is_jungsan', 1);
        return $query;
    }

    // 활성화 & 서비스 기간 만료 아닌 사업자
    public static function enabledVendor($query, $year, $month)
    {
        $query->where(function($query) use ($year, $month) {
            $query->where('vendor.is_valid', '=', 'Y')
                ->orWhere('vendor.service_ex_date', '>', "{$year}-{$month}-01");
        });
        return $query;
    }

    public static function checkRegisteredDate($query, $year, $month)
    {
        $month_check = Carbon::create($year, $month, 1, 0, 0, 0)->endOfMonth()->format('Y-m-d');
        $query ->whereNot('vendor.registered_date', '>', $month_check);
        return $query;
    }

    public static function selectedVendor($query, $idx_array)
    {
        if(!empty($idx_array)){
            $query-> whereIn('idx', $idx_array);
        }
        return $query;
    }

    public static function searchWord($query, $column, $word)
    {
        if(!empty($word)){
            $search_column = [
                'mall_name',                // 상호명
                'rep_name',                 // 대표자명
                'rep_tel',                  // 대표자 연락처
                'gen_number',               // 대표번호
                'did_number',               // DID
            ];

            if($column!== "all") {
                switch ($column) {
                    case "rep_tel":
                    case "gen_number":
                        $query->whereRaw("REPLACE({$word}, '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                        break;
                    default:
                        $query->where($column, 'like', "%{$word}%");
                }
            }else {
                $query -> where(function($query) use ($search_column,$word){
                    foreach ($search_column as $column) {
                        switch ($column) {
                            case "rep_tel":
                            case "gen_number":
                                $query->orWhereRaw("REPLACE({$column}, '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                                break;
                            default :
                                $query->orWhere($column, 'like', "%{$word}%");
                        }
                    }
                });
            }

        }
        return $query;
    }
}