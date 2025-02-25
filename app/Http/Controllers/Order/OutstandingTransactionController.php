<?php


namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\CommonCode;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\CodeOfCompanyInfo;
use Illuminate\Support\Facades\DB;

use App\Services\Order\OutstandingTransactionService;
use App\Utils\Common;

class OutstandingTransactionController extends Controller
{
########################################################################################################################
##################################################### View #############################################################

    ################################################# 미수 현황 리스트 ###############################################
    public function index(Request $request){
        $search = $request -> except('page');

        if(session('order_idx')) {
            $data = OutstandingTransactionService::getSelectedOrders();
        }else {
            $data = OutstandingTransactionService::getTransaction($search);
        }

        $data['commonDate'] =CommonCode::commonDate();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();

        return view('order.transaction.outstanding-transaction', $data);
    }

}
