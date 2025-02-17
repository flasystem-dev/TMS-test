@extends('layouts.master-without-nav')
@section('title')
    회원 상세정보
@endsection
@section('content')
@if(session('update'))
    <script>
        showToast('수정 완료');
    </script>
@endif
<link href="{{ URL::asset('/assets/css/user/user-form.css') }}" rel="stylesheet">
    <!-- 폼 양식 시작 -->
    <form id="user_form" method="post" action="{{url("user/user-form")}}" enctype="multipart/form-data">
        @csrf
        <!-- 페이지 내용 시작 -->
        <div class="layout-wrapper">
            <header id="page-topbar" style="left: 0">
                <div class="navbar-header">
                    <div class="d-flex text-xl-center">
                        <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center">
                            @empty($user)
                            회원 등록
                            @else
                            회원 수정
                            @endempty
                        </p>
                    </div>
                </div>
            </header>
            <input type="hidden" name="id" value="{{optional($user) -> id}}">
            <div class="row justify-content-center ms-1 mt-5">
                <!-- 주문 정보 시작-->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">기본정보</h4>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text" >브랜드</span>
                                        <select class="form-select" name="brand" aria-label="">
                                            <option value="">- 브랜드 선택 -</option>
                                            @foreach($brands as $brand)
                                            <option value="{{ $brand -> brand_type_code }}" {{ optional($user) -> brand === $brand -> brand_type_code ? 'selected' : '' }}>{{ $brand -> shop_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text" >채널</span>
                                        <select class="form-select" name="brand" aria-label="">
                                            <option value="">- 브랜드 선택 -</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand -> brand_type_code }}" {{ optional($user) -> brand === $brand -> brand_type_code ? 'selected' : '' }}>{{ $brand -> shop_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="user_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">상태</span>
                                        <input type="radio" class="btn-check" id="status_t" name="status" value="1" {{ optional($user) -> status === 1 ? 'checked' : '' }}><label for="status_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="status_f" name="status" value="0" {{ optional($user) -> status === 0 ? 'checked' : '' }}><label for="status_f" class="form-control" >비활성</label>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">선발주</span>
                                        <input type="radio" class="btn-check" id="is_credit_t" name="is_credit" value="1" {{ optional($user) -> is_credit === 1 ? 'checked' : '' }}><label for="is_credit_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="is_credit_f" name="is_credit" value="0" {{ optional($user) -> is_credit === 0 ? 'checked' : '' }}><label for="is_credit_f" class="form-control" >비활성</label>
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text form_required" id="user_id_span">ID</span>
                                        <input type="text" class="form-control" name="user_id" id="user_id" value="{{ optional($user) -> user_id }}" aria-label="user_id" autocomplete="off" {{ !empty($user) ? "readonly" : "" }}>
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">PW</span>
                                        <input type="text" class="form-control" name="user_pw" value="" aria-label="user_pw">
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text form_required">이름</span>
                                        <input type="text" class="form-control" name="name" value="{{ optional($user) -> name }}" aria-label="name">
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">생년월일</span>
                                        <input type="date" class="form-control datepicker" name="birth" value="{{ optional($user) -> birth }}" aria-label="birth">
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text form_required">휴대전화</span>
                                        <input type="text" class="form-control" name="phone" value="{{ optional($user) -> phone }}" aria-label="phone" oninput="auto_hyphen(event)">
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">일반전화</span>
                                        <input type="text" class="form-control" name="tel" value="{{ optional($user) -> tel }}" aria-label="tel" oninput="auto_hyphen(event)">
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">이메일</span>
                                        <input type="text" class="form-control" name="email" value="{{ optional($user) -> email }}" aria-label="email">
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">가입일</span>
                                        <input type="date" class="form-control datepicker" name="created_at" value="{{ !empty($user) ? Carbon\Carbon::parse($user -> created_at)->format('Y-m-d') : date('Y-m-d') }}" aria-label="email">
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text not_used">문자 수신</span>
                                        <input type="radio" class="btn-check" id="send_sms_t" name="send_sms" value="1" disabled><label for="send_sms_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="send_sms_f" name="send_sms" value="0" disabled><label for="send_sms_f" class="form-control" >비활성</label>
                                    </div>
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text not_used">메일 수신</span>
                                        <input type="radio" class="btn-check" id="send_email_t" name="send_email" value="1" disabled><label for="send_email_t" class="form-control">활성</label>
                                        <input type="radio" class="btn-check" id="send_email_f" name="send_email" value="0" disabled><label for="send_email_f" class="form-control" >비활성</label>
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">회원 메모</span>
                                        <textarea class="form-control" name="memo">{{ optional($user)->memo }}</textarea>
                                    </div>
                                </div>
                                <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div> <!-- end col -->
                </div>
                <!-- 기본 정보 끝 -->


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title mb-4">증빙정보</h4>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group input_select">
                                        <span class="input-group-text">현금영수</span>
                                        <input type="radio" class="btn-check" id="document_type_PMCR" name="document_type" value="PMCR" {{ optional($user) -> document_type === 'PMCR' ? 'checked' : ''  }}>
                                        <label for="document_type_PMCR" class="form-control">소득공제</label>
                                        <input type="radio" class="btn-check" id="document_type_PMPE" name="document_type" value="PMPE" {{ optional($user) -> document_type === 'PMPE' ? 'checked' : '' }}>
                                        <label for="document_type_PMPE" class="form-control" >지출증빙</label>
                                    </div>
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">식별번호</span>
                                        <input type="text" class="form-control" name="document_number" value="{{ optional($user) -> document_number }}" aria-label="document_number">
                                    </div>
                                </div>

                                <div class="user_form_row">
                                    <div class="mb-3 input-group">
                                        <span class="input-group-text">거래처</span>
                                        <select id="client_list" name="client_id" data-client="{{$user->client_id ?? 0}}">
                                            <option value=""></option>
                                            @foreach($clients as $client)
                                                <option value="{{$client->id}}"
                                                        data-name="{{$client->name ?? ''}}"
                                                        data-ceo-name="{{$client->ceo_name ?? ''}}"
                                                        data-bs-number="{{$client->business_number ?? ''}}">
                                                    <span class="client_name">{{$client->name ?? ''}}</span>
                                                    <span class="client_ceoName">{{$client->ceo_name ?? ''}}</span>
                                                    <span class="client_bsNumber">{{$client->business_number ?? ''}}</span>
                                                </option>
                                            @endforeach
                                        </select>
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
<script src="{{ URL::asset('/assets/js/user/user-form.js') }}"></script>
@endsection
