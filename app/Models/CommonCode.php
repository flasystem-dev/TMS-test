<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CommonCode extends Model
{
    protected $table = 'common_code';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable=['code','parent_code','code_name','description','order','use_yn'];

    public static function codeName($code){
        return CommonCode::where('code',$code)->select('code_name')->value('code_name');
    }
    public static function commonDate(){
        $now = time();
        $date_term = date('w', $now);
        $week_term = $date_term + 7;
        $last_term = strtotime(date('Y-m-01', $now));
        $commonDate['today']    = date("Y-m-d");
        $commonDate['yesterday']= date('Y-m-d', $now - 86400);
        $commonDate['tomorrow'] = date('Y-m-d', $now + 86400);
        $commonDate['week']     = date('Y-m-d', strtotime('-'.$date_term.' days', $now));
        $commonDate['month']    =  date('Y-m-01');
        $commonDate['preg_week_s']    = date('Y-m-d', strtotime('-'.$week_term.' days', $now));
        $commonDate['preg_week_e']    = date('Y-m-d', strtotime('-'.($week_term - 6).' days', $now));
        $commonDate['2month_ago_s']    = date('Y-m-01', strtotime('-2 Month', $last_term));
        $commonDate['2month_ago_e']    = date('Y-m-t', strtotime('-2 Month', $last_term));
        $commonDate['preg_month_s']    = date('Y-m-01', strtotime('-1 Month', $last_term));
        $commonDate['preg_month_e']    = date('Y-m-t', strtotime('-1 Month', $last_term));
        $commonDate['month3']    = date('Y-m-01', strtotime('-3 Month', $last_term));
        $commonDate['month_e']    = date('Y-m-t');
        $commonDate['month6']    = date('Y-m-01', strtotime('-6 Month', $last_term));
        $commonDate['year']    = date("Y-01-01");
        $commonDate['year_e']    = date("Y-12-31");
        $commonDate['preg_year_s']    = date('Y-01-01', strtotime('-1 Year', $last_term));
        $commonDate['preg_year_e']    = date('Y-12-31', strtotime('-1 Year', $last_term));

        return $commonDate;
    }
}
