<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlimLog extends Model
{
    use HasFactory;

    protected $table = 'alim_log';
    protected $primaryKey = 'idx';
    protected $fillable=['idx','od_id','templateName','templateCode','log_time','log_status','template'];

    public static function alimLog($search_arr){
        $query = alimLog::query();

        if($search_arr['sw_1']){
            $word1=$search_arr['word1'];
            $query->where($search_arr['sw_1'],'like',"%{$word1}%");
        }
        if($search_arr['start_date']&&$search_arr['end_date']){
            $query->whereBetween("log_time", [$search_arr['start_date']." 00:00:00",$search_arr['end_date']." 23:59:59"]);
        }
        $query ->orderBy("idx","desc");
        $alim_data = $query ->paginate(20) -> withQueryString();
        return $alim_data;
    }

    public static function roadAlimTalkTemplate($idx){
        $result = alimLog::query()->where("idx",$idx)->first();
        $template = $result-> template;
       return $template;
    }
}
