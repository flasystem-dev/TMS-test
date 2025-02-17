<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

use App\Models\TMS_Notification;
use App\Models\TMS_User_Noti;

class NotificationController extends Controller
{
    public static function notice(Request $request) {



        return view('Board.Notifications.Notice');
    }


    public static function notification(Request $request) {

        $notifications = DB::table('tms_notifications') -> orderBy('created_at', 'desc') -> paginate(15);


        return view('Board.Notifications.Notification', ['notifications' => $notifications]);
    }

    public static function checkIt_notification($noti_id) {
        if($noti_id == 'all') {
            DB::table('tms_user_noti')
                -> where('user_id', Auth::user() -> user_id)
                -> update([
                    'is_read' => 'Y'
                ]);
            return redirect('/order/ecommerce_orders');
        }else {
            DB::table('tms_user_noti')
                -> where('noti_id', $noti_id)
                -> where('user_id', Auth::user() -> user_id)
                -> update([
                    'is_read' => 'Y'
                ]);
            return Redirect::route('notification');
        }


    }

    public static function check_notification(Request $request) {

        $notification = TMS_Notification::find($request -> noti_id);
        $notification -> is_checked = 'Y';
        $notification -> save();

        DB::table('noti_log') -> insert([
            'noti_id' => $request -> noti_id,
            'log_by_name' => $request -> name,
            'log_status' => '알림확인',
            'log_content' => '알림 상태 확인으로 변경'
        ]);

        return "성공";
    }
}
