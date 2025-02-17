<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\CommonCode;
use App\Models\SMSLog;
use App\Models\Order\OrderData;

class SMSPageController extends Controller
{
    public function index(Request $request) {
        $search = $request->all();
        $data['list'] = SMSLog::sms_list($search);
        $data['commonDate'] =CommonCode::commonDate();
        return view('sms.index', $data);
    }

    public function sms_form(Request $request) {
        $brands = ['BTCP', 'BTCC', 'BTSP', 'BTBR', 'BTOM', 'BTCS', 'BTFC'];
        $query = DB::table('popbill_sms_sender');

        $used_brand = [];
        foreach ($brands as $brand) {
            if(session($brand)==='Y') {
                $used_brand[] = $brand;
            }
        }
        $query -> whereIn('brand_type_code', $used_brand);

        $query->orderBy('orderBy', 'asc')->orderBy('id', 'asc');

        $selectBrand = $request -> brand ?? "BTCP";
        $data['senders'] = $query -> get();
        $data['msg_list'] = DB::table('sms_note_save')->where('brand', $selectBrand)->get();

        if($request->order) {
            $order = OrderData::find($request->order);
            $data['selected'] = DB::table('popbill_sms_sender')->where('brand_type_code', $order->brand_type_code)->where('is_default', 1)->value('id') ?? "";
            $data['receiver'] = $order -> orderer_phone;
            $data['od_id'] = $order -> od_id;
        }

        return view('sms.sms-form', $data);
    }

    public function sms_form_memoList(Request $request) {
        $brand = $request -> brand ?? "BTCP";
        $data['msg_list'] = DB::table('sms_note_save')->where('brand', $brand)->get();

        return view('sms.include.sms-form-memoList', $data);
    }

    public function sms_message(Request $request) {
        return SMSLog::find($request->id)-> contents;
    }

    public function sms_memo(Request $request) {
        $brand = $request->brand ?? "BTCP";

        $data['memo_list'] = DB::table('sms_note_save')->where('brand', $brand)->get();

        return view('sms.popup.sms-memo', $data);
    }

    public function get_sms_note(Request $request) {
        $id = $request -> id;
        $note = DB::table('sms_note_save')->where('id', $id)->first();

        return response()->json(['brand'=>$note->brand, 'msg'=>$note->msg]);
    }

    public function delete_sms_note(Request $request) {
        $id = $request -> id;
        DB::table('sms_note_save')->where('id', $id)->delete();

        return response()->json(true);
    }

    public function upsert_sms_note(Request $request) {
        $id = $request->modal_id;
        $brand = $request->modal_brand;
        $msg = $request->modal_msg;

        if($id==='0') {
            DB::table('sms_note_save')->insert(['brand' => $brand, 'msg' => $msg]);
        }else {
            DB::table('sms_note_save')->where('id', $id)->update(['brand' => $brand, 'msg' => $msg]);
        }
        return response()->json(true);
    }
}
