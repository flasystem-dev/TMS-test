<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\AccountInfo;

class AccountInfoController extends Controller
{
    public static function check_admin_pw(Request $request) {
        return AccountInfo::check_admin_pw($request -> account_info);
    }
}
