<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\SendTransaction;
use App\Models\OrderData;
use App\Models\OrderDelivery;
use App\Models\CommonCode;
use App\Models\CodeOfCompanyInfo;
use App\Models\ContractCompany;
use App\Models\User;
use App\Utils\Common;
use App\Models\Transaction\TransactionLog;
use App\Models\Transaction\DocumentCheck;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


class TransactionController extends Controller
{
    public function __construct() {
//        $this -> middleware('user-refresh-check') -> only('transaction');
    }

    public function transaction(Request $request) {

        $data = array();
        $data['commonDate'] =CommonCode::commonDate();
        $data['orders'] = OrderData::transaction_orders($request);
        $data['totalAmount'] = 0;
        $data['user_list'] = User::where('brand', $request->brand) -> get();
        
        foreach ($data['orders'] as $order) {
            $data['totalAmount'] += $order -> payment_amount;
        }


        return view('Document.transaction-orders', $data);
    }

    public function update_order_data($idx, Request $request) {
        $input = $request -> all();

        $order = OrderData::find($idx);
        $delivery = OrderDelivery::firstWhere('order_idx', $idx);
        $dataAdd = OrderDataAdd::firstWhere('order_idx', $idx);
        $input['od_id'] = $order -> od_id;

        $order      -> fill($input);
        $delivery   -> fill($input);
        $dataAdd    -> fill($input);

        Common::order_log($order, $delivery, $dataAdd, $input);

        $order      -> save();
        $delivery   -> save();
        $dataAdd    -> save();

        return response() -> json('수정완료');
    }
    
    // 거래내역서 보기
    public static function transaction_document(Request $request) {
        $encodedData = $request -> query('data');

        if ($encodedData) {
            try {
                $decodedData = base64_decode($encodedData);

                $data = json_decode($decodedData, true);

                $data['orders'] = [];
                $brand = OrderData::findOrFail($data['orders_idx'][0]) -> brand_type_code;
                $data['total_sum_price'] = 0;

                foreach ($data['orders_idx'] as $order_idx) {
                    $order = OrderData::find($order_idx);
                    $data['orders'][] = $order;
                    $data['total_sum_price'] += $order -> total_amount;
                }

                $data['com_info'] = ContractCompany::find($data['issuance_id']);
                $data['brand_info'] = CodeOfCompanyInfo::firstWhere('brand_type_code', $brand);
                $data['data'] = $encodedData;

            }catch (\Exception $e) {
                return abort(404, 'Resource not found');
            }

            if($data['type'] == '1') {
                return view('Document.transaction-document', $data);
            }elseif($data['type'] == '2') {
                return view('Document.transaction-document2', $data);
            }else {
                return view('Document.transaction-document3', $data);
            }
        } else {
            return abort(404, 'Resource not found');
        }
    }
    
    public static function send_email(Request $request) {
        $data = array();

        $data['com_name']       = $request -> com_name;
        $data['brand']          = $request -> brand;
        $data['receipt_date']   = $request -> receipt_date;
        $data['brand_tel']      = $request -> brand_tel;
        $data['email']          = $request -> email;
        $data['link']           = route('transaction-view') . "?data=" . $request -> data;
        $data['handler']        = $request -> handler;

        Mail::mailer('transaction') -> to($data['email']) -> send(new SendTransaction($data));

        TransactionLog::createLog($data);
        DocumentCheck::transaction_check($request -> data);

        return response() -> json(['message' => '전송완료']);
    }

}
