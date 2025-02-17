<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CodeOfCompanyInfo extends Model
{
    use HasFactory;
    protected $table = 'code_of_company_info';

    protected $fillable=[
        'company_info_idx','brand_type_code','shop_name','brand_ini','shop_gen_tel','shop_tel','shop_url','toss_client_key','toss_secret_key','toss_shop_code',
        'newrun_api_url','newrun_api_mid','newrun_api_key','mobile_api_url','playauto_api_key', 'popbill_id','user_yn',
        'com_name','ceo_name','com_tel','com_fax','com_email','bank_info','com_address','com_type','com_kind','dep_in_charge'
    ];

    public static function comInfo($code){
        return CodeOfCompanyInfo::where('brand_type_code',$code)->select('brand_ini')->value('brand_ini');
    }

    public static function comList(){
        $ddd=CodeOfCompanyInfo::get();
      // dd($ddd);exit;
        return CodeOfCompanyInfo::get();
    }

    public static function getUrl($mall_code, $brand_type_code) {
            return DB::table('playauto2_api')->where('mall_code', $mall_code)
                ->where('brand_type_code', $brand_type_code)
                ->select('item_url', 'admin_url')
                ->first();
    }

    public static function goodsUrl($mall_code,$brand_type_code){
        return DB::table('playauto2_api')->where('mall_code',$mall_code)->where('brand_type_code',$brand_type_code)->select('item_url')->value('item_url');
    }

    public static function adminUrl($mall_code,$brand_type_code){
        return DB::table('playauto2_api')->where('mall_code',$mall_code)->where('brand_type_code',$brand_type_code)->select('admin_url')->value('admin_url');
    }
}
