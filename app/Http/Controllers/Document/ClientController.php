<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Client;
use App\Models\ClientManager;
use App\Models\User;

class ClientController extends Controller
{
    // 거래처 목록
    public function index(Request $request) {
        $search = $request -> except('page');

        $clients = Client::index_clientList($search);
        $data['brands'] = DB::table('code_of_company_info') -> select('brand_type_code', 'brand_ini') -> where('is_used', 1) -> get();
        $data['clients'] = $clients;
        return view('Document.client.index', $data);
    }

    // 거래처 폼
    public function clientForm($id) {
        $data['brands'] = DB::table('code_of_company_info')->select('brand_type_code', "brand_ini")->where('is_used', 1) -> get();
        $data['client'] = Client::find($id);

        return view('Document.client.client-form', $data);
    }

########################################################################################################################
#####################################################  정보 전달  ########################################################
    
    // 담당자 폼 HTML 전달
    public function managerForm($id) {
        $data['manager'] = ClientManager::find($id);

        return view('Document.include.manager-form', $data);
    }

    // 회원 옵션 HTML 전달
    public function userOption($brand) {
        $data['users'] = User::where('brand', $brand)->where('status', 1) -> get();

        return view('Document.include.select-user', $data);
    }

########################################################################################################################
#####################################################  등록, 수정  #######################################################

    ###########################################  거래처 - 등록 or 수정  ###################################################
    public function clientUpsert(Request $request) {
        $input = $request -> all();
        $id = $input['id'];
        if(DB::table('clients')->where('id', $id)->exists()) {
            $client = Client::find($id);
        }else {
            $client = new Client();
            $id = Client::max('id') + 1;
            $input['id'] = $id;
        }

        // 계약서
        if($request->hasFile('contract_file')){
            $file = $request->file('contract_file');
            $path_name = 'clients/contract/';
            $fileName = 'contract_' . time() . '.' . $file -> extension();
            $path = $path_name . $fileName;

            $result = $request -> contract_file -> storeAs('tms', $path);

            if($result) {
                $contract_url = asset('tms/'.$path);
                $client['contract'] = $contract_url;
            }
        }

        $client -> fill($input);
        write_table_log($client);
        $client -> save();

        session() -> flash("update", 1);
        return response() -> json(['status'=>true, 'url'=>url("/document/client/client-form/{$id}")]);
    }

    ###########################################  담당자 - 등록 or 수정  ###################################################
    public function managerUpsert(Request $request) {
        $input = $request -> all();
        $id = $input['id'];

        if(DB::table('clients_managers')->where('id', $id)->exists()) {
            $manager = ClientManager::find($id);
        }else {
            $manager = new ClientManager();
            $id = ClientManager::max('id') + 1;
            $input['id'] = $id;
        }

        $manager -> fill($input);
        write_table_log($manager);
        $manager -> save();

        if($input['is_default']==="1") {
            ClientManager::set_default($id);
        }

        session() -> flash("update", 1);
        return response() -> json(true);
    }

    ################################################  회원 - 등록  ######################################################
    public function userRegister(Request $request) {
        $input = $request -> all();

        $user = User::find($input['id']);
        $user -> client_id = $input['client'];
        $user -> manager_id = DB::table('clients_managers')->where('id', $input['client'])->where('is_default', 1)->value('id');
        write_table_log($user);
        $user -> save();

        Client::set_brand($input['client']);

        session() -> flash("update", 1);
        return response() -> json(true);
    }


    ################################################  회원 - 담당자 변경  #################################################
    public function user_manager(Request $request) {

        $user = User::find($request->user);
        $user ->manager_id = $request->manager;
        write_table_log($user);
        $user -> save();
        
        session() -> flash("update", 1);
        return response() -> json(true);
    }

########################################################################################################################
#####################################################  삭제  ############################################################

    ###########################################  담당자 - 삭제  #######################################################
    public function deleteManager($id) {
        DB::table('clients_managers')->where('id', $id)->delete();

        session() -> flash("update", 1);
        return response() -> json(true);
    }
}
