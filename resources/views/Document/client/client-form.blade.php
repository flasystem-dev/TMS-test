@extends('layouts.master-without-nav')
@section('title')
    거래처 상세정보
@endsection
@section('content')
@include('Document.modal.client-form-modal')
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
@endphp

@if(session('update'))
    <script>
        showToast('수정 완료');
    </script>
@endif
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/document/client/client-form.css') }}" rel="stylesheet">
<!-- 폼 양식 시작 -->
    @csrf
    <!-- 페이지 내용 시작 -->
<div class="layout-wrapper">
    <header id="page-topbar" style="left: 0">
        <div class="navbar-header">
            <div class="d-flex text-xl-center">
                <p class="font-size-20 fw-bold mt-3 mx-4" style="text-align:center">
                    @empty($client)
                        거래처 등록
                    @else
                        거래처 수정
                    @endempty
                </p>
            </div>
        </div>
    </header>
    <form id="client_form" method="post" action="{{url("document/client/client-form")}}">
    <!-- 기본 정보 시작-->
    <div class="row mx-2" style="margin-top: 80px;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">기본정보</h4>
                    <input type="hidden" name="id" value="{{collect(explode('/', Request::url()))->last()}}">
                    @if($client)
                    @php
                        $client_brands = explode("/", $client -> brand)
                    @endphp
                    <div class="form_row">
                        <div class="mb-3 input-group brand_btns">
                            <span class="input-group-text" >브랜드</span>
                            @foreach($brands as $brand)
                                <input type="checkbox" class="btn-check" id="check_brand_{{$brand->brand_type_code}}" disabled {{Str::contains($client -> brand, $brand->brand_type_code)? "checked" : ""}}>
                                <label for="check_brand_{{$brand->brand_type_code}}" class="form-control check_brand check_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">이름</span>
                            <input type="text" class="form-control" name="name" value="{{ optional($client) -> name }}">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">대표자명</span>
                            <input type="text" class="form-control" name="ceo_name" value="{{ optional($client) -> ceo_name }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">연락처</span>
                            <input type="text" class="form-control" name="tel" value="{{ optional($client) -> tel }}" oninput="auto_hyphen(event)">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">등록일</span>
                            <input type="date" class="form-control datepicker" name="created_at" value="{{ isset($client) ? Carbon::parse($client->created_at)->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">이메일</span>
                            <input type="text" class="form-control" name="email" value="{{ optional($client) -> email }}">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">팩스</span>
                            <input type="text" class="form-control" name="fax" value="{{ optional($client) -> fax }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">사업자번호</span>
                            <input type="text" class="form-control" name="business_number" value="{{ optional($client) -> business_number }}">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">종사업장번호</span>
                            <input type="text" class="form-control" name="tax_business_number" value="{{ optional($client) -> tax_business_number }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">업태</span>
                            <input type="text" class="form-control" name="business_type" value="{{ optional($client) -> business_type }}">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">업종</span>
                            <input type="text" class="form-control" name="business_kind" value="{{ optional($client) -> business_kind }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">주소</span>
                            <input type="text" class="form-control" name="address" value="{{ optional($client) -> address }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group input_select">
                            <span class="input-group-text">보증종류</span>
                            <input type="radio" id="assurance_ARNR" name="assurance" value="none"      {{$client->assurance==="none"     ? "checked":"" }} ><label for="assurance_ARNR" class="form-control">없음</label>
                            <input type="radio" id="assurance_ARPS" name="assurance" value="pass"      {{$client->assurance==="pass"     ? "checked":"" }} ><label for="assurance_ARPS" class="form-control">패스</label>
                            <input type="radio" id="assurance_ARIR" name="assurance" value="insurance" {{$client->assurance==="insurance"? "checked":"" }} ><label for="assurance_ARIR" class="form-control">보증보험</label>
                            <input type="radio" id="assurance_ARDS" name="assurance" value="contract"  {{$client->assurance==="contract" ? "checked":"" }} ><label for="assurance_ARDS" class="form-control">계약서</label>
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">계약서</span>
                            <input type="text" class="form-control" id="contract_fileName" placeholder="계약서 파일을 등록해 주세요." value="{{ $client->contract_fileName }}" readonly>
                            <input type="file" class="form-control d-none" name="contract_file" id="contract_file">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">보증금액</span>
                            <input type="number" class="form-control" name="assurance_amount" value="{{ $client->assurance_amount }}">
                        </div>
                        <div class="mb-3 input-group">
                            <span class="input-group-text">보증종료일</span>
                            <input type="date" class="form-control datepicker" name="assurance_ex_date" value="{{ $client->assurance_ex_date }}">
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="mb-3 input-group">
                            <span class="input-group-text">메모</span>
                            <textarea class="form-control" name="memo">{{ optional($client)->memo }}</textarea>
                        </div>
                    </div>

                    <div class="flex-wrap gap-3 mt-3" style="text-align: right;">
                        <button type="button" class="btn btn-primary waves-effect waves-light w-md" onclick="clientUpsert()">저장</button>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    </form>
    <!-- 기본 정보 끝 -->
    @if($client)
    <!-- 담당자 정보 -->
    <div class="row mx-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-secondary waves-effect waves-light add_btn" data-bs-toggle="modal" data-bs-target="#manager_form_modal" data-index="0">+ 담당자 추가</button>
                    <h4 class="card-title mb-4">담당자 정보</h4>
                    <div>
                        <table id="manager_table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>담당자 명</th>
                                    <th style="width: 120px">이메일</th>
                                    <th style="width: 120px;">연락처</th>
                                    <th>팩스</th>
                                    <th>메모</th>
                                    <th style="width: 80px;">관리</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($client->managers)
                                @foreach($client->managers as $manager)
                                <tr>
                                    <td class="text-center fw-bold">@if($manager->is_default===1) <span class="default_text">대표</span> @endif<p class="{{$manager->is_default===1? "default_name":""}}">{{ $manager -> name }}</p></td>
                                    <td><p>{{ $manager -> email }}</p></td>
                                    <td><p>{{ $manager -> tel }}</p></td>
                                    <td><p>{{ $manager -> fax }}</p></td>
                                    <td><p>{{ $manager -> memo }}</p></td>
                                    <td>
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#manager_form_modal" data-index="{{$manager->id}}" >수정</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeManager({{$manager->id}})">삭제</button>
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
    </div>
    <!-- 담당자 정보 끝 -->

    <!-- 회원 정보 -->
    <div class="row mx-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-secondary waves-effect waves-light add_btn" data-bs-toggle="modal" data-bs-target="#user_form_modal">+ 회원 추가</button>
                    <h4 class="card-title mb-4">회원 정보</h4>
                    <div>
                        <table id="user_table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width : 20px;">No</th>
                                <th style="width : 40px;">브랜드</th>
                                <th>이름</th>
                                <th style="width: 80px;">아이디</th>
                                <th>메모</th>
                                <th style="width: 90px;">담당자</th>
                                <th style="width: 40px;">관리</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($client->users)
                                @foreach($client->users as $user)
                                    <tr>
                                        <td></td>
                                        <td><p class="brand_type {{$user -> brand}}">{{ BrandAbbr($user -> brand) }}</p></td>
                                        <td class="text-center fw-bold"><p>{{ $user -> name }}</p></td>
                                        <td><p>{{ $user -> user_id }}</p></td>
                                        <td><p>{{ $user -> memo }}</p></td>
                                        <td>
                                            <select class="form-select" name="manager_id" id="manager_id" data-index="{{$user->id}}">
                                            @if($client->managers)
                                                @foreach($client->managers as $manager)
                                                <option value="{{$manager->id}}" {{$user->manager_id===$manager->id ? "selected" : ""}}>{{$manager->name}}</option>
                                                @endforeach
                                            @endif
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="userForm({{$user->id}})">수정</button>
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
    </div>
    <!-- 회원 정보 끝 -->
   @endif
</div>
@endSection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/document/client/client-form.js') }}?v={{time()}}"></script>
@endsection
