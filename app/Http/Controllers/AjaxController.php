<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AjaxController extends Controller
{
    public function brandSession(Request $request){
        // Request 쿼리 모두 가져오기
        $data = $request->brands;

        $brands = ['BTCP', 'BTCC', 'BTSP', 'BTBR', 'BTOM', 'BTCS', 'BTFC'];

        foreach ($brands as $brand) {
            if(in_array($brand, $data)){
                session([$brand => 'Y']);
            }else {
                session([$brand => 'N']);
            }
        }
    }

    public function appItemSelect(Request $request){
        $data = $request->all();
        //category
        $category = $data['category'];
        $brand = $data['brand'];
        $list = DB::table('app_goods_list')
            -> where('brand_type_code',"=",$brand)
            -> where('goods_ctgy', '=', "{$category}")->get();
        
        $option="<option value=''>{$category}의 상품을 선택해주세요</option>";
        foreach ($list as $item) {
            $goods_name= $item -> goods_name;
            $goods_amount = number_format($item->goods_amount);
            $goods_id = $item->goods_id;
            $option.= "<option value='{$goods_id}'>{$goods_name}({$goods_amount}원)</option>";
        }
        echo $option;
    }
}
