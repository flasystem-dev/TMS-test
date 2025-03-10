@extends('layouts.master')
@section('title')
    미수현황
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
                                <div class="input-group standard-label-container">
                                    <span class="input-group-text">기준</span>
                                    <input type="radio" class="btn-check" name="standard" id="standard-order">
                                    <label class="btn select-label standard-label" for="standard-order">주문</label>
                                    <input type="radio" class="btn-check" name="standard" id="standard-client" checked>
                                    <label class="btn select-label standard-label" for="standard-client">거래처</label>
                                    <input type="radio" class="btn-check" name="standard" id="standard-vendor">
                                    <label class="btn select-label standard-label" for="standard-vendor">사업자</label>
                                </div>
                            </div>
                            <div class="menu2">
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
                                           value="{{request()->start_date ?? now()->subMonths(3)->format('Y-m-d') }}">
                                    <input type="date" class="form-control datepicker" id="end_date" name='end_date'
                                           value="{{request()->end_date ?? $commonDate['today']}}">
                                </div>
                            </div>
                            <div class="menu2">
                                <div class="input-group">
                                    <button type="button" class="btn btn-light" onclick="dateSel('어제');">어제</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('오늘');">오늘</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('내일');">내일</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('이번주');">이번주</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('이번달');">이번달</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('지난주');">지난주</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('지지난달');">지지난달</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('지난달');">지난달</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('3개월');">3개월</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('6개월');">6개월</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('금년');">금년</button>
                                    <button type="button" class="btn btn-light" onclick="dateSel('전년');">전년</button>
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
                                    <th style="width: 8%">브랜드<br>채널</th>
                                    <th style="width: 15%">거래처 명</th>
                                    <th style="width: 10%">보증금액(보증종류)<br>계약종료일</th>
                                    <th style="width: 10%">미수금<br>(건수)</th>
                                    <th style="width: 10%">장기 미수금<br>(건수)</th>
                                    <th style="width: 10%">보증 잔액<br>(사용비율)</th>
                                    <th style="width: 10%">청구기간</th>
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
                                                <p class="cursor_p client-info">{{ number_format($client->assurance_amount) }}</p>
                                                <p class="cursor_p client-info">{{ $client->assurance_ex_date }}</p>
                                            </td>
                                            <!-- 미수금 / 건수 -->
                                            <td data-order="{{ $client->total_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->total_misu_amount) }}</p>
                                                <p class="cursor_p misu-orders" type="total">({{ number_format($client->total_misu_count) }})</p>
                                            </td>
                                            <!-- 장기 미수금 / 건수 -->
                                            <td data-order="{{ $client->past_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->past_misu_amount) }}</p>
                                                <p class="cursor_p misu-orders" type="past">({{ number_format($client->past_misu_count) }})</p>
                                            </td>
                                            <!-- 보증 잔액 / (사용비율) -->
                                            @php
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
                                            <td data-order="{{ $client->assurance_amount - $client->total_misu_amount }}">
                                                <p class="fw-bold">{{ number_format($client->assurance_amount - $client->total_misu_amount) }}</p>
                                                @if(!empty($client->assurance_amount))
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
                                        <td colspan="10" class="text-center"><h4 class="my-4">데이터가 없습니다.</h4></td>
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
    <script src="{{asset('assets/js/outstanding/clients.js')}}?v={{ time() }}"></script>
    <script>
        function order_detail(order_idx) {
            var url = main_url + '/order/order-detail/' + order_idx;

            @if(Auth::user()->auth < 8)
            $('#new_order' + order_idx).hide();
            @endif
            open_win(url, "주문서" + fix, 1440, 900, 0, 0);
            fix++;
        }

        function dateSel(type) {
            var start_date = '';
            var end_date = '';
            if (type == '오늘') {
                start_date = '{{$commonDate['today']}}';
                end_date = '{{$commonDate['today']}}';
            } else if (type == '어제') {
                start_date = '{{$commonDate['yesterday']}}';
                end_date = '{{$commonDate['yesterday']}}';
            } else if (type == '내일') {
                start_date = '{{$commonDate['tomorrow']}}';
                end_date = '{{$commonDate['tomorrow']}}';
            } else if (type == '이번주') {
                start_date = '{{$commonDate['week']}}';
                end_date = '{{$commonDate['today']}}';
            } else if (type == '이번달') {
                start_date = '{{$commonDate['month']}}';
                end_date = '{{$commonDate['month_e']}}';
            } else if (type == '지난주') {
                start_date = '{{$commonDate['preg_week_s']}}';
                end_date = '{{$commonDate['preg_week_e']}}';
            } else if (type == '지난달') {
                start_date = '{{$commonDate['preg_month_s']}}';
                end_date = '{{$commonDate['preg_month_e']}}';
            } else if (type == '지지난달') {
                start_date = '{{$commonDate['2month_ago_s']}}';
                end_date = '{{$commonDate['2month_ago_e']}}';
            } else if (type == '3개월') {
                start_date = '{{$commonDate['month3']}}';
                end_date = '{{$commonDate['month_e']}}';
            } else if (type == '6개월') {
                start_date = '{{$commonDate['month6']}}';
                end_date = '{{$commonDate['month_e']}}';
            } else if (type == '금년') {
                start_date = '{{$commonDate['year']}}';
                end_date = '{{$commonDate['year_e']}}';
            } else if (type == '전년') {
                start_date = '{{$commonDate['preg_year_s']}}';
                end_date = '{{$commonDate['preg_year_e']}}';
            }
            $('#start_date').val(start_date);
            $('#end_date').val(end_date);
        }
    </script>
@endsection




