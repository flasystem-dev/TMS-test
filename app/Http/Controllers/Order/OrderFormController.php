<?php
namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\Message\KakaoTalkController;
use App\Http\Controllers\Message\SMSController;
use App\Http\Controllers\Payment\NicePayController;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;

use App\Services\Order\OrderService;
use App\Services\Message\MessageService;

use App\DTOs\OrderProduct;

use App\Models\Product\Product;
use App\Models\OrderCart;
use App\Models\TMS_Product;
use App\Models\Vendor;
use App\Models\CommonCode;
use App\Models\User;

class OrderFormController extends Controller
{

########################################################################################################################
##################################################### View #############################################################

    // 수기 주문 창 오픈
    public static function order_form($brand, Request $request)
    {
        
        // 경조사어 카테고리
        $data['msg_templates'] = DB::table('common_code') -> select('code', 'code_name') -> where('parent_code','=','TP') -> orderBy('order')-> get();
        // 상품 카테고리
        $data['product_ctgy'] = DB::table('tms_ctgy') -> select('ct2','ct_name') -> where('ct1', 'A') -> where('ct2', '>', 0) -> get();
        // 자주 쓰는 메모
        $data['memo_list'] = DB::table('admin_note_save') -> select('orderby', 'note') -> where ('brand_type_code', '=', $brand) -> orderBy('orderby', 'desc') -> get();
        // 브랜드
        $data['brand'] = $brand;
        // 채널
        $data['vendors'] = Vendor::where('brand_type', 'like' , "%".$brand."%")->where('is_valid', 'Y') -> get();
        // 지역 추가금
        $data['locations'] = DB::table('loc_add_price') -> get();
        // 주문서 복사 정보
        $data['order'] = OrderData::find($request->order_idx);

        return view('order.popup.form-order', $data);
    }

    // 자주 쓰는 메모 편집 창
    public static function get_memo($brand){
        $memo_list = DB::table('admin_note_save') -> select('orderby', 'note', 'idx' ) -> where ('brand_type_code', '=', $brand) -> orderby('orderby', 'asc') -> get();

        return view('order.popup.admin_memo', [ 'memo_list' => $memo_list, 'brand' => $brand ]);
    }

########################################################################################################################
######################################################### 등록 ##########################################################

    // 수기 주문 등록
    public static function insert_order(Request $request){
        $input = $request -> all();

        $orderProduct = OrderProduct::fromJson($input['orderProduct_json']);

        // 주문 create를 위한 input 데이터 가공
        $input = OrderService::makeInput($input, $orderProduct);

        OrderData::create($input);
        OrderDelivery::create($input);
        OrderPayment::create($input);

        OrderService::upsert_orderItem($input['order_idx'], $orderProduct);

        DB::table('order_log')
            -> insert([
                'od_id' => $input['od_id'],
                'log_by_name' => Auth::user()->name,
                'log_time' => NOW(),
                'log_status' => '주문 등록',
                'log_content' => '신규 수기 주문 등록'
            ]);
        
        // 알림톡 or SMS 전송
        MessageService::sendMessage($input['order_idx'], 1, "order_check", $input['is_alim']);

        switch ($input['payment_type_code']) {
            case 'PTMN':
                return NicePayController::reqKeyIn($request, $input['payment_pid']);
            case 'PTVA':
                return NicePayController::payRequest($request, $input['od_id'], 1);
            case 'PTDP':
            case 'PTOP':
            case 'PTDB':
                return response() -> json(['status'=>1, 'msg'=>"주문 등록 완료"]);
        }
    }

    // 자주 쓰는 메모 입력
    public static function insert_memo(Request $request) {
        $brand = $request -> brand_type_code;
        $note = $request -> note;
        $orderby = $request -> orderby;

        DB::table('admin_note_save') -> insert([
            'brand_type_code' =>  $brand,
            'note' => $note,
            'orderby' => $orderby
        ]);

        return "success";
    }

########################################################################################################################
####################################################### 수정, 삭제 #######################################################

    // 자주 쓰는 메모 삭제
    public static function delete_memo(Request $request) {
        DB::table('admin_note_save') -> where('idx', '=', $request -> idx) -> delete();

        return "success";
    }

    // 자주 쓰는 메모 수정
    public static function update_memo(Request $request) {
        $idx = $request -> idx;
        $note = $request -> note;
        $orderby = $request -> orderby;

        DB::table('admin_note_save')
            -> where('idx', '=', $idx)
            -> update([
                'note' => $note,
                'orderby' => $orderby
            ]);

        return 'success';
    }

    // 사업자 발주 정보 수정
//    public function update_vendor_balju(Request $request) {
//        $data = $request -> all();
//
//        $order = OrderData::find($data['order_idx']);
//        $order ->fill($data);
//
//        if(!empty($data['option_id'])){
//            foreach ($data['option_id'] as $key => $value) {
//                $cart = OrderCart::find($value);
//
//                $content = CommonCode::log_contents_frame('vendor_option_price', $cart -> vendor_option_price, $data['vendor_option_price']);
//
//
//            }
//        }
//        CommonCode::order_log($order, '',  $data);
//    }
    
########################################################################################################################
######################################################### 정보 API ######################################################

    // 상품 선택 후 정보 폼에 추가
    public static function select_product(Request $request) {

        $data['orderProduct'] = OrderService::makeOrderProduct($request);

        return view('order.include.order-form.product-selected', $data);
    }

    // 상품 추가 검색 결과 찾기
    public static function get_productList(Request $request) {
        $brand = $request -> brand_type_code;
        $req_ctgy = $request -> product_ctgy;
        $category = "A".$request -> product_ctgy;
        $search_word = $request -> search_word;
        $price_type = $request -> price_type;

        $query = Product::with(['options', 'prices' => function ($query) use ($price_type) { $query -> where('price_type_id', $price_type); }])
            -> where('brand', $brand)
            -> where('is_view', 1);

        if(!empty($req_ctgy)) {
            $query->where('ctgyA', $category);
        }

        if(!empty($search_word)) {
            $query -> where(function($query) use ($search_word){
                $query -> where('name', 'like', '%'.$search_word.'%')
                    ->orWhere('search_words', 'like', '%'.$search_word.'%');
            });
        }

        $data['products'] = $query -> get();

        return view('order.include.order-form.products-list', $data);
    }

    // 경조사어 찾기 [ 카테고리 버튼 - 내용 ]
    public static function get_CategoryList($ctgy) {
        $list = DB::table('message_templates') -> select('message', 'description') -> where('code', '=', $ctgy) -> get();

        $btns = [];
        foreach ($list as $item) {
            $btns[] = '<button type="button" class="btn btn-outline-secondary btn-sm mb-1" data-value="'.$item -> message.'" onclick="get_event_msg(event);">'.$item -> description.'</button>';
        }
        return $btns;
    }

    // 최근 문구
    public static function previous_ribbon(Request $request) {
        $name = $request -> orderer_name;
        $phone = $request -> orderer_phone;
        $phone = str_replace("-", "", $phone);

        if(DB::table('order_data')->where('orderer_name', $name) -> whereRaw('REPLACE(orderer_phone,"-","") LIKE ?', ["%".$phone."%"]) -> exists()) {
            $order = OrderData::where('orderer_name', $name) -> whereRaw('REPLACE(orderer_phone,"-","") LIKE ?', ["%".$phone."%"]) -> first();
            $delivery = OrderDelivery::firstWhere('order_idx', $order -> order_idx);
            return response() -> json(['status' => 1 , 'ribbon' => $delivery-> delivery_ribbon_left]);
        }
        else {
            return response() -> json(['status'=>0, 'ribbon' => '']);
        }
    }

    // 상점 유저 정보
    public static function get_shop_user(Request $request) {
        $users = User::where('vendor_idx', $request->vendor_idx)-> get();
        $data['users'] = $users;
        return view('order.include.order-form.orderer_mall_id-option', $data);
    }

    // 지역추가금 정보
    public static function location_price(Request $request) {
        $type = $request -> type;
        [$sido, $sigungu] = explode("/", $request -> location);

        return DB::table('loc_add_price') -> where('sido', $sido) -> where('sigungu', $sigungu) -> value($type);
    }

    // 지역추가금 옵션 추가 한 상품 정보 업데이트
    public static function add_locationOption(Request $request) {
        $location = $request -> location;
        $orderProduct = OrderProduct::fromJson($request -> orderProduct_json);

        $orderProduct = OrderProduct::makeLocationOption($orderProduct, $location);

        $data['orderProduct'] = $orderProduct;
        return view('order.include.order-form.product-selected', $data);
    }
}
