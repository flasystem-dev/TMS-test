<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use App\Models\Order\OrderData;
use App\Models\Order\OrderDelivery;

class DeliveryController extends Controller
{
    public function delivery_photo($id) {

        try{
            $data = Crypt::decryptString($id);

            $idx = explode("/", $data);

            $delivery = OrderDelivery::find($idx[1]);

            return redirect() -> away($delivery->delivery_photo);
        }catch (\Exception $e){
            $order = OrderData::where('od_id',$id)->first();

            if(!$order){
                abort(404);
            }

            $delivery = OrderDelivery::find($order->order_idx);

            return redirect() -> away($delivery->delivery_photo);

//            return view('order.delivery-photo', ['img' => $order->delivery->delivery_photo]);
//            return redirect() -> away($order->delivery->delivery_photo);
        }

//        return view('order.delivery-photo', ['photo' => $order->delivery->delivery_photo]);
    }

    public function delivery_photo_url($id) {
        $order = OrderData::where('od_id', $id) -> first();

        $img = Http::get($order->delivery->delivery_photo);
        return response($img->body(), 200)
            ->header('Content-Type', $img->header('Content-Type'))
            ->header('Access-Control-Allow-Origin', '*');
    }
}
