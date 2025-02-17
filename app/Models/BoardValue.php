<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardValue extends Model
{
    use HasFactory;

    protected $fillable = ['id','brand','type','title','contents','writer','use_yn','hit','file1','file2','name','userid'];

    public static function boardView($id){
        $board = BoardValue::query();
        $board->Where('id','=',$id);
        return $board->first();
    }


  
    public static function searchBoardList($search,$boardType) {

        $boardList = BoardValue::query()->where("type",$boardType);
        if(isset($search['title'])){
            $title = $search['title'];
            $boardList->where("title",'like',"%{$title}%");
        }
        if(isset($search['brand'])){
            foreach($search['brand'] as $brand){
                $boardList->Where('brand','!=',$brand);
            }
        }
        return $boardList->get();
    }
}
