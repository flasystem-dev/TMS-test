<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Banner;
use App\Models\Popup;
use App\Models\CodeOfCompanyInfo;
use App\Http\Controllers\fileUploadController;

class BannerPopupController extends Controller
{
    public static function bannerList(Request $request) {
        $search = $request -> all();

        // 검색 필터
        $banners = Banner::SearchMainBanner($search);

        $company = CodeOfCompanyInfo::all();
        // 중복 상품 갯수


        return view('shop.bannerPopup.banner-list', ['banners' => $banners,'search' => $search,'company'=>$company ]);
    }

    public static function bannerForm(Request $request) {

        return view('shop.bannerPopup.banner-register');
    }

    //배너저장
    public static function BannerSave(Request $request){

        $banner = new Banner();
        $banner['photo'] = $request->temp_img;
        $banner['domain'] = $request->domain;
        $banner['orderBy'] = $request->orderBy;
        $banner['brand'] = $request->brand;
        $banner['text'] = $request->text;
        $banner['link'] = $request->link;
        $banner['name'] = Auth::user()->name;
        if($request->use_yn==='Y'){
            $banner['use_yn'] = "Y";
        }else{
            $banner['use_yn'] = "N";
        }
        $banner->save();

        return response()->json(true);
    }
    
    public static function bannerDelete($id){
        $banner = Banner::find($id);
        $banner -> delete();
        return "success";
    }

    public static function bannerUse($id){
        $banner = Banner::find($id);
        if($banner -> use_yn=='Y'){
            $banner->use_yn='N';
        }else{
            $banner->use_yn='Y';
        }
        $banner->update();
        return "success";
    }

    public static function popupList(Request $request) {
        $search = $request -> all();

        // 검색 필터
        $popups = Popup::SearchPopup($search);

        $company = CodeOfCompanyInfo::all();
        // 중복 상품 갯수

        return view('shop.bannerPopup.popup-list', ['popups' => $popups,'search' => $search,'company'=>$company ]);
    }

    public static function popupForm(Request $request) {

        return view('shop.bannerPopup.popup-register');
    }

    //팝업
    public static function popupSave(Request $request){

        $popup = new Popup();
        $popup['img'] = $request->temp_img;
        $popup['domain'] = $request->domain;
        $popup['brand'] = $request->brand;
        $popup['orderBy'] = $request->orderBy;
        $popup['title'] = $request->title;
        $popup['link'] = $request->link;
        $popup['name'] = Auth::user()->name;
        $popup['start_date'] = $request->start_date;
        $popup['end_date'] = $request->end_date;
        if($request->use_yn==='Y'){
            $popup['use_yn'] = "Y";
        }else{
            $popup['use_yn'] = "N";
        }
        $popup->save();

        return response()->json(true);
    }

    public static function popupUse($id){
        $popup = Popup::find($id);
        if($popup -> use_yn=='Y'){
            $popup->use_yn='N';
        }else{
            $popup->use_yn='Y';
        }
        $popup->update();
        return "success";
    }

    // 팝업, 배너 도메인 검색을 위한 옵션
    public function get_vendors($brand) {
        if($brand === "BTFC" || $brand === "BTCS") {
            $vendors = DB::table("vendor") -> select('mall_name', 'rep_name as name', 'domain') -> where('is_valid', 'Y') -> get();
        }else {
            $vendors = DB::table("vendor_pass") -> select('mall_name', 'name', 'domain') -> where('is_valid', 'Y') -> get();
        }
        return view('shop.include.banner-popup-vendorList', ['vendors' => $vendors]);
    }

    // 팝업, 배너 우선순위
    public function update_orderBy($type, Request $request) {
        if($type === "banner") {
            $model = Banner::find($request->id);
        }elseif ($type === "popup") {
            $model = Popup::find($request->id);
        }
        if($model) {
            $model->orderBy = $request->orderBy;
            $model->save();
        }

        session() -> flash('update',1);
        return response()->json(true);
    }

    // 팝업, 배너 삭제
    public function delete_model($type, $id) {
        if($type === "banner") {
            $model = Banner::find($id);
        }elseif ($type === "popup") {
            $model = Popup::find($id);
        }
        if($model) {
            $model->delete();
        }

        session() -> flash('update',1);
        return response()->json(true);
    }

    // 팝업, 배너 사용유무
    public function update_use($type, Request $request) {
        if($type === "banner") {
            $model = Banner::find($request->id);
        }elseif ($type === "popup") {
            $model = Popup::find($request->id);
        }
        if($model) {
            $model->use_yn = $request->check;
            $model->save();
        }

        session() -> flash('update',1);
        return response()->json(true);
    }
}
