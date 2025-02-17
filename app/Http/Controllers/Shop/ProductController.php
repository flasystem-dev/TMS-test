<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Decoders\DataUriImageDecoder;
use Intervention\Image\Decoders\Base64ImageDecoder;

use App\Services\Product\ProductService;

use App\Models\TMS_Product;
use App\Models\TMS_ProductOption;
use App\Models\CodeOfCompanyInfo;
use App\Models\LocAddPrice;
use App\Models\Product\Product;
use App\Models\Product\ProductOptionPrice;
use App\Models\Product\ProductPrice;

use App\Imports\ProductImport;
use App\Imports\ProductOptionImport;
use App\Imports\ProductSampleImport;
class ProductController extends Controller
{
########################################################################################################################
################################################  View  ################################################################

    ########################################  상품 리스트 index  #########################################################
    public static function products_index(Request $request){
        $search = $request -> all();

        // 검색 필터
        $data['products'] = ProductService::get_productsForIndex($search);

        // 카테고리2
        $data['ctgy2_options'] = self::get_category($request);

        // 브랜드 옵션 값
        $data['brands'] = DB::table('code_of_company_info') -> select('brand_type_code', 'brand_ini') -> where('is_used', 1) -> get();

        return view('shop.product.index', $data);
    }

    #######################################   상품 form   ###############################################################
    public static function product_form($id){
        $data['ctgy1'] = DB::table('tms_ctgy') -> where('ct1', '=', 'A') -> where('ct2', '>', 0) -> get();
        $data['ctgy2'] = DB::table('tms_ctgy') -> where('ct1', '=', 'B') -> where('ct2', '>', 0) -> get();
        $data['ctgy3'] = DB::table('tms_ctgy') -> where('ct1', '=', 'C') -> where('ct2', '>', 0) -> get();
        $data['price_types'] = DB::table('product_price_types') -> get();
        $data['option_types'] = DB::table('product_option_types') -> get();

        $product = Product::find($id);

        $data['search_words'] = explode('|', optional($product) -> search_words ?? "");

        $data['product'] = $product;
        return view('shop.product.product-form', $data);
    }


########################################################################################################################
################################################   조회   ###############################################################

    ######################################   상품 코드 중복 확인   ########################################################
    public static function check_duplicate_code(Request $request){
        $code = $request->code;
        $check = DB::table('products') -> where('code', $code) -> exists();
        return response()->json($check);
    }

########################################################################################################################
#####################################################   삽입   ##########################################################
    
    ##############################################  검색 단어 추가  ######################################################
    public static function insert_search_word(Request $request){
        $product = Product::find($request -> id);

        if(empty($product -> search_words)) {
            $product -> search_words = $request -> word;
        } else {
            $product->search_words .= '|' . $request->word;
        }
        $product -> save();

        session()->flash('update-search-word', 1);
        return response()->json(true);
    }

########################################################################################################################
################################################   수정   ###############################################################


    ##########################################  상품 수정 ( upsert )  ###################################################
    public static function upsert_product(Request $request){
        $input = $request -> all();

        if(!empty($input['ctgyB'])){
            $input['ctgyB'] = self::arrToStr($input['ctgyB']);
        }
        if(!empty($input['ctgyC'])) {
            $input['ctgyC'] = self::arrToStr($input['ctgyC']);
        }

        if($input['id'] === "0") {
            $product = new Product();
            $input['id'] = Product::max('id') + 1;
        }else {
            $product = Product::find($input['id']);
        }

        if(!isset($input['is_used'])) {
            $input['is_used'] = 0;
        }

        if ($request->hasFile('img') && $request->file('img')->isValid()){

            $file_name = $request -> id . "_detail";

            $image = $request -> file('img');

            // 사진 이름
            $img_name = $file_name . "." . $image -> extension();

            // 사진 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/product 기본 경로 )
            $request -> img -> storeAs('img', $img_name ,'product');

            // 대표 사진 URL
            $img_path = public_path("assets/images/product/img/".$img_name);

            $input['img'] = asset("assets/images/product/img/".$img_name);

            // 썸네일 만들기
            self::make_thumbnail($img_name, $img_path);

            // 썸네일 URL
            $input['thumbnail'] = asset("assets/images/product/img/thumb/thumb_".$img_name);

        }

        $product -> fill($input);
        write_table_log($product);
        $product -> save();

        // 가격 타입 변경
        ProductPrice::make_productPrices($input);

        // 옵션 만들기
        ProductOptionPrice::make_productOption($input);

        session()->flash('update', 1);
        return redirect('/shop/product/'.$input['id']);
    }

    #############################################  검색어 수정  ##########################################################
    public static function edit_search_word(Request $request){

        $product = Product::find($request -> id);
        $product -> search_words = str_replace($request -> pre_word, $request -> now_word, $product -> search_words );
        $product -> save();

        session()->flash('update-search-word', 1);
        return response() ->json(true);
    }

    #############################################   상품 상태 변경 (state)  #########################################
    public static function change_state($column, $id){
        $product = Product::find($id);

        $check = $product -> $column;

        if($check) {
            $product -> $column = 0;
        } else {
            $product -> $column = 1;
        }

        $product -> save();
        return response()->json(true);
    }


########################################################################################################################
#######################################################   삭제   ########################################################

    ##############################################  상품 숨김 ( Remove )  ###############################################
    public static function remove_product($id){
        Product::where('id',$id) -> update(['is_view' => 0]);

        return response()->json(true);
    }

    ##################################################  검색어 삭제  #####################################################
    public static function delete_search_word(Request $request){
        $product = Product::find($request -> id);

        $wordsArray = explode('|', $product->search_words);

        $wordsArray = array_filter($wordsArray, static function ($word) use ($request) {
            return $word !== $request->word;
        });

        $product->search_words = implode('|', $wordsArray);
        $product -> save();

        session()->flash('update-search-word', 1);
        return response() ->json(true);
    }


    // 상품 리스트 페이지에서 수정
    public static function simple_update_product(Request $request){
        $input = $request -> all();

        $product = TMS_Product::firstWhere('pr_id', $input['pr_id']);
        $product -> fill($input);
        write_table_log($product);

        $product -> save();

        return "수정 완료";
    }
    
    // 상품 등록 페이지 사진 업로드
    public static function upload_file(Request $request){
        // 상품 대표 사진 업로드
        if ($request->hasFile('product_img') && $request->file('product_img')->isValid()){

            $image = $request -> file('product_img');

            // 임시 사진 이름
            $temp_img_name = time() . "." . $image -> extension();

            // 사진 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/product 기본 경로 )
            $request -> product_img -> storeAs('Temp', $temp_img_name ,'product');

            return $temp_img_name;
        }

        // 삼풍 상세 정보 사진 업로드
        if ($request->hasFile('detail_img') && $request->file('detail_img')->isValid()){

            $image = $request -> file('detail_img');

            // 임시 사진 이름
            $temp_img_name = (int)(microtime(true)*10) . "." . $image -> extension();

            // 사진 파일 저장
            $request -> detail_img -> storeAs('detail', $temp_img_name ,'product');

            // 저장한 사진 URL
            $url = asset("assets/images/product/detail/".$temp_img_name);

            return response()-> json(['state'=>true, 'url'=>$url]);
        }

        return response()-> json(['state'=>false]);
    }

    // 대표 사진 썸네일 만들기
    protected static function make_thumbnail($img_name, $url){
        $img_path = public_path('assets/images/product/img/');
        $manager = ImageManager::withDriver(new Driver());

//        $decode = urldecode($url);
        $image = $manager -> read($url);
        $image -> resize(500, 500) -> save($img_path."/thumb/thumb_".$img_name);
    }

    // 검색 용 타입2 카테고리 옵션
    public static function get_category(Request $request){
        $ctgy1 = $request -> category1;

        $ctgy2 = $request -> category2;

        $options = '<option value="">- 전체 -</option>';

        if($ctgy1 !== 'all' ) {
            $category = DB::table('tms_ctgy') -> where('ct1', '=', $ctgy1) -> where('ct2', '>', 0) -> get();

            foreach($category as $ctgy) {
                $selected =  $ctgy -> ct1 . $ctgy -> ct2 == $ctgy2 ? 'selected' : '';
                $options .= '<option value="'. $ctgy -> ct1 . $ctgy -> ct2 . '" '. $selected .'>' . $ctgy -> ct_name . '</option>';
            }
        }

        return $options;
    }

    // 상품 정보 엑셀 파일 업로드
    public static function excelFile_upload(Request $request){
        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new ProductImport($request -> handler), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }
    }

    // 상품 옵션 엑셀 파일 업로드
    public static function optionFile_upload(Request $request){
        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new ProductOptionImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }
    }

    // 썸네일 URL 가져오기 ( 없으면 만들기 )
    public static function get_Thumbnail($product){

        $thumb = $product -> pr_thumb;

        try {
            $response = Http::get($thumb);
            if($response -> ok()){
                return $thumb;
            } else {
                $file_arr = explode('/', $product -> pr_img);
                $last_index = count($file_arr) - 1;
                $file_fullName = $file_arr[$last_index];
                self::make_thumbnail($file_fullName, $product -> pr_img);
                $thumb_url = asset("assets/images/product/img/thumb/thumb_".$file_fullName);

                $product -> pr_thumb = $thumb_url;

                return $thumb_url;
            }
        }catch (\Exception $e){
            $file_arr = explode('/', $product -> pr_img);
            $last_index = count($file_arr) - 1;
            $file_fullName = $file_arr[$last_index];
            self::make_thumbnail($file_fullName, $product -> pr_img);
            $thumb_url = asset("assets/images/product/img/thumb/thumb_".$file_fullName);

            $product -> pr_thumb = $thumb_url;
            $product -> save();
            return $thumb_url;
        }

    }


    //지역별 추가금 관리 페이지
    public static function deliveryPrice(request $request){
        // 브랜드 옵션 값
        $company = CodeOfCompanyInfo::all();

        $search_arr = $request->all();
        //리스트
        $delivery_info = LocAddPrice::searchLocAddPrice($search_arr);

        return view('shop.delivery-price', ['delivery_info'=>$delivery_info,'company'=>$company,'search_arr'=>$search_arr]);
    }
    
    //지역별 추가금 수정
    public static function updateLocAddPrice(request $request){

        $LocAddPrice = LocAddPrice::where("idx",$request->idx);
        $input = $request->all();
        $input['update_name'] = Auth::user()->name;
        $LocAddPrice->update($input);
        return "S";

    }
    
    // 상품 기타 엑셀 파일 업로드
    public static function etcFile_upload(Request $request){
        if($request->hasFile('files') && $request->file('files')->isValid()) {
            $result = Excel::import(new ProductSampleImport(), $request -> file('files'));

            if(!empty($result)) {
                return "업로드 완료";
            }else {
                return "[에러발생]업로드 실패";
            }
        } else {
            return "파일을 다시 확인해주세요.";
        }
    }
########################################################################################################################
#####################################################   보조 함수    #####################################################

    ################################################  카테고리 문자열 만들기  ##############################################
    // 배열 -> '/' 구분자로 문자열 만들기
    protected static function arrToStr($arr){
        $str = "";
        foreach($arr as $key => $value) {
            if($key === array_key_last($arr)){
                $str .= $value;
            }else{
                $str .= $value.'/';
            }
        }
        return $str;
    }
}


