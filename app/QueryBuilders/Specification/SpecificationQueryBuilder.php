<?php
namespace App\QueryBuilders\Specification;

use App\Models\Specification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SpecificationQueryBuilder
{

    public static function createQuery()
    {
        return Specification::query();
    }

    public static function joinVendor($query)
    {
        $query ->join('vendor', 'vendor.idx', '=', 'specification.mall_code');
        return $query;
    }

    public static function findId($query, $id)
    {
        $query -> find($id);
        return $query;
    }

    public static function upsert_specification($vendor, $year, $month, $deposit_date)
    {
        Specification::updateOrCreate(
            [
                'mall_code' => $vendor->idx,
                'year' => $year,
                'month' => $month
            ],
            [
                'sp_order_cnt' => $vendor->order_count(),
                'sp_order_amount' => $vendor->order_amount(),
                'sp_vendor_amount' => $vendor->vendor_amount(),
                'sp_vendor_options_amount' => $vendor->option_amount(),
                'sp_service_percent' => $vendor->service_percent(),
                'sp_service_fee' => $vendor->service_fee($year, $month),
                'sp_card_amount' => $vendor->card_amount(),
                'sp_card_fee' => $vendor->card_fee(),
                'sp_tax_amount'=> $vendor->tax_amount(),
                'sp_settlement_amount' => $vendor->settlement_amount($year, $month),
                'sp_etc1' => $vendor->etc1_amount(),
                'sp_etc2' => $vendor->etc2_amount(),
                'sp_etc3' => $vendor->etc3_amount(),
                'deposit_date' => $deposit_date,
                'user_name' => Auth::user()->name ?? ''
            ]
        );
    }

    public static function specificationsForSend($year, $month, $brand, $column, $word)
    {
        $query = DB::table('vendor')
            -> select('idx', 'brand_type', 'mall_name', 'rep_name', 'gen_number', 'rep_tel', 'rep_email', 'service_percent', 'rep_type', 'domain')
            -> where('brand_type', 'like' , "%{$brand}%");

        if(!empty($word)) {
            $search_column = [
                'mall_name',                // 상호명
                'rep_name',                 // 대표자명
                'gen_number',               // 대표번호
                'did_number',               // DID
            ];

            if($column!== "all") {
                switch ($column) {
                    case "gen_number":
                        $query->whereRaw("REPLACE({$column}, '-', '') LIKE ?", ["%" . str_replace('-', '', $word) . "%"]);
                        break;
                    default:
                        $query->where($column, 'like', "%{$word}%");
                }
            }else {
                $query -> where(function($query) use ($search_column,$word){
                    foreach ($search_column as $column) {
                        switch ($column) {
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

        $vendors = DB::table('vendor')
            ->fromSub($query, 'vendor')
            ->join('specification', function($join) use ($year, $month) {
                $join->on('vendor.idx', '=', 'specification.mall_code')
                    ->where('specification.year', '=', $year)
                    ->where('specification.month', '=', $month);
            })
            ->select([
                'idx', 'brand_type', 'mall_name', 'rep_name', 'gen_number', 'rep_tel', 'rep_email','service_percent', 'rep_type', 'domain',
                'specification.id as sp_id', 'specification.sp_settlement_amount as sp_settlement_amount',
                'specification.send_email as send_email', 'specification.send_talk as send_talk'
            ]);
        return $vendors -> get();
    }
}