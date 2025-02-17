<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Faq extends Model
{
    use HasFactory;
    protected $fillable = ['id','brand','title','question','answer','name'];
    protected $table = "faq";
    public static function searchFaqList($search) {

        $faqs = Faq::query();

        if($search){
            foreach($search['brand'] as $brand){
                $faqs->Where('brand','!=',$brand);
            }
        }
        $faqs = Faq::query();

        return $faqs->get();
    }

    public static function faqView($id) {
        $faq = Faq::query();
        $faq->Where('id','=',$id);
        return $faq->first();
    }
}
