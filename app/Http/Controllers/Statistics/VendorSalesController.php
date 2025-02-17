<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Hashids\Hashids;

use App\Services\Statistics\VendorService;
use App\Services\Statistics\SpecificationService;

use App\Models\Vendor;
use App\Models\Specification;

class VendorSalesController extends Controller
{
    // 사업자 리스트
    public function index(Request $request) {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        $brand = $request->brand ?? 'BTFCB';
        $dateType = $request->dateType ?? 'order';

        $vendors_count = VendorService::vendors_count_data();
        $vendors_data = VendorService::vendor_specification_data($year, $month, $brand, $dateType);

        $data['year'] = $year;
        $data['month'] = $month;
        $data['brand'] = $brand;
        $data['vendors_count'] = $vendors_count;
        $data['vendors'] = $vendors_data;

        return view('statistics.vendors-sales', $data);
    }

    // 사업자 연 명세서 리스트
    public function vendor_specification_list($idx, Request $request) {
        $year = $request->year ?? date('Y');

        $data['specifications'] = SpecificationService::vendor_specificationForYear($idx, $year);

        $data['rep_name'] = DB::table('vendor')->where('idx', $idx)->value('rep_name');

        return view('statistics.popup.vendor-specifications', $data);
    }

    // 추천인 검색 팝업 리스트
    public function recommendPerson_list(Request $request) {
        $year = $request->year;
        $month = $request->month;
        $recommend = $request->recommend;
        $dateType = $request->dateType;
        $brand = $request->brand;

        $vendors_data = VendorService::recommendPerson_specification($year, $month, $recommend, $dateType);

        $data['year'] = $year;
        $data['month'] = $month;
        $data['dateType'] = $dateType;
        $data['recommend'] = $recommend;
        $data['brand'] = $brand;
        $data['vendors'] = $vendors_data;

        return view('statistics.popup.recommendPerson-list', $data);
    }

    // 사업자 매출 캘린더 view
    public function vendor_salesCalendar($vendor, Request $request) {
        $data['year'] = $request->year;
        $data['month'] = $request->month;
        $data['dateType'] = $request->dateType;
        $data['vendor'] = $vendor;
        $data['rep_name'] = DB::table('vendor')->where('idx', $vendor)->value('rep_name');
        $data['selected_month'] = Carbon::create($request->year, $request->month, 1)->format('Y-m-d');

        return view('statistics.popup.vendor-salesCalendar', $data);
    }

########################################################################################################################
########################################################################################################################

    // 사업자 매출 풀캘린더 api
    public function vendor_sales_calender_api(Request $request) {

        $response = VendorService::vendorSales_calenderAPI($request);

        return response()->json($response);
    }
}
