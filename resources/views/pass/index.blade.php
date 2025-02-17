@extends('layouts.master')
@section('title')
    PASS사업자
@endsection

@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/pass/index.css') }}" rel="stylesheet" type="text/css" />
@if(session('update'))
    <script>
        showToast('수정 완료');
    </script>
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
                                <input type="radio" class="btn-check" name="brand" value="BTCP" id="select_brand_BTCP" autocomplete="off" checked>
                                <label class="btn select_brand select_brand_BTCP" for="select_brand_BTCP">꽃총</label>
                            </div>
                        </div>
                        <div class="menu2">

                        </div>
                        <div class="menu3">
                            <div class="input-group">
                                <select class="form-select" name="search">
                                    <option value="all"       {{ request()->search==="all"       ? "selected":""}}>전체</option>
                                    <option value="mall_name" {{ request()->search==="mall_name" ? "selected":""}}>상점명</option>
                                    <option value="name"      {{ request()->search==="name"      ? "selected":""}}>이름</option>
                                    <option value="domain"    {{ request()->search==="domain"    ? "selected":""}}>도메인</option>
                                    <option value="memo"      {{ request()->search==="memo"      ? "selected":""}}>메모</option>

                                </select>
                                <input type="text" class="form-control" name="search_word" value="{{request()->search_word}}">
                            </div>
                        </div>
                        <div class="menu4">
                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                            <button style="border-radius:3px;" type="button" class="btn btn-secondary waves-effect waves-light" id="passForm">+ Pass추가</button>
                        </div>
                    </div>
                    <div class="search_area_menu2">
                        <div class="menu1">

                        </div>
                        <div class="menu2">
                            <div class="form-check form-switch me-4">
                                <input type="checkbox" class="form-check-input" name="all_status" value="1" @checked(request()->all_status) id="all_status">
                                <label class="form-check-label" for="all_status">비활성 Pass 포함</label>
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
                <table id="passes_tbl" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="width: 50px;">번호</th>
                        <th style="width: 60px;">브랜드</th>
                        <th>상점명</th>
                        <th>이름</th>
                        <th>도메인</th>
                        <th style="width: 80px;">등록일</th>
                        <th>메모</th>
                        <th style="width: 70px;">상태</th>
                        <th style="width: 70px;">선발주<br>가능 여부</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($passes)
                        @foreach($passes as $pass)
                            <tr class="text-center align-middle px-0">
                                <td></td>
                                <!-- 브랜드 -->
                                <td>
                                    <p class="brand_type {{$pass->brand}}">{{ BrandAbbr($pass->brand) }}</p>
                                </td>
                                <!-- 상점명 -->
                                <td>
                                    <p class="tbl_name">{{ $pass -> mall_name }}</p>
                                </td>
                                <!-- 이름 -->
                                <td>
                                    <p class="fw-bold cursor_p tbl_name" onclick="passForm('{{$pass->id}}')">{{ $pass -> name }}</p>
                                </td>
                                <!-- 도메인 -->
                                <td>
                                    <p>{{ $pass -> domain }}</p>
                                </td>
                                <!-- 등록일 -->
                                <td>
                                    {{ Carbon\Carbon::parse($pass -> created_at)->format('Y-m-d') }}
                                </td>
                                <!-- 메모 -->
                                <td>
                                    <p class="tbl_memo">{{$pass -> memo}}</p>
                                </td>
                                <!-- 상태 -->
                                <td>
                                    <input class="checkbox_toggle" type="checkbox" id="pass_valid{{$loop->index}}" value="1" data-index="{{$pass->id}}" name="is_valid" {{$pass -> is_valid === 1 ? "checked" : ""}}>
                                    <label class="checkbox_toggle_label" for="pass_valid{{$loop->index}}"></label>
                                </td>
                                <!-- 선발주 -->
                                <td>
                                    <input class="checkbox_toggle" type="checkbox" id="pass_is_credit{{$loop->index}}" value="1" data-index="{{$pass->id}}" name="is_credit" {{$pass->is_credit === 1 ? "checked" : ""}}>
                                    <label class="checkbox_toggle_label" for="pass_is_credit{{$loop->index}}"></label>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{asset('assets/js/pass/index.js')}}"></script>
@endsection