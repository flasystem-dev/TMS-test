@extends('layouts.master-without-nav')
@section('content')
<link rel="stylesheet" href="{{ URL::asset('/assets/css/message/sms-form.css') }}">
<div class="container1">
    <div class="item1">
        <div class="card">
            <div class="card-body">
                <form id="sms_form">
                <input type="hidden" name="handler" value="{{Auth()->user()->name}}">
                <div>
                    <textarea class="contents" name="message" id="contents"></textarea>
                    <p class="text-end"><span id="content_byte">0</span><span> / 2,000 byte</span></p>
                </div>
                <div class="sender_container mb-3">
                    <span class="sender_title">발신번호</span>
                    <select class="form-select" name="sender" id="sender">
                        @foreach($senders as $sender)
                            <option value="{{$sender->id}}" data-sender="{{$sender->sender}}" data-ini="{{$sender->sender_ini}}">
                                <span class="sender">{{$sender->sender}}</span>
                                <span class="ini">{{$sender->sender_ini}}</span>
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sender_container mb-3">
                    <span class="sender_title">수신번호</span>
                    <input class="form-control" name="receive_phone" id="receive_phone" oninput="auto_hyphen(event)">
                </div>
                <input type="hidden" name="od_id" value="{{$od_id ?? 0}}">
                <input type="hidden" name="template" value="직접입력">
                </form>
                <div class="text-center">
                    <button class="btn btn-primary" onclick="send_SMS()">문자 전송</button>
                </div>
            </div>
        </div>
    </div>
    <div class="item2">
        <div class="card mb-2">
            <div class="card-body">
                <div>
                    <p class="mb-1">단문 : 90 byte</p>
                    <p class="m-0">장문 : 91 ~ 2000 byte</p>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div>
                    <span class="msg_title">자주쓰는 메세지</span>
                    <button class="btn btn-secondary msg_management" onclick="open_win('{{url('SMS/memo/manage')}}', 'memo', 600,600,800,50)">관리</button>
                    <select class="form-select mt-2" name="brand" id="memo_brand">
                        <option value="BTCP" @if(request()->brand==="BTCP") selected @endif>꽃파는총각</option>
                        <option value="BTCC" @if(request()->brand==="BTCC") selected @endif>칙칙폭폭플라워</option>
                        <option value="BTSP" @if(request()->brand==="BTSP") selected @endif>사팔플라워</option>
                        <option value="BTBR" @if(request()->brand==="BTBR") selected @endif>바로플라워</option>
                        <option value="BTOM" @if(request()->brand==="BTOM") selected @endif>오만플라워</option>
                        <option value="BTCS" @if(request()->brand==="BTCS") selected @endif>꽃파는사람들</option>
                        <option value="BTFC" @if(request()->brand==="BTFC") selected @endif>플라체인</option>
                    </select>
                </div>
                <div class="pt-2" id="msg_list">
                    @foreach($msg_list as $msg)
                        <div class="msg_container cursor_p" onclick="select_msg(event)"><pre onclick="select_msg(event)">{!! $msg -> msg !!}</pre></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{URL::asset('/assets/js/message/sms-form.js')}}"></script>
    @if(!empty($selected))
    <script>
        $('#sender').val('{{$selected}}').trigger('change');
        $('#receive_phone').val('{{$receiver}}');
    </script>
    @endif
@endsection