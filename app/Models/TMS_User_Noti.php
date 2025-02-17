<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TMS_User_Noti extends Model
{
    use HasFactory;

    protected $table = 'tms_user_noti';

    protected $primaryKey = 'noti_id';
    protected $fillable = ['noti_id', 'user_id', 'is_read', 'created_at', 'updated_at'];

    public function noti() {
        return $this -> belongsTo(TMS_Notification::class, 'noti_id');
    }

    public static function check_read($id) {
        $now = Carbon::now() ;
        $ago = Carbon::now() -> subHours(1);
        $notifications = TMS_User_Noti::whereBetween ('created_at',[$ago, $now])
            -> where('is_read', 'N')
            -> where('user_id', $id)
            -> get();

        return $notifications;
    }
}
