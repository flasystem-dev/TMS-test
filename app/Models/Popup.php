<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use HasFactory;
    protected $fillable = ['id','domain','brand','title','img','link','start_date','end_date','text','use_yn','name'];
    protected $table = 'popup';
    public static function SearchPopup($search) {

        $popup = Popup::query();
        if($search){
            foreach($search['brand'] as $brand){
                $popup->Where('brand','!=',$brand);
            }
        }
        return $popup->get();
    }

}
