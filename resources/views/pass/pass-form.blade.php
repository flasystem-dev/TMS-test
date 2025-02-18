@extends('layouts.master-without-nav')
@section('title')
    Pass 상세정보
@endsection
@section('content')
@if(session('update'))
    <script>
        showToast('수정 완료');
    </script>
@endif
    <link href="{{ URL::asset('/assets/css/pass/pass-form.css') }}" rel="stylesheet">
    <!-- 폼 양식 시작 -->
    <form id="pass_form" method="post" action="{{url("pass/pass-form")}}" enctype="multipart/form-data">
        @csrf
        <!-- 페이지 내용 시작 -->
        <div class="layout-wrapper">
            <header id="page-topbar" style="left: 0">
                <div class="navbar-header">
                    <div class="d-flex text-xl-center">
                        <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center">
                            @empty($pass)
                                Pass 등록
                            @else
                                Pass 수정
                            @endempty
                        </p>
                    </div>
                </div>
            </header>
            <div class="row justify-content-center ms-1 mt-5">
                <!-- 주문 정보 시작-->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">기본정보</h4>
                                <input type="hidden" name="id" value="{{collect(explode('/', Request::url()))->last()}}">
                                <div class="pass_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text" >브랜드</span>
                                        <select class="form-select" name="brand" aria-label="">
                                            <option value="BTCP" selected>꽃파는총각</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">가입일</span>
                                        <input type="date" class="form-control datepicker" name="created_at" value="{{ !empty($pass) ? Carbon\Carbon::parse($pass -> created_at)->format('Y-m-d') : date('Y-m-d') }}" aria-label="email">
                                    </div>
                                </div>
                                <div class="pass_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">상태</span>
                                        <input type="radio" class="btn-check" id="is_valid_t" name="is_valid" value="1" {{ optional($pass) -> is_valid === 1 ? 'checked' : '' }}><label for="is_valid_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="is_valid_f" name="is_valid" value="0" {{ optional($pass) -> is_valid === 0 ? 'checked' : '' }}><label for="is_valid_f" class="form-control" >비활성</label>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">선발주</span>
                                        <input type="radio" class="btn-check" id="is_credit_t" name="is_credit" value="1" {{ optional($pass) -> is_credit === 1 ? 'checked' : '' }}><label for="is_credit_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="is_credit_f" name="is_credit" value="0" {{ optional($pass) -> is_credit === 0 ? 'checked' : '' }}><label for="is_credit_f" class="form-control" >비활성</label>
                                    </div>
                                </div>

                                <div class="pass_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text" >상점명</span>
                                        <input type="text" class="form-control" name="mall_name" value="{{ optional($pass) -> mall_name }}" aria-label="mall_name" autocomplete="off" {{ !empty($pass) ? "readonly" : "" }}>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text" >도메인</span>
                                        <input type="text" class="form-control" name="domain" id="domain" value="{{ optional($pass) -> domain }}">
                                        <button type="button" class="btn btn-secondary" onclick="check_dup_domain()">중복 확인</button>
                                    </div>
                                </div>

                                <div class="pass_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text" >관리자 ID</span>
                                        <input type="text" class="form-control" name="vendor_id" value="{{ optional($pass) -> vendor_id }}" aria-label="vendor_id" autocomplete="off" {{ !empty($pass) && !empty(optional($pass) -> vendor_id) ? "readonly" : "" }}>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text" >관리자 PW</span>
                                        <input type="text" class="form-control" name="vendor_pw" value="">
                                    </div>
                                </div>

                                <div class="pass_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">이름</span>
                                        <input type="text" class="form-control" name="name" value="{{ optional($pass) -> name }}" aria-label="name">
                                    </div>
                                </div>

                                <div class="pass_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">메모</span>
                                        <textarea class="form-control" name="memo">{{ optional($pass)->memo }}</textarea>
                                    </div>
                                </div>

                                <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
                <!-- 기본 정보 끝 -->


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">금액 정보</h4>

                                <div class="pass_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text form_required">금액 타입</span>
                                        <select class="form-select" name="price_type">
                                            @for($i=1; $i<=10; $i++)
                                            <option value="{{$i}}" {{ optional($pass) -> price_type == $i ? "selected" : "" }}>{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">할인율</span>
                                        <input type="number" class="form-control" name="discount" value="{{ optional($pass) -> discount }}" aria-label="discount">
                                        <span class="input-group-text end_text">%</span>
                                    </div>
                                </div>

                                <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div> <!-- end col -->
                </div><!-- 추가정보 끝 -->
            </div> <!-- 내용 끝 ( col ) -->
        </div><!-- 페이지 끝 ( row ) -->
    </form>
@endSection
@section('script')
    <script src="{{ URL::asset('/assets/js/pass/pass-form.js') }}"></script>
@endsection
