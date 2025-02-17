@extends('layouts.master-without-nav')
@section('title')
    사업자 등록 및 수정
@endsection
@section('css')
    <link href="{{ URL::asset('/assets/css/flaChain.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('/assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ URL::asset('/assets/js/dropzone/dropzone.js') }}"></script>
@endsection
@section('content')
@if(session('alert'))
    <script>
        showToast('수정 완료');
    </script>
@endif
<!-- 폼 양식 시작 -->
<form name="vendor_form" method="post" action="{{ url('vendor/fla-business') }}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="idx" id="vendor_idx" value="{{$idx}}">
<!-- 페이지 내용 시작 -->
<div class="layout-wrapper">
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex text-xl-center">
                <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center"> 사업자등록
                </p>
            </div>
            <div class="col-10">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="brand_type" id="service_BTCS" value="BTCS" @if(isset($vendor_info['brand_type']) && $vendor_info['brand_type']=='BTCS') checked @elseif(!isset($vendor_info['brand_type'])) checked @endif>
                    <label class="btn btn-outline-primary" for="service_BTCS">꽃파는사람들</label>

                    <input type="radio" class="btn-check" name="brand_type" id="service_BTFCB" value="BTFCB" @if(isset($vendor_info['brand_type']) && $vendor_info['brand_type']=='BTFCB') checked @endif>
                    <label class="btn btn-outline-primary" for="service_BTFCB">플라체인 B2B</label>

                    <input type="radio" class="btn-check" name="brand_type" id="service_BTFCC" value="BTFCC"  @if(isset($vendor_info['brand_type']) && $vendor_info['brand_type']=='BTFCC') checked @endif>
                    <label class="btn btn-outline-primary" for="service_BTFCC">플라체인 B2C</label>
                </div>
            </div>
        </div>
    </header>
<div class="row justify-content-center ms-1 mt-5">
        <!-- 주문 정보 시작-->
        <div class="row mt-5">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">서비스이용정보</h4>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text" >상태</span>
                            <input type="radio" id="is_valid_y" name="is_valid" value="Y" {{ optional($vendor_info)->is_valid==="Y"? "checked" : "" }}><label for="is_valid_y"  class="form-control">가입</label>
                            <input type="radio" id="is_valid_n" name="is_valid" value="N" {{ optional($vendor_info)->is_valid==="N"? "checked" : "" }}><label for="is_valid_n" class="form-control">탈퇴</label>
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">서비스</span>
                            <input type="radio" class="btn-check" id="service_basic" name="service_type" value="basic" {{ optional($vendor_info)->service_type ==="basic" ? 'checked': "" }}><label for="service_basic" class="form-control">베이직</label>
                            <input type="radio" class="btn-check" id="service_pro" name="service_type" value="pro"     {{ optional($vendor_info)->service_type ==="pro" ? 'checked': "" }}><label for="service_pro" class="form-control" >프로</label>
                            <input type="radio" class="btn-check" id="service_etc" name="service_type" value="etc"     {{ optional($vendor_info)->service_type ==="etc" ? 'checked': "" }}><label for="service_etc" class="form-control" >기타</label>
                        </div>

                        <div class="mb-3 input-group">
                            <span class="input-group-text">월 수수료</span>
                            <input type="number" class="form-control" name="service_percent" value="@isset($vendor_info['service_percent']){{$vendor_info['service_percent']}}@endisset" aria-label="rep_name">
                            <button type="button" class="input-group-text rounded-end" data-bs-toggle="modal" >%</button>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">ID</span>
                            @php
                                $param = explode('/', $_SERVER['REQUEST_URI'])[4]
                            @endphp
                            <input type="text" class="form-control" name="vendor_id" id="vendor_id" value="@isset($vendor_info['vendor_id']){{$vendor_info['vendor_id']}}@endisset" aria-label="vendor_id" @if($param != 0 && !empty($vendor_info['vendor_id'])) readonly @endif>
                            <button type="button" class="input-group-text rounded-end" onclick="check_id();" data-bs-toggle="modal" >&nbsp;&nbsp;중복확인</button>
                            <input type="hidden" value="N" id="id_checked">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">PW</span>
                            <input type="text" class="form-control" name="vendor_pw" value="" aria-label="vendor_pw">
{{--                            <i class="uil-eye-slash"></i>--}}
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">상호명</span>
                            <input type="text" class="form-control" name="mall_name" value="@isset($vendor_info['mall_name']){{$vendor_info['mall_name']}}@endisset" aria-label="mall_name">
                        </div>

                        <div class="mb-3 input-group">
                            <button type="button" class="input-group-text" data-bs-toggle="modal" data-bs-target="#gen_did_modal">대표번호/DID</button>
                            <input type="text" class="form-control" name="gen_number" value="@isset($vendor_info['gen_number']){{$vendor_info['gen_number']}}@endisset" aria-label="gen_number">
                            <input type="text" class="form-control" name="did_number" id="input_did_num" value="{{optional($vendor_info)->did_number}}" placeholder="번호 선택" aria-label="did_number" data-bs-toggle="modal" data-bs-target="#gen_did_modal">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">도메인</span>
                            <input type="text" class="form-control" name="domain" id="domain" value="@isset($vendor_info['domain']){{$vendor_info['domain']}}@endisset" aria-label="domain">
                            <button type="button" class="input-group-text rounded-end" onclick="check_domain();" data-bs-toggle="modal" >&nbsp;&nbsp;중복확인</button>
                            <input type="hidden" value="N" id="domain_checked">
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">사업자 타입</span>
                            <input type="radio" class="btn-check" id="price_type1" name="price_type" value="1" {{optional($vendor_info)->price_type===1? "checked": ""}} checked><label for="price_type1" class="form-control">기본</label>
                            <input type="radio" class="btn-check" id="price_type2" name="price_type" value="2" {{optional($vendor_info)->price_type===2? "checked": ""}}><label for="price_type2" class="form-control" >타입2</label>
                            <input type="radio" class="btn-check" id="price_type3" name="price_type" value="3" {{optional($vendor_info)->price_type===3? "checked": ""}}><label for="price_type3" class="form-control" >타입3</label>
                            <input type="radio" class="btn-check" id="price_type4" name="price_type" value="4" {{optional($vendor_info)->price_type===4? "checked": ""}}><label for="price_type4" class="form-control" >타입4</label>
                            <input type="radio" class="btn-check" id="price_type5" name="price_type" value="5" {{optional($vendor_info)->price_type===5? "checked": ""}}><label for="price_type5" class="form-control" >타입5</label>
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text" >미수거래</span>
                            <input type="radio" id="is_credit1" name="is_credit" value="1" {{optional($vendor_info)->is_credit===1? "checked": ""}}><label for="is_credit1" class="form-control">가능</label>
                            <input type="radio" id="is_credit0" name="is_credit" value="0" {{optional($vendor_info)->is_credit===0? "checked": ""}}><label for="is_credit0"  class="form-control">불가능</label>
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text" >가입비결제</span>
                            <input type="radio" id="membership_complete" name="membership_pay_done" value="CP" {{optional($vendor_info)->membership_pay_done==="CP"? "checked": ""}}><label for="membership_complete" class="form-control">결제완료</label>
                            <input type="radio" id="membership_pass" name="membership_pay_done" value="PS"     {{optional($vendor_info)->membership_pay_done==="PS"? "checked": ""}}><label for="membership_pass"  class="form-control">패스</label>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">가입비</span>
                            <input type="number" class="form-control" name="membership" value="@isset($vendor_info['membership']){{$vendor_info['membership']}}@endisset" aria-label="rep_name">
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">결제수단</span>
                            <input type="radio" id="membership_pay_type_PTDB" name="membership_pay_type" value="PTDB" {{optional($vendor_info)->membership_pay_type==="PTDB"? "checked": ""}}><label for="membership_pay_type_PTDB" class="form-control">현금</label>
                            <input type="radio" id="membership_pay_type_PTCD" name="membership_pay_type" value="PTCD" {{optional($vendor_info)->membership_pay_type==="PTCD"? "checked": ""}}><label for="membership_pay_type_PTCD"  class="form-control">카드</label>
                            <input type="radio" id="membership_pay_type_none" name="membership_pay_type" value="" {{optional($vendor_info)->membership_pay_type===""? "checked": ""}}><label for="membership_pay_type_none"  class="form-control">없음</label>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">입금자명</span>
                            <input type="text" class="form-control" name="membership_pay_name" value="@isset($vendor_info['membership_pay_name']){{$vendor_info['membership_pay_name']}}@endisset" aria-label="membership_pay_name">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">결제일</span>
                            <input type="text" class="form-control datepicker" name="membership_pay_date" placeholder="0000-00-00" value="@isset($vendor_info['membership_pay_date']){{$vendor_info['membership_pay_date']}}@endisset" autocomplete="off" aria-label="membership_pay_date">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">월 이용금액</span>
                            <input type="number" class="form-control" name="service_price" value="@isset($vendor_info['service_price']){{$vendor_info['service_price']}}@endisset" aria-label="rep_name">
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">보증종류</span>
                            <input type="radio" id="assurance_ARNR" name="assurance" onchange="change_assurance('ARNR');" value="ARNR" {{optional($vendor_info)->assurance==="ARNR"? "checked": ""}}><label for="assurance_ARNR" class="form-control">없음</label>
                            <input type="radio" id="assurance_ARPS" name="assurance" onchange="change_assurance('ARPS');" value="ARPS" {{optional($vendor_info)->assurance==="ARPS"? "checked": ""}}><label for="assurance_ARPS"  class="form-control">패스</label>
                            <input type="radio" id="assurance_ARIR" name="assurance" onchange="change_assurance('ARIR');" value="ARIR" {{optional($vendor_info)->assurance==="ARIR"? "checked": ""}}><label for="assurance_ARIR" class="form-control">보증보험</label>
                            <input type="radio" id="assurance_ARDS" name="assurance" onchange="change_assurance('ARDS');" value="ARDS" {{optional($vendor_info)->assurance==="ARDS"? "checked": ""}}><label for="assurance_ARDS"  class="form-control">예치금</label>
                        </div>
                        <div class="mb-3 input-group" id="assurance_value">
                            <span class="input-group-text">보증금액</span>
                            <input type="text" class="form-control" name="assurance_amount" placeholder="단위(만)" value="@isset($vendor_info['assurance_amount']){{$vendor_info['assurance_amount']}}@endisset" autocomplete="off" aria-label="assurance_amount">
                        </div>
                        <div class="mb-3 input-group" id="assurance_ex_date">
                            <span class="input-group-text">보증종료일</span>
                            <input type="text" class="form-control datepicker" name="assurance_ex_date" placeholder="0000-00-00" value="@isset($vendor_info['assurance_ex_date']){{$vendor_info['assurance_ex_date']}}@endisset" autocomplete="off" aria-label="assurance_ex_date">
                        </div>
                        <div class="mb-3 input-group" id="deposit_form">
                            <span class="input-group-text">예치금신청서</span>
                            <input type="file" class="form-control datepicker" name="deposit_form"  value="@isset($vendor_info['deposit_form']){{$vendor_info['deposit_form']}}@endisset" autocomplete="off" aria-label="deposit_form">
                            <a class="input-group-text" href="">다운로드<i class="bx bx-download"></i></a>
                        </div>
                        <div class="input-group mb-3 recommend_area">
                            <span class="input-group-text recommend_text">추천인(번호)</span> <!-- recommend_person -->
                            <select class="form-select" name="recommend_person" id="recommend_person" data-recommend="{{$vendor_info['recommend_person'] ?? ""}}">

                            </select>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">가입 메모</span>
                            <textarea class="form-control" name="membership_memo" aria-label="membership_memo">@isset($vendor_info['membership_memo']){{ $vendor_info['membership_memo'] }}@endisset</textarea>
                        </div>
                        <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                            <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">기본정보</h4>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">대표자이름 *</span>
                            <input type="text" class="form-control" name="rep_name" value="@isset($vendor_info['rep_name']){{$vendor_info['rep_name']}}@endisset" aria-label="rep_name">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">연락처</span>
                            <input type="text" class="form-control" name="rep_tel" value="@isset($vendor_info['rep_tel']){{$vendor_info['rep_tel']}}@endisset" aria-label="rep_tel">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">이메일</span>
                            <input type="text" class="form-control" name="rep_email" value="@isset($vendor_info['rep_email']){{$vendor_info['rep_email']}}@endisset" aria-label="rep_email">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">동업자이름</span>
                            <input type="text" class="form-control" name="partner_name" value="@isset($vendor_info['partner_name']){{$vendor_info['partner_name']}}@endisset" aria-label="partner_name">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">연락처</span>
                            <input type="text" class="form-control" name="partner_tel" value="@isset($vendor_info['partner_tel']){{$vendor_info['partner_tel']}}@endisset" aria-label="partner_tel">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">이메일</span>
                            <input type="text" class="form-control" name="partner_email" value="@isset($vendor_info['partner_email']){{$vendor_info['partner_email']}}@endisset" aria-label="rep_email">
                        </div>
                        <div class="mb-3 input-group" >
                            <label class="input-group-text" for="registered_date">가입일</label>
                            <input type="text" class="form-control datepicker" id="registered_date" name="registered_date"  value="@isset($vendor_info['registered_date']) {{ $vendor_info['registered_date'] }} @endisset" autocomplete="off">
                        </div>
                        <div class="mb-3 input-group" >
                            <label class="input-group-text" for="service_ex_date">탈퇴일</label>
                            <input type="text" class="form-control datepicker" id="service_ex_date" name="service_ex_date"  value="@isset($vendor_info['service_ex_date']) {{ $vendor_info['service_ex_date'] }} @endisset" autocomplete="off">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">사업자 메모</span>
                            <textarea class="form-control" name="vendor_memo" aria-label="vendor_memo">@isset($vendor_info['vendor_memo']){{$vendor_info['vendor_memo']}}@endisset</textarea>
                        </div>
                        <h4 class="card-title mb-4">정산정보</h4>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">정산 여부</span>
                            <input type="radio" id="is_jungsan1" name="is_jungsan" value="1"  {{optional($vendor_info)->is_jungsan===1? "checked": ""}} ><label for="is_jungsan1" class="form-control">정산</label>
                            <input type="radio" id="is_jungsan0" name="is_jungsan" value="0" {{optional($vendor_info)->is_jungsan===0? "checked": ""}}><label for="is_jungsan0"  class="form-control">미정산</label>
                        </div>
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">구분</span>
                            <input type="radio" id="rep_type1" name="rep_type" value="개인"  onclick="change_bus_type('BTCS')" {{optional($vendor_info)->rep_type==="개인"? "checked": ""}} ><label for="rep_type1" class="form-control">개인</label>
                            <input type="radio" id="rep_type2" name="rep_type" value="사업자" onclick="change_bus_type('BTFCB')" {{optional($vendor_info)->rep_type==="사업자"? "checked": ""}}><label for="rep_type2"  class="form-control">사업자</label>
                            <input type="radio" id="rep_type3" name="rep_type" value="직원"  onclick="change_bus_type('BTFCC')" {{optional($vendor_info)->rep_type==="직원"? "checked": ""}} ><label for="rep_type3" class="form-control">직원</label>
                        </div>
                        <div class="mb-3 input-group person_area" id="iden_name">
                            <span class="input-group-text">명의자 이름</span>
                            <input type="text" class="form-control" name="iden_name" value="@isset($vendor_info['iden_name']){{$vendor_info['iden_name']}}@endisset" aria-label="iden_name">
                        </div>
                        <div class="mb-3 input-group person_area" id="rr_number1">
                            <span class="input-group-text">주민등록번호</span>
                            <input type="text" class="form-control"  name="rr_number1" value="@isset($vendor_info['rr_number1']){{$vendor_info['rr_number1']}}@endisset" placeholder="앞자리" aria-label="rr_number1" autocomplete="off">
                            <input type="text" class="form-control" id="rr_number2" name="rr_number22" value="" placeholder="뒷자리" aria-label="rr_number22" autocomplete="new-password">
                            @if(!empty($vendor_info['rr_number2']))
                                <span class="input-group-text" onclick="check_rrn()">뒷자리확인<i class="bx bx-download"></i></span>
                            @endif
                        </div>
                        <div class="mb-3 input-group company_area" id="company_name">
                            <span class="input-group-text">회사명</span>
                            <input type="text" class="form-control" name="company_name" value="@isset($vendor_info['company_name']){{$vendor_info['company_name']}}@endisset" aria-label="company_name">
                        </div>
                        <div class="mb-3 input-group company_area" id="business_number">
                            <span class="input-group-text">사업자번호</span>
                            <input type="text" class="form-control" name="business_number" value="@isset($vendor_info['business_number']){{$vendor_info['business_number']}}@endisset" aria-label="business_number">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text" >계좌은행</span>
                            @php
                                $bank_list = DB::table('code_of_nicepay_card_bank') -> where('type', "bank") -> get();
                            @endphp
                            <select class="form-select" name="bank_code" aria-label="">
                                <option value="">-- 은행 선택 -- </option>
                                @foreach($bank_list as $bank)
                                    <option value="{{ $bank -> code_no }}" @isset($vendor_info['bank_code']) @if($vendor_info['bank_code']==$bank -> code_no) selected @endif @endisset>{{ $bank -> code_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text" >계좌번호</span>
                            <input type="text" class="form-control" name="bank_number" value="@isset($vendor_info['bank_number']){{$vendor_info['bank_number']}}@endisset" aria-label="bank_number">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text" >예금주</span>
                            <input type="text" class="form-control" name="name_of_deposit" value="@isset($vendor_info['name_of_deposit']){{$vendor_info['name_of_deposit']}}@endisset" aria-label="bank_number">
                        </div>
                        <div class="mb-3 input-group"  id="business_file" style="">
                            <span class="input-group-text" >사업자등록증</span>
                            <input type="file" class="form-control" name="business_file" aria-label="business_file" multiple="multiple">
                            @if(!empty($vendor_info['business_file']))
                                <a class="input-group-text file_exist cursor_p" onclick="check_pw_info('{{$vendor_info['business_file']}}')">첨부된 파일 보기<i class="bx bx-link-external ms-2"></i></a>
                            @endif
                        </div>
                        <div class="mb-3 input-group"  id="iden_file" style="">
                            <span class="input-group-text" >주민등록증</span>
                            <input type="file" class="form-control" name="iden_file" multiple="multiple">
                            @if(!empty($vendor_info['iden_file']))
                                <a class="input-group-text file_exist cursor_p" onclick="check_pw_info('{{$vendor_info['iden_file']}}')">첨부된 파일 보기<i class="bx bx-link-external ms-2"></i></a>
                            @endif
                        </div>
                        <div class="mb-3 input-group" style="">
                            <span class="input-group-text" >통장사본</span>
                            <input type="file" class="form-control" id="bank_file" name="bank_file"  aria-label="bank_file" multiple="multiple" placeholder="파일 존재">
                            @if(!empty($vendor_info['bank_file']))
                                <a class="input-group-text file_exist" onclick="check_pw_info('{{$vendor_info['bank_file']}}')">첨부된 파일 보기<i class="bx bx-link-external ms-2"></i></a>
                            @endif
                        </div>

                        <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                            <button type="submit" class="btn btn-primary waves-effect waves-light w-md">저장</button>
                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->


        </div>
        <!-- 주문 정보 끝 -->

        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">배너 등록</h4>
                        <p class="card-title-desc">
                            최대 3개까지만 등록이 가능합니다.
                        </p>
                        <div>
                            <div class="dropzone" id="dropzone_banner">
                                <div class="fallback">
                                    <input name="banner" type="file" multiple="multiple">
                                </div>
                                <div class="dz-message needsclick">
                                    <div class="mb-3">
                                        <i class="display-4 text-muted uil uil-cloud-upload"></i>
                                    </div>
                                    <h4>여기에 배너 파일을 올려주세요.</h4>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary waves-effect waves-light" id="upload_banner">배너 업로드</button>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->

            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">팝업 등록</h4>
                        <p class="card-title-desc">
                            최대 3개까지만 등록이 가능합니다.
                        </p>
                        <div>
                            <div class="dropzone" id="dropzone_popup">
                                <div class="fallback">
                                    <input name="popup" type="file" multiple="multiple">
                                </div>
                                <div class="dz-message needsclick">
                                    <div class="mb-3">
                                        <i class="display-4 text-muted uil uil-cloud-upload"></i>
                                    </div>
                                    <h4>여기에 팝업 파일을 올려주세요.</h4>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary waves-effect waves-light" id="upload_popup">팝업 업로드</button>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- 내용 끝 ( col ) -->
</div><!-- 페이지 끝 ( row ) -->
</form>
@endsection
@section('script')
<script src="{{ URL::asset('/assets/js/dropzone/custom-dropzone.js') }}"></script>
<script src="{{ URL::asset('/assets/js/vendor/vendor-form.js') }}"></script>
@endsection
