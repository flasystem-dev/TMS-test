<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\AlimLog;
use App\Models\CommonCode;

class KaKaoTalkPageController extends Controller
{
    // 알림톡 페이지 열기
    public function Send_Talk_Page() {
        $data['templates'] = DB::table('popbill_template_info') ->groupBy('plusFriendID') -> get();

        return view('KakaoTalk.Manage-KakaoTalk', $data);
    }

    // 조회한 템플릿 정보 보내기
    public function GetTemplate(Request $request) {
        $data = [];

        $data['template_info'] = DB::table('popbill_template_info') -> where('plusFriendID', '=', $request -> plusFriendID) -> where('templateName', '=', $request -> templateName) -> first();
        $data['variables'] = json_decode($data['template_info'] -> variables, true);
        $data['values'] = json_decode($data['template_info'] -> values, true);

        return view('KakaoTalk.include.Set-Template', $data);
    }

    // 주로 사용하는 템플릿 정보 보내기
    public function GetUsedTemplate(Request $request) {
        $template_type = $request -> template_type;
        $templateCode = DB::table('popbill_template_numberInUse') -> where('brand_type_code', $request -> brand_type_code) -> first() -> $template_type;

        $data = [];


        $data['template_info'] = DB::table('popbill_template_info') -> where('templateCode', $templateCode) -> first();

        if(empty($data['template_info'])) {
            return response() -> json(['status' => 0, 'msg'=>"사용 중인 템플릿 정보가 없습니다."]);
        }

        $data['variables'] = json_decode($data['template_info'] -> variables, true);

        $data['values'] = json_decode($data['template_info'] -> values, true);

        return view('KakaoTalk.include.Set-Template', $data);
    }

    // 템플릿 변수 설정 값 입력하기
    public function SetValues(Request $request) {
        $data = $request -> all();

        $values = [];

        for($i=0; $i<count($data['variables']); $i++) {
            $value = [];
            $value['variable'] = $data['variables'][$i];
            $value['table'] = $data['table_name'][$i];
            $value['column'] = $data['column_name'][$i];
            $values[] = $value;
        }

        $json_values = json_encode($values, JSON_UNESCAPED_UNICODE);

        DB::table('popbill_template_info')
            -> where('templateCode', $data['templateCode'])
            -> update([
                'values' => $json_values
            ]);

        return true;
    }

    // 템플릿 변수 설정을 위한 컬럼 명 옵션 보내기
    public function getColumnName(Request $request) {
        $columns = DB::select("SELECT COLUMN_NAME, COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_COMMENT like '%-y%'", ['flasystemnet', $request -> table_name]);

        $options = [];

        foreach ($columns as $column) {
            $option = [];
            $option['name'] = $column->COLUMN_NAME;
            $option['comment'] = str_replace("-y", '',$column -> COLUMN_COMMENT );
            $options[] = $option;
        }

        $template_info = DB::table('popbill_template_info') -> where('templateCode', $request -> templateCode) -> first();
        $values = json_decode($template_info->values, true);

        $data['options'] = $options;
        $data['values'] = $values;
        return view('KakaoTalk.include.column-option', $data);
    }

    // 알림톡 로그 저장
    public static function SendATSLog(Request $request) {
        $sql = $request -> sql;
        DB::insert($sql);
    }

    // 알림 내역 보기
    public function AlimLog(Request $request)
    {
        $search_arr['sw_1'] = $request->query('sw_1');
        $search_arr['sw_1_view'] = $request->query('sw_1_view');
        $search_arr['word1'] = $request->query('word1');
        $search_arr['start_date'] = $request->query('start_date');
        $search_arr['end_date'] = $request->query('end_date');

        $alim_log = AlimLog::alimLog($search_arr);
        $commonDate =CommonCode::commonDate();
        $count = $alim_log->count();
        return view('KakaoTalk.alim-talk-log', ['alim_log' => $alim_log,'count'=>$count,'commonDate'=>$commonDate,'search_arr'=>$search_arr]);
    }

    public function roadAlimTalkTemplate(Request $request){
        $idx = $request-> idx;
        $template = AlimLog::roadAlimTalkTemplate($idx);
        echo $template;
    }

#######################################################################################################################

    // [플러스 친구 이름]으로 검색한 [템플릿 이름] 셀렉스 박스 안 옵션들 보내기 (HTML)
    public function GetTemplateNameList(Request $request) {
        $template_list = DB::table('popbill_template_info') -> where('plusFriendID', '=', $request -> plusFriendID) -> get();
        $options = '';
        foreach ($template_list as $template) {
            $name = $template -> templateName;
            $options .= '<option value="'.$name.'">'.$name.'</option>';
        }
        return response() -> json(['option'=>$options]);
    }
}
