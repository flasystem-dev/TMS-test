<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AESCryptoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Hashids\Hashids;
use Carbon\Carbon;
use Validator;

use App\Exports\ExcelDownloadExport;
use App\Exports\ExcelAlimDownloadExport;
use App\Exports\CalcVendorExport;
use App\Imports\MonthlyEtcPriceImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\Vendor\AdjustmentService;
use App\Services\Specification\SpecificationService;

use App\Models\User;
use App\Models\Vendor;
use App\Models\CommonCode;
use App\Models\Specification;
use App\Models\TotalInformations;

class FlaCalculateController extends Controller
{
    // 명세서 발급
    public function monthlySpecification(Request $request){

        SpecificationService::makeSpecification($request->all());

        return response() -> json(true);
    }
    
    // 정산 리스트
    public function flaCalList(Request $request){
        $search = $request -> all();

        // 쿼리 등록
        $vendors = AdjustmentService::get_monthVendorAdjustment($search);

        // 총 건수
        $data['total_order_cnt'] = 0;
        // 원청금액 합계
        $data['total_order_amount'] = 0;
        // 화훼금액 합계
        $data['total_vendor_amount'] = 0;
        // 옵션금액 합계
        $data['total_option_amount'] = 0;
        // 실지급액 합계
        $data['total_settlement_amount'] = 0;

        foreach ($vendors as $vendor) {
            $data['total_order_cnt'] += $vendor->order_count();
            $data['total_order_amount'] += $vendor->order_amount();
            $data['total_vendor_amount'] += $vendor->vendor_amount();
            $data['total_option_amount'] += $vendor->option_amount();
            $data['total_settlement_amount'] += max($vendor->settlement_amount($search['year'] ?? date('Y'), $search['month'] ?? date('m')), 0);
        }
        $data['vendors'] = $vendors;

        return view('Vendor.fla-cal-list', $data);
    }

    public function test_flaCalList(Request $request){
        $search = $request -> all();

        // 쿼리 등록
        $vendors = AdjustmentService::get_monthVendorAdjustment($search);

        // 총 건수
        $data['total_order_cnt'] = 0;
        // 원청금액 합계
        $data['total_order_amount'] = 0;
        // 화훼금액 합계
        $data['total_vendor_amount'] = 0;
        // 옵션금액 합계
        $data['total_option_amount'] = 0;
        // 실지급액 합계
        $data['total_settlement_amount'] = 0;

        foreach ($vendors as $vendor) {
            $data['total_order_cnt'] += $vendor->order_count();
            $data['total_order_amount'] += $vendor->order_amount();
            $data['total_vendor_amount'] += $vendor->vendor_amount();
            $data['total_option_amount'] += $vendor->option_amount();
            $data['total_settlement_amount'] += max($vendor->settlement_amount($search['year'] ?? date('Y'), $search['month'] ?? date('m')), 0);
        }
        $data['vendors'] = $vendors;

        return view('Vendor.test-adjustment', $data);
    }

    // 기타 금액 업로드
    public function monthlyEtcPriceUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx'
        ]);

        $year = $request->excel_year;
        $month = $request->excel_month;

        try {
            $file = $request->file('file');
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                // Excel 파일 임포트
                Excel::import(new MonthlyEtcPriceImport($year, $month), $file);
                Log::info('File imported successfully.');
                return response()->json(['message' => '성공했습니다.'], 200);
            } else {
                return response()->json(['message' => '파일이 잘못되었습니다.'], 400);
            }
        } catch (\Exception $e) {
            // 예외 처리 및 로그 기록
            Log::error('Excel import error: ' . $e->getMessage());
            return response()->json(['message' => '오류로인해 업로드 하지 못했습니다.', 'error' => $e->getMessage()], 500);
        }
    }
    
    // 기타금액 업로드 위해 프레임 파일 엑셀 다운로드 작업
    public function vendorExcelDownload(Request $request){
        $brand = $request->brand;
        $today = date("Y-m-d",time());
        $brand_name=BrandAbbr($brand);
        return Excel::download(new ExcelDownloadExport($brand),"기타금액업로드양식_{$brand_name}_{$today}.xlsx");
    }
    
    // 정산 엑셀 다운로드
    public function calcAmount_ExcelDownload(Request $request){
        $search = $request -> all();

        $vendors = AdjustmentService::get_monthVendorAdjustment($search);

        foreach ($vendors as $vendor) {
            // 은행명
            $vendor -> bank_name = self::get_bankName($vendor);
            // 서비스 이용료
            $vendor -> service_fee = $vendor -> service_fee($search['year'] ?? date('Y'), $search['month'] ?? date('m'));
            // 실지급액
            $vendor -> settlement_amount = $vendor -> settlement_amount($search['year'] ?? date('Y'), $search['month'] ?? date('m'));
        }

        return Excel::download(new CalcVendorExport($vendors), 'test.xlsx');
    }
    
    // 카드 금액 일괄 계산
    public function calc_card_amount(Request $request){

        try{
            AdjustmentService::calculateUpsertCardAmount_brand($request->all());
            return response() -> json(['status'=>true]);
        }catch (\Exception $e) {
            \Log::error("카드 결제 계산 실패 - 수동");
            \Log::error($request->year."-".$request->month);
            \Log::error($e);
            return response() -> json(['status'=>false]);
        }
    }
    
    // 카드 금액 개별 계산
    public static function calc_cardAmount_individual(Request $request){
        try {
            AdjustmentService::calculateUpsertCardAmount_vendor($request->all());

            return response() -> json(true);
        }catch (\Exception $e) {
            \Log::error("카드 총액 계산 실패");
            \Log::error($e->getMessage());
            return response() -> json(false);
        }

    }

    ############################################## 정산 계산 함수 ########################################################

    // 은행 명
    public static function get_bankName($vendor) {
        return DB::table('code_of_nicepay_card_bank') -> select('code_name') -> where('code_no', $vendor->bank_code) -> value('code_name') ?? "";
    }
    
}
