<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Hashids\Hashids;

use App\Mail\SendSpecification;
use App\Exports\SendTalkSpecificationExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\Specification\SpecificationService;

use App\Models\Vendor;
use App\Models\User;
use App\Models\Specification;

class SpecificationController extends Controller
{
    // 명세서 뷰
    public function specificationForm($sp_id){
        $data['sp'] = SpecificationService::getSpecificationWithVendor($sp_id);

        $data['canEdit'] = SpecificationService::checkDateforEdit($data['sp']['year'], $data['sp']['month']);

        return view('Vendor.specification',$data);
    }
    
    // 명세서 변경 폼
    public function specificationForm_edit($sp_id){
        $data['sp'] = SpecificationService::getSpecificationWithVendor($sp_id);
        return view('Vendor.specification-form',$data);
    }
    
    // 명세서 변경
    public function update_specification(Request $request) {
        $input = $request->all();
        $sp = Specification::find($input['sp_id']);
        $sp -> fill($input);
        write_table_log($sp);
        $sp->save();
    }

    // 명세서 전송 리스트
    public function specification_send_list(Request $request){
        $data['vendors'] = SpecificationService::get_specificationsForSend($request->all());
        return view('Vendor.specification-send', $data);
    }

    // 명세서 카톡 전송용 엑셀 다운로드
    public function specification_send_talkExcel(Request $request){
        $vendors = SpecificationService::get_specificationsForSend($request->all());
        $hashids = new Hashids('flasystem-dev');

        foreach ($vendors as $vendor) {
            $hash_id = $hashids -> encode($vendor->sp_id);
            $url = ".flachain.net";
            if($vendor -> brand_type === "BTCS") {
                $url = ".flapeople.com";
            }
            $vendor -> specification_url = "https://" . $vendor -> domain . $url . "/vendor/withdraw/specification/" . $hash_id;
        }

        return Excel::download(new SendTalkSpecificationExport($vendors), 'test.xlsx');
    }

    // 명세서 메일 전송
    public function send_email(Request $request){
        $data = $request->all();
        $hashids = new Hashids('flasystem-dev');

        $vendors = DB::table('vendor')
            -> whereIn('idx', $data['idx'])
            -> join('specification', 'vendor.idx', '=', 'specification.mall_code')
            -> where('specification.year', $data['year'])
            -> where('specification.month', $data['month'])
            -> select('vendor.brand_type as brand_type', 'vendor.domain as domain', 'vendor.rep_email as rep_email', 'specification.id as sp_id')
            -> get();

        foreach ($vendors as $vendor) {
            $hash_id = $hashids -> encode($vendor->sp_id);
            $url = ".flachain.net";
            if($vendor -> brand_type === "BTCS") {
                $url = ".flapeople.com";
            }
            $vendor -> specification_url = "https://" . $vendor -> domain . $url . "/vendor/withdraw/specification/" . $hash_id;

            Specification::where('id', $vendor->sp_id)->increment('send_email');
            Mail::mailer('transaction')->to($vendor->rep_email) -> queue(new SendSpecification($data['year'], $data['month'], $vendor));
        }

        return response()->json(true);
    }
    
    // 명세서 삭제
    public function delete_specification(Request $request){
        $id_list = $request->sp_id;

        foreach ($id_list as $id) {
            DB::table('specification')->where('id', $id)->delete();
        }
    }
}
