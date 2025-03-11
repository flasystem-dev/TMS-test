@extends('layouts.master')
@section('title')
    미수현황
@endsection
@section('content')
@php
    use Carbon\Carbon;
@endphp
@include('outstanding.modal.orders-modal')
<link href="{{ asset('/assets/css/outstanding/orders.css') }}" rel="stylesheet">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="get" id="search-form">
                    <div class="search_area_menu1 mb-3">
                        <div class="menu1">
                            <div class="input-group standard-label-container">
                                <span class="input-group-text">기준</span>
                                <input type="radio" class="btn-check" name="standard" id="standard-order" checked>
                                <label class="btn select-label standard-label" for="standard-order">주문</label>
                                <input type="radio" class="btn-check" name="standard" id="standard-client">
                                <label class="btn select-label standard-label" for="standard-client">거래처</label>
                                <input type="radio" class="btn-check" name="standard" id="standard-vendor">
                                <label class="btn select-label standard-label" for="standard-vendor">사업자</label>
                            </div>
                        </div>
                        <div class="menu2">
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
                                    <option value="all"                  {{ request()->search1==="all"              ? "selected":""}}>1차 조회항목</option>
                                    <option value="od_id"                {{ request()->search1==="od_id"            ? "selected":""}}>주문번호</option>
                                    <option value="rep_name"             {{ request()->search1==="rep_name"         ? "selected":""}}>사업자명</option>
                                    <option value="client_name"          {{ request()->search1==="client_name"         ? "selected":""}}>거래처명</option>
                                </select>
                                <input type="text" class="form-control" name="search_word1" value="{{request()->search_word1}}">
                            </div>
                        </div>
                        <div class="menu2">
                            <div class="input-group">
                                <select class="form-select" name="search2">
                                    <option value="all"                  {{ request()->search2==="all"               ? "selected":""}}>2차 조회항목</option>
                                    <option value="od_id"                {{ request()->search2==="od_id"             ? "selected":""}}>주문번호</option>
                                    <option value="rep_name"             {{ request()->search2==="rep_name"          ? "selected":""}}>사업자명</option>
                                    <option value="client_name"          {{ request()->search2==="client_name"         ? "selected":""}}>거래처명</option>
                                </select>
                                <input type="text" class="form-control" name="search_word2" value="{{request()->search_word2}}">
                            </div>
                        </div>
                        <div class="menu2">
                            <div class="input-group">
                                <span class="input-group-text">미수 타입</span>
                                <select class="form-select" name="is_client">
                                    <option value="all"                  {{ request()->is_client==="all"    ? "selected":""}}>전체</option>
                                    <option value="0"                    {{ request()->is_client==="0"      ? "selected":""}}>개인 미수</option>
                                    <option value="1"                    {{ request()->is_client==="1"      ? "selected":""}}>거래처 미수</option>
                                </select>
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
                                <input type="date" class="form-control datepicker" id="start_date" name="start_date" value="{{request()->start_date ?? now()->subMonths(3)->format('Y-m-d') }}">
                                <input type="date" class="form-control datepicker" id="end_date" name='end_date' value="{{request()->end_date ?? $commonDate['today']}}">
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
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#deposit_completed">일괄입금</button>
                        </div>
                    </div>
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
                            <table class="table table-striped table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                <tr>
                                    <th style="width: 2%">
                                        <label class="checkboxLabel">
                                            <input type="checkbox" name="checkAll">
                                        </label>
                                    </th>
                                    <th style="width: 3%">번호</th>
                                    <th style="width: 3%">브랜드<br>채널</th>
                                    <th style="width: 7%">주문일<br>배송일</th>
                                    <th style="width: 5%">주문자(거래처)</th>
                                    <th style="width: 10%">받는분<br>보내는분</th>
                                    <th style="width: 7%">주문상품<br>합계금액</th>
                                    <th style="width: 5%">미수금액</th>
                                    <th style="width: 5%">결제수단</th>
                                    <th style="width: 10%">거래명세서<br>증빙서류</th>
                                    <th style="width: 5%">입금예정일</th>
                                    <th style="width: 10%">결제메모</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($orders) && $orders->isNotEmpty())
                                    @foreach($orders as $order)
                                        <tr>
                                            <!-- 번호 -->
                                            <td>
                                                <label class="checkboxLabel">
                                                    <input type="checkbox" name="order_idx" value="{{$order->order_idx}}" data-paytype="{{$order->payment_type_code}}">
                                                </label>
                                            </td>
                                            <td>
                                                {{$order->order_idx}}
                                            </td>
                                            <!-- 브랜드 / 채널 -->
                                            <td>
                                                <p class="brand_type {{$order->brand_type_code}}">{{ BrandAbbr($order->brand_type_code)}}</p>
                                                <p class="brand_type {{$order->mall_code}}" style="margin-top: 3px">{{$order->channel_name}}</p>
                                            </td>
                                            <!-- 주문일/배송일 -->
                                            <td>
                                                <div style="position: relative" class="date_container simptip-position-bottom simptip-fade cursor_p" tooltip="{{$order->admin_memo}}" flow="down" onclick="order_detail('{{$order->order_idx}}')">
                                                    <span class="span_date" onclick="order_detail('{{$order->order_idx}}');">{{Carbon::parse($order->order_time)->format('Y-m-d')}}
                                                    <br>
                                                    <span class="deli_date span_date">{{$order->delivery->delivery_date ?? ""}}</span>
                                                        @if(!empty($order->admin_memo))
                                                        <i class="mdi mdi-note-text-outline memo_check"></i>
                                                       @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <!-- 주문자/연락처 -->
                                            <td>
                                                <p class="gs_name cursor_p"
                                                   data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->orderer_name}}"
                                                   onclick="clipBoardCopy(event)">
                                                    {{$order->orderer_name}}
                                                    @if($order->client)
                                                        ({{$order->client->name}})
                                                    @endif
                                                </p>
                                                <span onclick="clipBoardCopy(event)">{{$order->orderer_phone}}</span>
                                            </td>
                                            <!-- 받는분/보내는분 -->
                                            <td>
                                                <p class="gs_name"
                                                                  data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery->receiver_name ?? ""}}"
                                                                  onclick="clipBoardCopy(event)">
                                                    {{$order->delivery->receiver_name ?? ""}}</p>
                                                <p class="ribbon_left cursor_p" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery->delivery_ribbon_left ?? ""}}" onclick="clipBoardCopy(event)">{{$order->delivery->delivery_ribbon_left ?? ""}}</p>
                                            </td>
                                            <!-- 주문상품/결제금액 -->
                                            <td><p class="gs_name">{{$order->delivery->goods_name ?? ""}}</p><p class="amount product-price">{{number_format($order->total_amount)}}원</p></td>
                                            <!-- 미수금액 -->
                                            <td>
                                                <p class="amount">{{number_format($order->misu_amount)}}원</p>
                                            </td>
                                            <!-- 결제수단 -->
                                            <td>
                                                <p class="state_p {{$order->payment_type_code}}" style="margin: 0 auto;">{{ CommonCodeName($order->payment_type_code) }}</p>
                                            </td>
                                            <!-- 거래명세서 / 증빙서류 -->
                                            <td>

                                            </td>
                                            <!-- 입금예정일 -->
                                            <td>

                                            </td>
                                            <!-- 결제 메모 -->
                                            <td>

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
                    @if(isset($orders) && $orders->isNotEmpty())
                        <div class="row">
                            <div class="col-12">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
@section('script')
    <script src="{{asset('assets/js/outstanding/orders.js')}}?v={{ time() }}"></script>
    <script>
        function order_detail(order_idx){
            var url = main_url + '/order/order-detail/'+order_idx;

            @if(Auth::user()->auth < 8)
            $('#new_order'+order_idx).hide();
            @endif
            open_win(url,"주문서"+fix,1440,900,0,0);
            fix++;
        }
        function dateSel(type){
            var start_date = '';
            var end_date = '';
            if(type=='오늘'){
                start_date ='{{$commonDate['today']}}';
                end_date ='{{$commonDate['today']}}';
            }else if(type=='어제'){
                start_date ='{{$commonDate['yesterday']}}';
                end_date ='{{$commonDate['yesterday']}}';
            }else if(type=='내일'){
                start_date ='{{$commonDate['tomorrow']}}';
                end_date ='{{$commonDate['tomorrow']}}';
            }else if(type=='이번주'){
                start_date ='{{$commonDate['week']}}';
                end_date ='{{$commonDate['today']}}';
            }else if(type=='이번달'){
                start_date ='{{$commonDate['month']}}';
                end_date ='{{$commonDate['month_e']}}';
            }else if(type=='지난주'){
                start_date ='{{$commonDate['preg_week_s']}}';
                end_date ='{{$commonDate['preg_week_e']}}';
            }else if(type=='지난달'){
                start_date ='{{$commonDate['preg_month_s']}}';
                end_date ='{{$commonDate['preg_month_e']}}';
            }else if(type=='지지난달'){
                start_date ='{{$commonDate['2month_ago_s']}}';
                end_date ='{{$commonDate['2month_ago_e']}}';
            }else if(type=='3개월'){
                start_date ='{{$commonDate['month3']}}';
                end_date ='{{$commonDate['month_e']}}';
            }else if(type=='6개월'){
                start_date ='{{$commonDate['month6']}}';
                end_date ='{{$commonDate['month_e']}}';
            }else if(type=='금년'){
                start_date ='{{$commonDate['year']}}';
                end_date ='{{$commonDate['year_e']}}';
            }else if(type=='전년'){
                start_date ='{{$commonDate['preg_year_s']}}';
                end_date ='{{$commonDate['preg_year_e']}}';
            }
            $('#start_date').val(start_date);
            $('#end_date').val(end_date);
        }
    </script>
@endsection




