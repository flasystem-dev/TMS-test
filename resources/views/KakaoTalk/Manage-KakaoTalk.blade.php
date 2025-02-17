@extends('layouts.master')
@section('title')
    알림톡 보내기
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('/assets/css/message/talk.css') }}">
@endsection

@section('content')
@if(session('update'))
    <script>
        showToast('업데이트 완료')
    </script>
@endif
<div class="row justify-content-center mt-4" style="min-width: 800px;">
    <div class="col-11">
        <div class="card">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-8 p-2">
                        <input type="radio" class="btn-check" name="menu_radio" id="used_template" value="used_template" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="used_template">사용 중인 템플릿 설정</label>
                        <input type="radio" class="btn-check" name="menu_radio" id="manege_channel" value="manage_channel" autocomplete="off" >
                        <label class="btn btn-outline-warning ms-1" for="manege_channel">채널 관리</label>
                        <input type="radio" class="btn-check" name="menu_radio" id="manege_template" value="manage_template" autocomplete="off" >
                        <label class="btn btn-outline-info ms-1" for="manege_template">템플릿 관리</label>
                        <input type="radio" class="btn-check" name="menu_radio" id="all_template" value="all_template" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="all_template">모든 템플릿</label>
                    </div>
                    @if(Auth::user() -> auth > 9)
                    <div class="col-4 p-2 text-end">
                        <a href="{{ url('/KakaoTalk/ListATSTemplate') }}" class="btn btn-outline-success">템플릿 정보 업데이트</a>
                    </div>
                    @endif
                    <hr>
                </div>
                <div class="row" id="sub_menu">
                    <div class="col-12">
                        @include('KakaoTalk.include.Search-Kakao')
                    </div>
                </div>
            </div>
        </div>
        <div id="template_area">
            @include('KakaoTalk.include.Set-Template')
        </div>
    </div>
</div>
<script src="/assets/js/message/kakaoTalkPage.js"></script>
@endsection