@extends('layouts.master')
@section('title')
    미수현황 (거래처)
@endsection
@section('content')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('/assets/css/outstanding/clients.css') }}" rel="stylesheet">
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
                                        <input type="checkbox" class="btn-check" name="brand[]"
                                               value="{{$brand->brand_type_code}}"
                                               id="select_brand_{{$brand->brand_type_code}}"
                                               autocomplete="off" {{is_array(request()->input('brand')) && in_array($brand->brand_type_code, request()->input('brand'), true) ? "checked" : ""}}>
                                        <label class="btn select-label select_brand_{{$brand->brand_type_code}}"
                                               for="select_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="search_area_menu2 mb-3">
                            <div class="menu1">
                                <div class="input-group">
                                    <select class="form-select" name="search1">
                                        <option value="all" {{ request()->search==="all"              ? "selected":""}}>
                                            1차 조회항목
                                        </option>
                                        <option value="clients.name" {{ request()->search==="od_id"            ? "selected":""}}>
                                            거래처명
                                        </option>
                                        <option value="rep_name" {{ request()->search==="rep_name"         ? "selected":""}}>
                                            사업자명
                                        </option>
                                    </select>
                                    <input type="text" class="form-control" name="search_word1"
                                           value="{{request()->search_word1}}">
                                </div>
                            </div>
                            <div class="menu2">
                                <div class="input-group">
                                    <select class="form-select" name="search2">
                                        <option value="all" {{ request()->search==="all"               ? "selected":""}}>
                                            2차 조회항목
                                        </option>
                                        <option value="name" {{ request()->search==="od_id"             ? "selected":""}}>
                                            거래처명
                                        </option>
                                        <option value="order_number" {{ request()->search==="order_number"      ? "selected":""}}>
                                            사업자명
                                        </option>
                                    </select>
                                    <input type="text" class="form-control" name="search_word2"
                                           value="{{request()->search_word2}}">
                                </div>
                            </div>
                        </div>

                        <div class="search_area_menu3">
                            <div class="menu1">
                                <div class="input-group">
                                    <select class="form-select" name="date_type">
                                        <option value="delivery_date">배송일</option>
                                        <option value="order_time">주문일</option>
                                        <option value="create_ts">수집일</option>
                                    </select>
                                    <input type="date" class="form-control datepicker" id="start_date" name="start_date"
                                           value="{{request()->start_date ?? now()->subMonths(3)->toDateString() }}">
                                    <input type="date" class="form-control datepicker" id="end_date" name='end_date'
                                           value="{{request()->end_date ?? now()->toDateString() }}">
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
                                <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light">
                                    검색하기
                                </button>
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
                            <table class="table table-striped table-bordered"
                                   @if(isset($clients) && count($clients) > 0) id="client-table" @endif>
                                <thead>
                                <tr>
                                    <th style="width: 3%">
                                        <label class="checkboxLabel">
                                            <input type="checkbox" name="checkAll">
                                        </label>
                                    </th>
                                    <th style="width: 4%" class="text-center">번호</th>
                                    <th style="width: 7%">브랜드<br>채널</th>
                                    <th style="width: 15%">거래처 명</th>
                                    <th style="width: 8%">보증금액(보증종류)<br>계약종료일</th>
                                    <th style="width: 8%">미수금<br>(건수)</th>
                                    <th style="width: 8%">장기 미수금<br>(건수)</th>
                                    <th style="width: 8%">전월 미수금<br>(건수)</th>
                                    <th style="width: 8%">전체 미수금<br>(건수)</th>
                                    <th style="width: 8%">보증 잔액<br>(사용비율)</th>
                                    <th style="width: 5%">청구기간</th>
                                    <th style="width: 10%">결제방식<br>결제일(독촉여부)</th>
                                    <th>메모</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($clients))
                                    @foreach($clients as $client)
                                        <tr>
                                            <td>
                                                <label class="checkboxLabel">
                                                    <input type="checkbox" name="client_id[]" value="{{$client->id}}">
                                                </label>
                                            </td>
                                            <!-- 번호 -->
                                            <td>

                                            </td>
                                            <!-- 브랜드 / 채널 -->
                                            <td>
                                                <p class="brand_type {{$client->brand_type_code}}" >{{ BrandAbbr($client->brand_type_code)}}</p>
                                                <p class="brand_type channel-name" style="margin-top: 3px">{{ $client->rep_name }}</p>
                                            </td>
                                            <!-- 거래처 명 -->
                                            <td>
                                                <p class="client-name" data-brand="{{ $client->brand_type_code }}">{{ $client -> name }}</p>
                                            </td>
                                            <!-- 보증금액(보증종류) / 계약종료일 -->
                                            <td data-order="{{ $client->assurance_ex_date }}">
                                                @if(!empty($client->assurance_amount))
                                                <p class="cursor_p client-info">{{ number_format($client->assurance_amount) }}</p>
                                                <p class="cursor_p client-info">{{ $client->assurance_ex_date }}</p>
                                                @endif
                                            </td>
                                            <!-- 미수금 / 건수 -->
                                            <td data-order="{{ $client->misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->misu_amount) }}</p>
                                                <p class="cursor_p misu-orders" type="search">({{ number_format($client->misu_count) }})</p>
                                            </td>
                                            <!-- 장기 미수금 / 건수 -->
                                            <td data-order="{{ $client->longTerm_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->longTerm_misu_amount) }}</p>
                                                <p class="cursor_p misu-orders" type="longTerm">({{ number_format($client->longTerm_misu_count) }})</p>
                                            </td>
                                            <!-- 전월 미수금 / 건수 -->
                                            <td data-order="{{ $client->monthAgo_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->monthAgo_misu_amount) }}</p>
                                                <p class="cursor_p misu-orders" type="monthAgo">({{ number_format($client->monthAgo_misu_count) }})</p>
                                            </td>
                                            <!-- 전체 미수금 / 건수 -->
                                            <td data-order="{{ $client->total_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->total_misu_amount) }} 원</p>
                                                <p class="cursor_p misu-orders" type="total">({{ number_format($client->total_misu_count) }} 건)</p>
                                            </td>
                                            <!-- 보증 잔액 / (사용비율) -->
                                            @php
                                                $percentage = 0;
                                                $warning_text = "";
                                                if(!empty($client->assurance_amount)) {
                                                    $percentage = (int)($client->total_misu_amount / $client->assurance_amount * 100);
                                                    if($percentage > 60) {
                                                        $warning_text = "text-warning";
                                                    }
                                                    if($percentage > 90) {
                                                        $warning_text = "text-danger";
                                                    }
                                                }
                                            @endphp
                                            <td data-order="{{ $percentage }}">
                                                <p class="fw-bold">{{ number_format($client->assurance_amount - $client->total_misu_amount) }} 원</p>
                                                @if(!empty($percentage))
                                                <p class="fw-bold {{ $warning_text }}">({{ $percentage }}%)</p>
                                                @endif
                                            </td>
                                            <!-- 청구기간 -->
                                            <td data-order="{{ $client->charge_ex_date }}">
                                                <p></p>
                                            </td>
                                            <!-- 결제방식 / 결제일(독촉여부) -->
                                            <td>
                                                <p></p>
                                            </td>
                                            <!-- 메모 -->
                                            <td>
                                                <p>{{ $client->memo }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="13" class="text-center"><h4 class="my-4">데이터가 없습니다.</h4></td>
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
    <script src="{{asset('assets/js/outstanding/clients.js')}}?v={{ time() }}"></script>
@endsection




