@extends('layouts.master')
@section('title')
    미수현황
@endsection
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <link href="{{ asset('/assets/css/order/order-index.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/order/transaction/outstanding-transaction.css') }}" rel="stylesheet">
    @include('order.modal.order-index-modal')
    @if(session('alert'))
        <script>
            showToast('수정 완료');
        </script>
    @endif
    <div class="row">
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
                                                    <select class="form-select" name="search">
                                                        <option value="all"                  {{ request()->search==="all"                  ? "selected":""}}>전체</option>
                                                        <option value="od_id"                {{ request()->search==="od_id"                ? "selected":""}}>주문번호</option>
                                                        <option value="order_number"         {{ request()->search==="order_number"         ? "selected":""}}>쇼핑몰주문번호</option>
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
                                                    <span class="input-group-text">주문일</span>
                                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{request()->start_date ?? now()->subYear()->format('Y-m-d') }}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                                    <span class="input-group-text input-in-text">~</span>
                                                    <input type="date" class="form-control" id="end_date" name='end_date' value="{{request()->end_date ?? $commonDate['today']}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
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
{{--                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite')">하이라이트</button>--}}
{{--                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="operate_orders('highlite-off')">하이라이트 제거</button>--}}
{{--                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="location.href='{{url("order/ecommerce_orders")."?is_highlight=1"}}'">하이라이트만 보기</button>--}}
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="select_orders_view()">선택주문만 보기</button>
                        </div>
                        <div class="end_btns">
{{--                            <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_popup()">엑셀 일괄 다운로드</button>--}}
{{--                            <button type="button" class="btn btn-outline-success btn-sm" onclick="excel_download()">엑셀 다운로드</button>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
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




