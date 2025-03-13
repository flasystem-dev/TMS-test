@extends('layouts.master')
@section('title')
    미수현황 (사업자)
@endsection
@section('content')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/css/outstanding/vendors.css') }}" rel="stylesheet">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" id="search-form">
                        <div class="search_area_menu1 mb-3">
                            <div class="menu1">
                                <div class="input-group brand_btns">
                                    <span class="input-group-text">브랜드</span>
                                    @foreach($brands as $brand)
                                        <input type="radio" class="btn-check" name="brand" value="{{$brand->brand_type_code}}" id="select_brand_{{$brand->brand_type_code}}" autocomplete="off" {{request()->input('brand')===$brand->brand_type_code ? "checked" : ""}}>
                                        <label class="btn select-label select_brand_{{$brand->brand_type_code}}" for="select_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="search_area_menu2 mb-3">
                            <div class="menu1">
                                <div class="input-group">
                                    <select class="form-select" name="search1">
                                        <option value="all"                  {{ request()->search==="all"              ? "selected":""}}>1차 조회항목</option>
                                        <option value="od_id"                {{ request()->search==="od_id"            ? "selected":""}}>주문번호</option>
                                        <option value="rep_name"             {{ request()->search==="rep_name"         ? "selected":""}}>사업자명</option>
                                    </select>
                                    <input type="text" class="form-control" name="search_word1" value="{{request()->search_word1}}">
                                </div>
                            </div>
                            <div class="menu2">
                                <div class="input-group">
                                    <select class="form-select" name="search2">
                                        <option value="all"                  {{ request()->search==="all"               ? "selected":""}}>2차 조회항목</option>
                                        <option value="od_id"                {{ request()->search==="od_id"             ? "selected":""}}>주문번호</option>
                                        <option value="order_number"         {{ request()->search==="order_number"      ? "selected":""}}>사업자명</option>
                                    </select>
                                    <input type="text" class="form-control" name="search_word2" value="{{request()->search_word2}}">
                                </div>
                            </div>
                        </div>

                        <div class="search_area_menu3">
                            <div class="menu1">
                                <div class="input-group">
                                    <select class="form-select" name="date_type">
                                        <option value="delivery_date"  {{ request()->date_type==="delivery_date" ? 'selected' : "" }} >배송일</option>
                                        <option value="order_time"     {{ request()->date_type==="order_time"    ? 'selected' : "" }} >주문일</option>
                                        <option value="create_ts"      {{ request()->date_type==="create_ts"     ? 'selected' : "" }} >수집일</option>
                                    </select>
                                    <input type="date" class="form-control datepicker" id="start_date" name="start_date" value="{{request()->start_date ?? now()->subMonths(3)->format('Y-m-d') }}">
                                    <input type="date" class="form-control datepicker" id="end_date" name='end_date' value="{{request()->end_date ?? $commonDate['today']}}">
                                </div>
                            </div>
                            <div class="menu2">
                                <div class="input-group">
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('어제');">어제</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('오늘');">오늘</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('내일');">내일</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('이번주');">이번주</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('이번달');">이번달</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('지난주');">지난주</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('지지난달');">지지난달</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('지난달');">지난달</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('3개월');">3개월</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('6개월');">6개월</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('금년');">금년</button>
                                    <button type="button" class="btn btn-light" onclick="DateSelector.select('전년');">전년</button>
                                </div>
                            </div>
                            <div class="menu3">
                                <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light">검색하기</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-striped table-bordered" @if(isset($vendors) && count($vendors) > 0) id="vendor-table" @endif>
                                <thead>
                                <tr>
                                    <th style="width: 3%">
                                        <label class="checkboxLabel">
                                            <input type="checkbox" name="checkAll">
                                        </label>
                                    </th>
                                    <th style="width: 4%" class="text-center">번호</th>
                                    <th style="width: 8%">브랜드<br>채널</th>
                                    <th style="width: 13%">보증금액(보증종류)<br>계약종료일(계약자)</th>
                                    <th style="width: 10%">개인 미수금<br>(건수)</th>
                                    <th style="width: 10%">거래처 미수금<br>(건수)</th>
                                    <th style="width: 10%">장기 미수금<br>(건수)</th>
                                    <th style="width: 10%">전월 미수금<br>(건수)</th>
                                    <th style="width: 10%">전체 미수금<br>(건수)</th>
                                    <th style="width: 10%">보증 잔액<br>(사용비율)</th>
                                    <th>경영지원 메모</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($vendors))
                                    @foreach($vendors as $vendor)
                                        <tr>
                                            <td>
                                                <label class="checkboxLabel">
                                                    <input type="checkbox" name="vendor_idx[]" value="{{$vendor->idx}}">
                                                </label>
                                            </td>
                                            <!-- 번호 -->
                                            <td>

                                            </td>
                                            <!-- 브랜드 / 채널 -->
                                            <td>
                                                <p class="brand_type {{$vendor->brand_code()}}">{{ BrandAbbr($vendor->brand_code())}}</p>
                                                <p class="brand_type channel-name" style="margin-top: 3px" >{{$vendor->channel_name}}</p>
                                            </td>
                                            <!-- 보증금액(보증종류) / 계약종료일(계약자) -->
                                            @php
                                                $ex_date = $vendor->assurance_ex_date;
                                                if($vendor->assurance === 'ARPS') {
                                                    $ex_date = "";
                                                }
                                            @endphp

                                            <td data-order="{{ $ex_date }}">
                                                <p class="cursor_p vendor-info">{{ $vendor->assurance === 'ARDS' || $vendor->assurance === 'ARIR' ? number_format($vendor->assurance_amount) : "" }} ({{ CommonCodeName($vendor->assurance) }})</p>
                                                <p class="cursor_p vendor-info"><span class="fw-bold">{{ $ex_date }}</span> @if(!empty($vendor->assurance_contractor))({{ $vendor->assurance_contractor }})@endif</p>
                                            </td>
                                            <!-- 개인 미수금 / 건수 -->
                                            <td data-order="{{ $vendor->personal_misu_amount }}">
                                                @if(!empty($vendor->personal_misu_amount))
                                                <p class="fw-bold">{{ number_format($vendor->personal_misu_amount) }} 원</p>
                                                <p class="cursor_p misu-orders" data-type="personal">({{ number_format($vendor->personal_misu_count) }} 건)</p>
                                                @endif
                                            </td>
                                            <!-- 거래처 미수금 / 건수 -->
                                            <td data-order="{{ $vendor->client_misu_amount }}">
                                                @if(!empty($vendor->client_misu_amount))
                                                <p class="fw-bold">{{ number_format($vendor->client_misu_amount) }} 원</p>
                                                <p class="cursor_p misu-orders" data-type="client">({{ number_format($vendor->client_misu_count) }} 건)</p>
                                                @endif
                                            </td>
                                            <!-- 장기 미수금 / 건수 -->
                                            <td data-order="{{ $vendor->longTerm_misu_amount }}">
                                                @if(!empty($vendor->longTerm_misu_amount))
                                                <p class="fw-bold">{{ number_format($vendor->longTerm_misu_amount) }} 원</p>
                                                <p class="cursor_p misu-orders" data-type="longTerm">({{ number_format($vendor->longTerm_misu_count) }} 건)</p>
                                                @endif
                                            </td>
                                            <!-- 전월 미수금 / 건수 -->
                                            <td data-order="{{ $vendor->monthAgo_misu_amount }}">
                                                @if(!empty($vendor->monthAgo_misu_amount))
                                                    <p class="fw-bold">{{ number_format($vendor->monthAgo_misu_amount) }} 원</p>
                                                    <p class="cursor_p misu-orders" data-type="monthAgo">({{ number_format($vendor->monthAgo_misu_count) }} 건)</p>
                                                @endif
                                            </td>
                                            <!-- 전체 미수금 / 건수 -->
                                            <td data-order="{{ $vendor->total_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($vendor->total_misu_amount) }} 원</p>
                                                <p class="cursor_p misu-orders" data-type="total">({{ number_format($vendor->total_misu_count) }} 건)</p>
                                            </td>
                                            <!-- 보증 잔액 / 사용비율 -->
                                            @php
                                                $percentage = 0;
                                                $warning_text = "";
                                                if(!empty($vendor->assurance_amount)) {
                                                    $percentage = (int)($vendor->total_misu_amount / $vendor->assurance_amount * 100);
                                                    if($percentage > 60) {
                                                        $warning_text = "text-warning";
                                                    }
                                                    if($percentage > 90) {
                                                        $warning_text = "text-danger";
                                                    }
                                                }
                                            @endphp
                                            <td data-order="{{ $percentage }}">
                                                <p class="fw-bold">{{ number_format($vendor->assurance_amount - $vendor->total_misu_amount) }} 원</p>
                                                @if(!empty($percentage))
                                                <p class="fw-bold {{ $warning_text }}">({{ $percentage }}%)</p>
                                                @endif
                                            </td>
                                            <!-- 메모 -->
                                            <td>
                                                <p>{{ $vendor->support_memo }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center"><h4 class="my-4">데이터가 없습니다.</h4></td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{asset('assets/js/outstanding/outstanding.js')}}?v={{ time() }}"></script>
    <script src="{{asset('assets/js/outstanding/vendors.js')}}?v={{ time() }}"></script>
@endsection




