<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\BoardNotice;
use App\Models\BoardEvent;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['id','type','brand','skin','mobile_skin','title', 'table_name'];

    // 테이블명 가져오기
    public static function boardTable($type) {
        return Board::select('table_name')->where('type',$type)->first()->table_name;
    }

    //게시판 불러오기
    public static function selectBoard($boardType) {
        return Board::query()->Where('type',$boardType)->first();
    }

    public static function searchBoardList($search,$boardType) {
        $boardList = DB::table(self::boardTable($boardType));
        if(isset($search['title'])){
            $title = $search['title'];
            $boardList->where("title",'like',"%{$title}%");
        }
        if(isset($search['brand'])){
            foreach($search['brand'] as $brand){
                $boardList->Where('brand','!=',$brand);
            }
        }
        return $boardList -> get();
    }

    public static function boardView($type, $id) {
        return DB::table(self::boardTable($type)) -> where('id', $id) -> first();
    }

    public static function boardModel($type, $id) {
        if($type=='notice') {
            if($id==0){
                return new BoardNotice();
            }else {
                return BoardNotice::find($id);
            }
        }elseif($type=='event') {
            if($id==0){
                return new BoardEvent();
            }else {
                return BoardEvent::find($id);
            }
        }
    }
}
