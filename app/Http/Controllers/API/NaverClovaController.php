<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NaverClovaController extends Controller
{
    public function naverClovar() {
        $id = '0p3gsoo1yo';
        $key = 'jCX87t381Tw6tKDAh3sIgXDwoJr6AA64K54zVIBF';

        $text = '특별/광역/경기:축하 3단 최고급형/25900원/1개, 1.주문자성함/핸드폰:김미경 010 8212 5744/1개, 2.받는분성함/핸드폰:인천광역시 연수구 컨벤시아대로 153  쉐라톤그랜드 /1개, 3.배송날짜/시간:10월 28일 토요일  오후 12시 /1개, 4.경조사어(우측리본):축 결혼/1개, 5.보내는분(좌측리본):부경전기통신주식회사 대표이사 박진환/1개';
        $text2 =  "1.주문자성함/핸드폰:김미경 010 8212 5744/1개,";

        $response = Http::withHeaders([
            'X-NCP-APIGW-API-KEY-ID' => $id,
            'X-NCP-APIGW-API-KEY' => $key,
            'Content-Type' => 'application/json'
        ]) -> post('https://naveropenapi.apigw.ntruss.com/text-summary/v1/summarize', [
            'document' => [
                'title' => '주문 정보',
                'content' => $text
            ],
            'option' => [
                'language' => 'ko',
                'model' => 'general',
                'summaryCount' => 6
            ]
        ]);
        $res = $response;
        print($res);
//        return view('testPage', ['res' => $res]);
    }
}
