@extends('layouts.master')
@section('title')
    회원목록
@endsection
@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/user/user-list.css') }}" rel="stylesheet" type="text/css" />
@if(session('update'))
    <script>showToast('수정 완료');</script>
@endif
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="get" id="search_form">
                    <div class="search_area_menu1 mb-3">
                        <div class="menu1">
                            <div class="input-group brand_btns">
                                <span class="input-group-text">브랜드</span>
                                @foreach($brands as $brand)
                                    <input type="radio" class="btn-check" name="brand" value="{{$brand->brand_type_code}}" id="select_brand_{{$brand->brand_type_code}}" autocomplete="off" {{request()->input('brand')===$brand->brand_type_code ? "checked" : ""}}>
                                    <label class="btn select_brand select_brand_{{$brand->brand_type_code}}" for="select_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="menu2">
                            <div class="input-group brand_btns">
                                <span class="input-group-text">회원종류</span>
                                <input type="radio" class="btn-check" name="user_type" value="all" id="user_type_all" autocomplete="off" checked>
                                <label class="btn select_brand btn-outline-secondary" for="user_type_all">전체</label>
                                <input type="radio" class="btn-check" name="user_type" value="normal" id="user_type_normal" autocomplete="off" >
                                <label class="btn select_brand btn-outline-secondary" for="user_type_normal">일반</label>
                                <input type="radio" class="btn-check" name="user_type" value="customer" id="user_type_customer" autocomplete="off" >
                                <label class="btn select_brand btn-outline-secondary" for="user_type_customer">거래처</label>
                                <input type="radio" class="btn-check" name="user_type" value="vendor" id="user_type_vendor" autocomplete="off" >
                                <label class="btn select_brand btn-outline-secondary" for="user_type_vendor">사업자</label>
                                <input type="radio" class="btn-check" name="user_type" value="pass" id="user_type_pass" autocomplete="off" >
                                <label class="btn select_brand btn-outline-secondary" for="user_type_pass">패스</label>
                            </div>
                        </div>
                        <div class="menu3">
                            <div class="input-group">
                                <select class="form-select" name="search">
                                    <option value="all"    {{ request()->search==="all"   ? "selected":""}}>전체</option>
                                    <option value="name"   {{ request()->search==="name"  ? "selected":""}}>이름</option>
                                    <option value="phone"  {{ request()->search==="phone" ? "selected":""}}>휴대전화</option>
                                    <option value="tel"    {{ request()->search==="tel"   ? "selected":""}}>전화번호</option>
                                    <option value="memo"   {{ request()->search==="memo"  ? "selected":""}}>메모</option>

                                </select>
                                <input type="text" class="form-control" name="search_word" value="{{request()->search_word}}">
                            </div>
                        </div>
                        <div class="menu4">
                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                            <button style="border-radius:3px;" type="button" class="btn btn-secondary waves-effect waves-light" id="memberForm">+ 회원추가</button>
                        </div>
                    </div>
                    <div class="search_area_menu2">
                        <div class="menu1">

                        </div>
                        <div class="menu2">
                            <div class="form-check form-switch me-4">
                                <input type="checkbox" class="form-check-input" name="all_status" value="1" @checked(request()->all_status) id="all_status">
                                <label class="form-check-label" for="all_status">비활성 회원 포함</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="user_list_tbl" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>브랜드<br>채널</th>
                        <th>이름(회원등급)<br>아이디</th>
                        <th>휴대전화<br>일반전화</th>
                        <th>이메일</th>
{{--                        <th>보유 포인트<br>보유 쿠폰 수</th>--}}
                        <th>등록일</th>
                        <th>회원메모</th>
                        <th style="width: 70px;">상태</th>
                        <th style="width: 70px;">선발주<br>가능 여부</th>
                    </tr>
                    </thead>
                    <tbody id="user_tbl_tbody">
                    @if($users)
                    @foreach($users as $user)
                        <tr class="text-center align-middle px-0">
                            <td></td>
                            <!-- 브랜드 -->
                            <td>
                                <p class="brand_type {{$user->brand}}">{{ BrandAbbr($user->brand) }}</p>
                                <p class="brand_type mt-1">{{$user->channel_name ?? CommonCodeName($user->vendor_idx)}}</p>
                            </td>
                            <!-- 이름 / 아이디 -->
                            <td>
                                <p class="fw-bold cursor_p" onclick="userUpdateForm({{$user->id}});">{{ $user -> name }}</p>
                                <p>{{ $user -> user_id }}</p>
                            </td>
                            <!-- 휴대전화 / 일반전화 -->
                            <td>
                                <p>{{ $user -> phone }}</p>
                                <p>{{ $user -> tel }}</p>
                            </td>
                            <!-- 이메일 -->
                            <td>
                                {{ $user -> email }}
                            </td>
                            <!-- 등록일 -->
                            <td>
                                {{ Carbon\Carbon::parse($user -> created_at)->format('Y-m-d') }}
                            </td>
                            <!-- 회원메모 -->
                            <td>
                                <p>{{$user -> memo}}</p>
                            </td>
                            <!-- 상태 -->
                            <td>
                                <input class="checkbox_toggle" type="checkbox" id="user_status{{$loop->index}}" data-index="{{$user->id}}" name="status" {{$user -> status === 1 ? "checked" : ""}}>
                                <label class="checkbox_toggle_label" for="user_status{{$loop->index}}"></label>
                            </td>
                            <!-- 선발주 -->
                            <td>
                                <input class="checkbox_toggle" type="checkbox" id="user_is_credit{{$loop->index}}" data-index="{{$user->id}}" name="is_credit" {{$user->is_credit === 1 ? "checked" : ""}}>
                                <label class="checkbox_toggle_label" for="user_is_credit{{$loop->index}}"></label>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end col -->
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/user/user-list.js') }}"></script>
@endsection




