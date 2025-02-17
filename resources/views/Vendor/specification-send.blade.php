@extends('layouts.master-without-nav')
@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/vendor/specification-send.css') }}" rel="stylesheet" type="text/css" />
<div class="row m-2">
    <div class="card m-0">
        <div class="card-body">
            @php
                $year = request()->year ?? now()->year;
                $month = request()->month ?? now()->month;
            @endphp
            <div class="top_menu_container">
                <div class="menu_1">
                    <form action="{{url('vendor/specification/send')}}">
                    <select class="form-select select_date" name="brand" aria-label="Default select example">
                        <option value="BTCS" {{request()->brand === "BTCS" ? "selected" : ""}}>꽃사</option>
                        <option value="BTFC" {{request()->brand === "BTFC" ? "selected" : ""}}>플체</option>
                    </select>
                    <select class="form-select select_date" name="year" aria-label="Default select example">
                        @for($i=date('Y')-2;$i<=date('Y');$i++)
                            <option value="{{$i}}" {{$year==$i? "selected" : ""}} >{{$i}}년</option>
                        @endfor
                    </select>
                    <select class="form-select select_date" name="month" aria-label="Default select example">
                        @for($i=1;$i<13;$i++)
                            <option value="{{$i}}" {{$month==$i? "selected" : ""}}>{{$i}}월</option>
                        @endfor
                    </select>
                    <select class="form-select select_date" name="search" aria-label="Default select example">>
                        <option value="all">전체</option>
                        <option value="mall_name">상호명</option>
                        <option value="rep_name">대표자명</option>
                        <option value="gen_number">대표번호</option>
                        <option value="did_number">DID</option>
                    </select>
                    <input class="form-control input_word" name="word" aria-label="word" value="{{request()->word ?? ""}}">
                    <button class="btn btn-secondary search_btn">조회</button>
                    </form>
                </div>
                <div class="menu_2">
                    <button type="button" class="btn btn-secondary search_btn me-2" onclick="send_email()">메일 전송</button>
                    <button type="button" class="btn btn-success search_btn me-2" onclick="talk_excelDownload()">알림톡전송 Excel</button>
                    <button type="button" class="btn btn-danger search_btn" onclick="delete_specification()">명세서 삭제</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mx-2">
    <div class="card">
        <div class="card-body">
            <table id="specification_table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">순서</th>
                        <th class="text-center">화원명</th>
                        <th class="text-center">대표자</th>
                        <th class="text-center">대표번호</th>
                        <th class="text-center">지급명세서</th>
                        <th class="text-center">
                            <input type="checkbox" class="form-check-input select_checkbox" id="select_email_all">
                            이메일
                        </th>
                        <th class="text-center">전송여부</th>
                        <th class="text-center">
                            <input type="checkbox" class="form-check-input select_checkbox" id="select_tel_all">
                            연락처
                        </th>
                        <th class="text-center">전송여부</th>
                        <th class="text-center">
                            <input type="checkbox" class="form-check-input select_checkbox" id="select_id_all">
                            삭제
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                    <tr>
                        <td>{{$loop->index + 1}}</td>
                        <td>{{$vendor->mall_name}}</td>
                        <td>{{$vendor->rep_name}}</td>
                        <td>{{$vendor->gen_number}}</td>
                        <td class="text-end">{{number_format($vendor->sp_settlement_amount)}}<i class="uil-file-info-alt icon_specification cursor_p" onclick="specification_popup({{$vendor->sp_id}})"></i></td>
                        <td>
                            @if(!empty($vendor->rep_email))
                            <input type="checkbox" class="form-check-input select_checkbox" name="email[]" value="{{$vendor->idx}}">
                            @endif
                            {{$vendor->rep_email}}
                        </td>
                        <td class="text-end">{{!empty($vendor->send_email)? $vendor->send_email. " 회" : ""}}</td>
                        <td>
                            @if(!empty($vendor->rep_tel))
                                <input type="checkbox" class="form-check-input select_checkbox" name="tel[]">
                            @endif
                            {{$vendor->rep_tel}}
                        </td>
                        <td class="text-end">{{!empty($vendor->send_talk)? $vendor->send_talk. " 회" : ""}}</td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input fs-5" name="sp_id[]" value="{{$vendor->sp_id}}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/vendor/specification-send.js') }}"></script>
@endsection