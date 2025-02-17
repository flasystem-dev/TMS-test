<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $fillable = ['id','brand','domain','photo','link','title','text','use_yn'];

    public static function SearchMainBanner($search) {

        $banners = Banner::query();
        if($search){
            foreach($search['brand'] as $brand){
                $banners->Where('brand','!=',$brand);
            }
        }
        return $banners->get();
    }

    
}
