<?php

namespace App\Http\Controllers\Board;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Faq;
use App\Models\CodeOfCompanyInfo;
class faqController extends Controller
{
    //
    public static function faqList(Request $request) {

        $search = $request -> all();
        $faqs = Faq::searchFaqList($search);
        $company = CodeOfCompanyInfo::all();

        return view('Board.faq-list',['faqs'=>$faqs,'company'=>$company]);
    }

    public static function faqForm(Request $request){
        if($request->id){
            $id = $request->id;
            $faq = Faq::faqView($id);
        }
        if(isset($faq)){
            return view('Board.faq-register',['faq'=>$faq]);
        }else{
            return view('Board.faq-register');
        }

    }

    public static function faqSave(Request $request){
        if($request->id){
            $faq = Faq::find($request->id);
        }else{
            $faq = new Faq();
        }
        $faq['title'] = $request->title;
        //$faq['domain'] = 'flasystem';
        $faq['brand'] = $request->brand;
        $faq['question'] = $request->question;
        $faq['answer'] = $request->answer;
        $faq['name'] = Auth::user()->name;
        if($request->id){
            $faq->update();
        }else{
            $faq->save();
        }
        return "success";
    }

    public static function faqDelete($id){
        $popup = Faq::find($id);
        $popup -> delete();
        return "success";
    }


}
