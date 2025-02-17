<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\AESCryptoController;
use App\Models\User;
use App\Models\Vendor;
use Validator;

class FlaBusinessController extends Controller
{
    // 사업자 등록 , 수정 upsert
    public function flaBusinessSave(Request $request){

        $vendor_info = $request -> all();

        //주민등록증
        if($request->hasFile('iden_file')){
            $file = $request->file('iden_file');
            $path_name = time().'_iden_file.'.$file -> extension();
            $path = $request -> iden_file -> storeAs('iden_file', $path_name ,'vendor');
            if($path) {
                $iden_url = asset("assets/images/vendor/iden_file/".$path_name);
                $vendor_info['iden_file'] = $iden_url;
            }

        }

        //사업자등록증
        if($request->hasFile('business_file')){
            $file = $request->file('business_file');
            $path_name = time().'_business_file.'.$file -> extension();
            $path = $request -> business_file -> storeAs('business_file', $path_name ,'vendor');
            if($path) {
                $business_url = asset("assets/images/vendor/business_file/".$path_name);
                $vendor_info['business_file']  = $business_url;
            }
        }

        //통장사본
        if($request->hasFile('bank_file')){
            $file = $request->file('bank_file');
            $path_name = time().'_bank_file.'.$file -> extension();
            $path = $request -> bank_file -> storeAs('bank_file', $path_name ,'vendor');
            if($path) {
                $bank_url = asset("assets/images/vendor/bank_file/".$path_name);
                $vendor_info['bank_file']  = $bank_url;
            }
        }

        //예치금 계약서
        if($request->hasFile('deposit_form')){
            $file = $request->file('deposit_form');
            $path_name = time().'_deposit_form.'.$file -> extension();
            $path = $request -> deposit_form -> storeAs('deposit_form', $path_name ,'vendor');
            if($path) {
                $deposit_url = asset("assets/images/vendor/deposit_form/".$path_name);
                $vendor_info['deposit_form']  = $deposit_url;
            }
        }

        //주민번호암호화
        if(!empty($vendor_info['rr_number22'])) {
            $rr_number2 = Crypt::encryptString($vendor_info['rr_number22']);
            $vendor_info['rr_number2'] = $rr_number2;
        }

        // 신규 or 수정
        if(DB::table('vendor') -> where('idx', $vendor_info['idx']) -> exists()) {
            $vendor = Vendor::find($vendor_info['idx']);
        }else {
            $vendor = new Vendor();
            $vendor_info['idx'] = Vendor::max('idx') + 1;
        }
        $vendor -> fill($vendor_info);
        write_table_log($vendor);
        $vendor -> save();

        //회원가입 처리하기
        if(!empty($vendor_info['vendor_id'])) {
            $input = $request->all();
            if(User::where('user_id', $vendor_info['vendor_id'])->where('vendor_idx', $vendor_info['idx'])->exists()) {
                $user = User::where('user_id', $vendor_info['vendor_id'])->where('vendor_idx', $vendor_info['idx']) -> first();
            }else {
                $user = new User();
                $input['id'] = User::max('id') + 1;
            }
            $input['user_id'] = $vendor_info['vendor_id'];
            if (!empty($vendor_info['vendor_pw'])) {
                $input['password'] = bcrypt($vendor_info['vendor_pw']);
            }
            $input['vendor_idx']=$vendor_info['idx'];
            $input['brand'] = substr($vendor_info['brand_type'],0, 4);
            $input['name']=$vendor_info['rep_name'];
            $input['phone']=$vendor_info['rep_tel'];
            $input['email']=$vendor_info['rep_email'];
            $input['email_verified']='Yes';
            $input['is_vendor']=2;
            $user->fill($input);
            write_table_log($user);
            $user->save();
        }
        Session::flash('alert', '1');
        return redirect('vendor/fla-business/view/'.$vendor_info['idx']);
    }
    
    // 사업자 등록 , 수정 폼
    public function flaBusinessView($idx)
    {
        $data['idx'] = $idx;
        $data['vendor_info'] = Vendor::find($idx);

        return view('Vendor.fla-business-form', $data);

    }
    
    // 사업자 리스트
    public function flaBusinessList(Request $request)
    {
        // 쿼리 가져오기
        $search_arr = $request -> all();

        // 쿼리 등록
        $query = Vendor::vendorList($search_arr);
        $vendors = $query -> paginate(10)-> withQueryString();

        // 브랜드 별 인원 정보 ( 사업자, 신규, 탈퇴 )
        $member_info = self::getBrandInfo();

        return view('Vendor.fla-business-list', ['vendors' => $vendors , 'member_info' => $member_info ,'search_arr' => $search_arr ]);
    }
    
    // 배너, 팝업 업로드
    public static function upload_file(Request $request) {
        
        // 배너 업로드
        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {

            $image = $request -> file('banner');
            
            // 배너 이름
            $banner_name = "testBanner" . "." . $image -> extension();

            // 배너 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/homepage 기본 경로 )
            $request -> banner -> storeAs('banner', $banner_name ,'homepage');
            $url = asset("assets/images/homepage/banner/".$banner_name);

            return "업로드 완료";
        }
        
        // 팝업 업로드
        if ($request->hasFile('popup') && $request->file('popup')->isValid()) {

            $image = $request -> file('popup');
            
            // 팝업 파일 이름
            $popup_name = "testPopUp" . "." . $image -> extension();

            // 팝업 파일 저장 ( popup 폴더 내 , 파일 이름 , public/assets/images/homepage 기본 경로 )
            $request -> popup -> storeAs('popup', $popup_name ,'homepage');

            $url = asset("assets/images/homepage/popup/".$popup_name);

            return "업로드 완료";
        }

        return "업로드 실패";
    }

    // 브랜드 타입 별 정보 가져오기
    protected static function getBrandInfo() {
        $member_info = [];

        // 브랜드 타입
        $vendor_type = Vendor::groupBy('brand_type') -> get();

        foreach ($vendor_type as $type) {
            $member_info[] = self::countMemberInfo($type -> brand_type);
        }

        return $member_info;
    }


    // 브랜드별 사업자, 신규, 탈퇴 인원 체크
    protected static function countMemberInfo($brand) {

        $today = Carbon::now();
        $format_today = $today -> format('Y-m-d');
        
        // 한달 전을 신규 기준
        $month_ago = $today -> subMonth(1);
        $format_month_ago = $month_ago -> format('Y-m-d');

        // 사업자 ( 유효 : Y , 브랜드 타입 )
        $member = Vendor::where('is_valid', 'Y') -> where('brand_type', $brand) -> where('is_jungsan', 1) -> count();
        // 신규 ( 유효 : Y, 등록일 : 한달이내, 브랜드 타입 )
        $new_member_cnt = Vendor::whereBetween('registered_date', [$format_month_ago, $format_today]) -> where('brand_type', $brand) -> where('is_valid', 'Y') -> where('is_jungsan', 1) -> count();
        // 탈퇴 ( 유효 : N, 브랜드 타입)
        $withdraw = Vendor::where('is_valid', 'N') -> where('brand_type', $brand) -> where('is_jungsan', 1) -> count();

        $info = [
            'brand' => $brand,
            'new' => $new_member_cnt,
            'member' => $member,
            'withdraw' => $withdraw
        ];

        return $info;
    }

    // ID 중복 확인
    protected static function checkIdDup(Request $request) {
        $id= $request->id;
        if($request->type=='vendor'){
            $cnt= Vendor::where('vendor_id',$id)-> count();
            if($cnt>0){
                echo "N";
            }else{
                echo "Y";
            }
        }
    }

    // 도메인 중복 확인
    protected static function checkDomainDup(Request $request) {
        $domain= $request->domain;
        $cnt= Vendor::where('domain',$domain)-> count();
        if($cnt>0){
            echo "N";
        }else{
            echo "Y";
        }
    }
    
    // 주민번호 뒷자리 확인
    public static function check_rrNumber(Request $request) {
        $password = $request->input('password');
        $idx = $request->input('idx');

        if ($password === '100231') {
            $rrn_encrypt = Vendor::find($idx)->rr_number2;
            $rrn = Crypt::decryptString($rrn_encrypt);

            return response()->json([
                'success' => true,
                'message' => '비밀번호가 일치합니다.',
                'rr_number' => $rrn
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '비밀번호가 올바르지 않습니다.'
            ]);
        }
    }

    // 첨부된 파일 보기
    public static function check_file(Request $request) {
        $password = $request->input('password');
        if ($password === '100231') {
            return response()->json([
                'success' => true,
                'message' => '비밀번호가 일치합니다.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => '비밀번호가 올바르지 않습니다.'
            ]);
        }
    }
    
    // 추천인 벤더 리스트
    public static function get_vendor_list(Request $request) {

        $brand = substr($request->brand, 0, 4);

        $vendors = DB::table('vendor')
            -> select('idx', 'rep_name', 'partner_name', 'mall_name')
            -> where('brand_type', 'like' , $brand."%")
            -> where('is_valid', 'Y')
            -> get();

        $data['vendors'] = $vendors;
        return view('Vendor.include.form-recommendPerson', $data);
    }

########################################################################################################################

}
