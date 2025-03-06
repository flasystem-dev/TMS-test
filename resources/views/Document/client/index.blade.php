@extends('layouts.master')
@section('title')
    거래처
@endsection

@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('/assets/css/document/client/index.css') }}" rel="stylesheet" type="text/css" />
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
                                    <input type="radio" class="btn-check" name="brand" value="all" id="select_brand_all" autocomplete="off" {{request()->input('brand')==="all" ? "checked" : ""}}>
                                    <label class="btn select_brand select_brand_all" for="select_brand_all">전체</label>
                                    @foreach($brands as $brand)
                                        <input type="radio" class="btn-check" name="brand" value="{{$brand->brand_type_code}}" id="select_brand_{{$brand->brand_type_code}}" autocomplete="off" {{request()->input('brand')===$brand->brand_type_code ? "checked" : ""}}>
                                        <label class="btn select_brand select_brand_{{$brand->brand_type_code}}" for="select_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="menu2">

                            </div>
                            <div class="menu3">
                                <div class="input-group">
                                    <select class="form-select" name="search">
                                        <option value="all"             {{ request()->search==="all"             ? "selected":""}}>전체</option>
                                        <option value="name"            {{ request()->search==="name"            ? "selected":""}}>업체명</option>
                                        <option value="tel"             {{ request()->search==="tel"             ? "selected":""}}>연락처</option>
                                        <option value="ceo_name"        {{ request()->search==="ceo_name"        ? "selected":""}}>대표자명</option>
                                        <option value="business_number" {{ request()->search==="business_number" ? "selected":""}}>사업자번호</option>
                                        <option value="memo"            {{ request()->search==="memo"            ? "selected":""}}>메모</option>

                                    </select>
                                    <input type="text" class="form-control" name="search_word" value="{{request()->search_word}}">
                                </div>
                            </div>
                            <div class="menu4">
                                <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light me-2">검색하기</button>
                                <button style="border-radius:3px;" type="button" class="btn btn-secondary waves-effect waves-light" id="clientForm">+ 거래처 추가</button>
                            </div>
                        </div>
                        <div class="search_area_menu2">
                            <div class="menu1">

                            </div>
                            <div class="menu2">
                                <div class="form-check form-switch me-4">
                                    <input type="checkbox" class="form-check-input" name="all_status" value="1" @checked(request()->all_status) id="all_status">
                                    <label class="form-check-label" for="all_status">비활성 거래처 포함</label>
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
                    <table id="clients_tbl" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 50px;">번호</th>
                                <th style="width: 60px;">브랜드</th>
                                <th>업체명</th>
                                <th>사업자등록번호</th>
                                <th>대표자</th>
                                <th>업태</th>
                                <th>업종</th>
                                <th>담당자</th>
                                <th>담당자 연락처</th>
                                <th>담당자 이메일</th>
                                <th>계산서 이메일</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($clients)
                            @foreach($clients as $client)
                                @php
                                    $client_brands = explode("/", $client -> brand);
                                @endphp
                                <tr class="text-center align-middle px-0">
                                    <td></td>
                                    <!-- 브랜드 -->
                                    <td>
                                        @if(!empty($client_brands[0]))
                                            @foreach($client_brands as $client_brand)
                                            <p class="brand_type {{$client_brand}} mb-1">{{BrandAbbr($client_brand)}}</p>
                                            @endforeach
                                        @else
                                        <p class="brand_type">없음</p>
                                        @endif
                                    </td>
                                    <!-- 업체명 -->
                                    <td>
                                        <p class="cursor_p fw-bold" onclick="clientForm({{$client->id}})">{{ $client -> name }}</p>
                                    </td>
                                    <!-- 사업자등록번호 -->
                                    <td>
                                        <p>{{ $client -> business_number }}</p>
                                    </td>
                                    <!-- 대표자명 -->
                                    <td>
                                        <p>{{ $client -> ceo_name }}</p>
                                    </td>
                                    <!-- 업태 -->
                                    <td>
                                        <p>{{ $client -> business_type }}</p>
                                    </td>
                                    <!-- 업종 -->
                                    <td>
                                        <p>{{$client -> business_kind}}</p>
                                    </td>
                                    <!-- 담당자 -->
                                    <td>
                                        @if($client->managers)
                                            @foreach($client->managers as $manager)
                                                <p class="fw-bold cursor_p" onclick="clientForm({{$client->id}})">{{$manager -> name}}</p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <!-- 담당자 연락처 -->
                                    <td>
                                        @if($client->managers)
                                            @foreach($client->managers as $manager)
                                                <p>{{$manager -> tel}}</p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <!-- 담당자 이메일 -->
                                    <td>
                                        @if($client->managers)
                                            @foreach($client->managers as $manager)
                                                <p>{{$manager -> email}}</p>
                                            @endforeach
                                        @endif
                                    </td>
                                    <!-- 계산서 이메일 -->
                                    <td>
                                        <p>{{$client -> email}}</p>
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
    <script src="{{asset('assets/js/document/client/index.js')}}"></script>
@endsection