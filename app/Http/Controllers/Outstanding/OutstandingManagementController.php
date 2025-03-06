<?php


namespace App\Http\Controllers\Outstanding;

use App\Http\Controllers\Controller;
use App\Models\CommonCode;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\CodeOfCompanyInfo;
use Illuminate\Support\Facades\DB;

use App\Services\Outstanding\OutstandingOrderService;
use App\Services\Outstanding\OutstandingVendorService;
use App\Services\Outstanding\OutstandingClientService;
use App\Services\Outstanding\OutstandingService;
use App\Utils\Common;

class OutstandingManagementController extends Controller
{
########################################################################################################################
##################################################### View #############################################################

    ################################################# 미수 현황 리스트 ###############################################
    public function orders(Request $request){
        $search = $request -> except('page', 'standard');

        $data['orders'] = OutstandingOrderService::getOrders($search);

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('outstanding.orders', $data);
    }

    public function vendors(Request $request){
        $search = $request -> except('page', 'standard');

        $data['vendors'] = OutstandingVendorService::getVendors($search);

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('outstanding.vendors', $data);
    }

    public function clients(Request $request){
        $search = $request -> except('page', 'standard');

        $data['clients'] = OutstandingClientService::getClients($search);

//        dd($data['clients'][0]);

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('outstanding.clients', $data);
    }
}
