<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\TMS_ProductOption;
use App\Models\Vendor;

class TMS_Product extends Model
{
    use HasFactory;

    protected $table = 'tms_products';

    protected $primaryKey = 'idx';
    protected $fillable = ['pr_id', 'pr_ctgy1', 'pr_ctgy2', 'pr_ctgy3', 'pr_type', 'pr_name', 'pr_brand', 'pr_opt1_name', 'pr_opt2_name', 'pr_opt3_name',
        'pr_opt1_amount', 'pr_opt2_amount', 'pr_opt3_amount', 'pr_img', 'pr_thumb', 'pr_description', 'pr_order_amount', 'order_amount_type', 'pr_vendor_amount',
        'pr_amount_type1', 'pr_amount_type2', 'pr_amount_type3', 'pr_amount_type4', 'pr_amount_type5','pr_popular','pr_discount',
        'delivery_type', 'created_at', 'updated_at', 'is_used', 'pr_memo', 'search_words', 'pr_handler'
        ];

    public function options()
    {
        return $this -> hasMany(TMS_ProductOption::class, 'pr_id', 'pr_id');
    }

    public function vendor_price($vendor_idx){
        $type = Vendor::find($vendor_idx) -> price_type ?? 1;
        $price_type = "pr_amount_type".$type;
        return $this -> $price_type;
    }

    public static function SeachProduct($search) {

        $query = TMS_Product::query();


        if(isset($search['pr_brand'])) {
            $query -> whereIn('pr_brand', $search['pr_brand']);
        }

        if(isset($search['category1']) && isset($search['category2'])) {
            switch ($search['category1']) {
                case 'A' :
                    $query -> where('pr_ctgy1', '=', $search['category2']);
                    break;
                case 'B' :
                    $query -> where('pr_ctgy2', 'like', "%".$search['category2']."%");
                    break;
                case 'C' :
                    $query -> where('pr_ctgy3', 'like', "%".$search['category2']."%");
                    break;

            }
        }

        if(isset($search['pr_name'])) {
            $query -> where('pr_name', 'like', '%'.$search['pr_name'].'%');
        }

        if(!isset($search['is_not_used']) || $search['is_not_used'] != 'Y') {
            $query -> whereNotIn('is_used', ['N']);
        }

        if(isset($search['duplicate']) && $search['duplicate'] == 'Y') {
            $items = TMS_Product::groupBy('pr_id') -> selectRaw('pr_id, COUNT(*) as count') -> havingRaw('COUNT(*) >= 2') -> get();
            $pr_id = [];

            foreach ($items as $item) {
                $pr_id[] = $item -> pr_id;
            }

            $query -> whereIn('pr_id', $pr_id);
        }

        return $query ->paginate(10) -> withQueryString();
    }

    public static function get_ctgy_name($idx) {
        $ctgy = TMS_Product::find($idx) ->  pr_ctgy1;
        return DB::table('tms_ctgy') -> where('ct1', $ctgy[0]) -> where('ct2', $ctgy[1]) -> first() -> ct_name;
    }
}
