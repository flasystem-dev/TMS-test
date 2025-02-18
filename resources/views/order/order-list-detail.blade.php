@extends('layouts.master-without-nav')
@section('title')
    전체주문관리
@endsection
@section('content')
@php
    use Carbon\Carbon;
@endphp
<link href="{{ asset('/assets/css/order/order-index.css') }}" rel="stylesheet">
<link href="{{ asset('/assets/css/order/order-list-detail.css') }}" rel="stylesheet">
@include('order.modal.order-index-modal')
@if(session('alert'))
    <script>
        showToast('수정 완료');
    </script>
@endif
<div class="row w-100">
    <div class="col-12">
        <div class="card">
            <div class="card-body spacer_zero">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                <strong>검색 필터 열기</strong>
                            </button>
                        </h2>
                        <form method="get" action="?" id="search_form">
                            <div id="flush-collapseTwo" class="accordion-collapse collapse show" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="search_area_menu1 mb-3">
                                        <div class="menu1">
                                            <div class="input-group brand_btns">
                                                <span class="input-group-text">브랜드</span>
                                                @foreach($brands as $brand)
                                                    <input type="radio" class="btn-check" name="brand" value="{{$brand->brand_type_code}}" id="select_brand_{{$brand->brand_type_code}}" autocomplete="off" {{request()->input('brand')===$brand->brand_type_code ? "checked" : ""}}>
                                                    <label class="btn select_brand select_brand_{{$brand->brand_type_code}}" for="select_brand_{{$brand->brand_type_code}}">{{$brand->brand_ini}}</label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="menu2">
                                            <div class="input-group">
                                                <span class="input-group-text">주문-결제상태</span>
                                                <select class="form-select" name="payment_state_code">
                                                    <option value=""     {{request()->input('payment_state_code')===""     ? "selected" : ""}}>전체</option>
                                                    <option value="PSUD" {{request()->input('payment_state_code')==="PSUD" ? "selected" : ""}}>결제대기</option>
                                                    <option value="PSDN" {{request()->input('payment_state_code')==="PSDN" ? "selected" : ""}}>결제완료</option>
                                                    <option value="PSCC" {{request()->input('payment_state_code')==="PSCC" ? "selected" : ""}}>취소완료</option>
                                                    <option value="PSOC" {{request()->input('payment_state_code')==="PSOC" ? "selected" : ""}}>주문확인</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu3">
                                            <div class="input-group">
                                                <span class="input-group-text">주문-결제수단</span>
                                                <select class="form-select" name="payment_type_code">
                                                    <option value=""     {{request()->payment_type_code===""    ? "selected" : ""}}>전체</option>
                                                    <option value="PTCD" {{request()->payment_type_code==="PTCD"? "selected" : ""}}>신용카드</option>
                                                    <option value="PTMN" {{request()->payment_type_code==="PTMN"? "selected" : ""}}>수기결제</option>
                                                    <option value="PTBT" {{request()->payment_type_code==="PTBT"? "selected" : ""}}>계좌이체</option>
                                                    <option value="PTVA" {{request()->payment_type_code==="PTVA"? "selected" : ""}}>가상계좌</option>
                                                    <option value="PTDB" {{request()->payment_type_code==="PTDB"? "selected" : ""}}>무통장</option>
                                                    <option value="PTDP" {{request()->payment_type_code==="PTDP"? "selected" : ""}}>법인미수</option>
                                                    <option value="PTTD" {{request()->payment_type_code==="PTTD"? "selected" : ""}}>간편결제</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu4">
                                            <div class="input-group">
                                                <span class="input-group-text">주문-배송상태</span>
                                                <select class="form-select" name="delivery_state_code">
                                                    <option value=""     {{request()->delivery_state_code===""    ? "selected":""}}>전체</option>
                                                    <option value="DLUD" {{request()->delivery_state_code==="DLUD"? "selected":""}}>미배송</option>
                                                    <option value="DLSP" {{request()->delivery_state_code==="DLSP"? "selected":""}}>배송중</option>
                                                    <option value="DLDN" {{request()->delivery_state_code==="DLDN"? "selected":""}}>배송완료</option>
                                                    <option value="DLCC" {{request()->delivery_state_code==="DLCC"? "selected":""}}>취소주문</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu5">
                                            <div class="input-group">
                                                <select class="form-select" name="search">
                                                    <option value="all"                  {{ request()->search==="all"                  ? "selected":""}}>전체</option>
                                                    <option value="od_id"                {{ request()->search==="od_id"                ? "selected":""}}>주문번호</option>
                                                    <option value="order_number"         {{ request()->search==="order_number"         ? "selected":""}}>쇼핑몰주문번호</option>
                                                    <option value="order_idx"            {{ request()->search==="order_idx"            ? "selected":""}}>주문인덱스</option>
                                                    <option value="orderer_name"         {{ request()->search==="orderer_name"         ? "selected":""}}>주문자</option>
                                                    <option value="orderer_phone"        {{ request()->search==="orderer_phone"        ? "selected":""}}>주문자휴대폰</option>
                                                    <option value="receiver_name"        {{ request()->search==="receiver_name"        ? "selected":""}}>받는분</option>
                                                    <option value="receiver_phone"       {{ request()->search==="receiver_phone"       ? "selected":""}}>받는분휴대폰</option>
                                                    <option value="delivery_ribbon_left" {{ request()->search==="delivery_ribbon_left" ? "selected":""}}>보내는분</option>
                                                    <option value="rep_name"             {{ request()->search==="rep_name"             ? "selected":""}}>사업자명</option>
                                                    <option value="deposit_name"         {{ request()->search==="deposit_name"         ? "selected":""}}>입금자명</option>
                                                    <option value="admin_memo"           {{ request()->search==="admin_memo"           ? "selected":""}}>관리자메모</option>
                                                    <option value="payment_memo"         {{ request()->search==="payment_memo"         ? "selected":""}}>결제메모</option>
                                                    <option value="send_name"            {{ request()->search==="send_name"            ? "selected":""}}>담당자</option>
                                                </select>
                                                <input type="text" class="form-control" name="search_word" value="{{request()->search_word}}">
                                            </div>
                                        </div>
                                        <div class="menu6">
                                            <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light">검색하기</button>
                                        </div>
                                    </div>
                                    <div class="search_area_menu2 mb-3">
                                        <div class="menu1">
                                            <div class="input-group">
                                                <span class="input-group-text">날짜선택</span>
                                                <select class="form-select" name="date_type">
                                                    <option value="order_time"    {{ request()->date_type==="order_time"    ? "selected" : "" }}>주문일</option>
                                                    <option value="delivery_date" {{ request()->date_type==="delivery_date" ? "selected" : "" }}>배송일</option>
                                                    <option value="create_ts"     {{ request()->date_type==="create_ts"     ? "selected" : "" }}>수집일</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu2">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{request()->start_date ?? now()->subYear()->format('Y-m-d') }}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                                <strong class="mx-2 mt-2">~</strong>
                                                <input type="date" class="form-control" id="end_date" name='end_date' value="{{request()->end_date ?? $commonDate['today']}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                            </div>
                                        </div>
                                        <div class="menu3">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-light ms-4" onclick="dateSel('어제');">어제</button>
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
                                        <div class="menu4">
                                            <div class="input-group">
                                                <span class="input-group-text">합계금액</span>
                                                <input type="number" class="form-control" name="order_amount" value="{{request()->order_amount}}">
                                            </div>
                                        </div>
                                        <div class="menu5">
                                            <div class="cancel_check_container">
                                                <div class="form-check form-switch me-4">
                                                    <input type="checkbox" class="form-check-input" name="cancel_check" value="1" id="cancel_check" {{request()->cancel_check==="1"?"checked":""}}>
                                                    <label class="form-check-label" for="cancel_check">취소주문포함</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="search_area_menu3 mb-3">
                                        <div class="menu1">
                                            <div class="input-group">
                                                <span class="input-group-text">결제-결제상태</span>
                                                <select class="form-select" name="order_payment-payment_state_code">
                                                    <option value=""     {{request()->input('order_payment-payment_state_code')===""     ? "selected" : ""}}>전체</option>
                                                    <option value="PSUD" {{request()->input('order_payment-payment_state_code')==="PSUD" ? "selected" : ""}}>결제대기</option>
                                                    <option value="PSDN" {{request()->input('order_payment-payment_state_code')==="PSDN" ? "selected" : ""}}>결제완료</option>
                                                    <option value="PSCC" {{request()->input('order_payment-payment_state_code')==="PSCC" ? "selected" : ""}}>취소완료</option>
                                                    <option value="PSOC" {{request()->input('order_payment-payment_state_code')==="PSOC" ? "selected" : ""}}>주문확인</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu1">
                                            <div class="input-group">
                                                <span class="input-group-text">결제-결제수단</span>
                                                <select class="form-select" name="order_payment-payment_type_code">
                                                    <option value=""     {{request()->input('order_payment-payment_type_code')===""    ? "selected" : ""}}>전체</option>
                                                    <option value="PTCD" {{request()->input('order_payment-payment_type_code')==="PTCD"? "selected" : ""}}>신용카드</option>
                                                    <option value="PTMN" {{request()->input('order_payment-payment_type_code')==="PTMN"? "selected" : ""}}>수기결제</option>
                                                    <option value="PTBT" {{request()->input('order_payment-payment_type_code')==="PTBT"? "selected" : ""}}>계좌이체</option>
                                                    <option value="PTVA" {{request()->input('order_payment-payment_type_code')==="PTVA"? "selected" : ""}}>가상계좌</option>
                                                    <option value="PTDB" {{request()->input('order_payment-payment_type_code')==="PTDB"? "selected" : ""}}>무통장</option>
                                                    <option value="PTDP" {{request()->input('order_payment-payment_type_code')==="PTDP"? "selected" : ""}}>법인미수</option>
                                                    <option value="PTTD" {{request()->input('order_payment-payment_type_code')==="PTTD"? "selected" : ""}}>간편결제</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu1">
                                            <div class="input-group">
                                                <span class="input-group-text">결제-증빙</span>
                                                <select class="form-select" name="order_payment-document_type">
                                                    <option value=""     {{request()->input('order_payment-document_type')===""    ? "selected" : ""}}>전체</option>
                                                    <option value="PMCR" {{request()->input('order_payment-document_type')==="PTCD"? "selected" : ""}}>현금영수</option>
                                                    <option value="PMPE" {{request()->input('order_payment-document_type')==="PTMN"? "selected" : ""}}>지출증빙</option>
                                                    <option value="PMVI" {{request()->input('order_payment-document_type')==="PTBT"? "selected" : ""}}>자진발급</option>
                                                    <option value="PMIB" {{request()->input('order_payment-document_type')==="PTVA"? "selected" : ""}}>계산서</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu1">
                                            <div class="input-group">
                                                <span class="input-group-text">주문-유입정보</span>
                                                <select class="form-select" name="inflow">
                                                    <option value=""        {{request()->input('inflow')===""        ? "selected" : ""}}>전체</option>
                                                    <option value="mall"    {{request()->input('inflow')==="mall"    ? "selected" : ""}}>몰</option>
                                                    <option value="call"    {{request()->input('inflow')==="call"    ? "selected" : ""}}>전화</option>
                                                    <option value="sms"     {{request()->input('inflow')==="sms"     ? "selected" : ""}}>문자</option>
                                                    <option value="talk"    {{request()->input('inflow')==="talk"    ? "selected" : ""}}>카톡</option>
                                                    <option value="channel" {{request()->input('inflow')==="channel" ? "selected" : ""}}>카톡채널</option>
                                                    <option value="etc"     {{request()->input('inflow')==="etc"     ? "selected" : ""}}>기타</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="menu1">
                                            <div class="form-check form-switch me-4">
                                                <input type="checkbox" class="form-check-input" name="order_payment-advance_payment" value="1"  id="advance_payment" {{request()->input('order_payment-advance_payment')==="1"? "checked" : ""}}>
                                                <label class="form-check-label" for="advance_payment">사업자 선결제</label>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- accordion-body -->
                            </div><!-- accordion-col -->
                        </form>
                            </div>
                </div>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="order_index_btns">
                    <div class="front_btns">
                        <p class="count_text"><span>{{isset($orders) ? number_format($orders->total()) : 0}}</span> 건<span class="ms-2">{{isset($orders_amount) ? number_format($orders_amount) : 0}}</span> 원</p>
                        <select class="form-select select_perPage" id="perPage" aria-label="perPage">
                            <option value="20"  @if(session('perPage')) {{session('perPage') === "20" ? "selected" : ""}}@endif>20개씩</option>
                            <option value="50"  @if(session('perPage')) {{session('perPage') === "50" ? "selected" : ""}}@endif>50개씩</option>
                            <option value="100" @if(session('perPage')) {{session('perPage') === "100" ? "selected" : ""}}@endif>100개씩</option>
                            <option value="300" @if(session('perPage')) {{session('perPage') === "300" ? "selected" : ""}}@endif>300개씩</option>
                        </select>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite')">하이라이트</button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite-off')">하이라이트 제거</button>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="location.href='{{url("order/ecommerce_orders")."?is_highlight=1"}}'">하이라이트만 보기</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="select_orders_view()">선택주문만 보기</button>
                        @if(Auth::user()->auth > 5 || Auth::user()->auth === 10)
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#deposit_completed">일괄입금</button>
                        @endif
                        @if(Auth::user()->auth > 5 || Auth::user()->auth === 10)
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#batch_input">일괄입력</button>
                        @endif
                        @if(Auth::user()->auth > 3)
                            <button type="button" class="btn btn-outline-danger btn-sm ms-2" onclick="operate_orders('remove')">주문제거</button>
                        @endif
                    </div>
                    <div class="end_btns">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_popup()">엑셀 일괄 다운로드</button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_download()">엑셀 다운로드</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="handler" id="handler" value="{{Auth::user()->name}}">
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
                                <th style="width: 4%">번호</th>
                                <th style="width: 5%">브랜드<br>채널</th>
                                <th style="width: 8%">수집일<br>배송일</th>
                                <th style="width: 5%">주문자<br>연락처</th>
                                <th style="width: 5%">받는분<br>연락처</th>
                                <th style="width: 7%">주문상품<br>합계금액</th>
                                <th style="width: 11%">배송지<br>보내는분</th>
                                <th style="width: 30%">결제수단</th>
                                <th style="width: 4%">결제상태<br>배송상태</th>
                                <th style="width: 3%">담당자</th>
                                <th style="width: 3%">발주</th>
                                <th style="width: 5%">기타</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($orders) && $orders->isNotEmpty())
                                @foreach($orders as $order)
                                    <tr class="ordersTable_tr {{$order->is_highlight? "is_highlight": ""}} {{$order->is_view? '' : "not_view"}}">
                                        <!-- 번호 -->
                                        <td class="center" style="padding: 0;">
                                            <label class="checkboxLabel">
                                                <input type="checkbox" name="order_idx" value="{{$order->order_idx}}" data-paytype="{{$order->payment_type_code}}">
                                            </label>
                                        </td>
                                        <td class="center" style="vertical-align: center;">
                                            @if(Auth::user() -> auth > 9)
                                                <a href="javascript:order_log_popup('{{ $order -> od_id }}');" class="text-dark">{{$order->order_idx}}</a>
                                            @else
                                                {{$order->order_idx}}
                                            @endif
                                            @if($order -> order_quantity > 1)
                                                <p class="new_order" id="order_quantity">({{$order -> sub_index()}}/{{$order -> order_quantity}})</p>
                                            @endif
                                            @if($order->is_new)
                                                <p class="new_order" id="new_order{{$order->order_idx}}">미확인</p>
                                            @endif
                                        </td>
                                        <!-- 브랜드 -->
                                        <td class="center">
                                            <p class="brand_type {{$order->brand_type_code}}">{{ BrandAbbr($order->brand_type_code)}}</p>
                                            <!-- 벤더 도메인 연결 -->
                                            <a href="javascript:open_win('{{$order->channel_url()}}', 'vendor_domain', 1000, 900, 50, 50)" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$order->channel_mall()}}">
                                                <p class="brand_type {{$order->mall_code}}" style="margin-top: 3px">
                                                    {{$order->channel_name()}}
                                                </p>
                                            </a>
                                        </td>
                                        <!-- 수집일/배송일 -->
                                        <td class="center">
                                            <div style="position: relative" class="date_container simptip-position-bottom simptip-fade" tooltip="{{$order->admin_memo}}" flow="down">

                                            <span class="span_date" onclick="order_detail('{{$order->order_idx}}');">{{$order->create_ts}}
                                                @switch($order->inflow)
                                                    @case("mall")
                                                        <i class="bx bx-cart inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="몰"
                                                        ></i>
                                                        @break
                                                    @case("call")
                                                        <i class="bx bx-phone inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="전화"
                                                        ></i>
                                                        @break
                                                    @case("sms")
                                                        <i class="bx bx-envelope inflow_check icon_font_size"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="문자"
                                                        ></i>
                                                        @break
                                                    @case("talk")
                                                        <i class="far fa-comment inflow_check"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="카톡"
                                                        ></i>
                                                        @break
                                                    @case("channel")
                                                        <i class="far fa-comments inflow_check"
                                                           data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="click focus hover" data-bs-placement="top" data-bs-content="플친"
                                                        ></i>
                                                        @break
                                                @endswitch
                                            <br>
                                            <span class="deli_date span_date">{{$order->delivery_date}} {{$order->delivery_time}}
                                            </span>
                                            @if(!empty($order->admin_memo))
                                                    <i class="mdi mdi-note-text-outline memo_check"></i>
                                                @endif
                                            </span>
                                            </div>
                                        </td>
                                        <!-- 주문자/연락처 -->
                                        <td class="center">
                                            <p class="gs_name"
                                               data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->orderer_name}}"
                                               onclick="clipBoardCopy(event)">
                                                {{$order->orderer_name}}</p>
                                            <span class="cursor_p" onclick="send_SMS('{{$order->order_idx}}', '{{$order->brand_type_code}}')">
                                                @if(empty($order->orderer_phone))
                                                    {{$order->orderer_tel}}
                                                @else
                                                    {{$order->orderer_phone}}
                                                @endif
                                            </span>
                                        </td>
                                        <!-- 받는분/연락처 -->
                                        <td class="center"><p class="gs_name"
                                                              data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->receiver_name}}"
                                                              onclick="clipBoardCopy(event)">
                                                {{$order->receiver_name}}</p>
                                            @if(empty($order->receiver_phone))
                                                {{$order->receiver_tel}}
                                            @else
                                                {{$order->receiver_phone}}
                                            @endif
                                        </td>
                                        <!-- 주문상품/결제금액 -->
                                        <td class="center"><a href='javascript:void(0);' onclick="market_open('{{ App\Utils\Common::get_item_url($order->mall_code, $order->brand_type_code) ?? ''}}{{$order->open_market_goods_url}}');"><p class="gs_name">{{$order->goods_name}}</p></a><p class="amount">{{number_format((int)$order->total_amount - (int)$order->discount_amount)}}원</p></td>
                                        <!-- 배송지/보내는분 -->
                                        <td class="center">
                                            <p class="addr cursor_p"
                                               data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery_address}}"
                                               onclick="clipBoardCopy(event)"
                                            >
                                                {{$order->delivery_address}}
                                            </p>
                                            <p class="ribbon_left cursor_p" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$order->delivery_ribbon_left}}" onclick="clipBoardCopy(event)">{{$order->delivery_ribbon_left}}</p>
                                        </td>
                                        <!-- 결제수단 -->
                                        <td class="center">
                                            @if($order->payments->isEmpty())
                                                <p class="state_p {{$order->payment_type_code}} cursor_p" style="margin: 0 auto;">{{ CommonCodeName($order->payment_type_code) }}</p>
                                            @else
                                                @foreach($order->payments as $payment)
                                                    <div class="payments_area mb-1">
                                                        <span class="state_p {{$payment->payment_type_code}} span_PT" style="margin: 0 auto;">{{ CommonCodeName($payment->payment_type_code) }}</span>
                                                        @if(!empty($payment->deposit_name))
                                                        <span class="deposit_name_text ms-1"
                                                              data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="{{$payment->deposit_name}}"
                                                              onclick="clipBoardCopy(event)">{{ $payment->deposit_name }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach

                                            @endif
                                        </td>
                                        <!-- 결제상태/배송상태 -->
                                        <td class="center">
                                            <!-- 결제 상태 취소 요청 -->
                                            @if($order->payment_state_code === 'PSCR' || $order->payment_state_code === 'PSER' || $order->payment_state_code === 'PSRR')
                                                <div class="btn-group">
                                                    <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="javascript:cancel_progress('{{$order->order_idx}}', '{{Str::ucfirst(Auth::user()->name)}}')">취소 처리 중</a></li>
                                                        <li><a class="dropdown-item" href="#" id="cancel_refuse_btn" data-bs-toggle="modal" data-bs-target="#cancel_refuse" data-number="{{ $order-> order_number}}">주문진행</a></li>
                                                        <li><a class="dropdown-item" href="#" id="cancel_complete_btn" data-bs-toggle="modal" data-bs-target="#cancel_complete" data-state="{{ $order -> payment_state_code }}" data-number="{{ $order-> order_number}}">취소완료</a></li>
                                                    </ul>
                                                </div>
                                            @else
                                                <p class="state_p {{$order->payment_state_code}}">{{CommonCodeName($order->payment_state_code)}}</p>
                                            @endif

                                            <div class="btn-group">
                                                <p class="state_p mt-1 {{$order->delivery_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->delivery_state_code)}}</p>
                                                @if($order->payment_state_code === "PSCC" || $order->payment_state_code === "PSUD")
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" data-index="{{$order->order_idx}}" href="" onclick="change_deli_state(event,'DLCC', '{{Auth::user()->name}}')">배송취소 상태변경</a></li>
                                                    </ul>
                                                @endif
                                            </div>
                                        </td>
                                        <!-- 담당자 -->
                                        <td class="center" id="send_name{{$order->order_idx}}">{{$order->send_name}}</td>
                                        <!-- 전송 -->
                                        <td class="center" id="send_area{{$order->order_idx}}">
                                            @if($order -> brand_type_code === 'BTCS' || $order -> brand_type_code === 'BTFC')
                                                @if($order->is_balju === 1)
                                                    <span>완료</span>
                                                @elseif($order->is_credit() && $order->delivery_state_code!=="DLDN" )
                                                    <button class="btn btn-primary btn-soft-primary btn-sm" onclick="send_intranet('{{ $order->order_idx }}');">발주</button>
                                                @elseif($order->payment_state_code === 'PSDN' && $order->delivery_state_code!=="DLDN")
                                                    <button class="btn btn-primary btn-soft-primary btn-sm" onclick="send_intranet('{{ $order->order_idx }}');">발주</button>
                                                @else
                                                    <div></div>
                                                @endif
                                            @else
                                                @if($order->is_balju === 1)
                                                    <span>완료</span>
                                                @elseif(!($order->payment_state_code === 'PSDN' || $order->payment_type_code === 'PTDP'))
                                                    <div></div>
                                                @else
                                                    <button id="send_btn{{$order->order_idx}}" class="btn btn-primary btn-soft-primary btn-sm" onclick="nr_send(event,'{{ $order->order_idx }}');">전송</button>
                                                @endif
                                            @endif
                                        </td>
                                        <!-- 배송사진 -->
                                        <td class="center" style="max-width: 110px;">
                                            @if(!empty($order->delivery_insuName))
                                                <i class="uil-user etc_icon icon_insu" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                                   data-bs-placement="left" data-bs-content="{{$order->delivery_insuName}}"></i>
                                            @endif
                                            @if(!empty($order->delivery_photo))
                                                <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                                   data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo }}');"></i>
                                            @endif
                                            @if(!empty($order->delivery_photo2))
                                                <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                                   data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo2 }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo2 }}');"></i>
                                            @endif
                                            @if(!empty($order->delivery_photo3))
                                                <i class="uil-truck etc_icon icon_photo" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                                                   data-bs-placement="left" data-bs-content="<img src='{{ $order->delivery_photo3 }}' alt='배송 사진' width='150px' height='200px'>" onclick="photo_popup('{{ $order->delivery_photo3 }}');"></i>
                                            @endif
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
            <script src="{{asset('assets/js/order/order-index.js')}}"></script>
            <script src="{{asset('assets/js/order/order-list-detail.js')}}"></script>
            <script>
                function order_detail(order_idx){
                    var url = './order-detail/'+order_idx;

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




