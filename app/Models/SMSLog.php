<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    use HasFactory;

    protected $table = 'sms_log';
    protected $fillable = ['sender', 'phone', 'log_time', 'status', 'contents', 'handler'];

    public static function sms_list($search) {
        $query = SMSLog::query();

        if($search) {
            $search_column = [
                'sender',            // 주문번호
                'phone',             // 주문자명
                'contents',          // 주문자 핸드폰
            ];

            // 검색어
            if(!empty($search['word1'])){
                if($search['sw_1']!== "all") {
                    switch ($search['sw_1']) {
                        case "sender":
                        case "phone":
                            $query->whereRaw("REPLACE(" . $search['sw_1'] . ", '-', '') LIKE ?", ["%" . str_replace('-', '', $search['word1']) . "%"]);
                            break;
                        default:
                            $query->where($search['sw_1'], 'like', "%" . $search['word1'] . "%");
                            break;
                    }
                }else {
                    $query -> where(function($query) use ($search_column,$search){
                        foreach ($search_column as $column) {
                            switch ($column) {
                                case 'sender':
                                case 'phone':
                                    $query->orWhereRaw(
                                        "REPLACE(" . $column . ", '-', '') LIKE ?",
                                        ["%" . str_replace('-', '', $search['word1']) . "%"]
                                    );
                                    break;
                                default :
                                    $query->orWhere($column, 'like', "%". $search['word1']. "%");
                            }
                        }
                    });
                }
            }

            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case "date_type":
                            if (!empty($search['start_date']) && !empty($search['end_date']))
                                $query->whereBetween($value, [$search['start_date'] . " 00:00:00", $search['end_date'] . " 23:59:59"]);
                            break;
                    }
                }
            }
        }
        $query -> orderBy('log_time', 'desc');
        return $query -> paginate(15);
    }
}
