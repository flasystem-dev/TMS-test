<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class fileUploadController extends Controller
{
    //
    public static function fileUpload(Request $request) {

        // 사진업로드
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $image = $request -> file('file');
            // 임시 사진 이름
            $rand = rand(1,10000);
            $temp_file_name = time().$rand.".".$image -> extension();
            // 사진 파일 저장 ( 3번째 파라미터 내 폴더 설정 , 파일 이름 , public/assets/images/file 기본 경로 )
            $request -> file -> storeAs('basic', $temp_file_name ,'homepage');
            $file_url = asset("assets/images/homepage/basic/".$temp_file_name);
            return $file_url;
        }

        return "fail";
    }

}
