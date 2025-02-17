@extends('layouts.master')
@section('title')
    증빙관리
@endsection
@section('content')
<link rel="stylesheet" href="{{ URL::asset('/assets/css/document/document.css') }}">
@include('Document.modal.document-orders-modals')

    <div class="row">
        <div class="col-xl-12">
            <div class="card ">
                <div class="card-body spacer_zero">
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button {{$col_style}}" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                    <strong>검색 필터 열기</strong>
                                </button>
                            </h2>
                            <form method="get" action="?">
                            <div id="flush-collapseTwo" class="accordion-collapse collapse {{$show_box}}" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <div class="input-group mb-3">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="sw_1_title">
                                                    {{ request()->sw_1_view ?? "1차 조회 항목" }}
                                                </span>
                                            </button>
                                            <input type="hidden" id="sw_1" name="sw_1" value="{{request()->sw_1 ?? "all"}}">
                                            <input type="hidden" id="sw_1_view" name="sw_1_view" value="{{request()->sw_1_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','1차 조회 항목','all');">1차 조회 항목</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문번호','order_number');">주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자','orderer_name');">주문자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','주문자휴대폰','orderer_phone');">주문자휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분','receiver_name');">받는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','받는분휴대폰','receiver_phone');">받는분휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','보내는분','delivery_ribbon_left');">보내는분 문구</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','관리자메모','admin_memo');">관리자메모</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','메세지/요구사항','delivery_message');">메세지/요구사항</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_1','담당자','send_name');">담당자</a>
                                            </div>
                                        </div>
                                        <input class="form-control col-md-1  me-4" name="word1" type="text" id="selectedName" value="{{request()->word1}}">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="sw_2_title">
                                                    {{ request()->sw_2_view ?? "2차 조회 항목" }}
                                                </span>
                                            </button>
                                            <input type="hidden" id="sw_2" name="sw_2" value="{{request()->sw_2 ?? "all"}}">
                                            <input type="hidden" id="sw_2_view" name="sw_2_view" value="{{request()->sw_2_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','2차 조회 항목','all');">2차 조회 항목</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문번호','order_number');">주문번호</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문자','orderer_name');">주문자</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','주문자휴대폰','orderer_phone');">주문자휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','받는분','receiver_name');">받는분</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','받는분휴대폰','receiver_phone');">받는분휴대폰</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','관리자메모','admin_memo');">관리자메모</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','메세지/요구사항','delivery_message');">메세지/요구사항</a>
                                                <a class="dropdown-item" href="javascript:select_btn('sw_2','담당자','send_name');">담당자</a>
                                            </div>
                                        </div>
                                        <input class="form-control col-md-2  me-4" name="word2" type="text" id="selectedName" value="{{request()->word2}}">
                                        <button style="border-radius:3px;" class="btn btn-secondary waves-effect waves-light col-md-2">검색하기</button>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="date_type_title">
                                                    {{request()->date_type_view ?? "기간검색항목"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="date_type" name="date_type" value="{{request()->date_type}}">
                                            <input type="hidden" id="date_type_view" name="date_type_view" value="{{request()->date_type_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','기간검색항목','');">기간검색항목</a>
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','주문접수일','playauto_date');">주문수집일</a>
                                                <a class="dropdown-item" href="javascript:select_btn('date_type','배송요청일','delivery_date');">배송요청일</a>
                                            </div>
                                        </div>
                                        <div id="datepicker1">
                                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{request()->start_date}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                        </div>
                                        <strong class="mx-2 mt-2">~</strong>
                                        <div id="datepicker2" class="mh-10">
                                            <input type="date" class="form-control col-md-2" id="end_date" name='end_date' value="{{request()->end_date}}" data-date-format="yyyy-mm-dd" data-date-container='#datepicker1' data-provide="datepicker">
                                        </div>
                                        <button type="button" class="btn btn-light ms-4" onclick="dateSel('어제');">어제</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('오늘');">오늘</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('내일');">내일</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('이번주');">이번주</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('이번달');">이번달</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('지난주');">지난주</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('지난달');">지난달</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('3개월');">3개월</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('6개월');">6개월</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('금년');">금년</button>
                                        <button type="button" class="btn btn-light" onclick="dateSel('전년');">전년</button>
                                    </div>
                                    <div class="input-group">
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="mall_code_title">
                                                    {{request()->mall_code_view?? "오픈마켓선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="mall_code" name="mall_code" value="{{request()->mall_code}}">
                                            <input type="hidden" id="mall_code_view" name="mall_code_view" value="{{request()->mall_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','오픈마켓선택','');">오픈마켓선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','옥션','MLAC');">옥션</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','G마켓','MLGM');">G마켓</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','11번가','ML11');">11번가</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','스마트스토어','MLNV');">스마트스토어</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','톡스토어','MLKK');">톡스토어</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','간편주문','MLMW');">간편주문</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','수기주문','MLPL');">수기주문</a>
                                                <a class="dropdown-item" href="javascript:select_btn('mall_code','쿠팡','MLCP');">쿠팡</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="payment_state_code_title">
                                                    {{request()->payment_state_code_view ?? "결제상태선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="payment_state_code_status" name="payment_state_code" value="{{request()->payment_state_code}}">
                                            <input type="hidden" id="payment_state_code_view" name="payment_state_code_view" value="{{request()->payment_state_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제상태선택','');">결제상태선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제대기','PSUD');">결제대기</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','결제완료','PSDN');">결제완료</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','취소요청','PSCR');">취소요청</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_state_code','취소완료','PSCC');">취소완료</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="payment_type_code_title">
                                                    {{request()->payment_type_code_view ?? "결제수단선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="payment_type_code" name="payment_type_code" value="{{request()->payment_type_code}}">
                                            <input type="hidden" id="payment_type_code_view" name="payment_type_code_view" value="{{request()->payment_type_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="" >
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','결제수단선택','');">결제수단선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','카드','PTCD');">카드</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','무통장','PTDP');">무통장</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','가상계좌','PTVA');">가상계좌</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','간편결제','PTTD');">간편결제</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','법인후불','PTDP');">법인후불</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','수기','PTMN');">수기</a>
                                                <a class="dropdown-item" href="javascript:select_btn('payment_type_code','외부결제','PTOP');">외부결제</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="delivery_state_code_title">
                                                    {{request()->delivery_state_code_view ?? "배송상태선택" }}
                                                </span>
                                            </button>
                                            <input type="hidden" id="delivery_state_code" name="delivery_state_code" value="{{request()->delivery_state_code}}">
                                            <input type="hidden" id="delivery_state_code_view" name="delivery_state_code_view" value="{{request()->delivery_state_code_view}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송상태선택','');">배송상태선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','미배송','DLUD');">미배송</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송중','DLSP');">배송중</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','배송완료','DLDN');">배송완료</a>
                                                <a class="dropdown-item" href="javascript:select_btn('delivery_state_code','취소주문','DLCC');">취소주문</a>
                                            </div>
                                        </div>
                                        <div class="btn-group col-md-2 me-4">
                                            <button type="button" class="btn btn-light col-md-3 waves-effect">
                                                <span id="receipt_title">
                                                    {{request()->receipt_view ?? "증빙선택"}}
                                                </span>
                                            </button>
                                            <input type="hidden" id="receipt" name="receipt" value="{{request()->receipt}}">
                                            <input type="hidden" id="receipt_view" name="receipt_view" value="{{request()->receipt}}">
                                            <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','증빙선택','');">증빙선택</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','미신청','');">미신청</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','미발행','');">미발행</a>
                                                <a class="dropdown-item" href="javascript:select_btn('receipt','발행','');">발행</a>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="cancel_check" value="1" @checked(request()->cancel_check) id="customSwitch1">
                                                <label class="form-check-label" for="customSwitch1">취소주문포함</label>
                                            </div>
{{--                                                    <div class="form-check form-switch me-4">--}}
{{--                                                        <input type="checkbox" class="form-check-input" name="PSUD_check" value="Y" @checked($search_arr['PSUD_check']) id="customSwitch2">--}}
{{--                                                        <label class="form-check-label" for="customSwitch2">결제대기포함</label>--}}
{{--                                                    </div>--}}
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" name="PTOP_Except" value="1" @checked(request()->PTOP_Except) id="customSwitch3">
                                                <label class="form-check-label" for="customSwitch3">외부결제제외</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->


        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="" class="table table-striped table-bordered "
                           style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th class="th1"></th>
                            <th class="th2">주문인덱스</th>
                            <th class="th3">브랜드<br>채널</th>
                            <th class="th4">수집일<br>배송일</th>
                            <th class="th5">주문자<br>연락처</th>
                            <th class="th6">주문상품<br>결제금액</th>
                            <th class="th7">결제수단<br>결제상태</th>
                            <th class="th8">결제일</th>
                            <th class="th9">영수증</th>
                            <th class="th10">현금영수증</th>
                            <th class="th11">환불처리</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($count>0)
                            @foreach($orders as $order)

                                <tr>
                                    <!-- 번호 -->
                                    <td class="center" style="vertical-align: center;"><input type="checkbox" name="od_id[]"></td>
                                    <td class="center" style="vertical-align: center;">{{$order->order_idx}}
                                        @if($order -> order_quantity > 1)
                                            <p class="new_order" id="order_quantity">({{$order -> sub_idx}}/{{$order -> order_quantity}})</p>
                                        @endif
                                    </td>
                                    <!-- 브랜드 -->
                                    <td class="center">
                                        <p class="brand_type {{$order->brand_type_code}}">{{App\Utils\Common::brand_ini($order->brand_type_code)}}</p>
                                        <!-- 벤더 도메인 연결 -->
                                        @if($order->brand_type_code=="BTCS" || $order->brand_type_code=="BTFC")
                                            @php $vendor = App\Models\Vendor::find($order->mall_code) @endphp
                                            <a href="" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="right" data-bs-content="{{$vendor->mall_name?? ""}}">
                                                <p class="brand_type" style="margin-top: 3px">
                                                    {{$vendor->rep_name ?? "없음"}}
                                                </p>
                                            </a>
                                        @else
                                            <a onclick="open_admin_url('{{App\Models\CodeOfCompanyInfo::adminUrl($order->mall_code, $order->brand_type_code) ?? ''}}')">
                                                <p class="brand_type {{$order->mall_code}}" style="margin-top: 3px">
                                                    {{CommonCodeName($order->mall_code) ?? App\Models\Vendor::find($order->mall_code)->rep_name}}
                                                </p>
                                            </a>
                                            @endif
                                    </td>
                                    <!-- 주문일/배송일 -->
                                    <td class="center">
                                        <span class="simptip-position-bottom simptip-fade" tooltip="{{$order->admin_memo}}" flow="down">
                                        <a style='cursor:pointer' id="order_date" onclick="order_detail('{{$order->order_idx}}');">{{$order->create_ts}}<br>
                                            <p class="deli_date">{{$order->delivery_date}} {{$order->delivery_time}}
                                                @if($order->admin_memo!='')
                                                    <i class="mdi mdi-note-text-outline memo_check"></i>
                                                @endif
                                                </p></a></span>
                                    </td>
                                    <!-- 주문자/연락처 -->
                                    <td class="center">
                                        <p class="orderer_name m-0">{{$order->orderer_name}}</p>
                                        <span>
                                            @if($order->orderer_phone=='')
                                                {{$order->orderer_tel}}
                                            @else
                                                {{$order->orderer_phone}}
                                            @endif
                                        </span>
                                    </td>
                                    <!-- 주문상품/결제금액 -->
                                    <td class="center"><a href='javascript:void(0);' class="w-100" onclick="market_open('{{App\Models\CodeOfCompanyInfo::goodsUrl($order->mall_code, $order->brand_type_code) ?? ''}}{{$order->open_market_goods_url}}');"><p class="gs_name">{{$order->goods_name}}</p></a><p class="amount">{{number_format((int)$order->total_amount - (int)$order->discount_amount)}}원</p></td>
                                    <!-- 결제수단/결제상태 -->
                                    <?php ?>
                                    <td class="center">
                                        <p class="state_p mb-1 {{$order->payment_type_code}}" onclick="receipt_popup('');">{{ CommonCodeName($order -> payment_type_code) }}</p>
                                        <div class="btn-group">
                                            <p class="state_p {{$order->payment_state_code}} dropdown-toggle dropdown-toggle-split <?php if($order->payment_state_code == 'PSCR' || $order->payment_state_code == 'PSER' || $order->payment_state_code == 'PSRR') echo 'cursor_pointer' ?>" <?php if($order->payment_state_code == 'PSCR' || $order->payment_state_code == 'PSER' || $order->payment_state_code == 'PSRR') echo 'data-bs-toggle="dropdown"' ?> aria-expanded="false">{{CommonCodeName($order->payment_state_code)}}</p><br>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:cancel_progress('{{$order->order_idx}}', '{{Str::ucfirst(Auth::user()->name)}}')">취소 처리 중</a></li>
                                                <li><a class="dropdown-item" href="#" id="cancel_refuse_btn" data-bs-toggle="modal" data-bs-target="#cancel_refuse" data-number="{{ $order-> order_number}}">취소 요청 거절</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <!-- 결제일 -->
                                    <td class="center">
                                        <p class="m-0">{{ $order-> payment_date }}</p>
                                    </td>
                                    <!-- 영수증 -->
                                    <td class="center">
                                        @if(!empty($order->payment->payment_receipt_url))
                                            <button class="btn btn-sm btn-outline-success" onclick="popup_receipt('{{ $order->payment -> payment_receipt_url }}')">영수증</button>
                                        @else
                                            <div></div>
                                        @endif
                                    </td>
                                    <!-- 현금영수증 -->
                                    <td class="center">
{{--                                                @if(!empty($order -> bank_info) && $order -> pay_amount > 0 && empty($order -> payment_receipt_url))--}}
{{--                                                    <button class="btn btn-sm btn-outline-info cashReceipt_btn" data-id="{{ $order -> od_id }}" data-bs-toggle="modal" data-bs-target="#cash_receipt">발급하기</button>--}}
{{--                                                @else--}}
                                            <div></div>
{{--                                                @endif--}}

                                    </td>
                                    <!-- 환불처리 -->
                                    <td class="center">
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#complain_progress" data-id="{{ $order -> order_idx }}">환불처리</button>
{{--                                        @if(!$order->payment->isEmpty())--}}
{{--                                            @if($order -> refund_check())--}}
{{--                                            @elseif(!empty($order->payment[0]->refund_handler))--}}
{{--                                                <p class="m-0">환불완료</p>--}}
{{--                                                <p class="m-0">({{ $order->payment[0]->refund_handler }})</p>--}}
{{--                                            @else--}}
{{--                                                <div></div>--}}
{{--                                            @endif--}}
{{--                                        @endif--}}
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
                    {{ $orders->onEachSide(3)->links() }}
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@section('script')
    <script src="{{asset('assets/js/document/document.js')}}"></script>
    <script>
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

        @if($open == 'Y')
            $('#brand_btn').click();
        @endif

        function cancel_progress(order_idx, name ){
            if(confirm("취소 처리 중으로 변경하시겠습니까?")){
                $.ajax({
                    url: "{{ route('cancel-progress') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "order_idx" : order_idx,
                        "send_name" : name
                    },
                    success: function(response) {
                        if(response == "SUCCESS") {
                            alert("처리 완료");
                            // $('#send_name'+order_idx).text(name);
                            location.reload();
                        } else {
                            alert(response);
                        }
                    },
                    error: function (error){
                        console.log(error);
                        alert('에러발생! 개발팀에 문의하세요.');
                    }
                });
            }
        }

        function cancel_refuse(name) {
            let order_number = $('#cancel_refuse_btn').data('number');
            let memo_content = $('textarea[name="cancel_refuse_memo"]').val();
            if(confirm('취소 요청을 거절하시겠습니까?\n(관리자 메모 등록)')){
                $.ajax({
                    url: "{{ route('cancel-refuse') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        "order_number" : order_number,
                        "memo_content" : memo_content,
                        "register" : name
                    },
                    success: function(response) {
                        if(response == "SUCCESS") {
                            alert("등록 완료");
                            location.reload();
                        } else {
                            alert(response);
                        }
                    },
                    error: function (error){
                        console.log(error);
                        alert('에러발생! 개발팀에 문의하세요.');
                    }
                });
            }
        }

    </script>
@endsection




