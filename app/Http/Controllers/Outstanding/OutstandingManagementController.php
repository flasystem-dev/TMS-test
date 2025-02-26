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

use App\Services\Outstanding\OutstandingService;
use App\Utils\Common;

class OutstandingManagementController extends Controller
{
########################################################################################################################
##################################################### View #############################################################

    ################################################# 미수 현황 리스트 ###############################################
    public function orders(Request $request){
        $search = $request -> except('page', 'standard');

        $data['orders'] = OutstandingService::getOrders($search);

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('outstanding.orders', $data);
    }

}
