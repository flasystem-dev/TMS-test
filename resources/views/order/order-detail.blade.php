@extends('layouts.master')
@section('title')
    주문서
@endsection
@section('css')
@endsection
@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<script src="https://pg-web.nicepay.co.kr/v3/common/js/nicepay-pgweb.js" type="text/javascript"></script>
<link href="{{ asset('/assets/css/order/order-detail.css') }}" rel="stylesheet">
<link href="{{ URL::asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet">
<script src="{{ URL::asset('/assets/libs/select2/select2.min.js') }}"></script>
@inject('DB', 'Illuminate\Support\Facades\DB')
@include('order.modal.order-detail-modal')
@if(session('update'))
    <script>
        showToast('수정 완료');
    </script>
@endif
    <form name="order_update_form" id="order_update_form" action="{{url('order/order-update')}}" method="post">
    @csrf
    <input type="hidden" value="{{$order->order_idx}}" name="order_idx" id="order-idx">
    <input type="hidden" value="{{$order->brand_type_code}}" id="brand">
    <input type="hidden" value="{{$order->mall_code}}" id="vendor_idx">
    <div class="top_fixed">
        <span class="brand_type {{$order->brand_type_code}}" >{{BrandAbbr($order->brand_type_code)}}</span>
        <!-- 벤더 메뉴 -->
        @if($order->brand_type_code==="BTCS" || $order->brand_type_code==="BTFC")
            <a href='javascript:void(0);' data-bs-toggle="dropdown" aria-expanded="false"><span class="mall_type">{{ $order->channel_name }}</span></a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item order_dropdown_menu" href="#" data-bs-toggle="modal" data-bs-target="#change_vendor_modal">사업자 변경</a></li>
                <li><a class="dropdown-item order_dropdown_menu" href="#" data-bs-toggle="modal" data-bs-target="#change_vendor_modal">거래처 변경</a></li>
            </ul>
        @else
            <a href='javascript:void(0);' onclick="market_open('{{$order->admin_url}}');"><span class="mall_type {{$order->mall_code}}">{{CommonCodeName($order->mall_code) ?? "브랜드몰"}}</span></a>
        @endif
        @if($order -> order_quantity > 1)<span class="mall_type border-danger bg-danger text-light px-3">{{ $order -> order_quantity }}건 중 {{ $order -> sub_index() }}번</span> @endif

        <span onclick="make_sendBtn('{{ $order->order_idx }}', '{{$order->brand_type_code}}');" class="mall_type" style="color: #000000; cursor: pointer;">전송 버튼생성</span>
        <span class="mall_type cursor_p" style="color: #478379FF;" data-bs-toggle="modal" data-bs-target="#balju_amount_area">발주 정보</span>
        <span class="mall_type cursor_p" style="color: #4b93e7;" onclick="open_copyForm();">주문서 복사</span>
        @if(Auth::user() -> auth == 10)
            <span onclick="delete_order('{{ $order->order_idx }}');" class="mall_type ms-5" style="color: #ff0000; cursor: pointer;">주문 삭제</span>
        @endif
    </div>

    <div class="top_fixed_right" style="white-space: nowrap;">
        @if($order->playauto_connect())
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-info waves-effect mx-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >플레이오토</button>
                <div class="dropdown-menu" style="">
                    <a class="dropdown-item" href="javascript:playauto_send('delivery_send');">배송중 재전송</a>
                </div>
            </div>
        @else
            <div class="dropdown d-inline-block">
                <button type="button" class="btn btn-danger waves-effect mx-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >플레이오토 연결실패</button>
                <div class="dropdown-menu" style="">
                    <a class="dropdown-item" href="javascript:playauto_send('connect');">연결 시도</a>
                </div>
            </div>
        @endif

        @if(Auth::user()->auth>2)
        <div class="btn-group">
            <button type="button" class="btn btn-warning waves-effect" data-bs-toggle="modal" data-bs-target="#add_payment_modal">결제추가</button>
        </div>
        @endif

        <div class="btn-group">
            <button type="button" class="btn btn-secondary waves-effect waves-light mx-1" data-bs-toggle="modal" data-bs-target="#talk_modal_area">알림톡 v2</button>
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-secondary waves-effect">
                <span id="settle_title">알림톡</span>
            </button>
            <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu" style="">
                <a class="dropdown-item" href="javascript:send_ats('order_check','주문내용확인',{{$order->order_idx}}, {{isset($order->payments[0]) ? $order->payments[0]->payment_number : 1}});">주문내용확인</a>
                <a class="dropdown-item" href="javascript:send_ats('pay_complete','결제완료',{{$order->order_idx}},   {{isset($order->payments[0]) ? $order->payments[0]->payment_number : 1}});">결제완료</a>
                <a class="dropdown-item" href="javascript:send_ats('VA_guide','가상계좌발송',{{$order->order_idx}},    {{isset($order->payments[0]) ? $order->payments[0]->payment_number : 1}});">가상계좌발송</a>
                <a class="dropdown-item" href="javascript:send_ats('deli_photo','배송이미지',{{$order->order_idx}},    {{ isset($order->payments[0]) ?$order->payments[0]->payment_number : 1}});">배송이미지</a>
            </div>
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-primary waves-effect waves-light ms-1" onclick="form_submit();">수정하기</button>
        </div>
    </div>

    <div class="row" style="overflow: auto">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                       <table class="table table-bordered table-responsive">
                           <tr style="background-color: #f5f5f5">
                               <th>주문번호</th>
                               <th>오픈마켓 주문번호</th>
                               <th>상품명
                                   @if($order->brand_type_code=="BTFC" || $order->brand_type_code=="BTCS")
                                   <button type="button" class="btn mall_type product_change_btn" data-bs-toggle="modal" data-bs-target="#product_modal">상품수정</button>
                                   @endif
                               </th>
                               <th>결제상태</th>
                               <th>배송상태</th>
{{--                               <th style="width: 80px">총합금액<br>결제금액</th>--}}
                           </tr>
                           <tr style="text-align: center;vertical-align: middle;">
                               <td>{{$order->od_id}}</td>
                               <td>{{$order->order_number}}</td>
                               <td>
                                   <a href='javascript:void(0);' onclick="market_open('{{$order->item_url}}{{$order->goods_url}}');">
                                       <p class="gs_name_v">{{$order->item->product_name}}<span class="option_text"> ({{number_format($order->item->product_price)}})</span></p>
                                       @if($order->item && $order->item->options->isNotEmpty())
                                           @foreach($order->item->options as $option)
                                               @if($option->is_view)
                                               <p class="gs_name_v option_text">{{$option->option_name . " (+".number_format($option->option_price).")"}}</p>
                                               @endif
                                           @endforeach
                                       @endif
                                   </a>
                               </td>
                               <td>
                                   <div class="btn-group">
                                       @if($order->payment_state_code === 'PSCR' || $order->payment_state_code === 'PSER' || $order->payment_state_code === 'PSRR')
                                           <div class="btn-group">
                                               <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                               <ul class="dropdown-menu">
                                                   <li><a class="dropdown-item" href="#" id="cancel_refuse_btn" data-bs-toggle="modal" data-bs-target="#cancel_refuse" data-number="{{ $order-> order_number}}">주문진행</a></li>
                                                   <li><a class="dropdown-item" href="#" id="cancel_complete_btn" data-bs-toggle="modal" data-bs-target="#cancel_complete" data-state="{{ $order -> payment_state_code }}" data-number="{{ $order-> order_number}}">취소완료</a></li>
                                               </ul>
                                           </div>
                                       @elseif($order->payment_state_code === 'PSUD')
                                           <div class="btn-group">
                                               <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                               <ul class="dropdown-menu">
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSCC')">취소완료 상태변경</a></li>
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSDN')">결제완료 상태변경</a></li>
                                               </ul>
                                           </div>
                                       @elseif($order->payment_state_code === 'PSDN')
                                           <div class="btn-group">
                                               <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                               <ul class="dropdown-menu">
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSCC')">취소완료 상태변경</a></li>
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSUD')">결제대기 상태변경</a></li>
                                               </ul>
                                           </div>
                                       @elseif($order->payment_state_code === 'PSOC')
                                           <div class="btn-group">
                                               <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p>
                                               <ul class="dropdown-menu">
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSDN')">결제완료 상태변경</a></li>
                                                   <li><a class="dropdown-item" href="javascript:change_state('PSCC')">취소완료 상태변경</a></li>
                                               </ul>
                                           </div>
                                       @else
                                           <p class="state_p {{$order->payment_state_code}}">{{CommonCodeName($order->payment_state_code)}}</p>
                                       @endif
                                   </div>
                               </td>
                               <td>
                                   <div class="btn-group">
                                       <p class="state_p {{$order->delivery->delivery_state_code}} dropdown-toggle dropdown-toggle-split cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($order->delivery->delivery_state_code)}}</p>
                                       @if($order->payment_state_code === "PSCC" || $order->payment_state_code === "PSUD")
                                       <ul class="dropdown-menu">
                                           <li><a class="dropdown-item" href="javascript:change_deli_state('DLCC')">배송취소 상태변경</a></li>
                                       </ul>
                                       @endif
                                   </div>
                               </td>
                           </tr>
                       </table>
                    </div><!-- end row -->

                    @if($order->payments->isNotEmpty())
                    <div class="row">
                        <table class="table table-bordered payment_table">
                            <tr style="background-color: #f5f5f5">
                                <th style="width: 52px">번호</th>
                                <th style="width: 85px">결제수단</th>
                                <th style="width: 100px">금액</th>
                                <th style="min-width: 120px">입금자명</th>
                                <th style="max-width: 150px">증빙/결제일</th>
{{--                                <th style="max-width: 150px">결제일</th>--}}
                                <th>결제 메모</th>
                                <th style="width: 65px">기타</th>
                            </tr>
                            @foreach($order->payments as $payment)
                                <tr class="payment_row ">

                                    <!-- 번호 -->
                                    <td data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus hover" data-bs-placement="right" data-bs-content="{{$payment->payment_pid}}">
                                        <p class="m-0 {{ empty($payment->payment_pid)? "":"cursor_p" }}" @if(!empty($payment->payment_pid)) data-data="{{$payment->payment_pid}}" onclick="copyData(event)" @endif>{{$payment->payment_number}}</p>
                                    </td>
                                    <!-- 결제수단 -->
                                    <td>
                                        <div class="dropdown">
                                            <p class="state_p mb-1 {{$payment->payment_type_code}} cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($payment->payment_type_code)}}</p>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:receipt_popup('{{$payment->receipt_url()}}')">영수증</a></li>
                                                @if(empty($payment->payment_pg))
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#change_payment_type" data-number="{{$payment->payment_number}}">결제수단 변경</a></li>
                                                @elseif($payment->payment_type_code==="PTVA")
                                                    <li><a class="dropdown-item" href="#" data-bs-container="body" data-bs-trigger="hover" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="{{$payment->get_VA_info()}}" data-data="{{$payment->get_VA_info()}}" onclick="copyData(event)">가상계좌 정보</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="dropdown">
                                            <p class="state_p {{$payment->payment_state_code}} cursor_p" data-bs-toggle="dropdown" aria-expanded="false">{{CommonCodeName($payment->payment_state_code)}}</p>
                                            <ul class="dropdown-menu">
                                                @if(empty($payment->payment_key))
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#change_payment_state" data-number="{{$payment->payment_number}}" data-state="{{$payment->payment_state_code}}">결제상태 변경</a></li>
                                                @endif
                                                @if(!empty($payment->payment_key) && $payment->payment_state_code==="PSDN")
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#refund_modal" data-number="{{$payment->payment_number}}" style="background-color: #fd8b8b; color: #fff">환불</a></li>
{{--                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#refund_modal" data-number="{{$payment->payment_number}}" style="background-color: #999999; color: #fff">수기 환불</a></li>--}}
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                    <!-- 금액 -->
                                    <td>
                                        <input type="number" name="payment_amount" class="form-control payment_amount" value="{{$payment->payment_amount}}" aria-label="payment_amount" {{!empty($payment->payment_pg) || $payment->payment_state_code==="PSCC" ? "disabled":""}}>
                                        @if($payment->refund_amount!==0)
                                        <div class="payment_refund"><span class="refund_text">환불</span><span class="refund_amount">{{number_format($payment->refund_amount)}}</span></div>
                                        @endif
                                    </td>
                                    <!-- 입금자명 -->
                                    <td>
                                        <input type="text" class="form-control" name="deposit_name" value="{{$payment->deposit_name}}" aria-label="deposit_name"
                                               data-bs-container="body" data-bs-trigger="hover" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="{{$payment->deposit_name}}">
                                        <div class="form-check advance_payment_container">
                                            <input type="checkbox" value="1" class="form-check-input" name="advance_payment" id="advance_payment{{$payment->payment_number}}" {{ $payment->advance_payment ? "checked" : "" }}>
                                            <label class="form-check-label fw-bold" for="advance_payment{{$payment->payment_number}}">사업자 선결제</label>
                                        </div>
                                    </td>
                                    <!-- 결제일 / 증빙 -->
                                    <td>
                                        <div>
                                            <div class="document_area mb-1">
                                                <input type="datetime-local" name="payment_time" class="form-control datepicker payment_time_input" value="{{$payment->payment_time ?? ""}}">
                                            </div>
                                            <div class="document_area">
                                                <select class="form-select document_select" name="document_type" aria-label="document_type">
                                                    <option value="">- 증빙선택 -</option>
                                                    <option value="PMCR" {{$payment->document_type==="PMCR"? "selected": ""}}>현금영수</option>
                                                    <option value="PMPE" {{$payment->document_type==="PMPE"? "selected": ""}}>지출증빙</option>
                                                    <option value="PMVI" {{$payment->document_type==="PMVI"? "selected": ""}}>자진발급</option>
                                                    <option value="PMIB" {{$payment->document_type==="PMIB"? "selected": ""}}>계산서</option>
                                                </select>
                                                <select class="form-select document_select" name="is_publish" aria-label="is_publish">
                                                    <option value="0" {{$payment->is_publish ? "":"selected"}}>미발행</option>
                                                    <option value="1" {{$payment->is_publish ? "selected":""}}>발행</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- 결제 메모 -->
                                    <td><textarea class="form-control" name="payment_memo" style="height: 40px; font-size: 12px;" aria-label="payment_memo">{{$payment->payment_memo}}</textarea></td>
                                    <td>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-num="{{$payment->payment_number}}" onclick="update_payment(event)">수정</button>
                                        @if($payment->payment_state_code!=="PSDN")
                                        <button type="button" class="btn mt-1 btn-outline-danger btn-sm" data-num="{{$payment->payment_number}}" onclick="delete_payment(event)">삭제</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div><!-- end row -->
                    @endif
                    <div class="row mb-3">
                        <div class="col-6 p-0">
                            <table class="table table-bordered payment_table payment_total_tbl mb-0">
                                <tr style="background-color: #f5f5f5">
                                    <th>상품총액</th>
                                    <th>관리자할인</th>
                                    <th>합계</th>
                                </tr>
                                <tr class="pay_amount_check">
                                    <td>{{number_format($order->item->item_total_amount)}}</td>
                                    <td>- <input type="number" name="admin_discount" class="form-control pay_amount_check" style="text-align: end; padding: 2px; display: inline-block; width: 90%;" value="{{$order->admin_discount}}"></td>
                                    <td class="fw-bold">{{number_format($order->total_amount)}}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table table-bordered payment_table payment_total_tbl mb-0">
                                <tr style="background-color: #f5f5f5">
                                    <th>결제금액</th>
                                    <th>미수금액</th>
                                    <th>결제총액</th>
                                </tr>
                                <tr class="pay_amount_check">
                                    <td>{{number_format($order->pay_amount)}}</td>
                                    <td>{{number_format($order->misu_amount)}}</td>

                                    <td class="fw-bold">{{number_format($order->pay_amount+$order->misu_amount)}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                @if($order->brand_type_code==="BTFC"||$order->brand_type_code==="BTCS")
                <div class="row">
                    <div class="col-3 px-1">
                        <p class="balju_price_text"><span style="color: #0a53be;">사업자</span>발주</p>
                        <input type="text" class="form-control balju_price_input" value="{{number_format($order->vendor_amount)}}" disabled>
                    </div>
                    <div class="col-3 px-1">
                        <p class="balju_price_text"><span style="color: #0a53be;">사업자</span>옵션</p>
                        <input type="text" class="form-control balju_price_input" value="{{number_format($order->item->vendor_options_amount)}}" disabled>
                    </div>
                    <div class="col-3 px-1">
                        <p class="balju_price_text"><span style="color: #c7c700;">화원사</span>발주</p>
                        <input type="text" class="form-control balju_price_input" value="{{number_format($order->balju_amount)}}" disabled>
                    </div>
                    <div class="col-3 px-1">
                        <p class="balju_price_text"><span style="color: #c7c700;">화원사</span>옵션</p>
                        <input type="text" class="form-control balju_price_input" value="{{number_format($order->item->balju_options_amount)}}" disabled>
                    </div>
                </div>
                @endif
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">주문자 정보</h4>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">이름</span>
                        <input type="text" class="form-control" name="orderer_name" id="orderer_name"  value="{{$order->orderer_name}}">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">핸드폰</span>
                        <input type="text" class="form-control" name="orderer_phone"  value="{{$order->orderer_phone}}" oninput="auto_hyphen(event)">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">일반전화</span>
                        <input type="text" class="form-control" name="orderer_tel" value="{{$order->orderer_tel}}" oninput="auto_hyphen(event)">
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div> <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">받는사람 정보</h4>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="receiver_name">이름</span>
                        <input type="text" class="form-control" name="receiver_name" value="{{$order->delivery->receiver_name}}">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="receiver_phone">핸드폰</span>
                        <input type="text" class="form-control" name="receiver_phone" value="{{$order->delivery->receiver_phone}}" oninput="auto_hyphen(event)">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="receiver_tel">일반전화</span>
                        <input type="text" class="form-control" name="receiver_tel" value="{{$order->delivery->receiver_tel}}" oninput="auto_hyphen(event)">
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="delivery_info_top">
                        <div class="deli_top1">
                            <h4 class="card-title mb-4">배송정보</h4>
                        </div>
                        <div class="deli_top2">
                            <div class="deli_info2_area">
                                <div class="deli_info2_insu">
                                    @if(!empty($order->delivery->delivery_insuName))
                                        <span class="insu_text">인수자 :</span>
                                        <span class="">{{$order->delivery->delivery_insuName}}</span>
                                    @endif
                                </div>
                                <div class="deli_info2_photo">
                                    @if(!empty($order->delivery->delivery_photo))
                                        <i class="uil-truck etc_icon" onclick="photo_popup('{{ $order->delivery->delivery_photo }}');"></i>
                                    @endif
                                    @if(!empty($order->delivery->delivery_photo2))
                                        <i class="uil-truck etc_icon" onclick="photo_popup('{{ $order->delivery->delivery_photo2 }}');"></i>
                                    @endif
                                    @if(!empty($order->delivery->delivery_photo3))
                                        <i class="uil-truck etc_icon" onclick="photo_popup('{{ $order->delivery->delivery_photo3 }}');"></i>
                                    @endif
                                </div>
                            </div>
                            @if(Str::contains("@@", $order->delivery->delivery_insuName))
                            <p class="fw-bold bg-danger text-light m-0 text-center">인수자 정보를 확인해주세요!</p>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="delivery_address">배송주소</span>
                        <input type="text" class="form-control" name="delivery_address" value="{{$order->delivery->delivery_address}}">
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="delivery_date">배송일</label>
                                <input type="text" id="delivery_date" class="form-control datepicker" name="delivery_date" style='width:100px;' value="{{$order->delivery->delivery_date}}">
                            </div>
                        </div>
                        <div class=" col-md-6">
                            <div class="input-group">
                                <span class="input-group-text" id="delivery_time">배송시간</span>
                                <input type="text" class="form-control" name="delivery_time" value="{{$order->delivery->delivery_time}}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-12 bg-light mb-2 py-2 rounded-2">
                            <table  style="text-align: center">
                                <tr>
                                    <td><button type="button" onclick="ribbon_msg('祝 結婚');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 結婚</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 華婚');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 華婚</button></td>
                                    <td><button type="button" onclick="ribbon_msg('謹弔');" class="btn btn-outline-secondary btn-sm freq_btn w44">謹弔</button></td>
                                    <td><button type="button" onclick="ribbon_msg('弔儀');" class="btn btn-outline-secondary btn-sm freq_btn w44">弔儀</button></td>
                                    <td><button type="button" onclick="ribbon_msg('賻儀');" class="btn btn-outline-secondary btn-sm freq_btn w44">賻儀</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 發展');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 發展</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 開業');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 開業</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 榮轉');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 榮轉</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 昇進');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 昇進</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 生日');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 生日</button></td>
                                    <td><button type="button" onclick="ribbon_msg('祝 古稀');" class="btn btn-outline-secondary btn-sm freq_btn w60">祝 古稀</button></td>
                                    <td><button type="button" onclick="ribbon_msg('삼가 故人의 冥福을 빕니다');" class="btn btn-outline-secondary btn-sm freq_btn w165">삼가 故人의 冥福을 빕니다</button></td>
                                </tr>
                                <tr>
                                    <td><button type="button" onclick="ribbon_msg('축 결혼');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 결혼</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 화혼');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 화혼</button></td>
                                    <td><button type="button" onclick="ribbon_msg('근조');" class="btn btn-outline-secondary btn-sm freq_btn w44">근조</button></td>
                                    <td><button type="button" onclick="ribbon_msg('조의');" class="btn btn-outline-secondary btn-sm freq_btn w44">조의</button></td>
                                    <td><button type="button" onclick="ribbon_msg('부의');" class="btn btn-outline-secondary btn-sm freq_btn w44">부의</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 발전');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 발전</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 개업');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 개업</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 영전');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 영전</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 승진');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 승진</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 생일');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 생일</button></td>
                                    <td><button type="button" onclick="ribbon_msg('축 고희');" class="btn btn-outline-secondary btn-sm freq_btn w60">축 고희</button></td>
                                    <td><button type="button" onclick="ribbon_msg('삼가 고인의 명복을 빕니다');" class="btn btn-outline-secondary btn-sm freq_btn w165">삼가 고인의 명복을 빕니다</button></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text" id="delivery_ribbon_left">보내는분(좌측)</span>
                                <input type="text" class="form-control" name="delivery_ribbon_left" value="{{$order->delivery->delivery_ribbon_left}}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text">경조사어(우측)</span>
                                <input type="text" class="form-control" name="delivery_ribbon_right" id="delivery_ribbon_right" value="{{$order->delivery->delivery_ribbon_right}}">
                            </div>
                        </div>
                        <div class="col-md-2 form-switch mt-2">
                            <input type="checkbox" class="form-check-input" name="ribbon_exist" value="Y" checked="checked" id="ribbon_exist">
                            <label class="form-check-label" for="ribbon_exist">리본문구있음</label>
                        </div>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="delivery_card">카드메세지</span>
                        <input type="text" class="form-control" name="delivery_card" value="{{$order->delivery->delivery_card}}">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text" id="delivery_message">배송요청사항</span>
                        <input type="text" class="form-control" name="delivery_message" value="{{$order->delivery->delivery_message}}">
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="input-group mb-1 rounded radio_text">
                                <span class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0 inflow_text">유입 정보</span>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_mall" value="mall" {{$order->inflow ==="mall"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_mall">몰</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_call" value="call" {{$order->inflow==="call"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_call">전화</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_sms" value="sms" {{$order->inflow==="sms"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_sms">문자</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_talk" value="talk" {{$order->inflow==="talk"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_talk">카톡</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_channel" value="channel" {{$order->inflow==="channel"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_channel">카톡채널</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="inflow" required id="inflow_type_etc" value="etc" {{$order->inflow==="etc"? "checked" : ""}}>
                                    <label class="form-check-label" for="inflow_type_etc">기타</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-1 rounded radio_text">
                                <span class="input-group-text me-3 border-top-0 border-bottom-0 border-start-0 inflow_text">알림 전송</span>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="is_alim" required id="is_alim_sms" value="sms" {{$order->is_alim ==="sms"? "checked" : ""}}>
                                    <label class="form-check-label" for="is_alim_sms">SMS</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="is_alim" required id="is_alim_talk" value="talk" {{$order->is_alim ==="talk"? "checked" : ""}}>
                                    <label class="form-check-label" for="is_alim_talk">알림톡</label>
                                </div>
                                <div class="form-check form-check-inline mt-1">
                                    <input class="form-check-input" type="radio" name="is_alim" required id="is_alim_none" value="none" {{$order->is_alim ==="none"? "checked" : ""}}>
                                    <label class="form-check-label" for="is_alim_none">전송 안함</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed">
        <div class="card {{$order->options_parse_yn==='N'? "red_zone": ""}}">
            <div class="card-body">
                <h4 class="card-title">주문 옵션</h4>
                <div class="display_option">
                    @if($order -> options_type === "O")
                        @php $img_data = $DB::table('order_data_image') -> select('filename') -> where('order_idx','=',$order -> order_idx) -> first(); @endphp
                        <img src="{{ $img_data -> filename }}" alt="사진" width="50" height="50" class="position-absolute top-0 end-0 mt-3 me-4" onclick="popup_IMG('{{ $img_data -> filename }}');"><br>
                        @if(!empty($order->options_string_display))
                            <p class="ms-1 mb-0">{{ $order->options_string_display }}</p>
                        @endif
                    @else
                        @if($order->option_str_arr)
                            @foreach($order->option_str_arr as $option)
                                <span class="number_option">{{ $loop->index+1}}/{{$order->order_quantity}} 주문건 옵션</span>
                                {{$option}}
                                @if(!$loop->last)
                                    <hr class="option_hr"/>
                                @endif
                            @endforeach
                        @else
                            @if($order->event_url)
                                <p class="ms-1 mb-0">주문정보 URL 주소</p>
                                <p class="ms-1 mb-0">{{$order->event_url->url}}</p>
                                <button type="button" class="btn btn-outline-primary" onclick="popup_URL('{{$order->event_url->url}}');">URL</button>
                            @endif
                            {{$order->options_string_display}}
                            <hr>
                            {{$order->options_string}}
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">CS 기록</h4>
                <div class="mb-3">
                    <label class="form-label">클레임</label>
                    <div>
                        <textarea class="form-control" name="order_claim_memo">{{$order->order_claim_memo}}</textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">관리자메모</label>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-5" onclick="insert_time('date');">날짜</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-1" onclick="insert_time('time');">시간</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-1" onclick="insert_time('datetime');">날짜+시간</button>
                    <div>
                        <textarea class="form-control" name="admin_memo">{{$order->admin_memo}}</textarea>
                    </div>
                </div>
                {{--                    <div>--}}
                {{--                        <div class="input-group">--}}
                {{--                            <span class="input-group-text" id="send_name">담당자</span>--}}
                {{--                            <input type="text" class="form-control" name="send_name" value="{{$order->send_name}}">--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table style="">
                    <tbody><tr>
                        <td align="center"><p class="all_price_title">주문일</p></td>
                        <td align="center" class="all_price">{{$order->order_time}}</td>
                    </tr>
                    <tr>
                        <td align="center"><p class="all_price_title">결제일</p></td><td align="center" class="all_price">{{$order->payment_time}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
@endsection
@section('script')
    <script src="{{ URL::asset('/assets/libs/parsleyjs/parsleyjs.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/bootstrap-toasts.init.js') }}"></script>
    <script src="{{ URL::asset('assets/js/order/order-detail.js') }}?v={{ time() }}"></script>

    @if($order->total_amount !== $order->pay_amount+$order->misu_amount)
    <script>
        $(document).ready(function(){
            $('.pay_amount_check').addClass('need_check')
        });
    </script>
    @endif

@endsection
