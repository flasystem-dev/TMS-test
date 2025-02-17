<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\TMS_User;
use App\Models\TMS_User_Noti;

class TMS_Notification extends Model
{
    use HasFactory;

    protected $table = 'tms_notifications';

    protected $primaryKey = 'noti_id';
    protected $fillable = ['noti_id', 'type', 'title', 'content', 'created_at', 'updated_at'];

    public function user_noti() {
        return $this->hasMany(TMS_User_Noti::class, 'noti_id');
    }

    public static function create_noti(Request $request) {
        $title = $request -> title;
        $content = $request -> text;
        $type = $request -> type;

        $noti = New TMS_Notification();

        $noti -> type       = $type;
        $noti -> title      = $title;
        $noti -> content    = $content;

        $noti -> save();

        $noti_id = $noti -> noti_id;

        $users = TMS_User::all();

        foreach ($users as $user) {
            $user_noti = New TMS_User_Noti();
            $user_noti -> noti_id       = $noti_id;
            $user_noti -> user_id       = $user -> user_id;
            $user_noti -> is_read       = 'N';
            $user_noti -> save();
        }
    }
}