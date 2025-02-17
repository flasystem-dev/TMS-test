<?php

namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Board;
use App\Models\BoardValue;
use App\Models\CodeOfCompanyInfo;

class BoardController extends Controller
{
    //
    public static function boardList($boardType,Request $request) {

        $search = $request -> all();
        $data['board'] = Board::selectBoard($boardType);
        $data['list'] = Board::searchBoardList($search,$boardType);

        $data['company'] = CodeOfCompanyInfo::all();
        return view('Board.board-list',$data);

    }

    public static function boardForm($boardType,$id){
        $data['board'] = Board::boardView($boardType, $id);
        $data['type'] = Board::selectBoard($boardType);
        $data['company'] = CodeOfCompanyInfo::all();
        return view('Board.board-register', $data);
    }

    public static function downloadFile(Request $request){
        $headers = [
            'Content-Type' => 'application/octet-stream',
        ];
        return response()->download($request->file_path,$request->file_name,$headers);
    }

    public static function boardSave(Request $request){
        $input = $request -> all();

        $board = Board::boardModel($request->type, $request->id);

        $board -> fill($input);

        // 첨부파일1
        if ($request->hasFile('file1') && $request->file('file1')->isValid()) {
            $image = $request -> file('file1');
            $board['file1_name']  = $request->file('file1')->getClientOriginalName();
            $rand = rand(1,10000);
            $temp_file_name = time()."_".$rand.".".$image -> extension();
            // 사진 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/file 기본 경로 )
            $request -> file1 -> storeAs('basic', $temp_file_name ,'homepage');
            $board['file1_path'] = "assets/images/homepage/basic/".$temp_file_name;
            $board['file1'] = asset("assets/images/homepage/basic/".$temp_file_name);

        }

        // 첨부파일2
        if ($request->hasFile('file2') && $request->file('file2')->isValid()) {
            $image = $request -> file('file2');
            $board['file2_name']= $request->file('file2')->getClientOriginalName();
            $rand = rand(1,10000);
            $temp_file_name = time().$rand.".".$image -> extension();
            // 사진 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/file 기본 경로 )
            $request -> file2 -> storeAs('basic', $temp_file_name ,'homepage');
            $board['file2_path'] = "assets/images/homepage/basic/".$temp_file_name;
            $board['file2'] = asset("assets/images/homepage/basic/".$temp_file_name);
        }

        $board['writer'] = "관리자";

        $board['user_id'] = Auth::user()->userid;
        $board['name'] = Auth::user()->name;

        $board->save();

        return "success";
    }

    public static function boardDelete($id){
        $board = BoardValue::find($id);
        $board -> delete();
        return "success";
    }
}
