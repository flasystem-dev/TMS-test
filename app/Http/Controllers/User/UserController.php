<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Client;

class UserController extends Controller
{
    public static function index(Request $request) {
        $search = $request -> all();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();
        $data['users'] = User::user_list($search);

        return view('user.user-list', $data);
    }

    public static function userForm($id) {
        $data = array();

        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "shop_name")->where('is_used', 1) -> get();
        $data['clients'] = Client::where('is_valid', 1) -> get();
        $data['user'] = User::find($id);

        return view('user.user-form', $data);
    }


########################################################################################################################
######################################################## 수정 ###########################################################

    ####################################### 회원 정보 수정 ###############################################################
    public static function userSaveOrUpdate(Request $request){
        $input = $request->all();
        $pre_client = "";

        if (!empty($user['user_pw'])) {
            $input['password'] = bcrypt($input['user_pw']);
        }

        if(DB::table('users')->where('id', $input['id'])->exists()) {
            $user = User::find($input['id']);
            $pre_client = $user->client_id;
        }else {
            $user = new User();
            $input['id'] = User::max('id') + 1;
        }

        $user -> fill($input);
        write_table_log($user);
        $user -> save();

        Client::set_brand($user -> client_id);
        if(!empty($pre_client)) {
            Client::set_brand($pre_client);
        }

        Session::flash('update', 1);
        return response() -> json(true);
    }
    
    ############################################## 회원 리스트 간단 정보 변경 ##############################################
    public function simple_update_user_data(Request $request) {
        $column = $request -> column;
        $id = $request -> id;
        $check = $request -> check;

        $user = User::find($id);
        $user -> $column = $check;
        write_table_log($user);
        $user -> save();

        session()->flash('update', 1);
        return response() -> json(true);
    }
########################################################################################################################
###################################################### 정보 전달 #########################################################

}
