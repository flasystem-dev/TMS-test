<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class LocAddPrice extends Model
{
    use HasFactory;
    protected $table = 'loc_add_price';

    public static function SearchLocAddPrice($search_arr) {

        $locAddPrice = LocAddPrice::query();
        if(isset($search_arr['sido'])){
            $sido = $search_arr['sido'];
            if(!empty($sido)){
                $locAddPrice->where("sido",$sido);
            }
        }

        if(isset($search_arr['location_search'])) {
            $search_word = $search_arr['location_search'];
            if(!empty($search_word)){
                $locAddPrice->where("sido",'like',"%$search_word%")->orWhere("sigungu",'like',"%$search_word%");
            }

        }
        $result = $locAddPrice->paginate(15) -> withQueryString();

        return $result;
    }
}
