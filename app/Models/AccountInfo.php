<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountInfo extends Model
{
    use HasFactory;

    protected $table = 'account_info';

    protected $primaryKey = 'idx';

    protected $fillable=[
        'brand_code', 'channel_code', 'account_id', 'account_pw', 'admin_memo', 'created_at', 'updated_at'
    ];

    public static function check_admin_pw($pw) {
        $account = AccountInfo::find(1);

        if($pw == $account -> account_pw) {
            return true;
        }else {
            return false;
        }
    }
}
