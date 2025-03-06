<?php
namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;
use App\Models\Order\OrderPayment;

use App\Models\Transaction\OrderDataTran;

use App\Services\Order\OrderService;
use App\Services\Message\MessageService;
use App\Services\Order\OrderDetailService;
use App\Services\Order\IntranetService;

use App\DTOs\OrderProduct;

class OrderBaljuController extends Controller
{
    // 발주 폼 view
    public function balju_form($idx) {

        $data['order'] = OrderData::with('delivery', 'item')->find($idx);
        return view('order.popup.form-balju', $data);
    }

    public function order_balju(Request $request)
    {
        $input  = $request->all();

        $order = OrderData::with('delivery', 'item')->find($input['order_idx']);

        $orderProduct = IntranetService::makeOrderProduct($input);

        $data = IntranetService::makeBaljuData($order, $orderProduct, $input);

        $incoding_result = IntranetService::incodingToEucKr($data);

        // 인코딩 실패
        if($incoding_result['state']) {
            return response()->json(['state'=>0, 'msg'=>$incoding_result['msg'] ]);
        }
        $data = $incoding_result['data'];
        
        $result = IntranetService::balju_order($data);

        if($result==="0") {
            IntranetService::updateOrderData($order, $orderProduct);

        //거래 내역서 목록 입력
            $orderDelivery = OrderDelivery::find($input['order_idx']);
            $orderData = array_merge($order->toArray(), $orderDelivery->toArray());
            OrderDataTran::create($orderData);

        return response()->json(['state'=>1, 'msg'=>'발주 성공']);
        }else {
            \Log::error('[인트라넷 발주 실패]');
            \Log::error('[에러코드] : '.$result);

            $error_msg = array(
                "11" => "배송날짜를 확인해주세요.",
                "12" => "배송날짜를 확인해주세요.",
                "20" => "이미 발주된 주문입니다."
            );

            if($result === "") {
                return response()->json(['state'=>0, 'msg'=>"인트라넷의 일시적인 오류입니다. \n 잠시 후 다시 시도해주세요." ]);
            }

            return response()->json(['state'=>0, 'msg'=>"인트라넷 문제로 발주 실패\n". ($error_msg[$result] ?? "에러코드 : " . $result) ]);
        }

    }
}
