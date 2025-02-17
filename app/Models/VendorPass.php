<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorPass extends Model
{
    use HasFactory;

    protected $table = 'vendor_pass';
    protected $fillable=[
        'id', 'brand', 'mall_name', 'domain', 'vendor_id', 'name', 'price_type', 'discount', 'memo', 'is_credit', 'is_valid', 'personal_domain'
    ];

    

########################################################################################################################
########################################################## 조회 #########################################################

    ########################################## Pass 리스트 - index #######################################################

    public static function index_passList($search) {
        $passes = VendorPass::query();

        if($search) {
            $passes->where('brand',$search['brand']);

            if(isset($search['search'])){
                $search_column = [
                    'mall_name',
                    'domain',
                    'name',
                    'memo'
                ];


                if($search['search']!== "all") {
//                    $passes->whereRaw("REPLACE(" . $search['search'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['search_word']) . "%"]);
                    $passes->where($search['search'], "like", "%".$search['search_word']."%");
                }else {

                    $passes -> where(function($query) use ($search_column,$search){
                        foreach ($search_column as $column) {
//                            $query->orWhereRaw("REPLACE(" . $column . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['search_word']) . "%"]);
                            $query->orWhere($column, "like", "%".$search['search_word']."%");
                        };
                    });
                }
            }
        }
        return $passes -> get();
    }
}
